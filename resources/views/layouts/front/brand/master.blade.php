@extends('layouts.front.app')

@section('content')
    <div class="container">
        <div class="row bg-white px-0 pt-3 pb-5">
            {{-- filter --}}
            <div class="col-lg-3 pl-lg-0 pr-lg-3">
                @if (isset($brand))
                    <accordion-component current-link="{{ $brand->slug }}" identifier="brand" active="{{ $brand->slug }}" :links="{{$brands}}" ></accordion-component>
                @else
                    <accordion-component identifier="brand" active="" :links="{{$brands}}" ></accordion-component>
                @endif
            </div>
            {{-- ./filter --}}
            {{-- products list --}}
            <div class="col-lg-9 mt-5 mt-lg-0">
                @yield('page-content')
            </div>
            {{-- ./products list --}}
        </div>
    </div>
@endsection
