@extends('app')

@section('content')

    @if(!$cartItems->isEmpty())

        <div class="row py-3 px-2 cart-page">
            
            <div class="col-md-12 content">

                <div class="box-body">
                    @include('layouts.errors-and-messages')
                </div>

                <div class="row mb-md-4">
                    <div class="col-12">
                        <h4 class="mt-2 mb-4"><i class="fa fa-cart-plus"></i> My Cart</h4>
                        <table>
                            <thead>
                                <th class="py-1 pr-1">Product</th>
                                <th class="py-1 px-4"></th>
                                <th class="py-1 px-4">Quantity</th>
                                <th class="py-1 px-4">Price</th>
                                <th class="py-1 px-2"></th>
                            </thead>
                            <tbody>

                                @foreach($cartItems as $cartItem)

                                <tr class="border-bottom">
                                    <td class="align-middle py-2 pr-1">
                                        <a
                                            href="{{ route('front.get.product', [
                                                    'name' => $cartItem->seoTitle,
                                                    'id' => $cartItem->id
                                                    ]) 
                                                }}
                                            "
                                            class="hover-border text-center"
                                        >
                                            <img 
                                                src="{{ asset( Croppa::url("storage/$cartItem->cover", 150, 150, array('resize')) ) }}" 
                                                alt="{{ $cartItem->name }}" 
                                                class="img-fluid">
                                        </a>
                                    </td>
                                    <td class="align-middle py-2 px-4">
                                        <a 
                                            href="{{ route('front.get.product', [
                                                    'name' => $cartItem->seoTitle,
                                                    'id' => $cartItem->id
                                                    ]) 
                                                }}
                                            "
                                            class="hover-border text-left"
                                        >
                                            {{ $cartItem->name }}
                                        </a>
                                    </td>
                                    <td class="align-middle py-2 px-4">
                                        <form action="{{ route('cart.update', $cartItem->rowId) }}" class="form-inline" method="post">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="put">
                                            <input type="number" name="quantity" value="{{ $cartItem->qty }}" class="form-control jq-cart-quantity-input border-radius-1" />
                                        </form>
                                    </td>
                                    <td class="align-middle py-2 px-4">
                                        <span class="price">
                                            {{ config('cart.currency_symbol') . number_format($cartItem->price, 2) }}
                                        </span>
                                    </td>
                                    <td class="align-middle py-2 px-2">
                                        <form class="clearfix px-2" action="{{ route('cart.destroy', $cartItem->rowId) }}" method="post">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="delete">
                                            <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <form action="{{ route('update.selected.courier') }}">
                            <h4 class="font-weight-normal">
                                Shipping
                            </h4>
                            <button 
                                class="btn btn-warning btn-orange text-uppercase text-white border-radius-1 jq-calculate-shipping"
                            >
                                Calculate
                            </button>
                            <div class="shpping-container">
                                <table class="table mt-3 table-hover jq-courier-update">
                                    
                                    @foreach ($couriers as $loopCourier)
                                        <tr>
                                            <td class="text-left align-middle border-0 p-1">
                                                <label for="courier-{{ $loopCourier->id }}" class="mb-0 text-nowrap">
                                                    <input 
                                                        @if( $loopCourier->id == $courier->id ) checked @endif 
                                                        type="radio" name="courier" value="{{ $loopCourier->id }}" 
                                                        id="courier-{{ $loopCourier->id }}">

                                                    &nbsp;{{ $loopCourier->name }}
                                                </label>
                                            </td>
                                            <td class="text-right align-middle border-0 p-1">
                                                <label for="courier-{{ $loopCourier->id }}" class="mb-0 jq-shipping-cost-{{ $loopCourier->id }}">
                                                    {{ config('cart.currency_symbol').$loopCourier->dynamique_cost }}
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 offset-md-4">
                        <table class="table jq-cart-total-info">
                            <tr class="border-bottom">
                                <td class="text-left px-0">Subtotal</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">
                                    {{ config('cart.currency_symbol') }}
                                    <span class="subtotal">{{ $subtotal }}</span> 
                                </td>
                            </tr>
                            @if(isset($shippingFee) && $shippingFee != 0)
                            <tr class="border-bottom">
                                <td class="text-left px-0">Shipping</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">
                                    {{ config('cart.currency_symbol') }}
                                    <span class="shippingFee">{{ $shippingFee }}</span>
                                </td>
                            </tr>
                            @endif
                            <tr class="border-bottom">
                                <td class="text-left px-0">Tax</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">
                                    {{ config('cart.currency_symbol') }}
                                    <span class="tax">{{ number_format($tax, 2) }}</span>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-left px-0 font-size-2x">Grand Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right font-size-2x">
                                    {{ config('cart.currency_symbol') }}
                                    <span class="total">{{ number_format($total, 2) }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-4 offset-md-2">
                        <a href="{{ route('home') }}" class="btn btn-block btn-light border">Continue shopping</a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ auth()->guard('checkout')->check() ? route('checkout.index') : route('guestCheckout.store') }}" class="btn btn-block btn-warning text-white btn-orange">Go to checkout</a>
                    </div>
                </div>

            </div>
        </div>

    @else

        <div class="row">
            <div class="col-md-12 p-3">
                <p class="alert alert-warning text-center p-3">No products in cart yet. <a href="{{ route('home') }}">Shop now!</a></p>
            </div>
        </div>

    @endif
@endsection

@section('css')
    <style type="text/css">
        .product-description {
            padding: 10px 0;
        }
        .product-description p {
            line-height: 18px;
            font-size: 14px;
        }
    </style>
@endsection

@section('js')
    <script>
        var save_zipcode = @json( route('cart.save-zipcode') )
    </script>
@endsection