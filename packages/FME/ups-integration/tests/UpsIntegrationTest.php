<?php

namespace Tests\Feature;

use App\Order;
use App\Address;
use App\Zipcode;
use Tests\TestCase;
use App\Repositories\AddressRepository;
use App\Repositories\UPS\UpsRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpsIntegrationTest extends TestCase
{
    /**
     * @return void
     */
    public function it_can_validate_address()
    {
        $ups = new UpsRepository();

        $address = Address::find(29);

        // $address = Address::create([
        //     'address_1' => '105 Erickson Ave',
        //     'zipcode' => '32145',
        //     'city' => 'Hastings',
        //     'state_id' => '8'
        // ]);

        $response = $ups->validateAddress($address);

        if ($response === false) {
            // invalid address
        }

        if (! empty($response)) {
            // suggestions
        }

        $this->assertTrue(is_object($response));
    }

    /**
     * @return void
     */
    public function it_can_create_address_from_validated_data()
    {
        $data = [
            "addressClassification" => [
                "code" => [
                    0 => "0"
                ],
                "description" => [
                    0 => "Unknown"
                ]
            ],
            "consigneeName" => null,
            "buildingName" => null,
            "addressLine" => "10001-10099 ERICKSON AVE",
            "addressLine2" => null,
            "addressLine3" => null,
            "region" => "HASTINGS FL 32145-8870",
            "politicalDivision2" => "HASTINGS", // city
            "politicalDivision1" => "FL", // state
            "postcodePrimaryLow" => "32145",
            "postcodeExtendedLow" => "8870",
            "urbanization" => null,
            "countryCode" => "US",
        ];

        $addressRepo = new AddressRepository();

        $address = $addressRepo->createValidatedAddress($data);

        $this->assertTrue($address->state->abv == $data['politicalDivision1']);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_get_shipping_rates_for_order()
    {
        $ups = new UpsRepository();

        $order = Order::find(30);

        $response = $ups->getRates($order->validatedAddress, $order->products);
    }
}
