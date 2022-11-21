<div class="row">
    
    <div class="col-md-7 order-md-1">

        <div class="mb-4 border rounded alert bg-light">
            <div class="mb-3">
                <label class="text-muted" for="name">Name</label>

                @php
                    $name_arr = explode(' ', $customer->name);
                    $first_name = $name_arr[0];
                    $last_name = count($name_arr) > 1 ? $name_arr[1] : '';
                @endphp
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark" for="name">Full Name</label>
                            <input 
                                type="text" 
                                :class="'form-control ' + getValidationClass('name')"
                                id="name" 
                                placeholder="Enter your name" 
                                v-model="checkout.name"
                                required
                                name="name"
                            >
                        </div>
                    </div>
                </div>

                @if ($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif

            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted" for="phone">Phone <small class="text-muted">(required)</small></label>

                    <input 
                        type="text" 
                        class="form-control" 
                        id="phone" 
                        placeholder="3105551212" 
                        value="{{ old('phone', $customer->phone) }}" 
                        required="" 
                        name="phone"
                    >

                    @if ($errors->has('phone'))
                        <div class="invalid-feedback">
                            {{ $errors->first('phone') }}
                        </div>
                    @endif

                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted" for="email">Email <small class="text-muted">(required)</small></label>

                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        placeholder="email@example.com" 
                        value="{{ old('email', $customer->email) }}" 
                        required="" 
                        name="email"
                    >

                    @if ($errors->has('email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="mb-0">

            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Billing address</span>
            </h4>

            <ul class="list-group">

                @foreach ($billingAddresses as $address)

                    <address-item-component 
                        selected-address-id="{{ session('billing_address', false) }}" 
                        address-type="{{ $address->type }}" 
                        :city="{{ json_encode($address->city) }}"
                        :state="{{ json_encode($address->state) }}"
                        :address="{{ json_encode($address) }}"
                    >
                    </address-item-component>

                @endforeach
                
            </ul>

        </div>

        <div class="row">
            <div class="col-12">
                <div class="my-2">&nbsp;</div>
            </div>
        </div>
        
        <div class="mb-0">
            
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Shipping address</span>
            </h4>
            
            @if (!$shippingAddresses->isEmpty())
                <ul class="list-group">

                    @foreach ($shippingAddresses as $address)

                        <address-item-component 
                            selected-address-id="{{ session('billing_address', false) }}" 
                            address-type="{{ $address->type }}" 
                            :city="{{ json_encode($address->city) }}"
                            :state="{{ json_encode($address->state) }}"
                            :address="{{ json_encode($address) }}"
                        >
                        </address-item-component>

                    @endforeach

                </ul>    
            @else
                <div class="mb-4">
                    <p class="text-highlight">You don't have any saved shipping addresses. By default, your billing address will be used as your shipping address. If you want a different shipping address, please create one.</p>
                    <a href="{{ route('customer.address.create', Auth::user()->id) }}?type=shipping" class="btn btn-highlight">Add an address</a>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12">
                <div class="my-2">&nbsp;</div>
            </div>
        </div>
                
        <shipping-options-component 
            title="Your shipping method" 
            :selected="{{ session('shipping', 1) }}" 
            :shipping-options="{{ json_encode($shippingOptions) }}"
        >
        </shipping-options-component>

        <div class="row">
            <div class="col-12">
                <div class="my-2">&nbsp;</div>
            </div>
        </div>

        <div class="row my-2">
            <div class="col-12">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Payment Information</span>
                </h4>
            </div>
        </div>
        
        <div class="col-12 my-4 px-0">
            <div class="d-flex flex-wrap justify-content-left">
                <label class="btn border rounded btn-light mr-4 min-w-100px p-3">
                    <i class="fas fa-credit-card fa-4x"></i>
                    <div class="form-check mt-2">
                        <input 
                            class="form-check-input"
                            type="radio" 
                            name="payment_method"
                            id="credit-card"
                            value="credit_card"
                        >
                        <label class="form-check-label text-muted" for="credit-card">
                            Credit Card
                        </label>
                    </div>
                </label>
                <label class="btn border rounded btn-light min-w-100px p-3">
                    <i class="fab fa-paypal fa-4x"></i>
                    <div class="form-check mt-2">
                        <input 
                            class="form-check-input"
                            type="radio" 
                            name="payment_method" 
                            id="paypal" 
                            value="paypal" 
                        >
                        <label class="form-check-label text-muted" for="paypal">
                            Paypal
                        </label>
                    </div>
                </label>
            </div>
        </div>
        <div class="d-none col-12 px-0 flex-column flex-lg-row credit-card-form-wrapper" id="credit-card-container">
            {{-- payment --}}
            <div class="col-lg-6 px-0 mt-3">
                <div class="payment border alert rounded bg-light">
        
                    <div class="mb-0">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="text-muted" for="cc_number">Credit card number</label>
                                <input name="cc_number" type="text" class="form-control" id="cc_number" value="{{ old('cc_number') }}" required="">
        
                                @if ($errors->has('cc_number'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('cc_number') }}
                                    </div>
                                @endif
        
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="text-muted" for="cc_name">Name on card</label>
                                <input name="cc_name" type="text" class="form-control" id="cc_name" value="{{ old('cc_name') }}" required="">
                                <small class="text-muted">Full name as displayed on card</small>
        
                                @if ($errors->has('cc_name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('cc_name') }}
                                    </div>
                                @endif
                                
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted col-12 px-0" for="cc_expiration">Expiration</label>
                                <div class="col-12 px-0 d-flex">
                                    <input autocomplete="cc-exp-month" name="cc_expiration_month" type="number" min="1" max="12" class="form-control col-5 px-1" id="cc_expiration_month" value="{{ old('cc_expiration_month') }}" required="" maxlength="2" default="12" max="12">
                                    <div class="col-2 text-center">/</div> 
                                    <input autocomplete="cc-exp-year" name="cc_expiration_year" type="number" min="21" max="39" class="form-control col-5 px-1" id="cc_expiration_year" value="{{ old('cc_expiration_year') }}" required="" maxlength="2" min="20" default="20" max="99">
                                </div>
                                @if ($errors->has('cc_expiration_month'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('cc_expiration_month') }}
                                    </div>
                                @endif
                                @if ($errors->has('cc_expiration_year'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('cc_expiration_year') }}
                                    </div>
                                @endif
        
                            </div>
                            <div class="col-md-3">
                                
                                <label class="text-muted" for="cc_cvv">CVV</label>
        
                                <input name="cc_cvv" type="text" class="form-control" id="cc_cvv" value="{{ old('cc_cvv') }}" required="">
        
                                @if ($errors->has('cc_cvv'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('cc_cvv') }}
                                    </div>
                                @endif
                                
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-highlight py-2 px-5">Submit Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12 mb-4 px-lg-3 px-0 d-flex justify-content-start">
                <div class="card-wrapper"></div>
            </div>
            {{-- ./payment --}}
        </div>
    </div>

    <div class="col-md-4 order-md-2 mb-4 offset-md-1">

        {{-- Cart overview listen to cart component events --}}

        <cart-overview-component></cart-overview-component>
        
        <hr class="my-5">

        <coupon-code-component></coupon-code-component>

     </div>
</div>
