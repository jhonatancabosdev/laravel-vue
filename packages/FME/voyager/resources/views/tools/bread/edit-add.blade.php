@extends('voyager::master')

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css">
@endsection

@if (isset($dataType->id))
    @section('page_title', __('voyager::bread.edit_bread_for_table', ['table' => $dataType->name]))
    @php
        $display_name = $dataType->getTranslatedAttribute('display_name_singular');
        $display_name_plural = $dataType->getTranslatedAttribute('display_name_plural');
    @endphp
@else
    @section('page_title', __('voyager::bread.create_bread_for_table', ['table' => $table]))
@endif

@section('page_header')

<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-12">
            <div class="col-12">

                <h1 class="h2 page-title">
                    @if (isset($dataType->id))
                        {{ __('voyager::bread.edit_bread_for_table', ['table' => $dataType->name]) }}
                    @else
                        {{ __('voyager::bread.create_bread_for_table', ['table' => $table]) }}
                    @endif
                </h1>

                @php
                    $isModelTranslatable = (!isset($isModelTranslatable) || !isset($dataType)) ? false : $isModelTranslatable;
                @endphp

                @if (isset($dataType->name))
                    @php
                        $table = $dataType->name;
                    @endphp
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('content')

    <div class="page-content container-fluid" id="voyagerBreadEditAdd">

        <div class="row">
            <div class="col-12">
                <div class="col-12">

                    <form action="@if(isset($dataType->id)){{ route('voyager.bread.update', $dataType->id) }}@else{{ route('voyager.bread.store') }}@endif"
                          method="POST" role="form">
                    @if(isset($dataType->id))
                        <input type="hidden" value="{{ $dataType->id }}" name="id">
                        {{ method_field("PUT") }}
                    @endif
                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <input type="hidden" name="name" value="{{ $dataType->name ?? $table }}">

                        <div class="alert bg-light rounded-lg">

                            <div class="panel-heading">
                                <h3 class="panel-title mb-4 mt-3">
                                    Bread configuration
                                </h3>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <div class="col form-group">
                                        <label for="display_name_singular">{{ __('voyager::bread.display_name_singular') }}</label>
                                        <input type="text" class="form-control"
                                               name="display_name_singular"
                                               id="display_name_singular"
                                               placeholder="{{ __('voyager::bread.display_name_singular') }}"
                                               value="{{ $display_name }}">
                                    </div>
                                    <div class="col form-group">
                                        <label for="display_name_plural">{{ __('voyager::bread.display_name_plural') }}</label>
                                        <input type="text" class="form-control"
                                               name="display_name_plural"
                                               id="display_name_plural"
                                               placeholder="{{ __('voyager::bread.display_name_plural') }}"
                                               value="{{ $display_name_plural }}">
                                    </div>
                                    <div class="col form-group">
                                        <label for="slug">{{ __('voyager::bread.url_slug') }}</label>
                                        <input type="text" class="form-control" name="slug" placeholder="{{ __('voyager::bread.url_slug_ph') }}"
                                               value="{{ $dataType->slug ?? $slug }}">
                                    </div>
                                    <div class="col form-group">
                                        <label for="icon">{{ __('voyager::bread.icon_hint') }}</label>
                                        <input type="text" class="form-control" name="icon"
                                               placeholder="{{ __('voyager::bread.icon_class') }}"
                                               value="{{ $dataType->icon ?? '' }}">
                                    </div>
                                </div>
                            
                                <div class="row">
                                    <div class="col form-group">
                                        <label for="model_name">{{ __('voyager::bread.model_name') }}</label>
                                        <span class="voyager-question"
                                            aria-hidden="true"
                                            data-toggle="tooltip"
                                            data-placement="right"
                                            title="{{ __('voyager::bread.model_name_ph') }}"></span>
                                        <input type="text" class="form-control" name="model_name" placeholder="{{ __('voyager::bread.model_class') }}"
                                               value="{{ $dataType->model_name ?? $model_name }}">
                                    </div>

                                    <div class="col form-group">
                                        <label for="controller">{{ __('voyager::bread.controller_name') }}</label>
                                        <span class="voyager-question"
                                            aria-hidden="true"
                                            data-toggle="tooltip"
                                            data-placement="right"
                                            title="{{ __('voyager::bread.controller_name_hint') }}"></span>
                                        <input type="text" class="form-control" name="controller" placeholder="{{ __('voyager::bread.controller_name') }}"
                                               value="{{ $dataType->controller ?? '' }}">
                                    </div>

                                    @if (isset($hasNestedTrait) && $hasNestedTrait)
                                        <div class="col form-group d-flex flex-row">
                                            <div class="flex-fill mr-2">
                                                <label for="controller" class="text-danger">Nested set controller</label>
                                                <span class="voyager-question"
                                                    aria-hidden="true"
                                                    data-toggle="tooltip"
                                                    data-placement="right"
                                                    title="Controller used to order, add parent, child type of relations, This model must use 'NodeTrait' Trait"></span>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    name="nested_controller" 
                                                    placeholder="{{ __('voyager::bread.controller_name') }}"
                                                    value="{{ $dataType->nested_controller ?? config('voyager.controllers.nested') }}"
                                                >
                                            </div>
                                            <div>
                                                <label>Max depth</label>
                                                <span class="voyager-question"
                                                    aria-hidden="true"
                                                    data-toggle="tooltip"
                                                    data-placement="right"
                                                    title="Set max nested depth you need to edit, add... (0 = unlimited)"></span>
                                                <input 
                                                    type="number" 
                                                    name="nested_max_depth" 
                                                    step="1" min="0" 
                                                    class="form-control" 
                                                    style="max-width:100px;" 
                                                    value="{{ $dataType->nested_max_depth ?? 0 }}"
                                                >
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col form-group">
                                        <label for="policy_name">{{ __('voyager::bread.policy_name') }}</label>
                                        <span class="voyager-question"
                                              aria-hidden="true"
                                              data-toggle="tooltip"
                                              data-placement="right"
                                              title="{{ __('voyager::bread.policy_name_ph') }}"></span>
                                        <input type="text" class="form-control" name="policy_name" placeholder="{{ __('voyager::bread.policy_class') }}"
                                               value="{{ $dataType->policy_name ?? '' }}">
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col form-group">
                                        <label for="order_column">{{ __('voyager::bread.order_column') }}</label>
                                        <span class="voyager-question"
                                              aria-hidden="true"
                                              data-toggle="tooltip"
                                              data-placement="right"
                                              title="{{ __('voyager::bread.order_column_ph') }}"></span>
                                        <select name="order_column" class="select2 form-control">
                                            <option value="">-- {{ __('voyager::generic.none') }} --</option>
                                            @foreach($fieldOptions as $tbl)
                                            <option value="{{ $tbl['field'] }}"
                                                    @if(isset($dataType) && $dataType->order_column == $tbl['field']) selected @endif
                                            >{{ $tbl['field'] }}</option>
                                            @endforeach
                                          </select>
                                    </div>
                                    <div class="col form-group">
                                        <label for="order_display_column">{{ __('voyager::bread.order_ident_column') }}</label>
                                        <span class="voyager-question"
                                              aria-hidden="true"
                                              data-toggle="tooltip"
                                              data-placement="right"
                                              title="{{ __('voyager::bread.order_ident_column_ph') }}"></span>
                                        <select name="order_display_column" class="select2 form-control">
                                            <option value="">-- {{ __('voyager::generic.none') }} --</option>
                                            @foreach($fieldOptions as $tbl)
                                            <option value="{{ $tbl['field'] }}"
                                                    @if(isset($dataType) && $dataType->order_display_column == $tbl['field']) selected @endif
                                            >{{ $tbl['field'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col form-group">
                                        <label for="order_direction">{{ __('voyager::bread.order_direction') }}</label>
                                        <select name="order_direction" class="select2 form-control">
                                            <option value="asc" @if(isset($dataType) && $dataType->order_direction == 'asc') selected @endif>
                                                {{ __('voyager::generic.ascending') }}
                                            </option>
                                            <option value="desc" @if(isset($dataType) && $dataType->order_direction == 'desc') selected @endif>
                                                {{ __('voyager::generic.descending') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <div class="row flex-nowrap">
                                            <div class="col form-group">
                                                <label class="text-nowrap" for="generate_permissions">{{ __('voyager::bread.generate_permissions') }}</label><br>
                                                @php
                                                    $checked = (isset($dataType->generate_permissions) && $dataType->generate_permissions == 1) || (isset($generate_permissions) && $generate_permissions);
                                                @endphp
                                                <input  
                                                    type="checkbox"
                                                    name="generate_permissions"
                                                    class="toggleswitch"
                                                    data-size="sm"
                                                    data-onstyle="primary"
                                                    data-offstyle="default"
                                                    data-on="{{ __('voyager::generic.yes') }}"
                                                    data-off="{{ __('voyager::generic.no') }}"
                                                    @if($checked) checked @endif 
                                                >
                                            </div>
                                            <div class="col form-group">
                                                <label class="text-nowrap" for="server_side">{{ __('voyager::bread.server_pagination') }}</label><br>
                                                @php
                                                    $checked = (isset($dataType->server_side) && $dataType->server_side == 1) || (isset($server_side) && $server_side);
                                                @endphp
                                                <input 
                                                    type="checkbox"
                                                    name="server_side"                                                       
                                                    class="toggleswitch"   
                                                    data-size="sm"
                                                    data-onstyle="primary"
                                                    data-offstyle="default"
                                                    data-on="{{ __('voyager::generic.yes') }}"
                                                    data-off="{{ __('voyager::generic.no') }}"
                                                    @if($checked) checked @endif
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col form-group">
                                        <label for="default_search_key">{{ __('voyager::bread.default_search_key') }}</label>
                                        <span class="voyager-question"
                                              aria-hidden="true"
                                              data-toggle="tooltip"
                                              data-placement="right"
                                              title="{{ __('voyager::bread.default_search_key_ph') }}"></span>
                                        <select name="default_search_key" class="select2 form-control">
                                            <option value="">-- {{ __('voyager::generic.none') }} --</option>
                                            @foreach($fieldOptions as $tbl)
                                            <option value="{{ $tbl['field'] }}"
                                                    @if(isset($dataType) && $dataType->default_search_key == $tbl['field']) selected @endif
                                            >{{ $tbl['field'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if (isset($scopes) && isset($dataType))
                                        <div class="col form-group">
                                            <label for="scope">{{ __('voyager::bread.scope') }}</label>
                                            <select name="scope" class="select2 form-control">
                                                <option value="">-- {{ __('voyager::generic.none') }} --</option>
                                                @foreach($scopes as $scope)
                                                <option value="{{ $scope }}"
                                                        @if($dataType->scope == $scope) selected @endif
                                                >{{ $scope }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col form-group">
                                    
                                        <label for="description">{{ __('voyager::bread.description') }}</label>
                                        <textarea class="form-control"
                                                  name="description"
                                                  placeholder="{{ __('voyager::bread.description') }}"
                                        >{{ $dataType->description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div><!-- .panel-body -->
                        </div><!-- .panel -->


                        <div class="alert panel-primary mt-5 px-0">

                            <div class="panel-body">

                                <div class="d-flex flex-row flex-nowrap fake-table-hd border border-default rounded-top border-bottom-0 bg-default">
                                    <div class="col-3 px-3 font-weight-bold text-white bg-default border-default">{{ __('voyager::database.field') }}</div>
                                    <div class="col-2 px-2 font-weight-bold text-white bg-default border-default">
                                        <label class="mb-0 ml-1">
                                            <input type="checkbox" name="toggle-all" class="jq-toggle-checkboxes">
                                            {{ __('voyager::database.visibility') }}
                                        </label>
                                    </div>
                                    <div class="col-1 px-3 font-weight-bold text-white bg-default border-default">{{ __('voyager::database.input_type') }}</div>
                                    <div class="col-2 px-3 font-weight-bold text-white bg-default border-default">{{ __('voyager::bread.display_name') }}</div>
                                    <div class="col-4 px-3 font-weight-bold text-white bg-default border-default">{{ __('voyager::database.optional_details') }}</div>
                                </div>

                                <div class="border rounded-bottom mb-4" id="bread-items">
                                    @php
                                        $r_order = 0;
                                    @endphp
                                    @foreach($fieldOptions as $data)
                                        @php
                                            $r_order += 1;
                                        @endphp

                                        @if(isset($dataType->id))
                                            <?php $dataRow = Voyager::model('DataRow')->where('data_type_id', '=', $dataType->id)->where('field', '=', $data['field'])->first(); ?>
                                        @endif

                                        <div class="row row-dd">
                                            <div class="col">
                                                <h4 class="mb-1"><strong>{{ $data['field'] }}</strong></h4>
                                                <small>
                                                    <strong>{{ __('voyager::database.type') }}:</strong> <span>{{ $data['type'] }}</span><br/>
                                                    <strong>{{ __('voyager::database.key') }}:</strong> <span>{{ $data['key'] }}</span><br/>
                                                    <strong>{{ __('voyager::generic.required') }}:</strong>
                                                    @if($data['null'] == "NO")
                                                        <span>{{ __('voyager::generic.yes') }}</span>
                                                        <input type="hidden" value="1" name="field_required_{{ $data['field'] }}" checked="checked">
                                                    @else
                                                        <span>{{ __('voyager::generic.no') }}</span>
                                                        <input type="hidden" value="0" name="field_required_{{ $data['field'] }}">
                                                    @endif
                                                    <div class="handler voyager-handle"></div>
                                                    <input class="row_order" type="hidden" value="{{ $dataRow->order ?? $r_order }}" name="field_order_{{ $data['field'] }}">
                                                </small>
                                            </div>
                                            <div class="col-2">
                                                <div class="d-flex flex-row">
                                                    <div class="d-flex flex-column justify-content-start mr-3">

                                                        <label class="mb-0">
                                                            <input 
                                                                type="checkbox"
                                                                class="jq-toggle"
                                                                id="field_browse_{{ $data['field'] }}"
                                                                name="field_browse_{{ $data['field'] }}"
                                                                @if(isset($dataRow->browse) && $dataRow->browse)
                                                                    checked="checked"
                                                                @elseif($data['key'] == 'PRI')
                                                                @elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')
                                                                @elseif(!isset($dataRow->browse))
                                                                    checked="checked"
                                                                @endif>
                                                            {{ __('voyager::generic.browse') }}
                                                        </label>

                                                        <label class="mb-0">
                                                            <input 
                                                                type="checkbox"
                                                                class="jq-toggle"
                                                                id="field_read_{{ $data['field'] }}"
                                                                name="field_read_{{ $data['field'] }}"
                                                                @if(isset($dataRow->read) && $dataRow->read)
                                                                    checked="checked" 
                                                                @elseif( $data['key'] == 'PRI' )
                                                                @elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')
                                                                @elseif(!isset($dataRow->read)) 
                                                                    checked="checked" 
                                                                @endif
                                                            >
                                                            {{ __('voyager::generic.read') }}
                                                        </label>

                                                    </div>
                                                    <div class="d-flex flex-column justify-content-start">
                                                        
                                                        <label class="mb-0">
                                                            <input 
                                                                type="checkbox"
                                                                class="jq-toggle"
                                                                id="field_edit_{{ $data['field'] }}"
                                                                name="field_edit_{{ $data['field'] }}" 
                                                                @if(isset($dataRow->edit) && $dataRow->edit) 
                                                                    checked="checked" 
                                                                @elseif($data['key'] == 'PRI')
                                                                @elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')
                                                                @elseif(!isset($dataRow->edit)) 
                                                                    checked="checked" 
                                                                @endif
                                                            >
                                                            {{ __('voyager::generic.edit') }}
                                                        </label>

                                                        <label class="mb-0">
                                                            <input 
                                                                type="checkbox"
                                                                class="jq-toggle"
                                                                id="field_add_{{ $data['field'] }}"
                                                                name="field_add_{{ $data['field'] }}" 
                                                                @if(isset($dataRow->add) && $dataRow->add) 
                                                                    checked="checked" 
                                                                @elseif($data['key'] == 'PRI')
                                                                @elseif($data['type'] == 'timestamp' && $data['field'] == 'created_at')
                                                                @elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')
                                                                @elseif(!isset($dataRow->add)) 
                                                                    checked="checked" 
                                                                @endif
                                                            >
                                                            {{ __('voyager::generic.add') }}
                                                        </label>

                                                        <label class="d-none" for="field_delete_{{ $data['field'] }}">
                                                            <input 
                                                                type="checkbox"
                                                                class="jq-toggle"
                                                                id="field_delete_{{ $data['field'] }}"
                                                                name="field_delete_{{ $data['field'] }}" 
                                                                checked="checked"
                                                            >
                                                            {{ __('voyager::generic.delete') }}
                                                        </label>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <input type="hidden" name="field_{{ $data['field'] }}" value="{{ $data['field'] }}">
                                                @if($data['type'] == 'timestamp')
                                                    <p>{{ __('voyager::generic.timestamp') }}</p>
                                                    <input type="hidden" value="timestamp"
                                                           name="field_input_type_{{ $data['field'] }}">
                                                @else
                                                    <select class="form-control" name="field_input_type_{{ $data['field'] }}">
                                                        @foreach (Voyager::formFields() as $formField)
                                                            @php
                                                            $selected = (isset($dataRow->type) && $formField->getCodename() == $dataRow->type) || (!isset($dataRow->type) && $formField->getCodename() == 'text');
                                                            @endphp
                                                            <option value="{{ $formField->getCodename() }}" {{ $selected ? 'selected' : '' }}>
                                                                {{ $formField->getName() }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                            <div class="col-2">
                                                <input 
                                                    type="text" 
                                                    class="form-control mb-3"
                                                    value="{{ $dataRow->display_name ?? ucwords(str_replace('_', ' ', $data['field'])) }}"
                                                    name="field_display_name_{{ $data['field'] }}"
                                                >
                                            </div>
                                            <div class="col-4">
                                                <div class="alert alert-danger validation-error">
                                                    {{ __('voyager::json.invalid') }}
                                                </div>
                                                <textarea id="json-input-{{ json_encode($data['field']) }}"
                                                          class="resizable-editor"
                                                          data-editor="json"
                                                          name="field_details_{{ $data['field'] }}">
                                                    {{ json_encode(isset($dataRow->details) ? $dataRow->details : new class{}) }}
                                                </textarea>
                                            </div>
                                        </div>

                                    @endforeach

                                    @if(isset($dataTypeRelationships))
                                        @foreach($dataTypeRelationships as $relationship)
                                            @include('voyager::tools.bread.relationship-partial', $relationship)
                                        @endforeach
                                    @endif

                                </div>

                            </div><!-- .panel-body -->
                            <div class="panel-footer text-right">

                                <div class="btn-group">
                                     <button type="button" class="btn btn-pink btn-new-relationship">
                                        <span>{{ __('voyager::database.relationship.create') }}</span>
                                    </button>
                                     <button type="submit" class="btn btn-default">{{ __('voyager::generic.submit') }}</button>
                                </div>


                            </div>
                        </div><!-- .panel -->

                    </form>
                </div><!-- .col-md-12 -->
            </div>
        </div>

    </div><!-- .page-content -->

    @include('voyager::tools.bread.relationship-new-modal')

@endsection

@push('javascript')

    <script>
        window.invalidEditors = [];
        var validationAlerts = $('.validation-error');
        validationAlerts.hide();
        $(function () {

            $('body').on('click', '.jq-toggle-checkboxes', function(event) {
                $('input.jq-toggle').prop('checked', $(this).prop('checked'))
            });

            /**
             * Reorder items
             */
            reOrderItems();

            $('#bread-items').disableSelection();

            $('[data-toggle="tooltip"]').tooltip();

            $('.toggleswitch').bootstrapToggle();

            $('textarea[data-editor]').each(function () {
                var textarea = $(this),
                mode = textarea.data('editor'),
                editDiv = $('<div>').insertBefore(textarea),
                editor = ace.edit(editDiv[0]),
                _session = editor.getSession(),
                valid = false;
                textarea.hide();

                // Validate JSON
                _session.on("changeAnnotation", function(){
                    valid = _session.getAnnotations().length ? false : true;
                    if (!valid) {
                        if (window.invalidEditors.indexOf(textarea.attr('id')) < 0) {
                            window.invalidEditors.push(textarea.attr('id'));
                        }
                    } else {
                        for(var i = window.invalidEditors.length - 1; i >= 0; i--) {
                            if(window.invalidEditors[i] == textarea.attr('id')) {
                               window.invalidEditors.splice(i, 1);
                            }
                        }
                    }
                });

                // Use workers only when needed
                editor.on('focus', function () {
                    _session.setUseWorker(true);
                });
                editor.on('blur', function () {
                    if (valid) {
                        textarea.siblings('.validation-error').hide();
                        _session.setUseWorker(false);
                    } else {
                        textarea.siblings('.validation-error').show();
                    }
                });

                _session.setUseWorker(false);

                editor.setAutoScrollEditorIntoView(true);
                editor.$blockScrolling = Infinity;
                editor.setOption("maxLines", 30);
                editor.setOption("minLines", 4);
                editor.setOption("showLineNumbers", false);
                editor.setTheme("ace/theme/github");
                _session.setMode("ace/mode/json");
                if (textarea.val()) {
                    _session.setValue(JSON.stringify(JSON.parse(textarea.val()), null, 4));
                }

                _session.setMode("ace/mode/" + mode);

                // copy back to textarea on form submit...
                textarea.closest('form').on('submit', function (ev) {
                    if (window.invalidEditors.length) {
                        ev.preventDefault();
                        ev.stopPropagation();
                        validationAlerts.hide();
                        for (var i = window.invalidEditors.length - 1; i >= 0; i--) {
                            $('#'+window.invalidEditors[i]).siblings('.validation-error').show();
                        }
                        toastr.error('{{ __('voyager::json.invalid_message') }}', '{{ __('voyager::json.validation_errors') }}', {"preventDuplicates": true, "preventOpenDuplicates": true});
                    } else {
                        if (_session.getValue()) {
                            // uglify JSON object and update textarea for submit purposes
                            textarea.val(JSON.stringify(JSON.parse(_session.getValue())));
                        }else{
                            textarea.val('');
                        }
                    }
                });
            });

        });

        function reOrderItems(){
            $('#bread-items').sortable({
                handle: '.handler',
                update: function (e, ui) {
                    var _rows = $('#bread-items').find('.row_order'),
                        _size = _rows.length;

                    for (var i = 0; i < _size; i++) {
                        $(_rows[i]).val(i+1);
                    }
                },
                create: function (event, ui) {
                    sort();
                }
            });
        }

        function sort() {
            var sortableList = $('#bread-items');
            var listitems = $('div.row.row-dd', sortableList);

            listitems.sort(function (a, b) {
                return (parseInt($(a).find('.row_order').val()) > parseInt($(b).find('.row_order').val()))  ? 1 : -1;
            });
            sortableList.append(listitems);
        }

        /********** Relationship functionality **********/

       $(function () {
            $('.relationship_type').change(function(){
                if($(this).val() == 'belongsTo'){
                    $(this).parent().parent().find('.relationshipField').show();
                    $(this).parent().parent().find('.relationshipPivot').hide();
                    $(this).parent().parent().find('.relationship_taggable').hide();
                    $(this).parent().parent().find('.hasOneMany').removeClass('flexed');
                    $(this).parent().parent().find('.belongsTo').addClass('flexed');
                } else if($(this).val() == 'hasOne' || $(this).val() == 'hasMany'){
                    $(this).parent().parent().find('.relationshipField').show();
                    $(this).parent().parent().find('.relationshipPivot').hide();
                    $(this).parent().parent().find('.relationship_taggable').hide();
                    $(this).parent().parent().find('.hasOneMany').addClass('flexed');
                    $(this).parent().parent().find('.belongsTo').removeClass('flexed');
                } else {
                    $(this).parent().parent().find('.relationshipField').hide();
                    $(this).parent().parent().find('.relationshipPivot').css('display', 'flex');
                    $(this).parent().parent().find('.relationship_taggable').show();
                }
            });

            $('.btn-new-relationship').click(function(){
                // Update table data
                $('#new_relationship_modal .relationship_table').trigger('change');

                $('#new_relationship_modal').modal('show');
            });

            relationshipTextDataBinding();

            $('.relationship_table').on('change', function(){
                populateRowsFromTable($(this));
            });

            $('.voyager-relationship-details-btn').click(function(){
                $(this).toggleClass('open');
                if($(this).hasClass('open')){
                    $(this).parent().parent().find('.voyager-relationship-details').slideDown();
                    populateRowsFromTable($(this).parent().parent().find('select.relationship_table'));
                } else {
                    $(this).parent().parent().find('.voyager-relationship-details').slideUp();
                }
            });

        });

        function populateRowsFromTable(dropdown){
            var tbl = dropdown.val();

            $.get('{{ route('voyager.database.index') }}/' + tbl, function(data){
                var tbl_selected = $(dropdown).val();

                $(dropdown).parent().parent().find('.rowDrop').each(function(){
                    var selected_value = $(this).data('selected');

                    var options = $.map(data, function (obj, key) {
                        obj.id = key;
                        obj.text = key; 

                        return obj;
                    });

                    $(this).empty().select2({
                        data: options
                    });

                    if (selected_value == '' || !$(this).find("option[value='"+selected_value+"']").length) {
                        selected_value = $(this).find("option:first-child").val();
                    }

                    $(this).val(selected_value).trigger('change');
                });
            });
        }

        function relationshipTextDataBinding(){
            $('.relationship_display_name').bind('input', function() {
                $(this).parent().parent().parent().find('.label_relationship p').text($(this).val());
            });
            $('.relationship_table').on('change', function(){
                var tbl_selected_text = $(this).find('option:selected').text();
                $(this).parent().parent().find('.label_table_name').text(tbl_selected_text);
            });
            $('.relationship_table').each(function(){
                var tbl_selected_text = $(this).find('option:selected').text();
                $(this).parent().parent().find('.label_table_name').text(tbl_selected_text);
            });
        }

        /********** End Relationship Functionality **********/
    </script>
@endpush
