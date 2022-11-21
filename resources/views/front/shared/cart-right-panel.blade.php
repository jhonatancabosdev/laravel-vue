@if( !$cartItems->isEmpty() )
    <table>

        @foreach( $cartItems as $item )

            @if( $loop->iteration > 3 )
                @php
                    continue;
                @endphp
            @endif

            <tr>
                <td class="align-middle pb-2" data-product-id="{{ $item->id }}">
                    <a 
                        href="{{ route('front.get.product', [
                                    'name' => $item->seoTitle,
                                    'id' => $item->id
                                    ]) 
                                }}
                            "
                        class="text-dark">
                        ({{ $item->qty }}){{ $item->name ?? $item->short_description }}
                    </a>
                </td>
                <td class="align-top pb-2">
                    <a 
                        href="{{ route('front.get.product', [
                                'name' => $item->seoTitle,
                                'id' => $item->id
                                ]) 
                            }}
                        "
                        class="text-dark"
                        >
                        <span class="text-nowrap">
                            ${{ number_format($item->price, 2) }}
                        </span>
                    </a>
                </td>
            </tr>
        @endforeach

        @if( $cartItems->count() > 3 )

            <tr>
                <td colspan="2" class="align-middle pb-2">
                    <a 
                        href="{{ route('cart.index') }}"
                        class="btn btn-link">
                        ({{ $cartItems->count() - 3 }}) see more ...
                    </a>
                </td>
            </tr>

        @endif

        <tr>
            <td class="align-middle text-right pt-3">
                Cart subtotal : &nbsp;
            </td>
            <td class="text-right pt-3 align-top" colspan="2">
                <strong class="total">
                    {{ config('cart.currency_symbol') .''. number_format($subtotal, 2) }}
                </strong>
            </td>
        </tr>
    </table>
    <a href="{{ route('guestCheckout.index') }}" class="btn btn-warning text-white mt-3 py-md-1 px-md-5">Check out</a>
@else
    <div class="alert mt-3 my-4 px-md-1">
        There are no items in your cart.
    </div>
@endif
