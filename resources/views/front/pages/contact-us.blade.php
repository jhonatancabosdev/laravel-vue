@extends('layouts.front.app')

@section('breadcrumb_text', 'Contact Us')

@section('content')
	
	<div class="container px-2 mt-3 terms-container">
		<div class="row py-4">

			<div class="col-12 text-center mb-5">
				<h1 class="mb-4">Contact Us</h1>
				
			</div>

			@include('front.forms.contact-form')
			
		</div>
	</div>

@endsection