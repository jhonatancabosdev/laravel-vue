@component('mail::message')

# <h1 style="text-align: center; font-size: 25px !important; margin-bottom: 40px;">Your order has been placed!</h1>

## <strong>Order Number: <kbd style="color: #3d4852">#{{ $order->id }}</kbd></strong>

@component('mail::table')
| Product       |  &nbsp;&nbsp; | Quantity         | Price |
| ------------- | ------------- |:-------------:| --------:|
@foreach( $products as $product )|![{{ $product->name }}]({{ asset( Croppa::url("$product->main_image", 45, 45, ['resize']) ) }})|[@if ( (int) $product->pivot->is_subscription === 1)(Subscription) - @endif{{ \Illuminate\Support\Str::limit($product->name, 35) }}]({{ route('product.show', $product->slug) }})|{{ $product->pivot->quantity }}|${{ number_format($product->price * $product->pivot->quantity, 2) }}|
@endforeach
@endcomponent

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr style="border-bottom: 1px solid #555555; vertical-align: top;">
<td style="padding: 16px;">
@component('mail::promotion')
## Shipping Address
{{ $order->name }} <br>
{{ $shipping_address->address_1 }} <br>
@if( $shipping_address->address_2 !== null && trim($shipping_address->address_2) !== '' )
{{ $shipping_address->address_2 }} <br>
@endif
@if ( $shipping_address->city )
{{ ucfirst($shipping_address->city) }},
@endif
{{ $shipping_address->state->abv }} {{ $shipping_address->zipcode }}
@endcomponent
</td>
<td style="padding: 16px;">
@component('mail::promotion')
## Billing Address
{{ $order->name }} <br>
{{ $billing_address->address_1 }} <br>
@if( $billing_address->address_2 !== null && trim($billing_address->address_2) !== '' )
{{ $billing_address->address_2 }} <br>
@endif
@if ( $billing_address->city )
{{ ucfirst($billing_address->city) }},
@endif
{{ $billing_address->state->abv }} {{ $billing_address->zipcode }}
@endcomponent
</td>
</tr>
</table>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr style="border-bottom: 1px solid #555555; background: #edf2f7; vertical-align: top;">
<td>
@component('mail::panel')
## Details
<h3 style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; font-size: 14px; font-weight: normal; margin-top: 0; text-align: left; margin-bottom: 4px;">Subtotal: <kbd>${{ number_format($order->subtotal, 2) }}</kbd></h3>
@if ($discount)
<h3 style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; font-size: 14px; font-weight: normal; margin-top: 0; text-align: left; margin-bottom: 4px;">Discount: <kbd>${{ number_format($discount->amount, 2) }}</kbd></h3>
@endif
<h3 style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; font-size: 14px; font-weight: normal; margin-top: 0; text-align: left; margin-bottom: 4px;">Tax: <kbd>${{ number_format($order->tax, 2) }}</kbd></h3>
<h3 style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; font-size: 14px; font-weight: normal; margin-top: 0; text-align: left; margin-bottom: 4px;">Shipping: <kbd>${{ number_format($order->shipping_cost, 2) }}</kbd></h3>
<h3 style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; font-size: 14px; font-weight: bold; margin-top: 0; text-align: left; margin-bottom: 4px;">Grand Total: <kbd>${{ number_format($order->total, 2) }}</kbd></h3>
@endcomponent
</td>
<td>
@component('mail::panel')
## Payment
<strong>{{ ucwords($order->card_type) }}:</strong>
<kbd style="font-size: 13px !important;"> ####-####-####-{{ $order->LastCCDigits }}</kbd>
@endcomponent
</td>
</tr>
</table>

<br>
<br>
<br>

Thanks,<br>
{{ config('app.name') }}

@if (isset($showCancelSubscribe) && $showCancelSubscribe === true)
<br>
<div style="text-align: center;">
	<a style="color: #555555; text-align: center; text-decoration: none;" href="{{ $cancelSubscriptionUrl }}">Cancel subscription</a>
</div>
@endif

@endcomponent
