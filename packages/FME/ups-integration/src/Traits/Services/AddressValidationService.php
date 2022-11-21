<?php

namespace FME\Ups\Traits\Services;

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

trait AddressValidationService
{
    /**
     * @return \Ups\AddressValidation
     */
    public function getAddressValidatorService(): \Ups\AddressValidation
    {
        return tap(
            new \Ups\AddressValidation($this->config['accessKey'], $this->config['userId'], $this->config['password'], $this->config['sandbox']),
            function ($validator) {
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

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($self, $address) {
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
            $upsAddress,
            \Ups\AddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION_AND_CLASSIFICATION,
            3
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
}
