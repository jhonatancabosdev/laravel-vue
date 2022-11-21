
<div class="card m-2 pt-3 pb-0 h-100{{ isset($viewType) && $viewType === 'list' ? ' border flex-row' : ' border-0' }} w-100">
    @if($product->quantity === 0)
    <div class="position-absolute product-container__badge__wrapper product-container__badge--out-of-stock">
        <span class="badge badge-red text-white border-radius-0 py-1 px-2">Out of Stock</span>
    </div>
    @else
        @if($product->compare_at_price > 0)
        <div class="position-absolute product-container__badge__wrapper product-container__badge-sale">
            <span class="badge badge-orange monsterrat text-uppercase text-white border-radius-0 py-2 px-3">Sale</span>
        </div>
        @endif
        <div class="position-absolute">
            <add-to-favorites 
                context="short"
                icon="fa-star"
                defaultFilled="true"
                product='@json($product)'>
            </add-to-favorites>
        </div>
    @endif
    <a href="{{ route('product.show', ['product' => $product->slug, 'category' => $currentCategory->slug ?? NULL ]) }}" class="card-img-top text-center{{ isset($viewType) && $viewType === 'list' ? ' col-4 ' : '' }}{{ !isset($featureFirst) ? ' d-flex' : '' }}">
        <img
            src="{{ asset($product->main_image) }}"
            class="img-fluid img-responsive {{ isset($featureFirst) ? 'h-100 ' : '' }}w-auto d-block m-auto"
            alt="{{ $product->name }}"
        >
    </a>
    <div class="card-body h-100 d-flex flex-column px-0 py-3">
        <div class="d-flex flex-column flex-grow-1 justify-content-center py-2 px-3 text-left">
            @isset($product->brand)
            <a href="{{ route('brand.show',$product->brand) }}" class="text-secondary-5">
                <small class="text-uppercase">{{ $product->brand['name'] }}</small>
            </a>
            @endisset
            <h3 class="card-title monsterrat h6 my-1">
                <a href="{{ route('product.show', ['product' => $product->slug, 'category' => $currentCategory->slug ?? NULL]) }}" class="text-dark">
                    {{ $product->name }}
                </a>
            </h3>
            <div class="my-1">
                <span class="text-highlight">
                    {{-- @if($product->compare_at_price > 0)
                    <strike class="text-secondary-7"><small>{{ config('cart.currency_symbol') . number_format($product->compare_at_price, 2) }}</small></strike>
                    @else
                    <strike class="text-secondary-7"><small>{{ config('cart.currency_symbol') . number_format($product->original_price, 2) }}</small></strike>
                    @endif --}}
                    {{ config('cart.currency_symbol') . number_format($product->price, 2) }}
                </span>
            </div>
            {{-- @unless(isset($hide_description))
            <div class="card-text mb-3">
                {!! $product->short_description !!}
            </div>
            @endunless --}}
        </div>
        
        <div class="px-3 card-text text-center d-flex flex-lg-row flex-md-column mt-auto mb-0 align-self-end justify-content-between w-100 product__listing--buttons">
            <a
                href="{{ route('product.show', $product->slug) }}"
                class="w-100 btn border-radius-0 text-white bg-highlight px-2"
            >
                View More
            </a>
            {{-- <a 
                @click.prevent="$emit('showProductChildren', {{ json_encode($product) }}, {{ json_encode($product->children) }})"
                href="#"
                class="btn btn-secondary-2 border-radius-0 px-2 highlight mb-lg-0 mb-md-3"
            >
                Add To Cart
            </a> --}}
        </div>
    </div>
</div>