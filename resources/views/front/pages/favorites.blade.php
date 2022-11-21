@extends('layouts.front.app')

@section('body_class','favorites')
@section('breadcrumb_text','Wish List')

@section("title", "Green World Copier & Supplies | Wish List")

@section('meta_tags')
	<meta property="og:title" content="Green World Copier & Supplies | Wish List"/>
	<meta property="og:description" content="{{ config('app.description') }}"/>
    <meta name="description" content="{{ config('app.description') }}" />

	<meta name="keywords" content="" />
@endsection


@section('content')
  
<div class="container mt-3 px-0">
    <div class="row py-lg-4">
        <div class="col-12 text-center mb-5">

            <div>
                <h1>Wish List</h1>
                <div class="d-flex flex-row align-items-center justify-content-center alert alert-info mt-3 mx-3 jq-alert border-radius-0">
                    <div class="mr-3">
                        <strong class="font-weight-semi-bold">Please wait, loading your wish list</strong>
                    </div>
                    <div class="fa-2x">
                        <i class="fas fa-spinner fa-pulse"></i>
                    </div>
                </div>
            </div>

            <div class="jq-content mt-5">
                <div class="text-right px-3">
                    <button class="btn border-radius-0 mb-4 btn-highlight jq-add-items-to-cart">Add all favorites to cart</button>
                </div>
                <div>
                    <products-component :display-breadcrumb="false" :display-category-name="false" :initial-load="false"></products-component>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection

@section('js')
    <script>
        window.isInit = false;
        window.initProductsComponent = function() {
            try {

                $('.jq-page-content').parents('.container').busyLoad('show');
                
                let products = JSON.parse(localStorage.getItem('favorites'));

                if (products === null || products.length === 0) {
                    $('.jq-page-content').parents('.container').busyLoad('hide');
                    $('.jq-alert').remove();
                    $('.jq-content').empty().append('<div class="alert alert-warning">No products added to your favorites</div>')
                    window.isInit = true
                    return false;
                }

                $.ajax({
                    url: "{{ route('product.search') }}",
                    type: 'GET',
                    data: {
                        keyword: 'favorites',
                        ids: products,
                    },
                })
                .done(function(response) {
                    app.$root.$emit('refresh_products', response)
                    $('.jq-page-content').parents('.container').busyLoad('hide')
                    $('.jq-alert').remove()
                })
                .always(function() {
                    $('.jq-page-content').parents('.container').busyLoad('hide')
                    $('.jq-alert').remove()
                    window.isInit = true
                })

                $('body').on('click', '.jq-add-items-to-cart', function(event) {
                    $('.jq-page-content').parents('.container').busyLoad('hide')
                    event.preventDefault();
                    products.map(function(id, index) {
                        setTimeout(function() {
                            addToCart(id, 1)
                        }, (index + 1) * 300)
                    })
                });

                window.addToCart = function (id, quantity) {
                    $.ajax({
                        url: '/cart',
                        type: 'POST',
                        headers: { 'X-CSRF-Token' : $('meta[name="_token"]').attr('content') },
                        data: {
                            id: id,
                            quantity: quantity
                        },
                    })
                    .done(function(response) {
                        app.$root.$emit('cartItemAdded', response);
                    })
                    .always(function() {
                        $('.jq-page-content').parents('.container').busyLoad('hide')
                        $('.jq-alert').remove()
                    });
                }
                            
            } catch (e) {
                $('.jq-page-content').parents('.container').busyLoad('hide')
                $('.jq-content').empty().append('<div class="alert alert-warning">No products added to your favorites</div>')
                $('.jq-alert').remove()
                window.isInit = true
            }
        }
    </script>
@endsection

@push('scripts')
    <script>
        $(function() {
            setTimeout(function() {
                if (window.isInit === false) {
                    window.initProductsComponent();
                }
            }, 3 * 1000)
        })
    </script>
@endpush