@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.__('voyager::generic.bread'))

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <h1 class="page-title">
                        <i class="voyager-bread"></i> {{ __('voyager::generic.bread') }}
                    </h1>
                </div>
            </div>
        </div>
    </div>

@stop

@section('content')

    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <table class="table table-sm table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>{{ __('voyager::database.table_name') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        @foreach($tables as $table)
                            @continue(in_array($table->name, config('voyager.database.tables.hidden', [])))
                            <tr>
                                <td>
                                    <i class="fas fa-database"></i>&nbsp;{{ $table->name }}
                                </td>

                                <td class="actions text-right">
                                    @if($table->dataTypeId)
                                        <div class="btn-group min-w-110px shadow">
                                            <a href="{{ route('voyager.' . $table->slug . '.index') }}"
                                               class="btn btn-default btn-sm browse_bread" style="margin-right: 0;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('voyager.bread.edit', $table->name) }}"
                                               class="btn btn-default btn-sm edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#delete-bread" data-id="{{ $table->dataTypeId }}" data-name="{{ $table->name }}"
                                                 class="btn btn-pink btn-sm delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ route('voyager.bread.create', $table->name) }}"
                                           class="btn btn-light btn-sm min-w-110px shadow">
                                            <i class="fas fa-plus-circle"></i>&nbsp;Create Bread
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- Delete BREAD Modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_builder_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">
                        <i class="fas fa-trash-alt"></i>&nbsp; {!! __('voyager::bread.delete_bread_quest', ['table' => '<span id="delete_builder_name"></span>']) !!}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer border-0">
                    <form action="#" id="delete_builder_form" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="btn-group">
                            <input type="submit" class="btn btn-danger" value="{{ __('voyager::bread.delete_bread_conf') }}">
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal modal-info fade" tabindex="-1" id="table_info" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-data"></i> @{{ table.name }}</h4>
                </div>
                <div class="modal-body" style="overflow:scroll">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ __('voyager::database.field') }}</th>
                            <th>{{ __('voyager::database.type') }}</th>
                            <th>{{ __('voyager::database.null') }}</th>
                            <th>{{ __('voyager::database.key') }}</th>
                            <th>{{ __('voyager::database.default') }}</th>
                            <th>{{ __('voyager::database.extra') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="row in table.rows">
                            <td><strong>@{{ row.Field }}</strong></td>
                            <td>@{{ row.Type }}</td>
                            <td>@{{ row.Null }}</td>
                            <td>@{{ row.Key }}</td>
                            <td>@{{ row.Default }}</td>
                            <td>@{{ row.Extra }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">{{ __('voyager::generic.close') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@stop

@section('javascript')

    <script>

        var table = {
            name: '',
            rows: []
        };

        new Vue({
            el: '#table_info',
            data: {
                table: table,
            },
        });

        $(function () {

            // Setup Delete BREAD
            //
            $('table .actions').on('click', '.delete', function (e) {
                id = $(this).data('id');
                name = $(this).data('name');

                $('#delete_builder_name').text(name);
                $('#delete_builder_form')[0].action = '{{ route('voyager.bread.delete', ['__id']) }}'.replace('__id', id);
                $('#delete_builder_modal').modal('show');
            });

            // Setup Show Table Info
            //
            $('.database-tables').on('click', '.desctable', function (e) {
                e.preventDefault();
                href = $(this).attr('href');
                table.name = $(this).data('name');
                table.rows = [];
                $.get(href, function (data) {
                    $.each(data, function (key, val) {
                        table.rows.push({
                            Field: val.field,
                            Type: val.type,
                            Null: val.null,
                            Key: val.key,
                            Default: val.default,
                            Extra: val.extra
                        });
                        $('#table_info').modal('show');
                    });
                });
            });
        });
    </script>

@stop
