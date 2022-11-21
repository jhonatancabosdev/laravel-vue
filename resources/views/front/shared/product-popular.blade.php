<div class="card d-flex flex-column {{ $loop->iteration === 3 ? 'mx-0' : '' }}">
    <add-to-favorites 
        product='@json($product)'>
    </add-to-favorites>

    <a href="{{ route('product.show', $product->slug) }}" class="d-flex justify-content-center ">
        <img src="images/fme-logo-product.png" class="card-img-top img-fluid max-w-h-200px" alt="Card image cap">
    </a>
    <div class="card-body text-center pt-0 mb-1 pb-2 px-3 d-flex flex-column justify-content-between">
        <h5 class="card-title min-h-63 font-weight-bold py-0 mb-0">
            {{ $product->name }}
        </h5>
        <p class="card-text text-highlight py-3 mb-0">
            ${{number_format($product->price, 2) }}

        </p>
        <p class="card-text d-flex justify-content-between pt-0">
            <a href="{{ route('product.show', $product->slug) }}" class="btn btn-secondary-2 border-radius-0 px-2 highlight mb-lg-0 mb-md-3">View More</a>
            <a 
                @click.prevent="$emit('showProductChildren', {{ json_encode($product) }}, {{ json_encode($product->children) }})"
                href="#"
                class="btn btn-secondary-2 border-radius-0 px-2 highlight mb-lg-0 mb-md-3"
            >
                Add To Cart
                <product-modal-component></product-modal-component>
            </a>
        </p>
    </div>
</div>