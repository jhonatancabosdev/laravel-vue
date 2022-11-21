<?php

namespace App\Repositories\UPS;

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

class UpsRepository
{
	/**
	 * Max weight per package in lbs
	 */
	public const MAX_PACKAGE_WEIGHT = 150;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * UPS Repository constructor
	 */
	public function __construct()
	{
		$this->config = config('ups');

		if (empty($this->config)) {
			throw new \Exception("Invalid UPS config");
		}

		foreach (['accessKey', 'userId', 'password'] as $key) {
			if (isset($this->config[$key]) && trim($this->config[$key]) !== '') {
				continue;
			}

			throw new \Exception("$key is required for UPS");
		}
	}

	/**
	 * @return \Ups\AddressValidation
	 */
	public function getAddressValidatorService()
	{
		return tap(
			new \Ups\AddressValidation($this->config['accessKey'], $this->config['userId'], $this->config['password'], false), 
			function($validator) {
				$validator->activateReturnObjectOnValidate();
			}
		);
	}

	/**
	 * Address Validation
	 * 
	 * @return mixed
	 */
	public function validateAddress(Address $address)
	{
		if (! config('ups.cache')) {
			return json_decode(json_encode($this->validateAddressRequest($address)));
		}

		$self = $this;
		$cacheKey = md5($address->toJson());

		return Cache::remember($cacheKey, now()->addDays(30), function() use ($self, $address) {
			$response = $self->validateAddressRequest($address);
			return json_decode(json_encode($response));
		});
	}

	/**
	 * @param  Address $address
	 * @return response
	 */
	protected function validateAddressRequest(Address $address)
	{
		$upsAddress = new \Ups\Entity\Address();
		$upsAddress->setStateProvinceCode($address->state->abv);
		$upsAddress->setCity($address->city);
		$upsAddress->setAddressLine1($address->address1);
		$upsAddress->setAddressLine2($address->address2);
		$upsAddress->setCountryCode('US');
		$upsAddress->setPostalCode($address->zipcode);

		$response = $this->getAddressValidatorService()->validate(
			$upsAddress, \Ups\AddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION_AND_CLASSIFICATION, 3
		);

		// Invalid Address
		if ($response->noCandidates()) {
			return false;
		}

		// needs user validation
		if ($response->isAmbiguous()) {
			return $response->getCandidateAddressList();
		}

		// valid address
	    return $response->getValidatedAddress();
	}

	/**
	 * @return \Ups\Rate
	 */
	public function getRatesService()
	{
		return new \Ups\Rate(
			$this->config['accessKey'], $this->config['userId'], $this->config['password']
		);
	}

	/**
	 * @return \Ups\Entity\Address
	 */
	public function getShipFrom()
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
	public function getShipper()
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
	    	->setName(config('ups.shipFrom.name'))
	    	->setCompanyName(config('ups.shipFrom.company'))
	    	->setPhoneNumber(config('ups.shipFrom.phone'))
	    	->setEmailAddress(config('ups.shipFrom.email'));
	}

	/**
	 * @return \Ups\Entity\Address
	 */
	private function getShipTo(Address $address)
	{
		$validated = json_decode($address->validated_response);

		if (! is_object($validated)) {
			throw new \Exception("Invalid Address for rates");
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
	    	->setAttentionName($address->order->name)
	    	->setPhoneNumber($address->order->phone)
	    	->setEmailAddress($address->order->email);
	}

	/**
	 * @param  Collection   $products         
	 * @param  bool|boolean $allowFreeShipping
	 * @return array
	 */
	protected function getPackages(Collection $products, bool $allowFreeShipping = true)
	{
        $totalWeight = 0;
        $packages = [];

        $products->each(function($cartItem) use (&$totalWeight, &$packages, $allowFreeShipping) {

        	$product = $cartItem instanceof Product ? $cartItem : $cartItem->product;

        	if ($product->is_free_shipping && $allowFreeShipping) {
        		return true;
        	}

        	$weight = $product->weight;

        	if (! $cartItem instanceof Product) {
        		$weight *= $cartItem->quantity;
        	} else if (isset($product->pivot)) {
        		$weight *= $product->pivot->quantity;
        	}

    		// add package
        	if ( $totalWeight > abs(self::MAX_PACKAGE_WEIGHT - $weight) && $totalWeight < self::MAX_PACKAGE_WEIGHT ) {
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
	        if ( $i == ( $iterations - 1 ) ) {
	        	$weight = $totalWeight - self::MAX_PACKAGE_WEIGHT * ( $iterations -1 );
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
	private function setWeight(&$package, $weight)
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
	private function getEnabledServices(string $serviceCode = null)
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

	/**
	 * @param  Address     $address    
	 * @param  Collection  $products   
	 * @param  string|null $serviceCode
	 * @return array
	 */
	public function getRates(Address $address, Collection $products, string $serviceCode = null)
	{
		if (! config('ups.cache')) {
			return $this->getRatesRequest($address, $products, $serviceCode);
		}

		$self = $this;
		$cacheKey = md5($address->toJson . $products->toJson() . $serviceCode);

		return Cache::remember($cacheKey, now()->addDays(30), function() use ($self, $address, $products, $serviceCode) {
		    return $this->getRatesRequest($address, $products, $serviceCode);
		});
	}

	/**
	 * @param  \Ups\Entity\Shipment $shipment
	 * @param  array                $service
	 * @return arrya
	 */
	private function getRatesForService(\Ups\Entity\Shipment $shipment, array $service)
	{
		$response = [];	

		$ratesService = $this->getRatesService();

		try {
			    
		    $rate = $ratesService->getRate(
		    	$shipment->setService(new Service((Object) $service))
		    );

		    $rate = json_decode(json_encode($rate->RatedShipment), true)[0];

		    if (config('app.env') === 'local') {
		    	$response['response'] = $rate;
		    }

		    $response['label'] = $service['label'];
		    $response['slug'] = Str::slug($service['label']);
		    $response['serviceCode'] = $service['Code'];
		    $response['cost'] = $rate['TotalCharges']['MonetaryValue'] ?? 0;
		    $response['warning'] = $rate['RateShipmentWarning'];
		    $response['shipping_label'] = $rate['shipping_label'] ?? null;

		    if (isset($rate['GuaranteedDaysToDelivery']) && trim($rate['GuaranteedDaysToDelivery']) !== '') {
			    $response['GuaranteedDaysToDelivery'] = $rate['GuaranteedDaysToDelivery'];
			    $response['deliveryDate'] = now()->addWeekdays($rate['GuaranteedDaysToDelivery'])->format('l, m/d/Y');
		    }

		    return (Object) $response;

    	} catch (\Exception $e) {
    	}

    	return (Object) $response;
	}

	/**
	 * @param  Address     $address    
	 * @param  Collection  $products   
	 * @param  string|null $serviceCode
	 * @return array
	 */
	private function getRatesRequest(Address $address, Collection $products, string $serviceCode = null)
	{

	    $response = [];
		$shipment = new \Ups\Entity\Shipment();
	    $shipment->setShipper($this->getShipper());
	    $shipment->setShipFrom($this->getShipFrom());
	    $shipment->setShipTo($this->getShipTo($address));

	    $packages = $this->getPackages($products);

	    if (empty($packages) && ! $products->isEmpty()) {

	    	$freeShippingOptions = $products->map(function($product) {
	    		return $product instanceof Product 
	    			? $product->freeShippingService 
	    			: $product->product->freeShippingService;
	    	})
	    	->values();

	    	$packages = $this->getPackages($products, false);
	    }

	    $shipment->setPackages($packages);

	    foreach ($this->getEnabledServices($serviceCode) as $service) {

	    	try {

			    $key = Str::slug($service['label']);

	    		if (isset($freeShippingOptions) && $service['Code'] == $freeShippingOptions[0]->serviceCode) {
	    			$response[$key] = $freeShippingOptions[0];
	    			continue;
	    		}

	    		$response[$key] = $this->getRatesForService($shipment, $service);

	    	} catch (\Exception $e) {
	    	}
	    }

	    return $response;
	}
}