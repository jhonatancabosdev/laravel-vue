@extends('layouts.front.app')

@section('content')
    <div class="container">
        <div class="row bg-white px-0 pt-3 pb-5">
            {{-- filter --}}
            <div class="col-lg-3 pl-lg-0 pr-lg-3">
                @if (isset($category))
                @isset($tags)
                <div class="sidebar-filter-wrapper">
                    <h3 class="mt-5 h2 text-highlight text-uppercase">Filter Options</h3>
                    <sidebar-filters-component :price-to="{{ $maxPrice }}" :price-from="{{ $minPrice }}" category="{{ $category->slug }}" :filter-sets="{{ json_encode($tags) }}"></sidebar-filters-component>
                </div>
                @endisset
                <h3 class="h2 text-highlight text-uppercase">Categories</h3>
                @php
                    $accordionCat = $category->parent ? $category->parent->slug : $category->slug;
                @endphp
                <accordion-component current-link="{{ $category->slug }}" identifier="category" active="{{ $accordionCat }}" :links="{{$navigationCategories}}" ></accordion-component>
                
                @else
                    <accordion-component identifier="category" active="" :links="{{$navigationCategories}}" ></accordion-component>
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
