{{-- Price Filter --}}
<div class="filter filter--price border-0 mb-3 text-uppercase">
    
    <div class="bg-white border-radius-0 pt-2 px-0">
            <span class="h6 font-weight-bold text-capitalize text-black">Price Filter</span><br />
            $0 - ${{ number_format($maxPrice,2) }}
    </div>
    <div class="bg-white px-0">
        <div class="collapse show price-filter-container">
            <form method="GET" action="{{ route('category.filter', $currentCategory ?? 'search') }}">
                <div class="mt-3 mb-5 px-2">
                    <div data-max-value="{{ $maxPrice }}" id="price_range">
                    </div>
                    <input data-type="price_from" value="{{ request()->price_from }}" type="hidden" name="price_from" id="price-from-input" class="form-control" placeholder="$0">
                    <input data-type="price_to" value="{{ request()->price_to }}" type="hidden" name="price_to" id="price-to-input" class="form-control" placeholder="$100">
                </div>
            </form>
        </div>
    </div>
</div>
{{-- ./Price Filter --}}