<form class="search-form" action="{{ route('product.search') }}">
    <div class="row">
        <div class="col-md-10 col-sm-10 col-12 pr-md-0 d-flex align-items-center pl-md-1">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="search-icon">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
                <input 
                    type="text" 
                    class="typeahead form-control" 
                    name="q" 
                    value="{{ request()->input('q') }}" 
                    placeholder="Search products by keyword, brand, part number" 
                    aria-label="Search products by keyword, brand, part number" 
                    aria-describedby="search-icon"
                    >
            </div>
        </div>
        <div class="col-md-1 col-sm-1 px-sm-0 pl-md-2 col-12 pr-md-0 d-flex align-items-center">
            <button class="btn btn-warning jq-btn-search-products" type="submit">
                <strong class="text-white">Go!</strong>
            </button>
        </div>
    </div>
</form>
