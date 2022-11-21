@extends('layouts.front.blank')

@section('content')

<div class="container">

    @include('front.invoices.invoice', compact('order'))

</div>

<div class="container">

	<div class="px-4 bg-white mb-0 rounded-0 pb-5 text-right">

		<div class="d-flex justify-content-end">
		
			@if (back()->getTargetUrl() === request()->url() )
				<a href="{{ route('home') }}" class="btn btn-secondary d-print-none mx-2">Back home</a>
			@else
				<a href="{{ back()->getTargetUrl() }}" class="btn btn-secondary d-print-none mx-2">Back</a>
			@endif

			<button onclick="window.print();" class="btn btn-highlight border-radius-0 d-print-none">Print this page</button>

		</div>

	</div>

</div>

@endsection