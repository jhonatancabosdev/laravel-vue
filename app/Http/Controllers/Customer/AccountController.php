<?php

namespace App\Http\Controllers\Customer;

use App\Order;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    /**
     * Show customer account page
     *
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        $customer = $this->loggedUser();

        $addresses = $customer->addresses;

        return view('front.customers.account', [
            'customer' => $customer,
            'orders' => $customer->orders()->confirmed()->orderBy('id', 'desc')->paginate(),
            'shippingAddresses' => $addresses->where('type', 'shipping'),
            'billingAddresses' => $addresses->where('type', 'billing'),
        ]);
    }
}
