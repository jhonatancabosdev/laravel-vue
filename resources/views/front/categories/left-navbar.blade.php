
@if($category->parent_id == null) 
<div class="card-header text-black text-uppercase bg-light-green border-radius-0 py-2 px-2">

<li>
    <a 
        href="{{ route('category.show', $category) }}" 
        class="
            {{ is_active(route('category.show', $category)) || (isset($currentCategory) && $currentCategory->id === $category->id) ? 'font-weight-cat' : '' }}
            text-dark  
            border-top-0 
            {{-- bg-secondary-{{ $category->depth + 1 }}  --}}
            pl-{{ $category->depth === 0 ? 2 : $category->depth + 3 }} 
            pr-1
            d-block 
            py-2
        "
    > 
        {{ $category->name }}
    </a>
</li>

</div>
@else
<li>
<a 
    href="{{ route('category.show', $category) }}" 
    class="
        {{ is_active(route('category.show', $category)) || (isset($currentCategory) && $currentCategory->id === $category->id) ? 'font-weight-bold' : '' }}
        text-dark 
        bg-white
        border-top-0 
        {{-- bg-secondary-{{ $category->depth + 1 }}  --}}
        pad-l-2
        pr-1
        d-block 
        py-2
    "
>
    {{ $category->name }}
</a>
</li>
@endif

