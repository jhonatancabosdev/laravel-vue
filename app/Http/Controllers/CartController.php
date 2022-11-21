<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Repositories\Contracts\CartRepositoryContract;
use App\Repositories\Contracts\DiscountRepositoryContract;

class CartController extends Controller
{
    /**
     * Cart repository
     * 
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * Discount repository
     * 
     * @var DiscountRepository
     */
    protected $discountRepository;

    /**
     * @param CartRepositoryContract $cartRepository
     * @param DiscountRepositoryContract $discountRepository
     */
    public function __construct(CartRepositoryContract $cartRepository, DiscountRepositoryContract $discountRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->discountRepository = $discountRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'cartItems' => $this->cartRepository->getMappedCartItems(),
            'subtotal' => $this->cartRepository->getSubTotal(),
            'shipping' => $this->cartRepository->getShipping(),
            'discount' => $this->cartRepository->getDiscountValue(),
            'taxRate' => $this->cartRepository->getTaxRate(),
            'total' => $this->cartRepository->getTotal(),
            'tax' => $this->cartRepository->getTax(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AddToCartRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddToCartRequest $request)
    {
        $product = Product::findOrFail($request->id);
        $item = $this->cartRepository->findItem($product->id);
        $currentItemQty = $item ? $item->quantity : 0;
        if ($currentItemQty + $request->input('quantity', 0) > $product->quantity) {
            return response()->json([
                'message' => 'You are ordering more than we have in stock. Please order '.$product->quantity.' or less.',
                'maxQuantity' => $product->quantity
            ], 422);
        }

        $options = $request->except('_token', 'id');
        $options['main_image'] = $product->main_image;

        // create new cart item
        $cartItem = $this->cartRepository
                        ->addToCart($product, $request->quantity, $options)
                        ->get(getCartKey($product, 'direct'));

        $cartItem = $cartItem->all();
        $cartItem['deleted'] = false;


        return response()->json($cartItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCartRequest $request
     * @param int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCartRequest $request, $id)
    {

        $product = Product::findOrFail($id);
        $item = $this->cartRepository->findItem($id);
        if ($request->quantity > $product->quantity) {
            return response()->json([
                'message' => 'You are ordering more than we have in stock. Please order '.$product->quantity.' or less.',
                'maxQuantity' => $product->quantity
            ], 200);
        }

        if ($this->cartRepository->updateQuantityInCart($id , $request->quantity)) {
            return response()->json(true, 200);
        }

        return response()->json(false, 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->cartRepository->remove($id);

        return response()->json($id);
    }
    
    /**
     * Get the active Coupon Code.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCouponCode()
    {
        $discount = session()->get('currentDiscount', null);

        if($discount != null) {
            $discount = $this->applyCouponCode($discount->coupon_code);
        }

        return response()->json($discount);
    }

    /**
     * Apply the Coupon Code.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function applyCouponCode($code)
    {
        $discountResult = $this->discountRepository->applyCouponCode($code);

        return response()->json($discountResult);
    }

}
