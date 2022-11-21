<?php

namespace App\Repositories;

use App\State;
use App\Product;
use App\Zipcode;
use App\Discount;
use Illuminate\Http\Request;
use App\Shop\Carts\ShoppingCart;
use Illuminate\Support\Collection;
use App\Repositories\CartRepository;
use Gloudemans\Shoppingcart\CartItem;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Contracts\DiscountRepositoryContract;

class DiscountRepository implements DiscountRepositoryContract
{
    /**
     * DiscountRepository constructor.
     * @param Discount $discount
     */
    public function __construct(Discount $discount)
    {
        $this->model = $discount;
    }

    /**
     * Create the discount
     *
     * @param array $params
     * @return Discount
     * @throws QueryException
     */
    public function createDiscount(array $params) : Discount
    {
        $params['coupon_code'] = strtoupper($params['coupon_code']);
        try {
            return $this->create($params);
        } catch (QueryException $e) {
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * Update the discount
     *
     * @param array $params
     *
     * @return bool
     * @throws QueryException
     */
    public function updateDiscount(array $params) : bool
    {
        $params['coupon_code'] = strtoupper($params['coupon_code']);
        try {
            return $this->update($params);
        } catch (QueryException $e) {
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * Return the discount
     *
     * @param int $id
     *
     * @return Discount
     * @throws ModelNotFoundException
     */
    public function findDiscountById(int $id) : Discount
    {
        try {
            return $this->findDiscountsByIsActive()->where('id', $id)->first();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Discount not found.');
        }
    }
    /**
     * Return discounts
     *
     *
     * @return Collection:mixed
     * @throws ModelNotFoundException
     */
    public function findDiscountsByIsActive()
    {
        try {
            return $this->model::all()->where('is_active', 1)->where('activation_date', '<=', date('Y-m-d h:i:s \G\M\T'))->where('expiration_date', '>', date('Y-m-d h:i:s \G\M\T'));
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Discount not found.');
        }
    }

    /**
     * Return boolean
     *
     * @param Mixed $code - id or coupon code
     * @return Boolean;
     * @throws ModelNotFoundException
     * 
     */
    public function isActive($code)
    {
        if (gettype($code) === 'string') {
            $discount = $this->findDiscountByCouponCode($code);
        } elseif (gettype($code) === 'integer') {
            $discount = $this->findDiscountById($code);
        } else {
            throw new ModelNotFoundException('Invalid discount search.');
        }

        return $this->findDiscountsByIsActive()->contains($discount);
    }

    /**
     * Return discounts or null
     *
     *
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findActiveRedemptionDiscounts()
    {
        try {
            return $this->findDiscountsByIsActive()->where('collects_email', 1);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    public function isRedeemable($code)
    {
        if (gettype($code) === 'string') {
            $discount = $this->findDiscountByCouponCode($code);
        } elseif (gettype($code) === 'integer') {
            $discount = $this->findDiscountById($code);
        } else {
            throw new ModelNotFoundException('Invalid discount search.');
        }

        try {
            return $this->findActiveRedemptionDiscounts()->contains($discount);
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Return the discount associated with a provided coupon code
     *
     * @param String $code
     *
     * @return Discount or Null
     * @throws ModelNotFoundException
     */
    public function findDiscountByCouponCode($code)
    {
        try {
            return $this->findDiscountsByIsActive()->where('coupon_code', $code)->first();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Discount not found.');
        }
    }

    /**
     * Return all the discounts
     *
     * @param string $order
     * @param string $sort
     * @return Collection|mixed
     */
    public function listDiscounts(string $order = 'id', string $sort = 'desc') : Collection
    {
        return $this->all(['*'], $order, $sort);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteDiscount()
    {
        return $this->delete();
    }

    /**
     * Get the value of the active coupon code if any
     *
     * ****TODO: Switch to using the discount_applied session variable *******
     * This function should wind up providing flexibility for different types of discounts
     *
     * @param $code
     *
     * @return double
     */
    public function getCouponCodeVal($code)
    {
        $validCodesData = $this->findDiscountsByIsActive();

        $validCodes = $validCodesData->flatMap(function ($vals) {
            return array($vals->coupon_code => $vals->discount_amount);
        })->toArray();

        $discount = 1.00;

        if (gettype($code) === 'string') {
            if (array_key_exists($code, $validCodes)) {
                $discount = $validCodes[$code];
            }
        }

        return $discount;
    }

    /**
     * Convert a discount quantity to a displayable value
     *
     * @param mixed $discountAmount - the discount quantity
     *
     * @return String
     */
    public function formatDiscountAmount($discountAmount, $discountMethod = 'dollars')
    {
        if ($discountMethod == 'percentage') {
            $formatted_discount = ($discountAmount * 100) . '%';
        } elseif ($discountMethod == 'dollars') {
            $formatted_discount = '$' . $discountAmount;
        } else {
            $formatted_discount = '';
        }
        return $formatted_discount;
    }

    /**
     * Return shipping rate with best applicable discount applied
     *
     * @param decimal $total - The cart total for comparison to trigger value
     * @param decimal $shipping - The preexisting shipping rate
     *
     * @return decimal updated shipping rate
     * @throws ModelNotFoundException
     */
    /* public function applyDiscountShipping($total, $shipping)
    {
        //Are there any shipping-related discounts?
        //If no, return the passed-in $shipping.
        //Otherwise, pass that list down
        try {// Check if there are any discounts linked to shipping
            $shippingDiscounts = $this->findDiscountsByIsActive()->where('shipping_id', '!=', null);
        } catch (ModelNotFoundException $e) {// If none return original shipping value
            return !is_null($shipping) ? $shipping : $shippingRepo->findActiveShipping()->cost;
        }

        $shippingRepo = new ShippingRepository(new Shipping);
        $activeShipping = $shippingRepo->findActiveShipping();

        $activeShipping = is_null($activeShipping) ? null: $activeShipping->id;
        if (!is_null($activeShipping)) {
            session()->put('shippingId', $activeShipping);
        }
        $activeDiscountId = request()->session()->get('active_discount_id');
        // is there an active discount and does that discount have a linked shipping?
        if (!is_null($activeDiscountId) && !is_null($this->findDiscountById($activeDiscountId)->shipping_id)) {
            // If yes, set that shipping to the active shipping and return that
            $activeShipping = $shippingRepo->findShippingById($this->findDiscountById($activeDiscountId)->shipping_id);
            if (!is_null($activeShipping)) {
                request()->session()->put('shippingId', $activeShipping->id);
                $shipping = $activeShipping->cost;
            }
        } else {
            // Check if there are applicable discounts
            $discountsApplicable = $shippingDiscounts->where('is_triggerable', 1)->where('trigger_amount', '<=', $total);// Only discounts that have been triggered

            $shippingDiscount = $discountsApplicable->reduce(function ($carry, $item) {// get the lowest discount rate
                return $item->discount_amount < $carry->discount_amount ? $item : $carry;
            }, $discountsApplicable->first());

            // Set shipping if there is a discount to be applied
            if (!is_null($shippingDiscount) && (float)$total >= $shippingDiscount->trigger_amount) {
                $activeShipping = $shippingDiscount->shipping;
                $shipping = $activeShipping->is_free === 0 ? $activeShipping->cost : 0.00;// account for percentages?
            } else {
                $shipping = is_null($activeShipping) ? 0 : $activeShipping->cost;
            }
        }

        if(!is_null($activeShipping)) request()->session()->put('shippingId', $activeShipping->id);

        return $shipping;
    } */
    
    /**
     * Gather active discount details and deliver them back to the controller.
     *
     * @param Request $request
     * @param boolean $wholesale
     *
     * @return Array
     */
    public function getDiscountData(Request $request, $wholesale = false) : array
    {
        $currentDiscount = $request->session()->get('currentDiscount');
        $discountAmount = $this->formatDiscountAmount($currentDiscount->discount_amount, $currentDiscount->discount_method);
        $discountType = $request->session()->get('active_discount_type', 'total');
        $discountId = $request->session()->get('active_discount_id', false);
        $discount = $discountId ? $this->findDiscountById($discountId) : null;
        $discount_products = is_array($request->session()->get('discount_products')) ? $request->session()->get('discount_products') : [];
        $discountProducts = $discountType === 'products' ? $discount->products->map(function ($item) {
            return $item->id;
        })->toArray() : [];
        return compact('discountAmount', 'discountType', 'discountId', 'discount', 'discountProducts', 'discountAmount');
    }

    /**
     * Apply Coupon Code
     * 
     * @param String $code
     * 
     */
    public function applyCouponCode($code)
    {

        $cartRepo = new CartRepository();
        if ($this->isActive($code) && !$this->isRedeemable($code)) {// Supplied code is valid
            $discount = $this->findDiscountByCouponCode($code);

            $cartRepo->setDiscount($discount);

            $rate = $discount->discount_amount;
            // Put in controls to make sure discount is properly formatted
            $formattedRate = $discount->discount_method == 'percentage' ? $rate * 100 : $rate;
            
            session([
                'currentDiscount' => $discount
            ]);
            
            $discountProducts = $discount->discount_type === 'products' ? $discount->products->map(function ($item) {
                return $item->id;
            })->toArray() : null;
            $totals = $this->updateOrderTotals(request(), $discountProducts);
            // $shipping = $this->applyDiscountShipping($totals->subtotal, null);
            return json_encode(array(
                'discount'  => $discount,
                'products'  => $discountProducts,
                'totals'    => $totals));
        } else {
            return 'code invalid';
        }
    }

    /**
     * updateOrderTotals
     *
     * @param  Request $request
     * @param  mixed $discountProducts
     *
     * @return void
     */
    public function updateOrderTotals(Request $request, $discountProducts = null)
    {
        $cartRepo = new CartRepository();
        
        // Get session-based discount data
        $discountData = $this->getDiscountData($request);

        $subtotal = $cartRepo->getSubTotal();

        $total = $cartRepo->getTotal();
        
        return $discountData;

        $tax = $cartRepo->getTax();
        if(strpos($tax,',') !== FALSE) {
            $tax = join("",explode(",", $tax));
        }
        $tax = (float)$tax;
        $tax_amount = $tax + $shippingTax;
        
        return json_encode(array(
            'discount'  => $discountData['discountAmount'],
            'total'     => $total,
            'subtotal'  => $subtotal,
            'shipping'  => number_format($shipping, 2),
            'tax'       => number_format($tax_amount, 2)
            // Shipping to repopulate list dynamically
        ));
    }

    /**
     * applyDiscountProductPrice
     *
     * @param  Product $product
     * @param  array $discountData
     *
     * @return Product
     */
    public function applyDiscountProductPrice(Product $product, array $discountData) : Product
    {
        $product->is_discounted = FALSE;
        if($discountData['discount'] && $discountData['discount']->products->contains($product)) {
            $discountProduct = $discountData['discount']->products->where('id', $product->id)->first();
            $originalPrice = $product->price;
            $product->price = number_format($discountProduct->pivot->discount_product_price === NULL ? $originalPrice : $discountProduct->pivot->discount_product_price, 2);
            if(auth()->user() && auth()->user()->wholesale) {
                $product->price = $product->price - (auth()->user()->wholesale_discount * $product->price);
                $product->sale_price = $product->sale_price - (auth()->user()->wholesale_discount * $product->sale_price);
            }
            $product->is_discounted = TRUE;
        }
        return $product;
    }
}
