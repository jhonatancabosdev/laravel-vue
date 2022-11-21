@extends('layouts.front.app')

@section('breadcrumb_text', 'About Us')

@section("title", "Green World Copier & Supplies | About Us")

@section('meta_tags')
	<meta property="og:title" content="Green World Copier & Supplies | About Us"/>
	<meta property="og:description" content="{{ config('app.description') }}"/>
    <meta name="description" content="{{ config('app.description') }}" />

	<meta name="keywords" content="" />
@endsection



@section('content')
	
	<div class="container px-2 mt-3 terms-container">
		<div class="row py-4">

			<div class="col-12 text-center mb-5">
				<h1 class="mb-4">About us</h1>
				
			</div>

			<div class="col-12"> 
				<p>We specialize in rebuilding and refurbishing top-tier office equipment to make them more accessible to a wider audience of consumers without breaking the bank. Our supplies are all new OEM, furthering our professional services. Additionally, our Green Initiative enables us to help save the environment by keeping lightly-used office equipment and supplies out of landfills, and instead, distributed among new owners at a fair price. We take pride in our prime equipment and the extensive rebuilding processes that take place, giving the customer the product he or she deserves. We are located at 1220 N Old Rand Rd, Wauconda, IL 60084 Call us at 877-211-0039 Monday to Friday 8:00 AM to 5:00 PM</p>
			</div>
			
		</div>
	</div>

@endsection