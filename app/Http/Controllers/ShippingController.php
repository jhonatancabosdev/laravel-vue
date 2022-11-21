<?php

namespace App\Http\Controllers;

use App\Order;
use FME\Ups\UpsFacade;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Repositories\CartRepository;
use App\Repositories\UPS\UpsRepository;
use App\Http\Requests\UpdateUpsOptionsRequest;
use App\Http\Requests\UpdateShippingOptionRequest;
use App\Repositories\ShipStation\ShipStationRepository;

class ShippingController extends Controller
{
    /**
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * Shipping controller
     */
    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @return \Illuminate\Support\Collection|Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $order = Order::whereIn('id', Arr::wrap(session('order')))
            ->firstOrFail();

        $products = $this->cartRepository->getCartItemsTransformed();

        try {
            $rates = UpsFacade::getRates($order->validatedAddress, $products);
            return collect($rates)->sortBy('cost');
        } catch (\Exception $e) {
            return response()->json([
                'message'=> $e->getMessage()
            ], 422);
        }
    }

    /**
     * @param UpdateUpsOptionsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUpsOptionsRequest $request): \Illuminate\Http\JsonResponse
    {
        $rate = (Object) $request->only([
            'label', 'slug', 'serviceCode', 'cost'
        ]);

        if (! is_object($rate)) {
            throw new \Exception("Invalid shipping");
        }

        $this->cartRepository->setShipping($rate->cost);

        session(['shipping' => $rate]);

        return response()->json(
            (float) $this->cartRepository->getShipping()
        );
    }
}
