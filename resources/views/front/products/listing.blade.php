<product-listing-component 
    v-bind:on-listing="{{ isset($onListing) ? 'true' : 'false' }}"
    :product="{{ json_encode($product) }}"
    :tags="{{ $product->tags }}"
    :current-category="{{ json_encode(isset($currentCategory) ? $currentCategory : $product->categories->first()) }}"
    v-bind:feature-first="false"
    asset-url="{{ asset('__var') }}"
    view-type="{{ $viewType ?? '' }}"
    class="card m-0 pt-0 pb-0 h-100 {{ isset($viewType) && $viewType === 'list' ? 'border flex-row' : 'border-0' }} w-100"
>    
</product-listing-component>