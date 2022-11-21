@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' nested '.strtolower($dataType->display_name_plural))

@section('page_header')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-12">

                    <h1 class="page-title">
                        <i class="h3 mb-0 {{ $dataType->icon }}"></i>&nbsp;Nested {{ $dataType->display_name_plural }}
                    </h1>

                    <div class="btn-group my-3">
                        <button class="btn btn-default btn-sm jq-add-new-root-item">Add top level {{ $dataType->display_name_singular }}</button>
                        <button class="btn btn-default btn-sm jq-expend-all">Expand All</button>
                        <button class="btn btn-default btn-sm jq-collapse-all">Collapse All</button>
                    </div>

                    @if (! request()->has('fix-tree'))
                        <div class="alert alert-info mt-3">
                            If {{ strtolower($dataType->display_name_plural) }} are not updated, please click <code><a href="?fix-tree">here</a></code> to fix.
                        </div>
                    @endif

                    @if (config('voyager.show_dev_tips'))
                        <div class="alert alert-info mt-3">
                            
                            <p class="mb-3">In order to have <code>{{ $dataType->model_name }}</code> ordered, make sure to add this global scope to you model.</p>

                            <div class="bg-dark p-4 rounded-lg d-flex flex-column">
                                <code class="code">
                                    /**<br>
                                     * The "booting" method of the model.<br>
                                     *<br>
                                     * @return void<br>
                                     */<br>
                                    protected static function boot()<br>
                                    {<br>
                                        &nbsp; &nbsp; parent::boot();<br>
                                        <br>
                                        &nbsp; &nbsp; static::addGlobalScope(<strong class="text-white">orderScope</strong>, function($query) { <br>
                                        &nbsp; &nbsp; &nbsp; &nbsp; return $query->orderBy(<kbd>'{{ $dataType->details->order_column ?? 'your_order_column' }}'</kbd>); <br>
                                        &nbsp; &nbsp; });<br>
                                    }
                                </code>
                            </div>
                        </div>
                    @endif

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

                    @if (! $errors->isEmpty())
                        @foreach($errors->all() as $message)
                            <div class="alert alert-danger alert-dismissible mb-3">
                                {{ $message }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        @endforeach
                    @endif

                    @if (session()->has('message'))
                        <div class="alert alert-dismissible my-4 {{ session('alert-type') === 'success' ? 'alert-success' : 'alert-danger' }}">
                            {{ session('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    @endif

                    <div class="panel-heading">
                        <p class="panel-title text-muted">Drag and drop the {{ $dataType->display_name_singular }} Items below to re-arrange them.</p>
                    </div>
                    <div class="panel-body">
                        <div class="dd">
                            @include('voyager::menu.nested-bread', [
                                'dataType' => $dataType,
                                'imageField' => $dataType->rows()->where('type', 'image')->first(),
                                'items' => $items,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Are you sure you want to delete this item ?</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer border-0">
                    <form
                        action="{{ route('voyager.'.$dataType->slug.'.nested.destroy', ['slug' => $dataType->slug, 'id' => '__id']) }}"
                        id="delete_form"
                        method="POST"
                    >
                        @method('DELETE')
                        @csrf
                        <div class="btn-group">
                            <input 
                                type="submit" 
                                class="btn btn-danger pull-right delete-confirm" 
                                value="Delete">
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal modal-info fade" tabindex="-1" id="item_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 mb-0 pb-0">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <form action="" method="POST">

                    @method('PUT')
                    @csrf

                    <div class="modal-body">
                        <div class="row">
                            @foreach ($dataType->requiredRows()->get() as $row)
                                <div class="{{ in_array($row->type, ['checkbox', 'radio_btn']) ? 'col-3' : 'col-12' }}">
                                    <div class="form-group jq-form-element">
                                        <label class="control-label mb-0">{{ $row->display_name }}</label>
                                        {!! app('voyager')->formField($row, $dataType) !!}
                                    </div>
                                </div>
                            @endforeach

                            <div class="jq-form-element">
                                <input type="hidden" name="id" value="">
                            </div>

                            <div class="jq-form-element">
                                <input type="hidden" name="parent_id" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-pink jq-save-modal">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@stop

@section('javascript')

    <script>
        $(document).ready(function () {

            $('.dd').nestable({
                scroll: true,
                expandBtnHTML: '',
                collapseBtnHTML: '',
                callback: function(l, e) {

                    $.ajax({
                        url: '{{ route('voyager.'.$dataType->slug.'.nested.order') }}',
                        type: 'PUT',
                        data: {
                            order: JSON.stringify($('.dd').nestable('serialize')),
                            _token: '{{ csrf_token() }}',
                            root: @json(isset($root) ? $root : '')
                        },
                    })
                    .done(function(response) {

                    })
                    .fail(function(response) {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                    });
                }
            });
 
            // add root item
            $('.jq-add-new-root-item').on('click', function(event) {
                event.preventDefault();
                $('#item_modal').data('item', '')
                $('#item_modal').find('[name="id"]').val('')
                $('#item_modal').find('[name="parent_id"]').val('')
                $('#item_modal').modal('show')

                $('#item_modal').find('form').attr('action', @json(
                    route('voyager.'.$dataType->slug.'.nested.store'))
                )
            });

            $('.jq-add-child').click(function() {
                event.preventDefault();
                $('#item_modal').data('item', '')
                $('#item_modal').find('[name="id"]').val('')
                $('#item_modal').find('[name="parent_id"]').val($(this).attr('data-id'))
                $('#item_modal').modal('show')

                $('#item_modal').find('form').attr('action', @json(
                    route('voyager.'.$dataType->slug.'.nested.store'))
                )
            });

            // Edit item
            $('body').on('click', '.jq-edit', function (e) {
                $('#item_modal').data('item', $(this))
                $('#item_modal').modal('show');
                $('#item_modal').find('form').attr('action', @json(
                    route('voyager.'.$dataType->slug.'.nested.update'))
                )
            });

            $('#item_modal').on('hide.bs.modal', function(e) {
                $('#item_modal').find('[name="id"]').val('')
                $('#item_modal').find('[name="parent_id"]').val('')
                $('#item_modal').data('item', '')
            })

            $('#item_modal').on('show.bs.modal', function(e) {

                $('#item_modal').find('[name="id"]').val('')

                try {
                    let btn = $(this).data('item');
                    var data = btn.parents('.jq-item-data').attr('data-item')
                    var item = $.parseJSON(data);

                    $.each($('#item_modal').find('.jq-form-element').find('input'), function(i, e){
                        try {
                            var type = $(e).attr('type');
                            var val = item[$(e).attr('name')]
                            if ( $.inArray(type, ['checkbox', 'radio']) !== -1 ) {
                                $(e).prop('checked', val == 1)                        
                            } else {
                                $(e).val(val);
                            }
                        } catch (e) {

                        }
                    })
                } catch (e) {

                }
            })

            /**
             * Delete menu item
             */
            $('.item_actions').on('click', '.jq-delete', function (e) {
                id = $(e.currentTarget).data('id');
                $('#delete_form')[0].action = '{!! route('voyager.'.$dataType->slug.'.nested.destroy', ['slug' => htmlentities($dataType->slug), 'id' => '']) !!}' + id;
                $('#delete_modal').modal('show');
            });

            $('[data-toggle="popover"]').popover()
        });
    </script>
@stop
