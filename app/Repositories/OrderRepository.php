<?php

namespace App\Repositories;

use App\Order;
use App\Product;
use App\Shipping;
use FME\Ups\UpsFacade;
use Illuminate\Support\Collection;
use Darryldecode\Cart\ItemCollection;
use App\Repositories\Contracts\CartRepositoryContract;

class OrderRepository extends BaseRepository
{
    /**
     * @var CartRepositoryContract
     */
    protected $cartRepository;

    /**
     * @param CartRepositoryContract $cartRepository
     * @param mixed $order
     */
    public function __construct(CartRepositoryContract $cartRepository, $order = null)
    {
        parent::__construct($order);
        $this->cartRepository = $cartRepository;
    }

    /**
     * Create order from the given data
     *
     * @param  Array $data
     * @return Order
     */
    public function createOrder(array $data) : Order
    {
        return Order::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'gclid' => session('gclid'),
            'payment_method' => $data['payment_method'] ?? '',
            'total' => $this->cartRepository->getTotal(),
            'subtotal' => $this->cartRepository->getSubTotal(),
            'shipping_cost' => $this->cartRepository->getShipping(),
            'tax_rate' => $this->cartRepository->getTaxRate(),
            'tax' => $this->cartRepository->getTax(),
            'customer_id' => $data['customer_id'] ?? null,
            'order_status_id' => 1,
        ]);
    }

    /**
     * @param  Order  $order
     * @param  array  $data
     * @param  mixed $selectedShipping
     * @return Order
     */
    public function updateOrder(Order $order, array $data, $selectedShipping)
    {
        if (strtolower($data['payment_method']) === 'credit_card') {

            // get credit card type and remove spaces from the card number
            $ccNumber = str_replace(' ', '', $data['cc_number']);
            $data['card_type'] = getCreditCardType($ccNumber);
            $data['cc_number'] = encrypt($ccNumber);

            // extract expiration month and year from the given string
            $data['cc_expiration_month'] = trim($data['cc_expiration_month']);
            $data['cc_expiration_year'] = trim($data['cc_expiration_year']);
            $data['cc_expiration'] = $data['cc_expiration_month'] . '/' . $data['cc_expiration_year'];
        }

        // remove all products from this order
        $order->products()->detach();

        // get saved cart items
        $cartItems = $this->cartRepository->getCartItemsTransformed();

        // attach products to order
        $this->buildOrderDetails($order, $cartItems);

        $rates = UpsFacade::getRates($order->validatedAddress, $cartItems);

        $rate = (Object) collect($rates)->values()->firstWhere('slug', $selectedShipping->slug);

        if (! is_object($rate)) {
            throw new \Exception("Invalid shipping");
        }

        $this->cartRepository->setShipping($rate->cost);

        $order->carriers()->delete();

        $order->carriers()->create([
            'service_name' => $rate->label,
            'service_code' => $rate->serviceCode,
            'shipment_cost' => $rate->cost,
            'other_cost' => 0,
            'carrier_code' => $rate->slug,
        ]);

        $order->update([
            'payment_method' => $data['payment_method'],
            'cc_number' => $data['cc_number'] ?? null,
            'cc_name' => $data['cc_name'] ?? null,
            'cc_expiration' => $data['cc_expiration'] ?? null,
            'cc_expiration_month' => $data['cc_expiration_month'] ?? null,
            'cc_expiration_year' => $data['cc_expiration_year'] ?? null,
            'card_type' => $data['card_type'] ?? null,
            'cc_cvv' => $data['cc_cvv'] ?? null,
            'total' => $this->cartRepository->getTotal(),
            'subtotal' => $this->cartRepository->getSubTotal(),
            'shipping_cost' => $this->cartRepository->getShipping(),
            'tax_rate' => $this->cartRepository->getTaxRate(),
            'tax' => $this->cartRepository->getTax()
        ]);

        return $order;
    }

    /**
     * @param Order $order
     * @param Collection $collection
     * @return  Collection [<description>]
     */
    public function buildOrderDetails(Order $order, Collection $collection)
    {
        return $collection->each(function ($item) use ($order) {
            $order->products()->attach(
                Product::findOrFail($item->attributes['id']),
                [
                    'quantity' => $item->quantity,
                    'price'    => $item->price,
                    'options'  => json_encode($item->attributes->toArray()),
                ]
            );
        });
    }

    /**
     * For each product we reduce quantity and increase sales counter
     * @return mixed
     */
    public function confirmOrder(Order $order)
    {
        return $order->products->each(function ($product) {
            $product->decrement('quantity', (int) $product->pivot->quantity);
            $product->increment('sales_count');
        });
    }
}
