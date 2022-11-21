<?php

namespace FME\Ups\Traits;

use App\State;
use App\Address;
use App\Zipcode;

trait ValidatedAddressTrait
{

    /**
     * @param  array  $data
     * @return Address
     */
    public function createValidatedAddress(array $data) : Address
    {
        try {
            $state = State::whereAbv($data['politicalDivision1'])->firstOrFail();
            $stateId = $state->id;
        } catch (Exception $e) {
            try {
                $zipcode = Zipcode::whereName($data['postcodePrimaryLow'])->firstOrFail();
                $stateId = $zipcode->state_id;
            } catch (\Exception $e) {
            }
        }

        $cityName = $data['politicalDivision2'] ?? null;

        try {
            if ($cityName === '') {
                throw new \Exception("Error Processing Request");
            }
        } catch (\Exception $e) {
            try {
                $zipcode = Zipcode::whereName($data['postcodePrimaryLow'])->firstOrFail();
                $city = $zipcode->city;
                if (! $city instanceof City) {
                    throw new \Exception("Error Processing Request");
                }
                $cityName = $city->name;
            } catch (\Exception $e) {
            }
        }

        return Address::create([
            'country_id' => 1,
            'address_1' => $data['addressLine'],
            'address_2' => ($data['addressLine2'] ?? null) . ($data['addressLine3'] ?? null),
            'validated_response' => json_encode($data),
            'zipcode' => $data['postcodePrimaryLow'] . '-' . $data['postcodeExtendedLow'],
            'state_id' => $stateId ?? null,
            'city' => $cityName ?? null,
            'type' => 'validated',
        ]);
    }
}
