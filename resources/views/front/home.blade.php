@extends('layouts.front.app')

@section('meta_tags')

<meta property="og:title" content="{{ config('app.name') }} "/>
<meta property="og:description" content="{{ config('app.description') }}"/>
<meta name="description" content="{{ config('app.description') }}" />
<meta name="keywords" content="" />
@endsection

@section('body_class', 'home')

@section('content')

<div class="container mb-3">

    <div class="row bg-secondary-4 py-4 top-box-shadow" id="search">
        <div class="col-lg-4 col-md-5">
            <p class="text-gray-2 text-uppercase text-md-right my-2">Find the products you like:</p>
        </div>
        <div class="col-md-7">
            <form action="{{ route('product.brandCatSearch') }}" class="row">
                <div class="col-md-4 pb-md-0 pb-3 col-6">
                    <select name="search_category" id="search_category" class="form-control border-radius-0">
                        <option value="">Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-6">
                    <select name="search_brand" id="search_brand" class="form-control border-radius-0">
                        <option value="">Manufacturer</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <button class="col-12 px-0 btn btn-highlight border-radius-0" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="jumbotron-fluid d-flex justify-content-center bg-secondary row">
        <div class="col-xl-8 col-lg-7 px-0 overflow-hidden">
            <img src="{{ asset('images/greenworld_hp_banner1.jpg') }}" class="img-sm-fluid" alt="Best Deals">
        </div>
        <div class="col-xl-4 col-lg-5 py-3 py-lg-0 text-center d-flex flex-column justify-content-center align-items-center">
            <img src="{{ asset('images/bestdeals.png') }}" alt="Best Deals" width="250">
            <h2 class="h1 text-black text-uppercase pt-4">On Refurbished<br />Copiers & Printers</h2>
            <h3 class="text-uppercase text-red">Fully Warrantied!</h3>
            <p>
                <a class="home__promo__btn--search-inventory text-uppercase my-2 px-5 btn btn-blue border-radius-0 d-block" href="#" class="btn">Search Inventory</a>
                <a href="{{ route('contact-us') }}?contact_type=instant-quote" class="my-2 text-uppercase btn btn-blue px-5 border-radius-0 d-block">Get Quote</a>
            </p>
        </div>
    </div>

    {{-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!POPULAR PRODUCTS START!!!!!!!!!!!!!!!! --}}
    {{-- <product-modal-component></product-modal-component> --}}
    <div class="container mt-4 pt-4">
        <div class="row pb-3">
                <h2 class="w-100 border-bottom border-secondary-5 h1 w-100 mb-5 text-uppercase text-center underline--special">
                    Clearance items <span class="text-highlight">on sale!</span>
                </h2>
        </div>
        <div class="row">
            <div id="products-home" class="row__products owl-carousel owl-theme text-black text-right">
                @foreach ($products as $product)
                    @include('front.products.listing', ['onListing' => TRUE])  
                @endforeach
            </div>
        </div>
    </div>
    <div>
        <div id="bf-revz-widget-123456798006823">
            <div class="bf-dv">
                <span class="bf-spn"> powered by <a class="bf-pwr" href=https://birdeye.com?utm_source=SRC&utm_medium=widget_review-feed&utm_campaign=birdeye_widget&utm_term=powered-by-birdeye&utm_content=rotating-widget_#006823 target="_blank">Birdeye</a></span>
            </div>
        </div>
    </div>
    <div class="container text-center my-5">
            <h2 class="text-uppercase mb-3">FOR OUR LOCAL CUSTOMERS IN CHICAGOLAND, DO YOU NEED SERVICE?</h2>
            <h2 class="text-uppercase mb-3" style="color: #808000; font-family: sans-serif">SCHEDULE A COPIER SERVICE APPOINTMENT WITH OUR TEAM</h2>
            <a href="https://my.setmore.com/bookingpage/18f8755a-9159-4d61-862f-3c270f73fbd6" target="_blank" class="btn btn-highlight text-uppercase text-white shadow font-weight-bold mb-5 product__listing--buttons--view-more" style="font-size:19px; height:43px; width: 280px">Book Appointment</a>
    </div>


    {{-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!POPULAR PRODUCTS FINISH!!!!!!!!!!!!!!!! --}}

    @include('front.shared.usp-bar')

    {{-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!FEATURED BRANDS START!!!!!!!!!!!!!!!! --}}

    <section class="pb-3 bg-lighter">
        <div class="container py-4">
            <h2 class="h1 text-center text-uppercase">
                <div class="text-black w-100 border-bottom border-secondary-5 h1 w-100 mb-5 text-uppercase text-center underline--special">Brands</div>
            </h2>
            <div class="row">
                <div class="px-5 col-12 d-flex brands" id="brands-home">
                    @foreach ($brands as $brand)
                        <div class="">
                            <a class="brand d-block text-center card-img-top py-2 popular-bg h-100 hover-trans nounderline" 
                                href="{{ route('brand.show', $brand) }}">
                                    <div class="brand__tab text-black text-left bg-secondary-3 p-2 position-relative">
                                        {{ $brand->name }}
                                    </div>
                                    <img src="{{Croppa::url('/storage/' . $brand->cover, 200, 200, array('pad')) }}" alt="{{ $brand->name }}" class="img-fluid" />
                            </a>
                        </div>
                    @endforeach 
                </div>
            </div>
        </div>
    </section>

    {{-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!FEATURED BRANDS END!!!!!!!!!!!!!!!! --}}

</div>

@endsection

@section('js')
    <script type="text/javascript" src=https://birdeye.com/embed/v4/162992411982674/8/123456798006823 async></script>
@endsection