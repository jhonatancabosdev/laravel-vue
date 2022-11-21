@php
	$name = explode(' ', $order->name);
	$firstName = $name[0] ?? '';
	$lastName = $name[1] ?? '';
	$billing_address = $order->$billing_address;
	$shipping_address = $order->$shipping_address;
@endphp
Customer information:
First name: {{ $firstName }}
Last name: {{ $lastName }}
{{-- Billing address: {{ $billing_address['address1'] .' '. $billing_address['address2'] }}
Billing city: {{ $billing_address['city'] }}
Billing state: {{ $billing_address['state'] }}
Billing zip: {{ $billing_address['zip'] }}
Shipping address : {{ $shipping_address['address1'] .' '. $shipping_address['address2'] }}
Shipping city: {{ $shipping_address['city'] }}
Shipping state: {{ $shipping_address['state'] }}
Shipping zip: {{ $shipping_address['zip'] }} --}}
Last 4 credit card: {{ $order->LastCCDigits }}
Total shipping amount paid: {{ $order->shipping_cost }}
Tax collected: {{ $order->tax }}
Promo or Discount: {{ $order->discounts }}
Timestamp of order: {{ $order->created_at }}
Total PN’s ordered: {{ count($products) }}
@foreach ($products as $index => $product)
@if( is_array($product->pivot->options) )@php $product->pivot->options = json_encode($product->pivot->options);@endphp@endif
@php $product->pivot->options = json_decode($product->pivot->options, true); @endphp
Item{{ $index + 1 }} Part number: {{ $product->id }}
Item{{ $index + 1 }}  Shipping method: {{ $shipping_name }}
Item{{ $index + 1 }}  Length uom: {{ $product->length_uom }}
Item{{ $index + 1 }}  Height uom: {{ $product->height_uom }}
Item{{ $index + 1 }}  Width uom: {{ $product->width_uom }}
Item{{ $index + 1 }}  Weight uom: {{ $product->weight_uom }}
Item{{ $index + 1 }}  Volume uom: {{ $product->volume_uom }}
Item{{ $index + 1 }}  Sales uom: {{ $product->sales_uom }}
@endforeach

