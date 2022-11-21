@extends('layouts.front.app')

@section('body_class', 'product')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="pb-5">

                {{-- product --}}

                    @yield('page-content')

                {{-- ./products list --}}

            </div>
        </div>
    </div>
</div>

@endsection

