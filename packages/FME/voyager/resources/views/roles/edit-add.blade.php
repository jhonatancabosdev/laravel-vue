@extends('voyager::master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-12">
                    <h1 class="page-title">
                        <i class="h3 mb-0 {{ $dataType->icon }}"></i>
                        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="page-content edit-add container-fluid pb-5">
    <div class="row">
        <div class="col-12">
            <div class="col-12">
                
                @include('voyager::alerts')

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form class="form-edit-add" role="form"
                          action="@if(isset($dataTypeContent->id)){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->id) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
                          method="POST" enctype="multipart/form-data">

                        <!-- PUT Method if we are editing -->
                        @if(isset($dataTypeContent->id))
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="d-flex flex-row flex-nowrap p-4 bg-light rounded-lg mb-3">
                                @foreach($dataType->addRows as $row)
                                    <div class="form-group d-flex flex-column w-100 mr-4">
                                        <label for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                        {!! Voyager::formField($row, $dataType, $dataTypeContent) !!}
                                    </div>
                                @endforeach
                            </div>


                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <label for="permission" class="h3 mb-0 mr-4">{{ __('voyager::generic.permissions') }}</label>

                                <div class="btn-group">
                                    <a href="#" class="btn btn-pink btn-sm permission-select-all">{{ __('voyager::generic.select_all') }}</a>
                                    <a href="#" class="btn btn-dark btn-sm permission-deselect-all">{{ __('voyager::generic.deselect_all') }}</a>
                                </div>
                            </div>

                            <div class="mt-2">
                                <ul class="mx-0 px-0 permissions checkbox d-flex flex-row align-items-start justify-content-around flex-wrap">

                                    @php
                                        $role_permissions = (isset($dataTypeContent)) ? $dataTypeContent->permissions->pluck('key')->toArray() : [];
                                    @endphp

                                    @foreach(Voyager::model('Permission')->all()->groupBy('table_name') as $table => $permission)

                                        @continue(trim($table) === '')

                                        <li class="bg-light rounded-lg p-3 d-flex flex-column">
                                            <span class="d-flex flex-nowrap text-nowrap align-items-center justify-content-start mb-2">
                                                <input type="checkbox" id="{{$table}}" class="permission-group mb-0"> &nbsp;
                                                <label class="text-nowrap h5 mb-0" for="{{$table}}"><strong>{{\Illuminate\Support\Str::title(str_replace('_',' ', $table))}}</strong></label>
                                            </span>
                                            <ul class="pl-4">
                                                @foreach($permission as $perm)
                                                    <li>
                                                        <input type="checkbox" id="permission-{{$perm->id}}" name="permissions[{{$perm->id}}]" class="the-permission" value="{{$perm->id}}" @if(in_array($perm->key, $role_permissions)) checked @endif>
                                                        <label for="permission-{{$perm->id}}">{{\Illuminate\Support\Str::title(str_replace('_', ' ', $perm->key))}}</label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>


                            <div class="d-flex w-100 flex-row align-content-end">
                                <button type="submit" class="btn btn-pink px-5 mr-0 ml-auto">Save</button>
                            </div>
                        </div>
                        
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                          enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        {{ csrf_field() }}
                        <input name="image" id="upload_file" type="file"
                               onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')
    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            $('.permission-group').on('change', function(){
                $(this).parent().siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
            });

            $('.permission-select-all').on('click', function(){
                $('ul.permissions').find("input[type='checkbox']").prop('checked', true);
                return false;
            });

            $('.permission-deselect-all').on('click', function(){
                $('ul.permissions').find("input[type='checkbox']").prop('checked', false);
                return false;
            });

            function parentChecked(){
                $('.permission-group').each(function(){
                    var allChecked = true;
                    $(this).parent().siblings('ul').find("input[type='checkbox']").each(function(){
                        if(!this.checked) allChecked = false;
                    });
                    $(this).prop('checked', allChecked);
                });
            }

            parentChecked();

            $('.the-permission').on('change', function(){
                parentChecked();
            });
        });
    </script>
@stop
