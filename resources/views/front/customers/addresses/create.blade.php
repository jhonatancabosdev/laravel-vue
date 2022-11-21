@extends('layouts.front.app')

@section('content')
    <section class="container">

        <div class="bg-white">

            <div class="row">

                <div class="col-12">

                    <div class="row justify-content-center py-5">

                        <div class="col-md-8 mb-5">

                            <div class="alert">

                                <form novalidate action="{{ route('customer.address.store', $customer->id) }}" method="post" class="form" enctype="multipart/form-data">

                                    {{ csrf_field() }}
                                    
                                    <div id="billing-address" class="p-4">

                                        <h1 class="text-center h4 mb-1">Create new address</h1>

                                        <p class="text-center mb-4">
                                            <small>
                                                {{-- Additional Text Here --}}
                                            </small>
                                        </p>

                                        <address-component 
                                            :address="{{ 
                                                json_encode((object)[
                                                    'address_1' => old('billing_address_1', ''),
                                                    'address_2' => old('billing_address_2', ''),
                                                    'zipcode' => old('billing_address_zipcode', ''),
                                                    'state_id' => old('billing_address_state_id', ''),
                                                    'city' => old('billing_address_city', ''),
                                                ])
                                            }}"
                                            address-type="{{ $type }}"
                                            :errors="{{ json_encode($errors->getMessages()) }}"
                                        >
                                        </address-component>
                                    </div>
                                
                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <div class="form-group px-4">
                                                <button type="submit" class="btn btn-highlight">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                        
                                </form>
                            </div>

                        </div>

                    </div>                
                </div>
            </div>
            
        </div>

    </section>
@endsection

