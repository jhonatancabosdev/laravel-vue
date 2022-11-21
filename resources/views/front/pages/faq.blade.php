@extends('layouts.front.app')

@section('breadcrumb_text', 'Frequently Asked Questions')

@section("title", "Green World Copier & Supplies | Frequently Asked Questions")

@section('meta_tags')
	<meta property="og:title" content="Green World Copier & Supplies | Frequently Asked Questions"/>
    <meta property="og:description" content="{{ config('app.description') }}"/>
    <meta name="description" content="{{ config('app.description') }}" />

	<meta name="keywords" content="" />
@endsection

@section('content')
	
	<div class="container px-2 mt-3 terms-container">
		<div class="row py-4">

			<div class="col-12 text-center mb-5">
				<h1 class="mb-4 border-bottom pb-3 border-gray">FAQ</h1>
				
			</div>

			<div class="col-12"> 
				<dl>
                    <dt>> Is it safe to buy in GWCS?</dt>
                    <dd>Yes, in Green World Copier and Supplies We guarantee 100% the security of your payments.Remember that at any time you can contact us through the following means:<br />
                        - By email: <a href="mailto:info@gwcopiers.com">info@gwcopiers.com</a> - On the telephone <a href="tel:+18772110039">877-211-0039</a> at UN from Monday to Friday from 8 a.m. to 6 p.m. For our social networks</dd>
                    <dt>> What are the methods of payment?</dt>
                    <dd>Credit cards.<br />
                        Debit cards.<br />
                        Deposit or bank transfer.<br />
                        Paypal.</dd>
                    <dt>> The products have a guarantee?</dt>
                    <dd>All products in GWCS, provided that it is used under normal conditions and with the minimum necessary care, have a limited warranty against non-conformities of materials or workmanship (manufacturing defects).</dd>
                    <dt>> How I can cancel my order?</dt>
                    <dd>To be able to cancel it, you must contact our customer service center, for which you must have the order number (invoice) at hand. Once more than 24 hours have elapsed, the order can no longer be canceled.</dd>
                    <dt>> What package does GWCS use to send products?</dt>
                    <dd>All shipments are sent through our Ups provider.</dd>
                    <dt>> What is the delivery time?</dt>
                    <dd>The delivery time depends on the product purchased and the final destination.</dd>
                    <dt>> What is the shipping cost?</dt>
                    <dd>The shipping cost will depend on the quantity of products you buy and the zip code.</dd>
                    <dt>> Does GWCS deliver throughout the United States?</dt>
                    <dd>Shipments to the 50 States of U.S.</dd>
                    <dt>> Does GWCS deliver outside of the United States?</dt>
                    <dd>Shipping to Mexico, Central America and south America.</dd>
                    <dt>> How can I track my order?</dt>
                    <dd>You can follow up directly on the UPS webpage: https://www.UPS.com/</dd>
                </dl>
			</div>
			
		</div>
	</div>

@endsection