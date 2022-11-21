<?php

namespace FME\Ups;

use App\Address;
use App\Product;
use Ups\Entity\Package;
use Ups\Entity\Service;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Ups\Entity\PackagingType;
use Ups\Entity\UnitOfMeasurement;
use Illuminate\Support\Collection;
use Ups\Entity\ShipmentTotalWeight;
use Illuminate\Support\Facades\Cache;
use Ups\Entity\ShipmentServiceOptions;
use FME\Ups\Traits\Services\RatesService;
use FME\Ups\Traits\Services\ShippingService;
use FME\Ups\Traits\Services\AddressValidationService;

class UpsRepository
{
    use AddressValidationService, RatesService, ShippingService;

    /**
     * Max weight per package in lbs
     */
    public const MAX_PACKAGE_WEIGHT = 150;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * UPS Repository constructor
     */
    public function __construct()
    {
        $this->config = config('ups');

        if (empty($this->config)) {
            throw new \Exception("Invalid UPS config");
        }

        foreach (['accessKey', 'userId', 'password', 'sandbox'] as $key) {
            if (isset($this->config[$key])) {
                continue;
            }

            throw new \Exception("{$key} is required for UPS");
        }

        set_time_limit(0);
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     *
     * @return self
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return \Ups\Entity\Address
     */
    protected function getShipFrom()
    {
        $address = (new \Ups\Entity\Address())
            ->setPostalCode(config('ups.shipFrom.postalCode'))
            ->setAddressLine1(config('ups.shipFrom.street1'))
            ->setStreetName(config('ups.shipFrom.street1'))
            ->setCity(config('ups.shipFrom.city'))
            ->setPoliticalDivision2(config('ups.shipFrom.city'))
            ->setStateProvinceCode(config('ups.shipFrom.state'))
            ->setPoliticalDivision1(config('ups.shipFrom.state'))
            ->setCountryCode(config('ups.shipFrom.country'));

        return (new \Ups\Entity\ShipFrom())
            ->setAddress($address)
            ->setName(config('ups.shipFrom.name'))
            ->setCompanyName(config('ups.shipFrom.company'))
            ->setPhoneNumber(config('ups.shipFrom.phone'))
            ->setEmailAddress(config('ups.shipFrom.email'));
    }

    /**
     * @return \Ups\Entity\Address
     */
    protected function getShipper()
    {
        $address = (new \Ups\Entity\Address())
            ->setPostalCode(config('ups.shipFrom.postalCode'))
            ->setAddressLine1(config('ups.shipFrom.street1'))
            ->setStreetName(config('ups.shipFrom.street1'))
            ->setCity(config('ups.shipFrom.city'))
            ->setPoliticalDivision2(config('ups.shipFrom.city'))
            ->setStateProvinceCode(config('ups.shipFrom.state'))
            ->setPoliticalDivision1(config('ups.shipFrom.state'))
            ->setCountryCode(config('ups.shipFrom.country'));

        return (new \Ups\Entity\Shipper())
            ->setAddress($address)
            ->setShipperNumber(config('ups.accountNumber'))
            ->setName(config('ups.shipFrom.name'))
            ->setCompanyName(config('ups.shipFrom.company'))
            ->setPhoneNumber(config('ups.shipFrom.phone'))
            ->setEmailAddress(config('ups.shipFrom.email'));
    }

    /**
     * @return \Ups\Entity\Address
     */
    protected function getShipTo(Address $address)
    {
        $validated = json_decode($address->validated_response);

        if (! is_object($validated)) {
            throw new \Exception("Invalid ups Address");
        }

        $upsAddress = (new \Ups\Entity\Address())
            ->setAddressLine1($validated->addressLine)
            ->setAddressLine2($validated->addressLine2)
            ->setAddressLine3($validated->addressLine3)
            ->setPostalCode($address->zipcode)
            ->setPostcodePrimaryLow($validated->postcodePrimaryLow)
            ->setPostcodeExtendedLow($validated->postcodeExtendedLow)
            ->setCity($address->city)
            ->setPoliticalDivision2($validated->politicalDivision2)
            ->setStateProvinceCode($address->state->abv)
            ->setPoliticalDivision1($validated->politicalDivision1)
            ->setCountryCode($validated->countryCode);

        return (new \Ups\Entity\ShipTo())
            ->setAddress($upsAddress)
            ->setCompanyName($address->order->name)
            ->setAttentionName($address->order->name)
            ->setPhoneNumber($address->order->phone)
            ->setEmailAddress($address->order->email);
    }

    /**
     * @return \Ups\Entity\SoldTo
     */
    protected function getSoldTo(Address $address)
    {
        $validated = json_decode($address->validated_response);

        if (! is_object($validated)) {
            throw new \Exception("Invalid ups Address");
        }

        $upsAddress = (new \Ups\Entity\Address())
            ->setAddressLine1($validated->addressLine)
            ->setAddressLine2($validated->addressLine2)
            ->setAddressLine3($validated->addressLine3)
            ->setPostalCode($address->zipcode)
            ->setPostcodePrimaryLow($validated->postcodePrimaryLow)
            ->setPostcodeExtendedLow($validated->postcodeExtendedLow)
            ->setCity($address->city)
            ->setPoliticalDivision2($validated->politicalDivision2)
            ->setStateProvinceCode($address->state->abv)
            ->setPoliticalDivision1($validated->politicalDivision1)
            ->setCountryCode($validated->countryCode);

        return (new \Ups\Entity\SoldTo())
            ->setAddress($upsAddress)
            ->setAttentionName($address->order->name)
            ->setPhoneNumber($address->order->phone)
            ->setEmailAddress($address->order->email);
    }

    /**
     * @param  Collection   $products
     * @param  bool|boolean $allowFreeShipping
     *
     * @return array
     */
    protected function getPackages(Collection $products, bool $allowFreeShipping = false)
    {
        $totalWeight = 0;
        $packages = [];

        $products->each(function ($cartItem) use (&$totalWeight, &$packages, $allowFreeShipping) {
            $product = $cartItem instanceof Product ? $cartItem : $cartItem->product;

            if ($product->is_free_shipping && $allowFreeShipping) {
                return true;
            }

            $weight = floatval($product->weight) > 0
                ? $product->weight
                : 1;

            if (! $cartItem instanceof Product) {
                $weight *= $cartItem->quantity;
            } elseif (isset($product->pivot)) {
                $weight *= $product->pivot->quantity;
            }

            // add package
            if ($totalWeight > abs(self::MAX_PACKAGE_WEIGHT - $weight) && $totalWeight < self::MAX_PACKAGE_WEIGHT) {
                $package = new Package();
                $package->getPackagingType()->setCode(PackagingType::PT_PACKAGE);
                $this->setWeight($package, $totalWeight);
                $packages[] = $package;
                $totalWeight = 0;
            }

            $totalWeight += $weight;
        });

        // total packges to send
        $iterations = ceil($totalWeight/self::MAX_PACKAGE_WEIGHT);

        for ($i = 0; $i < $iterations; $i++) {
            $package = new Package();
            $package->getPackagingType()->setCode(PackagingType::PT_PACKAGE);

            $weight = self::MAX_PACKAGE_WEIGHT;

            // last iteration
            if ($i == ($iterations - 1)) {
                $weight = $totalWeight - self::MAX_PACKAGE_WEIGHT * ($iterations -1);
            }

            if ($weight < 0) {
                continue;
            }
            
            // add product as a package
            $this->setWeight($package, $weight);
            $packages[] = $package;
        }

        return $packages;
    }

    /**
     * Set package weight
     *
     * @param &$package
     * @param float $product
     */
    protected function setWeight(&$package, $weight)
    {
        // set package weight
        $weight = round($weight);
        
        // we can't send a package with 0lbs
        if ($weight <= 0) {
            $weight = 1;
        }

        $package->getPackageWeight()->setWeight($weight);
        $package->getPackageWeight()->setUnitOfMeasurement(
            ( new UnitOfMeasurement )->setCode(UnitOfMeasurement::UOM_LBS)
        );
    }

    /**
     * @param  string|null $serviceCode
     * @return array
     */
    public function getEnabledServices(string $serviceCode = null)
    {
        if ($serviceCode === null) {
            return config('ups.services');
        }

        $index = array_search($serviceCode, array_column(config('ups.services'), 'Code'));

        if ($index === false || ! isset(config('ups.services')[$index])) {
            throw new \Exception("Invalid service");
        }

        return [
            config('ups.services')[$index]
        ];
    }
}
