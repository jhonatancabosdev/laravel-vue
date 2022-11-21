{{-- Other filters --}}
<div class="row card border-0 p-3 mb-3 text-uppercase bar bar--filters">
    @if(isset($currentCategory))
        <input type="hidden" name="category_slug" id="category_slug" value="{{ $currentCategory->slug }}">
    @else
        <input type="hidden" name="category_slug" id="category_slug" value="all">
    @endif
    <div class="row">

        <div class="col-md-3">
            @include('front.shared.filters.price-filter')
        </div>
        <div class="bar--filters__wrapper d-flex col-9 card-body bg-white collapse show filter" id="products-filter-container">
            <div>
                {{-- Results per page --}}
                <select name="pagination_count" data-type="pagination_count" id="pagination_count" class="text-center border-highlight border px-2 py-1 rounded text-highlight bg-white">
                    <option value="24" data-val="24" @if($paginationCount === 24)selected="selected"@endif>24 Per Page</option>
                    <option value="16" data-val="16" @if($paginationCount === 16)selected="selected"@endif>16 Per Page</option>
                    <option value="8" data-val="8" @if($paginationCount === 8)selected="selected"@endif>8 Per Page</option>
                </select>
                {{-- ./Results per page --}}
            </div>
            <div>

                {{-- Results per page --}}
                <select name="sort_by" id="sort_by" data-type="sort_by" class="text-center border-highlight border px-2 py-1 rounded text-highlight bg-white">
                    <option value="relevance" data-val="relevance"@if($sortBy === 'relevance') selected="selected"@endif>Relevance</option>
                    <option value="h-t-l" data-val="h-t-l"@if($sortBy === 'h-t-l') selected="selected"@endif>Price Highest to Lowest</option>
                    <option value="l-t-h" data-val="l-t-h"@if($sortBy === 'l-t-h') selected="selected"@endif>Price Lowest to Highest</option>
                </select>
                {{-- ./Results per page --}}
            </div>
            {{-- Grid Types --}}
            <div>
                {{-- list --}}
                <a
                    href="#list"
                    data-type="view_type"
                    data-val="list"
                    class="d-block py-1 px-2 border rounded border-highlight{{ $viewType === 'list' ? ' text-white bg-highlight' : ' text-highlight bg-white' }}"
                ><i class="fas fa-bars"></i><span class="sr-only">List</span></a>
                {{-- two-across --}}
            </div>
            <div>
                <a
                    href="#grid-large"
                    data-type="view_type"
                    data-val="grid-large"
                    class="d-block py-1 px-2 border rounded border-highlight{{ $viewType === 'grid-large' ? ' text-white bg-highlight' : ' text-highlight bg-white' }}"
                ><i class="fas fa-th-large"></i><span class="sr-only">Large Grid</span></a>
                {{-- three-across --}}
            </div>
            <div>
                <a
                    href="#grid"
                    data-type="view_type"
                    data-val="grid"
                    class="d-block py-1 px-2 border rounded border-highlight{{ $viewType === 'grid' ? ' text-white bg-highlight' : ' text-highlight bg-white' }}"
                ><i class="fas fa-th"></i><span class="sr-only">Small Grid</span></a>
            </div>
            {{-- ./Grid Types --}}
        </div>
    </div>
</div>
{{-- ./Other Filters --}}