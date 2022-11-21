@extends('layouts.front.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="bg-white px-5 py-4">
                <div class="code">
                    <h1 class="text-center font-weight-bold">@yield('code')</h1>
                </div>
        
                <div class="message" style="padding: 10px;">
                    <h2 class="text-center font-italic">@yield('message')</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection