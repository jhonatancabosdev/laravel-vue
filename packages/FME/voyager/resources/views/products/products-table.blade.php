<div class="table-responsive">
    <table id="dataTable" class="table border table-sm table-striped table-hover">
        <thead class="thead-dark border border-default">
            <tr>
                <th>
                    <input type="checkbox" class="select_all">
                </th>

                @foreach($dataType->browseRows as $row)
                    <th class="align-middle text-nowrap">

                        <a class="font-weight-normal text-decoration-none" href="{{ $row->sortByUrl($orderBy, $sortOrder) }}">

                        <span>
                            {{ $row->display_name }}
                        </span>

                        <span>
                            @if ($row->isCurrentSortField($orderBy))
                                @if ($sortOrder == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @endif
                            </a>
                        </span>

                    </th>
                @endforeach

                <th class="actions text-right"></th>
            </tr>
        </thead>
        <tbody>

            @foreach($dataTypeContent as $data)

            <tr>
                <td>
                    <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}" value="{{ $data->getKey() }}">
                </td>

                @foreach($dataType->browseRows as $row)

                    @if ($data->{$row->field.'_browse'})
                        @php
                            $data->{$row->field} = $data->{$row->field.'_browse'};
                        @endphp
                    @endif

                    @if ($row->field === 'children')
                        <td>
                            @if ($data->nested_type === 'child')
                                <a target="_blank" href="{{ route('product.show', $data->children->parent) }}">Child</a>
                            @else 
                                {{ $data->nested_type }}
                            @endif
                        </td>
                        @continue(true)
                    @endif

                    @if ($row->field === 'product_belongsto_brand_relationship')
                        <td>
                            {{ $data->brand->name ?? 'No Result' }}
                        </td>
                        @continue(true)
                    @endif

                    @if ($row->field === 'name')
                        <td>
                            <a
                                href="#"
                                role="button"
                                class="text-default text-nowrap"
                                data-toggle="popover" 
                                data-html="true"
                                data-container="body" 
                                data-toggle="popover" 
                                data-placement="top"
                                data-trigger="hover"
                                data-content='
                                    {{ $data->name }}
                                '>
                                {{ 
                                    \Illuminate\Support\Str::limit($data->name, 60)
                                }}
                            </a>
                        </td>
                        @continue(true)
                    @endif

                    @if ($row->field === 'product_hasmany_product_image_relationship')

                        <td>
                            <a
                                href="#"
                                role="button"
                                class="text-default"
                                data-toggle="popover" 
                                title="Product Image" 
                                data-html="true"
                                data-container="body" 
                                data-toggle="popover" 
                                data-placement="top"
                                data-trigger="hover"
                                data-content='
                                    <img src="{{ asset($data->main_image) }}" class="img-fluid" style="max-width:100px">
                                '>
                                Show
                            </a>
                        </td>

                        @continue(true)

                    @endif

                    @if ($row->field === 'product_belongstomany_category_relationship')
                        <td>
                            @php
                                $categories = $data->categories->pluck('name');
                            @endphp

                            <a
                                href="#"
                                role="button"
                                class="text-default text-nowrap"
                                data-toggle="popover" 
                                data-html="true"
                                data-container="body" 
                                data-toggle="popover" 
                                data-placement="top"
                                data-trigger="hover"
                                data-content='
                                    {{ $categories->implode(' > ')}}
                                '>
                                {{ 
                                    \Illuminate\Support\Str::limit(
                                        $categories->map(function($category) {
                                            return \Illuminate\Support\Str::limit($category, 6);
                                        })->implode(' > ')
                                    , 30)
                                }}
                            </a>
                        </td>
                        @continue(true)
                    @endif

                    @if ($row->field === 'status')
                        <td>
                            <a 
                                class="jq-toggler" href="#"
                                data-options='["Enabled", "Disabled"]' 
                                data-values='[1, 0]' 
                                data-route="{{ route('voyager.products.toggle-column', [$data, 'status']) }}"
                            >
                                @if ( (int) $data->status === 2)
                                    Draft
                                @elseif ( (int) $data->status === 1) 
                                    Enabled
                                @else
                                    Disabled
                                @endif
                            </a>
                        </td>
                        @continue(true)
                    @endif

                    @if ($row->field === 'availability_id')
                        <td>
                            <a 
                                class="jq-toggler" href="#"
                                data-options='["In stock", "Out of stock"]' 
                                data-values='[1, 0]' 
                                data-route="{{ route('voyager.products.toggle-column', [$data, 'availability_id']) }}"
                            >
                                {{ ucfirst($data->availability->name ?? 'Out of stock') }}
                            </a>
                        </td>
                        @continue(true)
                    @endif

                    <td 
                        @if (isset($row->details) && isset($row->details->display) && isset($row->details->display->class))
                            class="{{ $row->details->display->class }}"
                        @endif
                    >

                        @if (isset($row->details->view))
                            @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $data->{$row->field}, 'action' => 'browse', 'view' => 'browse', 'options' => $row->details])
                        @elseif($row->type == 'image')
                            <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:100px">
                        @elseif($row->type == 'relationship')
                            @include('voyager::formfields.relationship', ['view' => 'browse','options' => $row->details])
                        @elseif($row->type == 'select_multiple')
                        
                            @if(property_exists($row->details, 'relationship'))

                                @foreach($data->{$row->field} as $item)
                                    {{ $item->{$row->field} }}
                                @endforeach

                            @elseif(property_exists($row->details, 'options'))
                                @if (!empty(json_decode($data->{$row->field})))
                                    @foreach(json_decode($data->{$row->field}) as $item)
                                        @if (@$row->details->options->{$item})
                                            {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                        @endif
                                    @endforeach
                                @else
                                    {{ __('voyager::generic.none') }}
                                @endif
                            @endif

                            @elseif($row->type == 'multiple_checkbox' && property_exists($row->details, 'options'))
                                @if (@count(json_decode($data->{$row->field})) > 0)
                                    @foreach(json_decode($data->{$row->field}) as $item)
                                        @if (@$row->details->options->{$item})
                                            {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                        @endif
                                    @endforeach
                                @else
                                    {{ __('voyager::generic.none') }}
                                @endif

                        @elseif(($row->type == 'select_dropdown' || $row->type == 'radio_btn') && property_exists($row->details, 'options'))

                            {!! $row->details->options->{$data->{$row->field}} ?? '' !!}

                        @elseif($row->type == 'date' || $row->type == 'timestamp')
                            @if ( property_exists($row->details, 'format') && !is_null($data->{$row->field}) )
                                {{ \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($row->details->format) }}
                            @else
                                {{ $data->{$row->field} }}
                            @endif
                        @elseif($row->type == 'checkbox')
                            @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                @if($data->{$row->field})
                                    <span class="label label-info">{{ $row->details->on }}</span>
                                @else
                                    <span class="label label-primary">{{ $row->details->off }}</span>
                                @endif
                            @else
                            {{ $data->{$row->field} }}
                            @endif
                        @elseif($row->type == 'color')
                            <span class="badge badge-lg" style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>
                        @elseif($row->type == 'text')
                            @include('voyager::multilingual.input-hidden-bread-browse')
                            <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                        @elseif($row->type == 'text_area')
                            @include('voyager::multilingual.input-hidden-bread-browse')
                            <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                        @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                            @include('voyager::multilingual.input-hidden-bread-browse')
                            @if(json_decode($data->{$row->field}) !== null)
                                @foreach(json_decode($data->{$row->field}) as $file)
                                    <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}" target="_blank">
                                        {{ $file->original_name ?: '' }}
                                    </a>
                                    <br/>
                                @endforeach
                            @else
                                <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($data->{$row->field}) }}" target="_blank">
                                    Download
                                </a>
                            @endif
                        @elseif($row->type == 'rich_text_box')
                            @include('voyager::multilingual.input-hidden-bread-browse')
                            <div>{{ mb_strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? mb_substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>
                        @elseif($row->type == 'coordinates')
                            @include('voyager::partials.coordinates-static-image')
                        @elseif($row->type == 'multiple_images')
                            @php $images = json_decode($data->{$row->field}); @endphp
                            @if($images)
                                @php $images = array_slice($images, 0, 3); @endphp
                                @foreach($images as $image)
                                    <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif" style="width:50px">
                                @endforeach
                            @endif
                        @elseif($row->type == 'media_picker')
                            @php
                                if (is_array($data->{$row->field})) {
                                    $files = $data->{$row->field};
                                } else {
                                    $files = json_decode($data->{$row->field});
                                }
                            @endphp
                            @if ($files)
                                @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                    @foreach (array_slice($files, 0, 3) as $file)
                                    <img src="@if( !filter_var($file, FILTER_VALIDATE_URL)){{ Voyager::image( $file ) }}@else{{ $file }}@endif" style="width:50px">
                                    @endforeach
                                @else
                                    <ul>
                                    @foreach (array_slice($files, 0, 3) as $file)
                                        <li>{{ $file }}</li>
                                    @endforeach
                                    </ul>
                                @endif
                                @if (count($files) > 3)
                                    {{ __('voyager::media.files_more', ['count' => (count($files) - 3)]) }}
                                @endif
                            @elseif (is_array($files) && count($files) == 0)
                                {{ trans_choice('voyager::media.files', 0) }}
                            @elseif ($data->{$row->field} != '')
                                @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                    <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:50px">
                                @else
                                    {{ $data->{$row->field} }}
                                @endif
                            @else
                                {{ trans_choice('voyager::media.files', 0) }}
                            @endif
                        @else
                            @include('voyager::multilingual.input-hidden-bread-browse')
                            <span>{{ $data->{$row->field} }}</span>
                        @endif
                    </td>
                @endforeach
                <td class="no-sort no-click bread-actions">
                    <div class="btn-group">
                        @foreach($actions as $action)
                            @if (!method_exists($action, 'massAction'))
                                @include('voyager::bread.partials.actions', ['action' => $action])
                            @endif
                        @endforeach
                    </div>
                </td>
            </tr>

            @endforeach

        </tbody>
    </table>
</div>

<div>
    <div class="pull-left">
        <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
            'voyager::generic.showing_entries', $dataTypeContent->total(), [
                'from' => $dataTypeContent->firstItem(),
                'to' => $dataTypeContent->lastItem(),
                'all' => $dataTypeContent->total()
            ]) }}</div>
    </div>
    <div class="pull-right">
        {{ $dataTypeContent->appends([
            's' => $search->value,
            'order_by' => $orderBy,
            'sort_order' => $sortOrder,
            'status' => request()->input('status'),
            'family' => request()->input('family'),
            'images' => request()->input('images'),
            'availability_id' => request()->input('availability_id')
        ])->links() }}
    </div>
</div>