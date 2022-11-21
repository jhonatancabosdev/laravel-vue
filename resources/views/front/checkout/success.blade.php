@extends('layouts.front.app')

@section('content')

	<div class="container">
		@include('front.invoices.invoice', compact('order'))
	</div>

	<div class="container">
		<div class="px-4 bg-white mb-0 rounded-0 pb-5 text-right">
			<a href="{{ route('invoice.show') }}" class="btn btn-highlight border-radius-0">Get my invoice</a>
		</div>
	</div>

@endsection



@push('scripts')
{{-- DATALAYER CONVERSION REPORTING FOR Google Tag Manager --}}
<script>
	
	try {
		window.dataLayer.push({
            'event': 'sale',
            'conversionValue': {{ number_format($order->subtotal, 2, '.', '') }},
            'transactionId': '{{ (string) $order->id }}'
        });
	} catch (e) {
		console.log(e)
	}

	try {

		window.dataLayer.push({
		   	'event': 'purchase',
		   	'value': {{ number_format($order->subtotal, 2, '.', '') }},
		   	'ecommerce': {
			    'purchase': {
					'actionField': {
						'id': @json($order->id),
						'affiliation': @json(config('app.name')),
						'revenue': {{ number_format($order->subtotal, 2, '.', '') }},
						'tax': {{ number_format($order->tax, 2, '.', '') }},
						'shipping': {{ number_format($order->shipping_cost, 2, '.', '') }}
					},
					'products': [
						@foreach ($order->products as $product)
							{
								"id": @json($product->id),
								"name": @json($product->name),
								"category": @json($product->category->name ?? ''),
								"quantity": {{ $product->pivot->quantity }},
								"price": {{ number_format($product->price, 2, '.', '') }}
							}
							@if(! $loop->last)
							,
							@endif

						@endforeach
					]
			    }
		  	}
	   	});

	} catch(e) {
		console.log(e)
	}

</script>

{{-- Survey Opt-in code for google ratings --}} 
	
{{-- <script>
	window.renderOptIn = function() {
	  window.gapi.load('surveyoptin', function() {
			window.gapi.surveyoptin.render({
			  "merchant_id": 222222222,// replace with numerical id of current project
			  "order_id": @json($order->id),
			  "email": @json($order->email),
			  "delivery_country": "US",
			  "estimated_delivery_date": @json($order->estimated_delivery_date->format('Y-m-d')),

		  });
	  });
	}
</script> --}}

@endpush