<?php

namespace App\Http\Controllers;

use FME\Ups\UpsFacade;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Repositories\AddressRepository;
use App\Http\Requests\GuestCheckoutRequest;

class AddressValidationController
{
    /**
     * @var AddressRepository
     */
    protected $addressRepository;

    /**
     * @param AddressRepository $addressRepository
     */
    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GuestCheckoutRequest $request)
    {
        if (! $request->ajax()) {
            throw new \Exception("Something went wrong");
        }

        $data = $request->except('_token');
        $address = $request->shipping_address_different == "true"
           ? $this->addressRepository->createShippingAddress($data)
           : $this->addressRepository->createBillingAddress($data);
        $response = UpsFacade::validateAddress($address);

        if ($response === false) {
            return response()->json([
                'message'=> 'Invalid address'
            ], 422);
        }

        return response()->json(
           Arr::wrap($response)
        );
    }
}
