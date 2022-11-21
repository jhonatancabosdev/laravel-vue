<ol class="dd-list">

    @foreach ($items as $item)

        <li class="dd-item position-relative" data-id="{{ $item->id }}" data-depth="{{ $item->depth }}">

            @if(! $item->children->isEmpty() && ( (int) $dataType->nested_max_depth !== 0 && $item->depth < ($dataType->nested_max_depth - 1) ))
                <button style="left: 3px; top: 3px; z-index: 100" class="position-absolute btn btn-sm jq-expend-nested">
                    <i class="fas fa-caret-down"></i>
                </button> 
            @endif
            
            <div class="dd-handle position-relative">
                <span>{{ $item->name ?? $item->title ?? '--' }}</span>
            </div>
            <div class="item_actions btn-group jq-item-data" data-item='@json($item->toArray())'>

                @if (! is_null($imageField) && isset($item->{$imageField->field}))
                    <a
                        href="#"
                        role="button"
                        class="btn font-weight-bold btn-light btn-sm py-0"
                        data-toggle="popover" 
                        data-html="true"
                        data-container="body" 
                        data-toggle="popover" 
                        data-placement="top"
                        data-trigger="hover"
                        data-content='
                            <img src="{{ asset($item->{$imageField->field}) }}" class="img-fluid" style="max-width:100px">
                        '>
                        Image
                    </a>
                @endif

                @if ($item->depth < ($dataType->nested_max_depth - 1))
                    <div 
                        class="btn font-weight-bold btn-light btn-sm jq-add-child py-0"
                        data-id="{{ $item->id }}"
                    >
                        Add Under
                    </div>
                @endif
                <div 
                    class="btn font-weight-bold btn-light btn-sm jq-edit py-0"
                    data-id="{{ $item->id }}"
                >
                    Edit
                </div>
                <a href="{{ route('voyager.'.$dataType->slug.'.nested.show', $item->id) }}" class="btn font-weight-bold btn-light btn-sm d-none py-0">
                    View
                </a>
                <div class="btn font-weight-bold btn-sm btn-light jq-delete px-2 py-0" data-id="{{ $item->id }}">
                    <i class="voyager-trash"></i>
                </div>
            </div>

            @if(! $item->children->isEmpty() && ( (int) $dataType->nested_max_depth !== 0 && $item->depth < ($dataType->nested_max_depth - 1) ))
                @include('voyager::menu.nested-bread', [
                    'imageField' => $imageField,
                    'items' => $item->children, 
                    'dataType' => $dataType
                ])
            @endif
        </li>

    @endforeach

</ol>
