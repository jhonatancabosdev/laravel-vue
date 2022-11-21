<?php

namespace App\Http\Controllers;

use App\{Order, Shipping, Discount, Address};
use Illuminate\Http\Request;
use App\Repositories\CheckoutRepository;
use App\Http\Requests\CustomerCheckoutRequest ;

class CheckoutController extends CheckoutBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $customer = $this->loggedUser();

        if ($customer->addresses->isEmpty()) {
            return redirect()->route('customer.address.create', $customer)
                ->with('message', 'Create your billing address first.');
        }

        $addresses = $customer->addresses()->whereIn('type', ['billing', 'shipping'])->with(['state', 'country'])->get();

        return view('front.customers.checkout', compact('addresses', 'customer'));
    }

    /**
     * Checkout the items
     *
     * @param CartCheckoutRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @codeCoverageIgnore
     */
    public function store(CustomerCheckoutRequest $request)
    {
        $data = $request->except('_token');

        $data['customer_id'] = $this->loggedUser()->id;

        $this->cartRepository->checkItemsStock();

        $cartItems = $this->cartRepository->getCartItemsTransformed();

        if ($cartItems->isEmpty()) {
            return redirect()->route('guest.checkout.index');
        }

        // Create customer order with required attributes
        $order = $this->orderRepository->createOrder($data);

        // Build order details
        $this->orderRepository->buildOrderDetails($order, $cartItems);

        // associate address to current order
        $order->addresses()->saveMany([
            $this->addressRepository->createValidatedAddress($data['validatedAddress']),
            $this->addressRepository->createBillingAddress($data),
        ]);

        // Customer has shipping address diffrent than billing address
        // need to create shipping address and create order relation
        if ($request->boolean('shipping_address_different') === true) {
            $order->addresses()->save(
                $this->addressRepository->createShippingAddress($data)
            );
        }

        $discount = $this->cartRepository->getDiscount();

        if ($discount instanceof Discount) {
            $order->discount()->associate($discount);
        }

        session(['order' => $order->id]);

        return response()->json(true);
    }
}
