<section class="container product__buy-box">
	<div class="row">

		<div class="col-md-6 px-0">
			<div class="product--image__wrapper alert alert-light border rounded text-center d-flex align-items-center mb-4">
			<product-images-component :product="{{ json_encode($product) }}"></product-images-component>
			</div>
		</div>

		<div class="col-md-6">

			<div class="col-12">

				<h1 class="h2">{{ $product->name }}</h1>
				@isset($product->brand)
					<h2 class="h5">{{ $product->brand['name'] }}</h2>
				@endisset

				<div class="form-group mt-4 col-md-12">
					<span class="text-decoration-line-through">{{ config('cart.currency_symbol') }}{{ number_format($product->original_price, 2) }}</span>
					<span class="text-highlight h3">{{ config('cart.currency_symbol') }}{{ number_format($product->price, 2) }}</span>
				</div>

				<table class="table">
					<tr>
						<th class="py-2">
							Brand
						</th>
						<td class="py-2 text-right">
							@isset($product->brand)
								{{ $product->brand['name'] }}
							@endisset
						</td>
					</tr>
					<tr>
						<th class="py-2">Product Code</th>
						<td class="py-2 text-right">{{ $product->sku }}</td>
					</tr>
					<tr>
						<th class="py-2">Availability</th>
						<td class="py-2 text-right">
							@if($product->quantity > 0)
							<span class="text-success">{{$product->quantity}} in stock</span>
							@else
							<span class="text-danger">Out of Stock</span>
							@endif
						</td>
					</tr>
				</table>


				<product-cart-component :product="{{ json_encode($product) }}">
					
				</product-cart-component>

				
				@isset($product->short_description)					
				<div class="pb-2 mb-3">
					{!! $product->short_description !!}
				</div>
				@endisset
				<p><a href="#"><i class="fa fa-star text-secondary-5"></i> <span class="pl-2">Add to Favorites</span></a></p>
				
				<p><b>Share</b></p>
				<ul class="list-inline">
					<li class="list-inline-item px-1">
						<a target="_blank" class="text-black" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->full() }}">
							<i class="fab fa-facebook-f"></i>
						</a>
					</li>
					<li class="list-inline-item px-1">
						<a target="_blank" href="https://twitter.com/intent/tweet?text={{ urlencode("Check out this " . $product->name) . '+' . url($product->id) }}" class="text-black">
							<i class="fab fa-twitter"></i>
						</a>
					</li>
					<li class="list-inline-item px-1">
						<a target="_blank"
					href="http://pinterest.com/pin/create/button/?url={{ url($product->id) }}&media={{ url($product->main_image) }}&description={{ urlencode("Check out this " . $product->name) }}"
							class="text-black">
							<i class="fab fa-instagram"></i>
						</a>
					</li>
					<li class="list-inline-item px-1">
						<a target="_blank" href="mailto:?subject={{ urlencode("Check out this " . $product->name) }}&amp;body={{ urlencode("Check out this " . $product->name) }}" class="text-black">
							<i class="fa fa-envelope"></i>
						</a>
					</li>
				</ul>
			</div>
			
		</div>
		
	</div>
</section>