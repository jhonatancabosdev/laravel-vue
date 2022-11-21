@extends('voyager::master')

@section('page_title', $dataType->getTranslatedAttribute('display_name_plural') . ' ' . __('voyager::bread.order'))

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <h1 class="page-title">
                        <i class="h3 voyager-list"></i>
                        {{ $dataType->getTranslatedAttribute('display_name_plural') }} {{ __('voyager::bread.order') }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="page-content container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="col-12">
                <div class="panel-heading">
                    <p class="panel-title text-muted">{{ __('voyager::generic.drag_drop_info') }}</p>
                </div>
                <div class="panel-body">
                    <div class="dd">
                        <ol class="dd-list">
                            @foreach ($results as $result)
                            <li class="dd-item" data-id="{{ $result->getKey() }}">
                                <div class="dd-handle" style="height:inherit">
                                    @if (isset($dataRow->details->view))
                                        @include($dataRow->details->view, ['row' => $dataRow, 'dataType' => $dataType, 'dataTypeContent' => $result, 'content' => $result->{$display_column}, 'action' => 'order'])
                                    @elseif($dataRow->type == 'image')
                                        <span>
                                            <img src="@if( !filter_var($result->{$display_column}, FILTER_VALIDATE_URL)){{ Voyager::image( $result->{$display_column} ) }}@else{{ $result->{$display_column} }}@endif" style="height:100px">
                                        </span>
                                    @else
                                        <span>{{ $result->{$display_column} }}</span>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')

<script>
$(document).ready(function () {
    $('.dd').nestable({
        maxDepth: 1
    });

    /**
    * Reorder items
    */
    $('.dd').on('change', function (e) {
        $.post('{{ route('voyager.'.$dataType->slug.'.order') }}', {
            order: JSON.stringify($('.dd').nestable('serialize')),
            _token: '{{ csrf_token() }}'
        }, function (data) {
            toastr.success("{{ __('voyager::bread.updated_order') }}");
        });
    });
});
</script>
@stop
