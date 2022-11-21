<?php

namespace App\Repositories\Contracts;

use App\Discount;
use Illuminate\Support\Collection;

interface DiscountRepositoryContract
{

    public function createDiscount(array $params) : Discount;

    public function updateDiscount(array $params) : bool;

    public function findDiscountById(int $id) : Discount;

    public function listDiscounts(string $order = 'id', string $sort = 'desc') : Collection;

    public function deleteDiscount();
}
