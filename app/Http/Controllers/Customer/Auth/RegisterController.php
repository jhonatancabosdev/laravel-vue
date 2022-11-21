<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Requests\Auth\Customer\CustomerRegisterRequest;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/checkout';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest:customer');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Customer
     */
    protected function create(array $params)
    {
        $data = collect($params)->except('password')->all();

        $customer = new Customer($data);

        if(array_key_exists('apply_for_vendor', $params)) {
            $customer->vendor_status = 1;
        }

        if (isset($params['password'])) {
            $customer->password = bcrypt($params['password']);
        }

        $customer->save();

        return $customer;
    }

    /**
     * @param CustomerRegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(CustomerRegisterRequest $request)
    {
        $customer = $this->create( 
            $request->except('_method', '_token') 
        );

        $this->guard()->login($customer);

        return redirect()->route('checkout.index');
    }

    /**
     * Guard Customization
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard(): \Illuminate\Contracts\Auth\Guard
    {
        return Auth::guard('customer');
    }
}
