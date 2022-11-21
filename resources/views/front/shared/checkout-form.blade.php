<form 
method="POST" 
action="{{ $type == 'customer' ? route('checkout.store') : route('guest.checkout.store') }}" 
class="form form--checkout form--checkout--{{ $type }} needs-validation {{ $errors->count() > 0 ? 'was-validated' : '' }} jq-checkout-form mb-3" 
novalidate=""
>

    <div class="mb-4">
        @include('layouts.errors-and-messages')
    </div>

    @csrf

    <div class="container">

        @if ($type == 'customer'){{-- Logged in User --}}
            @include('front.shared.form-parts.customer-checkout-form')
        @else {{--Guest --}}
            @include('front.shared.form-parts.guest-checkout-form')
        @endif
    </div>
</form>      