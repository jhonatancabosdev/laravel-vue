<?php

namespace Tests\Feature;

use App\Order;
use App\State;
use App\Address;
use Tests\TestCase;
use FME\Ups\UpsFacade;
use App\TrackingNumber;
use FME\Ups\UpsRepository;
use FME\Ups\UpsServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use FME\Ups\Http\Controllers\AddressValidationController;

class UpsPackageIntegrationTest extends TestCase
{
    /**
     * @return void
     */
    public function it_can_get_config()
    {
        $upsConfig = config('ups');

        $this->assertTrue(is_array($upsConfig) && ! empty($upsConfig));
    }

    /**
     * @return void
     */
    public function it_can_initialize_classes()
    {
        $ups = new UpsRepository();

        $this->assertTrue($ups instanceof UpsRepository);
    }

    /**
     * @return void
     */
    public function it_can_validate_address()
    {
        $address = Address::first();
        $address->address_1 = '1721 S ELLIOTT ST';
        $address->city = 'NewYork';
        $address->state_id = State::where('abv', 'NY')->first()->id;
        $address->zipcode = '74361';

        $response = UpsFacade::validateAddress($address);

        $this->assertTrue(is_object($response));
    }

    /**
     * @return void
     */
    public function it_can_get_rates()
    {
        $order = Order::confirmed()->orderBy('id', 'desc')->first();

        $rates = UpsFacade::getRates($order->validatedAddress, $order->products);

        $this->assertTrue(is_array($rates) || is_object($rates));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_register_shipments()
    {
        $order = Order::whereHas('products')->orderBy('id', 'desc')->first();

        $order->markAsConfirmed();

        $shipping = UpsFacade::setAccountNumber(config('ups.accountNumber'))
            ->getShipping($order, $order->products);

        $disk = 'tracking';

        $tracking = UpsFacade::storeTrackingNumber($order, $shipping, $disk);

        $this->assertTrue($tracking->exists);
        $this->assertTrue($tracking instanceof TrackingNumber);
        $this->assertTrue(
            Storage::disk($disk)->exists($tracking->file_name)
        );
    }
}
