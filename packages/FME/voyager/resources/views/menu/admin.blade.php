<ol class="dd-list">

    @foreach ($items as $item)

        <li class="dd-item position-relative" data-id="{{ $item->id }}">
            @if (! $item->children->isEmpty())
                <button style="left: 3px; top: 3px; z-index: 10000" class="position-absolute btn btn-sm jq-expend-nested">
                    <i class="fas fa-caret-down"></i>
                </button> 
            @endif
            
            <div class="dd-handle position-relative">
                <span>{{ $item->title }}</span> <small class="url">{{ $item->link() }}</small>
            </div>
            <div class="item_actions btn-group">
                <div class="btn font-weight-bold btn-light btn-sm edit"
                    data-id="{{ $item->id }}"
                    data-title="{{ $item->title }}"
                    data-url="{{ $item->url }}"
                    data-target="{{ $item->target }}"
                    data-icon_class="{{ $item->icon_class }}"
                    data-color="{{ $item->color }}"
                    data-route="{{ $item->route }}"
                    data-parameters="{{ json_encode($item->parameters) }}"
                >
                    Edit
                </div>
                <div class="btn font-weight-bold btn-sm btn-light delete px-2" data-id="{{ $item->id }}">
                    <i class="voyager-trash"></i>
                </div>
            </div>
            @if(!$item->children->isEmpty())
                @include('voyager::menu.admin', ['items' => $item->children])
            @endif
        </li>

    @endforeach

</ol>
