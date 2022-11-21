<?php

namespace App\Http\Controllers\Customer;

use App\Address;
use App\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Repositories\AddressRepository;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\CartController;
use App\Http\Requests\CreateAddressRequest;

class CustomerAddressController extends Controller
{
    /**
     * @var AddressRepository
     */
    protected $addressRepository;

    /**
     * @var CartController
     */
    protected $cartController;

    /**
     * @param AddressRepository $addressRepository
     */
    public function __construct(AddressRepository $addressRepository, CartController $cartController)
    {
        $this->addressRepository = $addressRepository;
        $this->cartController = $cartController;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $customer = $this->loggedUser();
        return view('front.customers.addresses.list', [
            'customer' => $customer,
            'addresses' => $customer->addresses
        ]);
    }

    /**
     * @param int $customerId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, int $customerId)
    {
        $type = $request->query('type') ?? 'billing';
        return view('front.customers.addresses.create', [
            'type'      => $type,
            'customer'  => $this->loggedUser(),
            'countries' => Country::all()
        ]);
    }

    /**
     * @param CreateAddressRequest $request
     * @param int $customerId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateAddressRequest $request)
    {
        $data = $request->except('_token');

        if($request->input('address_type') === 'billing') {
            $address = $this->addressRepository->createBillingAddress($data);
        } else {
            $address = $this->addressRepository->createShippingAddress($data);
        }

        $this->loggedUser()->addresses()->save($address);

        $cartItems = $this->cartController->index()->getData()->cartItems;

        if(count($cartItems) > 0) {
            return redirect()
                ->route('checkout.index')
                ->with('message', 'Address Created and useable during checkout.');
        } else {
            return redirect()
                ->route('customer.account')
                ->with('message', 'Address Created and useable during checkout.');
        }
    }

    /**
     * Update selected address
     * 
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->has('billing_address')) {
            session(['billing_address' => (int) $request->billing_address]);
        }

        if ($request->has('shipping_address')) {
            session(['shipping_address' => (int) $request->billing_address]);
        }

        return response()->json(true);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $cust_id
     * @param  int  $add_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($cust_id, $add_id)
    {
        $address = Address::where('id', $add_id)->first();
        $address->delete();
        
        return Redirect::to(URL::previous() . "#v-pills-addresses")->with('message', 'Address successfully deleted.');
    }

}
