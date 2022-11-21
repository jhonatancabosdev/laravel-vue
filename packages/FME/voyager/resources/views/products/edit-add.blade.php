@extends('voyager::master')

@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <h1 class="page-title">
                        <i class="h3 mb-0 {{ $dataType->icon }}"></i> {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <div class="panel panel-bordered">
                        <!-- form start -->
                        <form 
                            role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" 
                            enctype="multipart/form-data"
                        >
                            <!-- CSRF TOKEN -->
                            @csrf

                            <!-- PUT Method if we are editing -->
                            @if($edit)
                                @method('PUT')
                            @endif

                            <div class="panel-body">

                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Adding / Editing -->
                                @php
                                    $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )}->sortByDesc('type');
                                @endphp

                                <div class="row">
                                    <div class="col-md-4">

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-0">
                                            <div class="mb-0">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">Product title</label>
                                                    <small class="text-danger align-self-end">Required (<span class="text-danger jq-length-conter">0/120</span>)</small>
                                                </div>

                                                <textarea 
                                                    name="name" 
                                                    class="form-control jq-count" 
                                                    cols="30" 
                                                    rows="3" 
                                                    placeholder="Product title." 
                                                    maxlength="120"
                                                >{{ $dataTypeContent->name ?? '' }}</textarea>
                                                <p class="text-dark small mb-0 mt-2">
                                                    Goal is <strong>80-120 characters</strong>. This title will be showing in advertising and on the website. 
                                                    Grammar is important. Use Title Case.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-3 px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-0">
                                            <div class="mb-0">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold" for="feature_on_homepage">Feature on Homepage? <span class="pl-4"><input type="checkbox" name="feature_on_homepage" id="feature_on_homepage" @if($dataTypeContent->homepage_order && $dataTypeContent->homepage_order > 0) checked="checked" @endif ></span></label>
                                                </div>
                                                
                                                <div class="homepage_order__wrapper  @unless($dataTypeContent->homepage_order && $dataTypeContent->homepage_order > 0) d-none @endunless">
                                                    <label for="homepage_order">Order of display on homepage (1 will appear first. Products with the same number will be displayed in alphabetical order.)</label>
                                                    <input type="number" 
                                                        name="homepage_order" 
                                                        id="homepage_order" 
                                                        value="{{ $dataTypeContent->homepage_order ?? 1 }}"
                                                        class="form-control jq-count" 
                                                        min="1"
                                                    />
                                                </div>

                                            </div>
                                        </div>

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">UPC/GTIN</label>
                                                    <small class="text-warning align-self-end">Strongly Suggested</small>
                                                </div>
                                                <input 
                                                    type="text" 
                                                    placeholder="UPC/GTIN"
                                                    name="upc" 
                                                    class="form-control mb-2" 
                                                    value="{{ $dataTypeContent->upc ?? '' }}" 
                                                >
                                                <p class="text-dark mb-0 small mt-2">
                                                    This is the UPC of your product. Entering the UPC helps marketing work more efficiently.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0">

                                                @php
                                                    $row = $dataTypeRows->where('field', 'product_belongstomany_category_relationship')->first();
                                                @endphp

                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">Category</label>
                                                    <small class="text-danger align-self-end">Required</small>
                                                </div>
                                                <div class="mb-3">

                                                    <div id="products-categories-modal">

                                                        <div v-if="selectedCategories.length > 0">

                                                            <ul>
                                                               <li v-for="category in selectedCategories">@{{ category.name }}</li> 
                                                            </ul>

                                                            <select name="selected_categories[]" class="d-none" v-model="categories" multiple>
                                                                <option :value="category.id" v-for="category in selectedCategories">@{{ category.name }}</option> 
                                                            </select>

                                                        </div>


                                                        <button 
                                                            type="button" 
                                                            @click="$root.$emit('showProductCategoriesModal', product)" 
                                                            class="btn btn-pink btn-block">Categories
                                                        </button>

                                                        @php
                                                            $categories = \App\Category::get();
                                                        @endphp

                                                        <product-categories-modal 
                                                            @categories-updated="updatedCategories($event)"
                                                            save-categories-route="{{ route('voyager.products.categories') }}"
                                                            :flat-categories="{{ json_encode($categories) }}" 
                                                            :nested-categories="{{ json_encode($categories->toTree()) }}"
                                                        >    
                                                        </product-categories-modal>
                                                    </div>

                                                </div>
                                                <div>
                                                    <small class="text-dark">
                                                        Select the categories that apply to this product.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="d-flex row flex-row mb-2">
                                                <div class="col-4">
                                                    <label class="font-weight-semi-bold">MSRP</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="compare_at_price" value="{{ number_format($dataTypeContent->compare_at_price, 2) ?? '00' }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <label class="font-weight-semi-bold">Selling price <span class="font-weight-bold text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="price" value="{{ number_format($dataTypeContent->price, 2) ?? '00' }}">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <label class="font-weight-semi-bold">MAP</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="map_price" value="{{ number_format($dataTypeContent->map_price, 2) ?? '00' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex row flex-row mb-2">
                                                <div class="col-4">
                                                    <label class="font-weight-semi-bold">Cost</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="cost" value="{{ number_format($dataTypeContent->cost, 2) ?? '00' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <p class="text-dark small mb-0">
                                                    MAP PRICING: If this product is MAP protected AND you want to sell it below MAP simply enter the Selling price. If the Selling price is less than MAP, the system will make the user add the product to the cart to see the selling price.if item.sell_price >= item.map_price (OR item.map_price IS NULL), we show item.msrp_price crossed out and item.selling_price next to it.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0">
                                                
                                                @php
                                                    $row = $dataTypeRows->where('field', 'product_belongsto_availability_relationship')->first();
                                                @endphp

                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">Availability (Stock) Status</label>
                                                    <small class="text-danger align-self-end">Required</small>
                                                </div>
                                                <div class="mb-3">
                                                    @include('voyager::formfields.relationship', ['options' => $row->details])
                                                </div>
                                                <div>
                                                    <p class="text-dark mb-0 small mt-2">
                                                        Is this product currently In Stock? If not then the site will stop marketing spend on this product.
                                                    </p>
                                                </div>
                                                <div class="mt-3 d-flex flex-row justify-content-between">
                                                    <label for="quantity" class="font-weight-semi-bold">Quantity in Stock</label>
                                                </div>
                                                <div class="mb-3">
                                                    <input type="number" class="form-control" name="quantity" id="quantity" value="{{ $dataTypeContent->quantity }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0 d-flex align-items-center">
                                                <input 
                                                    type="checkbox"
                                                    name="is_on_feed"
                                                    {{ isset($dataTypeContent) && (int) $dataTypeContent->is_on_feed === 1 ? 'checked' : '' }}
                                                    class="form-control mb-2"
                                                    data-style="ios"
                                                    data-off="No"
                                                    data-on="Yes"
                                                    data-toggle="toggle"
                                                    data-offstyle="danger"
                                                    data-onstyle="success"
                                                >
                                                <label class="mb-0">&nbsp;Include in Google Feed</label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-4">

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-0">
                                            <div class="text-right">
                                                <small class="text-warning">
                                                    Required for marketing unless shipping is flat rate/free
                                                </small>
                                            </div>
                                            <div class="d-flex flex-row mb-3">

                                                <div class="mr-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">W.</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="width" value="{{ $dataTypeContent->width ?? '00' }}">
                                                    </div>
                                                </div>

                                                <div class="mr-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Len.</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="length" value="{{ $dataTypeContent->length ?? '00' }}">
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Ht.</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="height" value="{{ $dataTypeContent->height ?? '00' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-dark mb-0 small mt-2">
                                                Enter the approximate dimensions of the BOXED LxWxH. This allows for shipping price estimates both 
                                                on the site and from Google. <span class="text-danger">Enter INCHES</span>.
                                            </p>
                                        </div>

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">Weight in pounds</label>
                                                    <small class="text-warning align-self-end">Required for marketing</small>
                                                </div>
                                                <input 
                                                    type="text" 
                                                    placeholder="Weight in pounds"
                                                    name="weight" 
                                                    class="form-control mb-2" 
                                                    value="{{ $dataTypeContent->weight ?? '' }}" 
                                                >
                                                <p class="text-dark mb-0 small mt-2">
                                                    Enter the approximate weight in pounds of the boxed unit. This allows for shipping price estimates both on the site and from Google.
                                                </p>
                                            </div>
                                        </div>

                                        @if ($edit)
                                            <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                                <div class="mb-3">
                                                    <label class="font-weight-semi-bold">Parent/Child Relationship</label>
                                                    <select name="relation_type" class="form-control">
                                                        <option @if(isset($dataTypeContent->nested_type) && $dataTypeContent->nested_type === 'standalone') selected @endif value="standalone">Standalone</option>
                                                        <option @if(isset($dataTypeContent->nested_type) && $dataTypeContent->nested_type === 'parent') selected @endif value="parent">Parent</option>
                                                        <option @if(isset($dataTypeContent->nested_type) && $dataTypeContent->nested_type === 'child') selected @endif value="child">Child</option>
                                                    </select>
                                                </div>
                                                <button type="button" data-toggle="modal" data-target="#options-modal" class="btn btn-pink btn-block">Add/Edit Options</button>
                                            </div>
                                        @endif

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0">
                                                
                                                @php
                                                    $row = $dataTypeRows->where('field', 'product_belongsto_brand_relationship')->first();
                                                @endphp

                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">Brand (Maker) of this product</label>
                                                    <small class="text-danger align-self-end">Required</small>
                                                </div>
                                                <div class="mb-2">
                                                    @include('voyager::formfields.relationship', ['options' => $row->details])
                                                </div>
                                                <div>
                                                    <small class="text-dark font-weight-semi-bold">
                                                        Type then <code>press enter</code> to add new value
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                         <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">Part/Model Number</label>
                                                    <small class="text-danger align-self-end">Required</small>
                                                </div>
                                                <input 
                                                    type="text" 
                                                    placeholder="Part/Model Number"
                                                    name="mpn" 
                                                    class="form-control mb-2" 
                                                    value="{{ $dataTypeContent->mpn ?? '' }}" 
                                                >
                                                <p class="text-dark mb-0 small mt-2">
                                                    This is the model or manufacturers part number. Adding this is strongly recommended, required if there is no UPC/GTIN.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">SKU</label>
                                                    <small class="text-warning align-self-end">Required</small>
                                                </div>
                                                <input 
                                                    type="text" 
                                                    placeholder="SKU"
                                                    name="sku" 
                                                    class="form-control mb-2" 
                                                    value="{{ $dataTypeContent->sku ?? '' }}" 
                                                >
                                                <small class="text-dark">
                                                    This is your own internal SKU. Must be unique.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-3">
                                                <label class="font-weight-semi-bold">Free Shipping</label>
                                                <select name="free_shipping_option" class="form-control">

                                                    @foreach (\App\Product::getFreeShippingOptions() as $option)
                                                        <option @if(isset($dataTypeContent->free_shipping_option) && strtolower($dataTypeContent->free_shipping_option) === strtolower($option)) selected @endif value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach

                                                </select>
                                                <input type="hidden" name="is_free_shipping" value="{{ isset($dataTypeContent->is_free_shipping) ? $dataTypeContent->is_free_shipping : 0 }}">
                                            </div>
                                        </div>
                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-3">
                                                <label class="font-weight-semi-bold">Chart Details</label>
                                                @foreach ($dataTypeContent->allTagTypes as $tag)
                                                    <div class="mt-3 d-flex flex-row justify-content-between">
                                                        <label for="quantity" class="font-weight-semi-bold">{{$tag->type}}</label>
                                                    </div>
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control" name="tags[{{$tag->type}}]" value="{{ $tag->title }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-4">

                                        <div class="px-4 py-4 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-0">
                                            @if(! $dataTypeContent->images->where('is_main', false)->isEmpty())
                                                <div class="mb-3">
                                                    <div class="card-columns">
                                                        @foreach($dataTypeContent->images->where('is_main', false) as $image)
                                                            <div class="card rounded-lg shadow-none">
                                                                <div class="img_settings_container" data-field-name="src">
                                                                    <a href="#" class="voyager-x remove-multi-image" style="position: absolute;"></a>
                                                                    <img 
                                                                        src="{{ str_replace(['storage/storage/', '//storage', '///'], ['storage/', '/storage', '/'], Voyager::image($image->src)) }}" 
                                                                        data-file-name="{{ $image->src }}" 
                                                                        data-id="{{ $image->id }}" 
                                                                        class="img-fluid rounded-lg p-3"
                                                                    >
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            <div>
                                                <label class="font-weight-semi-bold">Product Images</label>
                                                <input type="file" name="images[]" multiple="multiple" accept="image/*">
                                            </div>
                                        </div>

                                        <div class="px-4 py-4 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            @if(! $dataTypeContent->images->where('is_main', true)->isEmpty())
                                                <div class="mb-0 rounded-lg p-2 bg-white mb-2">
                                                    <div class="text-center position-relative" data-field-name="main_image">
                                                        <a href="#" class="voyager-x remove-single-image" style="position:absolute; left: 0"></a>
                                                        <img 
                                                            src="{{ str_replace(['storage/storage/', '//storage', '///'], ['storage/', '/storage', '/'], Voyager::image($dataTypeContent->main_image)) }}" 
                                                            data-file-name="{{ $dataTypeContent->main_image }}" 
                                                            data-id="{{ $dataTypeContent->images->where('is_main', true)->first()->id }}"
                                                            class="img-fluid rounded-lg p-2"
                                                        >
                                                    </div>
                                                </div>
                                            @endif
                                            <div>
                                                <label class="font-weight-semi-bold">Main image</label>
                                                <input type="file" name="main_image" accept="image/*">
                                            </div>
                                        </div>

                                         @foreach ($dataTypeRows->where('type', 'rich_text_box') as $row)
                                            <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">

                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="font-weight-semi-bold">Product description</label>
                                                    <small class="text-danger align-self-end">Required</small>
                                                </div>

                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}

                                                @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                                    {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                                @endforeach

                                                @if (isset($row->display_options->text_helper))
                                                    <small class="d-block mt-2 text-dark">
                                                        {{ $row->display_options->text_helper }}
                                                    </small>
                                                @endif

                                                <div>
                                                    <p class="text-dark mb-0 small mt-2">
                                                        The product description is extremely important for marketing and appearance. It should be between 500-1500 characters in length and should include many of the keywords that describe this product. 
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="px-4 py-2 form-group bg-light rounded-lg shadow-none border shadow-hover-sm mb-0 mt-3">
                                            <div class="mb-0 d-flex align-items-center">
                                                <input 
                                                    type="checkbox"
                                                    name="status"
                                                    {{ isset($dataTypeContent) && $dataTypeContent->status === 1 ? 'checked' : '' }}
                                                    class="form-control mb-2"
                                                    data-style="ios"
                                                    data-off="No"
                                                    data-on="Yes"
                                                    data-toggle="toggle"
                                                    data-offstyle="danger"
                                                    data-onstyle="success"
                                                >
                                                <label class="mb-0">&nbsp;Enable product after save?</label>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                

                                @if ($errors->has(''))
                                    @foreach ($errors->get('') as $error)
                                        <span class="d-block help-block">{{ $error }}</span>
                                    @endforeach
                                @endif

                            </div><!-- panel-body -->

                            <div class="panel-footer text-right mt-3 pb-4">
                                @section('submit-buttons')
                                    <div class="btn-group shadow rounded-lg">
                                        <button type="button" class="btn btn-default save min-w-120px jq-save-draft">Save draft</button>
                                        <button type="submit" class="btn btn-pink save min-w-120px jq-submit-btn">{{ __('voyager::generic.save') }}</button>
                                    </div>
                                @stop
                                @yield('submit-buttons')
                            </div>
                        </form>

                        <iframe id="form_target" name="form_target" style="display:none"></iframe>
                        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                                enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                            <input name="image" id="upload_file" type="file"
                                     onchange="$('#my_form').submit();this.value='';">
                            <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                            {{ csrf_field() }}
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="options-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <product-options 
            class="modal-dialog modal-xl" 
            :product='@json($dataTypeContent)' 
            data-route="{{ route('voyager.products.index') }}"
            update-route="{{ route('voyager.products.update.options', $dataTypeContent) }}"
            destroy-route="{{ route('voyager.products.destroy.options', $dataTypeContent) }}"
        >
            <h5 class="modal-title" id="staticBackdropLabel">Add/Edit options</h5>
        </product-options>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                
                <div class="modal-header border-0">
                    <h4 class="modal-title">{{ __('voyager::generic.are_you_sure') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} <span class="confirm_delete_name"></span></h4>
                </div>

                <div class="modal-footer border-0">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                        <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>

        new Vue({
            el: '#options-modal'
        });

        $('body').on('change', 'select[name="free_shipping_option"]', function(event) {
            if ($(this).val() === 'Not free shipping') {
                $('[name="is_free_shipping"]').val(0)
            } else {
                $('[name="is_free_shipping"]').val(1)
            }
        });

        var table = new Vue({
            el: '#products-categories-modal',
            data: {
                product: @json($dataTypeContent->loadMissing('categories')),
                selectedCategories: @json($dataTypeContent->categories),
                categories: []
            },
            methods: {
                updatedCategories(categories) {
                    this.selectedCategories = categories;

                    this.categories = categories.map(function(e) {
                        return e.id
                    });
                }
            }
        });

        $('.jq-save-draft').on('click', function(event) {
            event.preventDefault();
            // set status as draft
            $('input[name="status"]').val('2').prop('checked', true)
            $('.jq-submit-btn').trigger('click')
        });

        function formatOption (option) {
            if (!option.id) {
                return option.text;
            }
            var $option = $(
                '<span>' + option.title + ' ' + option.text + '</span>'
            );
            return $option;
        };

        $(".jq-custom-select2").select2({
            templateResult: formatOption,
            width: '100%'
        });

        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.ajax({
                    url: '{{ route('voyager.products.destroy.image', $dataTypeContent) }}',
                    type: 'POST',
                    data: params,
                })
                .done(function(response) {

                    $file.parent().fadeOut(300, function() { 
                        $(this).parents('.card').remove(); 
                    })

                    $('#confirm_delete_modal').modal('hide');
                })
                .fail(function() {
                    toastr.error("Error removing file.");
                })
            });

            $('[data-toggle="tooltip"]').tooltip();

            $.each($('.jq-length-conter'), function(index, el) {

                var input = $(el).parents('.form-group').find('.jq-count')
                var count = input.val().length;
                var mexlength = input.attr('maxlength');
                $(el).text(count + '/' + mexlength);

                $(el).parents('.form-group').find('.jq-count').on('input', function(event) {
                    var count = $(this).val().length;
                    var mexlength = $(this).attr('maxlength');
                    $(el).text(count + '/' + mexlength);
                });
            });

            $('[name="relation_type"]').on('change', function(event) {
                if ($(this).val() === 'parent') {
                    $('[data-target="#options-modal"]').slideDown()
                } else {
                    $('[data-target="#options-modal"]').slideUp()
                }
            });

            $('[name="relation_type"]').trigger('change')
        });

        $('body').on('change', '#feature_on_homepage', function(e) {
            $('.homepage_order__wrapper').toggleClass('d-none');
        });
    </script>
@stop
