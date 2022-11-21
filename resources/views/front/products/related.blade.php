<div class="row col-12 product-container pr-0 pl-sm-4">
    @foreach ($relatedProducts as $product)
        <div class="my-3 col-lg-4 col-12 px-0 px-sm-3">
            @include('front.products.listing', compact('product'))
        </div>
    @endforeach
</div>
