<?php

namespace App\Repositories\Contracts;

use App\Product;
use App\Shipping;
use Illuminate\Support\Collection;

interface CartRepositoryContract
{
    public function addToCart(Product $product, int $int, array $options = []);

    public function getCartItems(): Collection;

    public function remove(string $rowId): bool;
    
    public function clear(): void;

    public function countItems(): int;

    public function isEmpty(): bool;

    public function getWeight(): float;

    public function getSubTotal();

    public function getTotal();

    public function getDiscount();
    
    public function setDiscount($discount);

    public function getDiscountValue();

    public function getMappedCartItems(): Collection;
    
    public function getShipping();
    
    public function setShipping($shipping);

    public function getTaxRate();

    public function getTax();

    public function setTax(float $taxRate);

    public function checkItemsStock();

    public function getCartItemsTransformed();

    public function updateQuantityInCart(string $rowId, int $quantity);

    public function findItem(int $rowId);
}
