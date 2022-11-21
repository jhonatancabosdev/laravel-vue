@extends('layouts.front.brand.master')

@section('page-content')

	<div class="row mb-5">
		@isset($brandPage)
			<div class="col-12">
				@if (request()->has('q'))
					Looking for {{ request()->q }}?
				@else
					@if ($brand->cover)
						<h1 class="text-center mb-0"><img src="{{ Croppa::url('/storage/' . $brand->cover, 200, NULL, 'auto') }}" alt="{{ $brand->name }}" class="img-fluid"></h1>
					@endif
				@endif
			</div>
		@endisset

		@if (!$products->isEmpty())
			<products-component :filter="false" :prods="{{ json_encode($products) }}" :display-breadcrumb="false" :display-category-name="false" :initial-load="false"></products-component>
		@else

			<div class="col-12 mt-4">
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					We're sorry, we currently do not have any products from this brand.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>

				{{-- product recommendations --}}
				
			</div>

		@endif


	</div>

@endsection