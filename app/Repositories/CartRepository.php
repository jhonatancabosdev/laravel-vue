<?php

namespace App\Repositories;

use App\Order;
use App\Product;
use App\Shipping;
use App\Customer;
use App\Discount;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Darryldecode\Cart\CartCondition;
use App\Facades\FrozenCartFacade as FrozenCart;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Repositories\Contracts\CartRepositoryContract;

class CartRepository implements CartRepositoryContract
{
    /**
     * @param Product $product
     * @param int $quantity
     * @param array $options
     *
     * @return \Darryldecode\Cart\Cart
     */
    public function addToCart(Product $product, int $quantity, array $options = []): \Darryldecode\Cart\Cart
    {
        $options['id'] = $product->id;
        $options['slug'] = $product->slug;
        
        if (! isset($options['main_image']) && isset($product->main_image)) {
            $options['main_image'] = $product->main_image;
        }

        if (! isset($options['original_price']) && isset($product->original_price)) {
            $options['original_price'] = $product->original_price;
        }
        
        if (! isset($options['is_free_shipping']) && isset($product->is_free_shipping)) {
            $options['is_free_shipping'] = $product->is_free_shipping;
        }
        
        if (! isset($options['free_shipping_option']) && isset($product->free_shipping_option)) {
            $options['free_shipping_option'] = $product->free_shipping_option;
        }

        if (! isset($options['weight']) && isset($product->weight)) {
            $options['weight'] = $product->weight;
        }

        $options['type'] = 'direct';

        return Cart::add(
            getCartKey($product, $options['type']),
            $product->name,
            $product->price,
            $quantity,
            $options
        );
    }

    /**
     * @param int $rowId
     * @param int $quantity
     *
     * @return bool
     */
    public function updateQuantityInCart(string $rowId, int $quantity): bool
    {
        return Cart::update($rowId, [
            'quantity' => [
                'value' => $quantity,
                'relative' => false
            ]
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getCartItems(): Collection
    {
        return Cart::getContent()->reject(function ($item) {
            return ! (is_object($item) && (int) $item->attributes['id'] !== 0);
        });
    }

    /**
     * @param string $rowId
     *
     * @return bool
     */
    public function remove(string $rowId): bool
    {
        if (! Cart::remove($rowId)) {
            return false;
        }

        return true;
    }
    
    /**
     * Clear the Cart
     *
     * @return void;
     */
    public function clear(): void
    {
        Cart::removeConditionsByType('discount');
        Cart::removeConditionsByType('shipping');
        Cart::removeConditionsByType('tax');
        Cart::clear();
    }

    /**
     * Count the items in the cart
     *
     * @return int
     */
    public function countItems(): int
    {
        return Cart::getContent()->count();
    }

    /**
     * Count the items in the cart
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->countItems() === 0;
    }

    /**
     * Get the sub total of all the items in the cart
     *
     * @return float
     */
    public function getSubTotal()
    {
        return Cart::getSubTotal();
    }

    /**
     * Get cart weight
     *
     * @return float
     */
    public function getWeight(): float
    {
        return $this->getCartItems()->reduce(function ($carry, $item) {
            return $carry + ($item->attributes['weight'] * $item->quantity);
        }, 0);
    }

    /**
     * Get the final total of all the items in the cart minus tax
     *
     * @return float
     */
    public function getTotal()
    {
        return Cart::getTotal();
    }

    /**
     * Get shipping price
     *
     * @return float
     */
    public function getShipping()
    {
        $shipping = Cart::getConditionsByType('shipping');

        if ($shipping->isEmpty()) {
            return 0;
        }

        if (is_null($shipping->first()->parsedRawValue)) {
            return $shipping->first()->getCalculatedValue(0);
        }

        return floatval($shipping->first()->parsedRawValue);
    }

    /**
     * Get shipping price
     *
     * @return float
     */
    public function getDiscountValue()
    {
        try {
            $discountObject = $this->getDiscount();

            if (($discountObject instanceof Discount) && ! $discountObject->isValid()) {
                Cart::removeConditionsByType('discount');
                return 0;
            }
        } catch (\Exception $exception) {
            logger($exception->getMessage());
        }

        try {
            $discount = Cart::getConditionsByType('discount');

            if ($discount->isEmpty()) {
                return 0;
            }

            if (is_null($discount->first()->parsedRawValue)) {
                return $discount->first()->getCalculatedValue(0);
            }

            return floatval($discount->first()->parsedRawValue);
        } catch (\Exception $exception) {
            return 0;
        }
    }

    /**
     * Set shipping
     *
     * @param mixed $shipping
     */
    public function setShipping($shipping)
    {
        if ($shipping instanceof Shipping) {
            $name = $shipping->name;
            $cost = $shipping->cost;
        } elseif ($shipping instanceof Collection) {
            $cost = $shipping->sum('cost');
            $name = Str::slug(implode('-', $shipping->pluck('name')->toArray()));
        } elseif (is_string($shipping) || is_numeric($shipping)) {
            $cost = $shipping;
            $name = 'shipping';
        }

        Cart::removeConditionsByType('shipping');

        $condition = new CartCondition([
            'name' => $name ?? '',
            'type' => 'shipping',
            'target' => 'total',
            'value' => '+' . floatval($cost ?? 0),
            'order' => 2
        ]);

        return Cart::condition($condition);
    }

    /**
     * Set tax
     *
     * @param float $taxRate
     */
    public function setTax(float $taxRate)
    {
        Cart::removeConditionsByType('tax');

        $condition = new CartCondition([
            'name' => 'tax',
            'type' => 'tax',
            'target' => 'total',
            'value' => '+' . $taxRate . '%',
            'order' => 1
        ]);

        return Cart::condition($condition);
    }

    /**
     * Get tax
     *
     * @return float
     */
    public function getTax()
    {
        $tax = Cart::getConditionsByType('tax');

        if ($tax->isEmpty()) {
            return 0;
        }

        return round($tax->first()->parsedRawValue, 2);
    }

    /**
     * Get tax rate
     *
     * @return float
     */
    public function getTaxRate()
    {
        $tax = Cart::getConditionsByType('tax');

        if ($tax->isEmpty()) {
            return 0;
        }

        $value = (string) str_replace(['+', '%'], '', $tax->first()->getValue());

        return round($value, 2);
    }

    /**
     * Set Discount
     *
     * @param mixed $discount
     * @return \Darryldecode\Cart\Facades\CartFacade
     */
    public function setDiscount($discount)
    {
        Cart::removeConditionsByType('discount');

        $discount = ! ($discount instanceof Discount)
            ? Discount::where('id', $discount)->firstOrFail()
            : $discount;

        $condition = new CartCondition([
            'name' => $discount->coupon_code,
            'type' => 'discount',
            'target' => $discount->discount_type,
            'value' => '-' . $discount->value,
            'order' => 3
        ]);

        return Cart::condition($condition);
    }

    /**
     * Get discount
     *
     * @return mixed
     */
    public function getDiscount()
    {
        $discountCondition = Cart::getConditionsByType('discount')->first();

        if ($discountCondition === null) {
            return null;
        }

        try {
            return Discount::where('coupon_code', $discountCondition->getName())->firstOrFail();
        } catch (\Exception $exception) {
            return null;
        }
    }
    
    /**
     * Return the specific item in the cart
     *
     * @param int $rowId
     * @return mixed
     */
    public function findItem(int $rowId)
    {
        return Cart::get($rowId);
    }

    /**
     * Get Mapped cart items
     *
     * @return collection
     */
    public function getMappedCartItems(): Collection
    {
        return $this->getCartItems()->map(function ($item) {
            $array = is_array($item) ? $item : $item->all();
            $array['deleted'] = false;
            return $array;
        })->values();
    }

    /**
     * @return mixed
     */
    public function checkItemsStock()
    {
        if (config('default-variables.force-checkout')) {
            return true;
        }

        return $this->getCartItems()->map(function ($item) {
            try {
                $id = $item->attributes['id'];
                $product = Product::withoutGlobalScopes()->where('id', $id)->firstOrFail();
                if ((int) $product->status === 0 || ! is_null($product->deleted_at)) {
                    Cart::remove($id);
                }
            } catch (\Exception $exception) {
                logger($exception->getMessage());
            }
        });
    }

    /**
     * Get Cart Items After Transformation
     *
     * @return collection
     */
    public function getCartItemsTransformed() : Collection
    {
        return $this->getCartItems()->transform(function ($item) {
            try {
                $id = $item->attributes['id'];
                $item->product = Product::where('id', $id)
                        ->remember(config('default-variables.cache_life_time'))
                        ->firstOrFail();
            } catch (\Exception $exception) {
                logger($exception->getMessage());
            }
            return $item;
        });
    }
}
