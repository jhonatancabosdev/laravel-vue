@extends('layouts.front.app')

@section('breadcrumb_text', 'Refunds')

@section("title", "Green World Copier & Supplies | Refunds")

@section('meta_tags')
	<meta property="og:title" content="Green World Copier & Supplies | Refunds"/>
	<meta property="og:description" content="{{ config('app.description') }}"/>
    <meta name="description" content="{{ config('app.description') }}" />

	<meta name="keywords" content="" />
@endsection

@section('content')
	
	<div class="container px-2 mt-3 terms-container">
		<div class="row py-4">

			<div class="col-12 text-center mb-5">
				<h1 class="mb-4">Refunds Policy</h1>
			</div>
			
			<div class="col-12">
                <p>You will be notified as soon as the unit has returned to our location. The status of your refund will be revealed to you after the unit has undergone a careful inspection.</p>
                <p>If your return is approved, we will initiate a refund to your credit card (or original method of payment).</p>
                <p>You will receive the credit within 5-10 business days, depending on your card issuer's policies.</p>
                <p>For additional information please give us a call <a href="tel:+18772120039">877-211-0039</a>.</p>
                
			</div>
		
		</div>

	</div>

@endsection
