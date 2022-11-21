@extends('layouts.front.app')

@section('breadcrumb_text', 'Company Information')

@section("title", "Green World Copier & Supplies | Company Information")

@section('meta_tags')
	<meta property="og:title" content="Green World Copier & Supplies | Company Information"/>
	<meta property="og:description" content="{{ config('app.description') }}"/>
    <meta name="description" content="{{ config('app.description') }}" />

	<meta name="keywords" content="" />
@endsection

@section('content')
	
	<div class="container px-2 mt-3 terms-container">
		<div class="row py-4">

			<div class="col-12 text-center mb-5">
				<h1 class="mb-4 border-bottom pb-3 border-gray">Company Information</h1>
				
			</div>

			<div class="col-12"> 
                <h2 class="text-uppercase">Green World Copier & Supplies</h2>
                <p class="text-uppercase font-family-alt">Where the Customer Meets Their Printing Expectations</p>
                <p class="font-family-alt">Tel: <a href="tel:+18772110039">(877)-211-0039</a></p>
                <p class="font-family-alt">Green World Copier & Supplies provides high-quality Xerox printing equipment to offices and print shops to meet every printing need.</p>
                <p class="font-family-alt">Shipping to all 50 states and extensive international locations, we invest in each and every machine for the ultimate consumer experience.</p>
			</div>
			
		</div>
	</div>

@endsection