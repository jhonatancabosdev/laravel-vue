<?php

namespace App\Http\Controllers;

use App\Shipping;
use App\{Zipcode, Order};
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Events\OrderCreateEvent;
use App\Http\Controllers\Controller;
use App\Repositories\AddressRepository;
use App\Http\Requests\ConfirmCheckoutRequest;
use App\Http\Requests\ExecuteCheckoutRequest;
use App\Repositories\Contracts\CartRepositoryContract;
use App\Repositories\Payment\{AuthorizeNetRepository, PaypalRepository};
use App\Repositories\{CartRepository, NofraudRepository, OrderRepository, OrderProductRepository};

class CheckoutBaseController extends Controller
{
    /**
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var PaypalRepository
     */
    protected $paypalRepository;

    /**
     * @var AddressRepository
     */
    protected $addressRepository;

    /**
     * @var AuthorizeNetRepository
     */
    protected $authorizeNetRepository;

    /**
     * @var NofraudRepository
     */
    protected $nofraudRepository;

    /**
     * -------------------------------------------------------------------
     * Checkout base controller
     * -------------------------------------------------------------------
     *
     */
    public function __construct(
       OrderRepository $orderRepository,
       PaypalRepository $paypalRepository,
       AddressRepository $addressRepository,
       AuthorizeNetRepository $authorizeNetRepository,
       CartRepositoryContract $cartRepository,
       NofraudRepository $nofraudRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->addressRepository = $addressRepository;
        $this->authorizeNetRepository = $authorizeNetRepository;
        $this->paypalRepository = $paypalRepository;
        $this->nofraudRepository = $nofraudRepository;
    }

    /**
     * Choose shipping
     *
     * @return Response
     */
    public function shipping(Request $request)
    {
        $order = Order::findOrFail(
           Arr::wrap(session('order'))
        )->first();

        $weight = $this->cartRepository->getTotalWeight();// Shipping becomes flat rate when the weight is over 50 lbs

        return view('front.checkout.shipping', [
            'order'     => $order,
            'weight'    => $weight
        ]);
    }

    /**
     * Execute payment
     *
     * @param ExecuteCheckoutRequest $request
     *
     * @return Illuminate\Http\JsonResponse|string
     */
    public function execute(ExecuteCheckoutRequest $request)
    {
        $order = Order::findOrFail(session('order'));

        if (! session()->has('shipping') && ! is_object(session('shipping'))) {
            return back()->with('error', 'Invalid shipping option');
        }

        $selectedShipping = session('shipping');

        try {
            $order = $this->orderRepository->updateOrder($order, $request->all(), $selectedShipping);
            $order->refresh();
        } catch (\Exception $exception) {
            return response()->json([
                'message'=> 'Invalid shipping option'
            ], 422);
        }

        // if user select to pay with paypal process method will get redirect url
        // all payment process is done within paypal sandbox or live api
        // if anything went wrong user is returned to checkout page
        if ($order->payment_method === 'paypal') {
            $this->paypalRepository->init();
            return response()->json($this->paypalRepository->process($order));
        }

        try {
            $this->nofraudRepository->process($order);
            $this->authorizeNetRepository->process($order);
            $this->cartRepository->clear();
            return response()->json(route('checkout.success'));
        } catch (\Exception $exception) {
            return response()->json([
                'message'=> $exception->getMessage()
            ], 422);
        }
    }

    /**
     * Confirm paypal payment
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request)
    {
        $order = Order::findOrFail(session('order'));
        
        if ($order->payment_method !== 'paypal') {
            return redirect()->back();
        }

        try {
            $this->paypalRepository->check($request)->confirm($order);
            $this->cartRepository->clear();
            return redirect()->route('checkout.success');
        } catch (\Exception $exception) {
            return response()->json([
                'message'=> $exception->getMessage()
            ], 422);
        }
    }

    /**
     * Get success page
     *
     * @return \Illuminate\View\View
     */
    public function success(): \Illuminate\View\View
    {
        $order = Order::findOrFail(session('order'));

        $this->orderRepository->confirmOrder($order);
        
        session()->forget('order');
        session()->forget('shipping');
        
        event(new OrderCreateEvent($order));

        return view('front.checkout.success', [
            'order' => $order
        ]);
    }
}