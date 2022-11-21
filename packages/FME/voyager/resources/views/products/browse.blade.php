@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-12">

                    <h1 class="page-title">
                        <i class="h3 mb-0 {{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
                    </h1>

                    <div class="btn-group rounded-lg align-items-center">

                        @can('add', app($dataType->model_name))
                            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-default btn-add-new btn-sm">
                                <i class="fas fa-plus-circle"></i>&nbsp;{{ __('voyager::generic.add_new') }}
                            </a>
                        @endcan
                        
                        @can('delete', app($dataType->model_name))
                            @include('voyager::partials.bulk-delete')
                        @endcan

                        @can('edit', app($dataType->model_name))

                            @if (! is_null($dataType->nested_controller))
                                <a href="{{ route('voyager.'.$dataType->slug.'.nested.index') }}" class="btn btn-default btn-add-new btn-sm">
                                    <i class="voyager-list"></i> <span>Nested</span>
                                </a>
                            @endif
                            
                        @endcan

                        <a class="btn btn-default btn-sm" href="{{ route('voyager.products.export') }}" target="_blank">
                            <i class="fas fa-file-csv"></i> Exports
                        </a>

                        @foreach($actions as $action)
                            @if (method_exists($action, 'massAction'))
                                @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
                            @endif
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')

    <div id="products-table" class="container-fluid pb-5">
        <div class="row">
            <div class="col-12">
                <div class="col-12 page-content browse">

                    <div class="row">
                        <div class="col-12">
                            @include('voyager::alerts')
                        </div>
                    </div>

                    <products-table 
                        class="pb-3" 
                        :start-page="{{ request()->input('page', 1) <= 1 ? (int) session('admin_page', 1) : request()->input('page', 1) }}"
                        :brands="{{ json_encode($brands) }}"
                        store-brand-route="{{ route('voyager.brands.store.attach', ['__id']) }}"
                        destroy-product-route="{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}"
                        data-route="{{ route('voyager.products.index') }}"
                        toggle-status-route="{{ route('voyager.products.toggle-column', ['__id', 'status']) }}"
                        toggle-availability-route="{{ route('voyager.products.toggle-column', ['__id', 'availability_id']) }}"
                        update-column-route="{{ route('voyager.products.toggle-column', ['__id', '__column']) }}"
                        view-route="{{ route('product.show', [
                            'product' => '__id'
                        ]) }}"
                        edit-route="{{ route('voyager.products.edit', [
                            'id' => '__id'
                        ]) }}"
                    >
                    </products-table>

                </div>      
            </div>
        </div>

        <product-categories-modal 
            save-categories-route="{{ route('voyager.products.categories') }}"
            :flat-categories="{{ json_encode($categories) }}" 
            :nested-categories="{{ json_encode($categories->toTree()) }}"
        >    
        </product-categories-modal>

    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">{{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }} ?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-footer border-0">
                    <form action="#" id="delete_form" method="POST">
                        @method('DELETE')
                        @csrf
                        <div class="btn-group">
                            <button type="submit" class="btn btn-danger pull-right delete-confirm">{{ __('voyager::generic.delete_confirm') }}</button>
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Modal -->
    <div class="modal fade" id="price-mass-update-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title">Price updater</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="d-flex flex-column">
                        <div class="d-flex flex-column">
                            <div class="jq-markup-container w-100" style="display: none;">
                                <div class="mb-3">
                                    <label>Markup</label>
                                    <input class="form-control" name="markup" type="number" min="0" step="0.01" placeholder="1.15">
                                </div>
                                <div class="alert alert-info">
                                    Example: set markup to <code>(1.15)</code> <small class="text-muted text-nowrap">(price * 1.15) = 15% price increase</small>
                                </div>
                            </div>

                            <div class="jq-amount-container w-100">
                                <div class="mb-3">
                                    <label>Amount</label>
                                    <input class="form-control" name="amount" type="number" min="0" step="0.01">
                                </div>
                                <div class="alert alert-info">
                                    Example: set amount to <code>$5</code> <small class="text-muted text-nowrap">(price + $5) = $5 price increase</small>
                                </div>
                            </div>
                            <input type="hidden" name="column" value="price">
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0 d-flex flex-row align-items-center justify-content-around">
                    <input 
                        type="checkbox" 
                        id="price-update-type" 
                        data-width="120"
                        data-height="33.25"
                        data-toggle="toggle" 
                        data-size="sm"
                        data-onstyle="default"
                        data-offstyle="pink"
                        data-on="Markup" 
                        data-off="Amount"
                    >
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-pink jq-modal-save">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="availability-mass-update-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title">Availability update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="d-flex flex-column">
                        <select name="availability_id" class="form-control">
                            @foreach ($availabilities as $availability)
                                <option value="{{ $availability->id }}">{{ $availability->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="column" value="availability_id">
                    </form>
                </div>
                <div class="modal-footer border-top-0">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-pink jq-modal-save">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="selected_ids[]" class="selected_ids">

@endsection

@section('javascript')
    
    <script>

        var table = new Vue({
            el: '#products-table'
        });

        $(document).ready(function () {

            if ($('.jq-money-formatter').length > 0) {
                $.each($('.jq-money-formatter'), function(index, val) {
                    try {
                        var e = $(val).text()
                        $(val).text('$' + formatMoney(e))
                    } catch (e) {

                    }
                });
            }
            
            $('body').on('change', '#price-update-type', function(event) {
                if ($(this).prop('checked') === true) {
                    // markup
                    $('.jq-amount-container').slideUp(300)
                    $('.jq-markup-container').slideDown(300);
                } else {
                    // amount
                    $('.jq-markup-container').slideUp(300)
                    $('.jq-amount-container').slideDown(300)
                }
            });

            $('body').on('click', '.jq-modal-save', function(event) {
                if ($('.selected_ids').val().length === 0) {
                    toastr.error('No rows selected');
                    return false;
                }

                let form = $(this).parents('.modal').find('form')

                let markup = $('#price-update-type').prop('checked') ? 'markup' : 'amount';

                $.ajax({
                    url: @json(route('voyager.bread.ajax.update')),
                    type: 'POST',
                    data: form.serialize() + '&markup_type=' + markup + '&ids=' + $('.selected_ids').val(),
                })
                .done(function() {
                    toastr.success('Data saved successfully')
                })
                .fail(function(response) {
                    toastr.error(response.responseJSON.message)
                })
                .always(function() {
                    
                });
                
            });

            $('body').on('click', '.select_all', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });

            var deleteFormAction;

            $('body').on('click', '.delete', function (e) {
                $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', $(this).data('id'));
                $('#delete_modal').modal('show');
            });

            $('body').on('change', 'input[name="row_id"]', function () {
                var ids = [];

                $('input[name="row_id"]').each(function() {
                    if ($(this).is(':checked')) {
                        ids.push($(this).val());
                    }
                });

                $('.selected_ids').val(ids);

                $('.jq-selected-items-count').text(ids.length)
            });

        });

    </script>
@stop
