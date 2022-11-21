@extends('layouts.front.product.master')

@section("title", "Green World Copier & Supplies | $product->name")

@section('meta_tags')

<meta property="og:title" content="Green World Copier & Supplies | {{ $product->name }}"/>
<meta property="og:description" content="Green World specializes in rebuilding and refurbishing copiers & supplies for offices. Search our products for the best deals on top-tier office equipment!"/>
<link rel="canonical" href="{{ config('app.url') }}/product/{{ $product->slug }}" />
<meta name="description" content="{{ $product->description }}" />
<meta name="keywords" content="" />
@endsection

@section('page-content')

<div class="row p-3">
	<product-component :product="{{ json_encode($product) }}"></product-component>
	{{-- @include('front.shared.product-buy-box') --}}
</div>

<section class="px-0 px-sm-3 section section--full-description pt-5 font-family-alt" id="full">
	<div class="row">
		<div class="col-12">
			{!!$product->description !!}
		</div>
	</div>
</section>

@if ($relatedProducts && !$relatedProducts->isEmpty())
<section class="section section--related-products container py-3 my-4 px-0">
	<div class="grid grid--products grid--products--related container mt-3 px-0">
		<div class="row">
			<h2 class="col-12 text-center h1 mb-5 pb-0 text-uppercase underline--special border-bottom">Clearance Items <span class="text-highlight">On Sale</span></h2>
			{{-- Get First 3 Related Products --}}
		</div>
		<div class="card-deck">
			@include('front.products.related', ['onListing' => TRUE])
		</div>
	</div>
	@if(method_exists($relatedProducts,'total') && $relatedProducts->total() > 3)
	
	<div class="row my-3">
		<p class="col-12 text-center">
		<a href="{{ route('product.related', $product) . '?page=' }}" class="see-more" data-page="2">See More <i class="d-inline-block mx-1 fas fa-angle-down text-secondary-2"></i></a>
		</p>
	</div>
	@endif
</section>
<product-modal-component></product-modal-component>
@endif

<script type="application/ld+json">
	{
	  "@context": "http://schema.org",
	  "@type": "Product",
	  "description": "{{ $product->short_description }}",
	  "name": "{{ $product->name }}",
	  "image": "{{ asset($product->main_image) }}",
	  "offers": {
		"@type": "Offer",
		"availability": "{{ $product->quantity > 0 ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock' }}",
		"price": "{{ number_format($product->price, 2) }}",
		"priceCurrency": "USD"
	  }
	}
</script>


@endsection
