<nav class="section section--breadcrumb row font-family-alt">

    <div class="px-3 px-md-0 col-12 my-4 text-secondary-5">
        <a class="{{ isset($isHome) ? '' : ' text-secondary-5' }}" href="/">Home</a>
        @if(isset($currentCategory))
            @if($currentCategory->parent)
                /<a class="text-secondary-5 px-1" href="{{ route('category.show', $currentCategory->parent) }}">{{ $currentCategory->parent->name }}</a>
            @endif
            @if(strpos(url()->current(), 'product'))
                /<a href="{{ route('category.show', $currentCategory) }}" class="pl-1 text-secondary-5">{{ $currentCategory->name }}</a>
                /<span class="pl-1 text-highlight">{{ $product->name }}</span>
            @else
                /<span class="pl-1 text-highlight">{{ $currentCategory->name }}</span>
            @endif
        @elseif(isset($brand) && isset($category))
            /<span class="pl-1 text-highlight">{{ $category->name }} by {{ $brand->name }}</span>
        @elseif(isset($brand))
            /<span class="pl-1 text-highlight">products by {{ $brand->name }}</span>
        @elseif(isset($search))
        /<span class="pl-1 text-highlight">search results for {{ request()->keyword }}</span>
        @elseif(isset($product))
        /<span class="pl-1 text-hightlight">{{ $product->name }}</span>
        @elseif(View::hasSection('breadcrumb_text'))
            /<span class="pl-1 text-highlight">@yield('breadcrumb_text')</span>
        @endif
    </div>
</nav>