@extends("layouts.front.category.master")

@if($category)

	@section("title", "Green World Copier & Supplies | $category->name")
	@section("body_class", "category category--$category->slug $category->slug")

	@section("meta_tags")

	<meta property="og:title" content="{{ $category->name }} :: {{ config('app.name') }} "/>
	<meta property="og:description" content="{{ $category->description ?? config('app.description') }}"/>
	<link rel="canonical" href="{{ config('app.url') }}/category/{{ $category->slug }}" />
	<meta name="description" content="{{ $category->description !== '' ?? 'Shop Green World Copiers & Supplies for affordable, top-tier office equipment. Rebuilt and Refurbished Copiers, Printers, Scanners, Ink and Toner.' }}" />
	<meta name="keywords" content="" />
	@endsection
@elseif(request()->has('keyword'))
	@section("title", "Green World Copier & Supplies | Search for" . request()->get('keyword'))
	@section("body_class", "category search search--" . request()->keyword)
@else
	@section("title", "Green World Copier & Supplies")
	@section("body_class", "category search")
@endif

@section("page-content")

	<div class="row mb-5">
		@if (request()->has('keyword') || isset($category->name))
			<div class="col-12">
				<h1 class="h1--category h1 text-center text-uppercase mb-4">
					@if (request()->has('keyword'))
					Looking for {{ request()->keyword }}?
					@else
					{{ $category->name ?? '' }}					
					@endif
				</h1>
			</div>
		@endif

		{{-- <div class="col-12">
			@include('front.shared.filter-bar')
		</div> --}}

		{{-- <div class="col-12">
			<products-component :filter="false" currentCategory="{{ $category }}" products="{{ $products }}" :display-breadcrumb="false" :display-category-name="true" :initial-load="false"></products-component>
		</div> --}}

		<products-component :filter="false" current-category="{{ json_encode($category) }}" :prods="{{ json_encode($products) }}" :display-breadcrumb="false" :display-category-name="true" :initial-load="{{ isset($search) ? 'false' : 'true' }}"></products-component>

		{{-- <div class="col-12 mt-5 pagination">
			<div class="flex-wrap align-items-center justify-content-center w-100 d-flex">
				{{ 
					$products->appends([
						'keywords' => request()->keywords,
						'from' => request()->from,
						'to' => request()->to,
					])
					->links() 
				}}
			</div>	
		</div> --}}

		<product-modal-component></product-modal-component>

	</div>

@endsection