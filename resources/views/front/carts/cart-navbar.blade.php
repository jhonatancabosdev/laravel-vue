<a class="nav-link px-3 p-2 cart"
    href="#" 
    id="basket"
    role="button" 
    data-toggle="dropdown" 
    aria-haspopup="true" 
    aria-expanded="false">
    
    <div class="fa-2x cart">
        <span class="fa-layers fa-fw">
            <i class="fas fa-shopping-cart"></i>
            @if( !$cartItems->isEmpty() )
                <span class="fa-layers-counter">
                    {{ $cartItems->count() }}
                </span>
            @endif
        </span>
    </div>
</a>

<div class="dropdown-menu basket-dropdown-container dropdown-container" aria-labelledby="basket">
    <ul class="basket-dropdown p-0 m-0 nav-list-container">
        
        @if( !$cartItems->isEmpty() )
        
            @foreach( $cartItems as $item )

                <div class="d-flex justify-content-center align-items-center flex-row border-bottom">
                    <div class="py-2 px-1 col-3 m-0">
                        <a href="{{ route('front.get.product', ['slug' => $item->product->slug]) }}">
                            <img src="{{ asset( Croppa::url("storage/$item->cover", 110, 110, array('resize')) ) }}" class="img-fluid rounded" alt="{{ $item->name }}">
                        </a>
                    </div>
                    <div class="py-2 px-1 col-6 m-0">
                        <h3>
                            <a href="{{ route('front.get.product', ['slug' => $item->product->slug]) }}" class="text-primary">
                                {{ $item->name }}    
                            </a>
                        </h3>
                        <p class="m-0">Qty: <strong>{{ $item->qty }}</strong></p>
                    </div>
                    <div class="py-2 px-0 col-2">
                        <a href="{{ route('front.get.product', ['slug' => $item->product->slug]) }}" class="text-primary">
                            <span>{{ env('CURRENCY_SYMBOL') . number_format($item->price, 2) }}</span>
                        </a>
                    </div>
                    <div class="py-2 px-0 col-1">
                        <form action="{{ route('cart.destroy', $item->rowId) }}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="delete">
                            <button onclick="return confirm('Are you sure?')" class="btn"><i class="fa fa-times text-danger"></i></button>
                        </form>
                    </div>
                </div>

            @endforeach

            <div class="d-flex justify-content-center align-items-center flex-row">
                <div class="py-2 px-1 col-6 m-0">
                    <a href="{{ route('cart.index') }}" class="btn btn-light btn-block border">View cart</a>
                </div>
                <div class="py-2 px-1 col-6 m-0">
                    <a href="{{ route('checkout.index') }}" class="btn btn-highlight btn-block">Go to checkout</a>
                </div>

            </div>

        @else
            
            <div class="d-flex alert m-0">
                <p class="p-0 m-0">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Cart empty 
                </p>
            </div>

        @endif
        
    </ul>
</div>