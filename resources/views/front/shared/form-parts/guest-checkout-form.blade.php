@extends('layouts.front.app')

@section('content')

<div class="mt-4">
	<checkout-component 
		class="container mb-md-5 pb-md-5"
		route-url="{{ route('guest.checkout.store') }}"
        validate-url="{{ route('address-validation.store') }}"
		:errors="{{ json_encode($errors->getMessages()) }}"
		:billing-address="{{ 
            json_encode((object)[
                'address_1' => old('billing_address_1', session('billing_address_1', '')),
                'address_2' => old('billing_address_2', session('billing_address_2', '')),
                'zipcode' => old('billing_address_zipcode', session('billing_address_zipcode', '')),
                'state_id' => old('billing_address_state_id', session('billing_address_state_id', '')),
                'city' => old('billing_address_city', session('billing_address_city', '')),
            ])
        }}"
        :shipping-address="{{ 
            json_encode((object)[
                'address_1' => old('shipping_address_1', session('shipping_address_1')),
                'address_2' => old('shipping_address_2', session('shipping_address_2')),
                'zipcode' => old('shipping_address_zipcode', session('shipping_address_zipcode')),
                'state_id' => old('shipping_address_state_id', session('shipping_address_state_id')),
                'city' => old('shipping_address_city', session('shipping_address_city')),
            ])
        }}"
        name="{{ old('name', session('name')) }}"
        phone="{{ old('phone', session('phone')) }}"
        email="{{ old('email', session('email')) }}"
        :shipping-address-different="{{ old('shipping_address_different', session('shipping_address_different', false)) ? 'true' : 'false' }}"
	>
		<slot>
			<div class="row mb-4">
	            <div class="col-12">
	                <h1 class="text-center text-uppercase h2 mt-4 font-weight-bold text-dark">Checkout</h1>
	            </div>
	        </div>
	        @csrf
		</slot>
	</checkout-component>
</div>

@endsection
