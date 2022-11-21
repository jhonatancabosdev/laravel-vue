@extends('layouts.front.app')

@section('content')

<div>
	<form 
	    method="POST" 
	    action="{{ route('checkout.execute') }}" 
	    class="form--checkout--shipping form mt-4 form--checkout needs-validation {{ $errors->count() > 0 ? 'was-validated' : '' }} jq-checkout-form mb-3" 
		novalidate=""
	>
	    @csrf
	    <div class="container">

	        <div class="row mb-4 mt-5">
	            <div class="col-12">
	                <h1 class="text-center text-uppercase h4 mb-3 font-weight-bold text-dark">Checkout</h1>
	            </div>
	        </div>
						
	        <div class="row">
				
				<div class="col-12 col-md-7 col-lg-7 col-xl-7 order-xl-1">
					
					<div class="row">
						<div class="col-12">
							@if ($weight > 50)
								<p>When the total weight of the products in an order is greater than 50 lbs, we apply $250 flat-rate shipping via UPS Ground.</p>
								<p>The current cart weight is {{ $weight }}.</p>
							@else								
								<ups-shipping-options
									class="mb-4"
									title="Choose your shipping method" 
									:selected="{{ json_encode(session('shipping', 1)) }}" 
								>
								</ups-shipping-options>
							@endif
	            		</div>

	            	</div>
		
					<div class="col-12 px-0">
						<div class="my-2">&nbsp;</div>
						<h2 class="h4 font-weight-bold d-flex justify-content-between align-items-center mb-3">Payment Information</h2>
					</div>

					<div class="col-12 px-0">
						<div class="d-flex flex-wrap justify-content-start">
							<label class="btn border border-radius-0 btn-light mr-4 min-w-100px p-3">
								<i class="fas fa-credit-card fa-4x"></i>
								<div class="form-check mt-2">
									<input 
										class="form-check-input"
										type="radio" 
										name="payment_method"
										id="credit-card"
										value="credit_card"
									>
									<label class="form-check-label text-muted" for="credit-card">
										Credit Card
									</label>
								</div>
							</label>
							<label class="btn border border-radius-0 btn-light min-w-100px p-3">
								<i class="fab fa-paypal fa-4x"></i>
								<div class="form-check mt-2">
									<input 
										class="form-check-input"
										type="radio" 
										name="payment_method" 
										id="paypal" 
										value="paypal" 
									>
									<label class="form-check-label text-muted" for="paypal">
										Paypal
									</label>
								</div>
							</label>
						</div>
					</div>

					{{-- Credit Card Form --}}
					<div class="d-none col-12 px-0 flex-column flex-lg-row credit-card-form-wrapper" id="credit-card-container">
						<div class="my-2">&nbsp;</div>
						{{-- payment --}}
						<div class="col-12 payment border alert rounded bg-light">
							<div class="mb-0">
								<div class="row">
									<div class="col-md-12 mb-3">
										<label class="text-muted" for="cc_number">Credit card number</label>
										<input name="cc_number" type="text" class="form-control" id="cc_number" value="{{ old('cc_number') }}" required="">
			
										@if ($errors->has('cc_number'))
											<div class="invalid-feedback">
												{{ $errors->first('cc_number') }}
											</div>
										@endif
			
									</div>
								</div>
								<div class="row mb-2">
									<div class="col-12">
										<label class="text-muted" for="cc_name">Name on card</label>
										<input name="cc_name" type="text" class="form-control" id="cc_name" value="{{ old('cc_name') }}" required="">
										<small class="text-muted">Full name as displayed on card</small>
			
										@if ($errors->has('cc_name'))
											<div class="invalid-feedback">
												{{ $errors->first('cc_name') }}
											</div>
										@endif
										
									</div>
									<div class="col-md-3">
										<label class="text-muted col-12 px-0" for="cc_expiration">Expiration</label>
										<div class="col-12 px-0 d-flex">
											<input autocomplete="cc-exp-month" name="cc_expiration_month" min="1" max="12" type="number" class="form-control col-5 px-1" id="cc_expiration_month" value="{{ old('cc_expiration_month') }}" required="" maxlength="2" default="12" max="12">
											<div class="col-2 text-center">/</div> 
											<input autocomplete="cc-exp-year" name="cc_expiration_year" type="number" min="20" max="49" class="form-control col-5 px-1" id="cc_expiration_year" value="{{ old('cc_expiration_year') }}" required="" maxlength="2" min="20" default="20" max="99">
										</div>
										@if ($errors->has('cc_expiration_month'))
											<div class="invalid-feedback">
												{{ $errors->first('cc_expiration_month') }}
											</div>
										@endif
										@if ($errors->has('cc_expiration_year'))
											<div class="invalid-feedback">
												{{ $errors->first('cc_expiration_year') }}
											</div>
										@endif
	
									</div>
									<div class="col-md-3">
										
										<label class="text-muted" for="cc_cvv">CVV</label>
			
										<input name="cc_cvv" type="text" class="form-control" id="cc_cvv" value="{{ old('cc_cvv') }}" required="">
			
										@if ($errors->has('cc_cvv'))
											<div class="invalid-feedback">
												{{ $errors->first('cc_cvv') }}
											</div>
										@endif
										
									</div>
								</div>
								<div class="row mb-2">
									<div class="col-12 text-right">
										<button type="submit" class="btn btn-highlight py-2 px-5">Confirm</button>
									</div>
								</div>
							</div>
						</div>
						<div class="row col-12 card-wrapper py-3"></div>
						{{-- ./payment --}}
					</div>

	            </div>
	    
	            <div class="col-12 col-md-5 col-lg-5 offset-lg-0 col-xl-4 offset-xl-1 order-xl-2 mb-4">
	                <div class="bg-lighter border-success border shadow px-4 pt-3 pb-4 mb-5">
	                    <cart-overview-component></cart-overview-component>
	                </div>
	            </div>
			</div>
		</div>
		
	</form>
</div>

  <!-- Modal -->
  <div class="modal fade" id="paymentModal" role="dialog">
    <div class="modal-dialog">
          <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body">
          <p id="paymentModalMessage"></p>
        </div>
      </div>
      
    </div>
  </div>

@endsection

@section('css')
	<style>
		.jp-card-container{
			margin: auto !important;
		}
		.form-control {
			border-bottom-left-radius: 5px;
		    border-top-left-radius: 5px;
		}
		.jp-card-container {
			transform: scale(1.14) !important;
		}
		.form-control {
			border: 1px solid #bbbbbb;
		}
	</style>
@endsection

@push('scripts')

	<script src="{{ mix('js/card.js') }}"></script>
	<script>
		$(function(){
			$('[name="payment_method"]').on('change', function(event) {
				var $cc_container = $('#credit-card-container');
				if ($(this).val() === 'credit_card') {
					$cc_container.removeClass('d-none').fadeOut(0);
					$cc_container.fadeIn(500);
					$([document.documentElement, document.body]).animate({
						scrollTop: $cc_container.offset().top
					}, 1000);
					$('#cc_number').focus();
				} else {
					$cc_container.fadeOut(300);
					$("#paymentModalMessage").html("You will be redirected PayPal shortly. You will be sent back to the site when you complete payment.");
					$('#paymentModal').modal("show");
					$(this).delay(1000).parents('form').submit();
				}
			});
		});
	</script>

@endpush
