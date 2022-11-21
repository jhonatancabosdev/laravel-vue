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

                <div>
                    <form class="form-edit-add" role="form"
                          action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
                          method="POST" enctype="multipart/form-data" autocomplete="off">
                        <!-- PUT Method if we are editing -->
                        @if(isset($dataTypeContent->id))
                            {{ method_field("PUT") }}
                        @endif
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-md-8">
                                <div class="panel panel-bordered">
                                {{-- <div class="panel"> --}}
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="panel-body">

                                        <div class="d-flex flex-row flex-nowrap p-4 bg-light rounded-lg mb-3">
                                            <div class="form-group d-flex flex-column w-100 mr-4">
                                                <label for="name">{{ __('voyager::generic.name') }}</label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('voyager::generic.name') }}"
                                                       value="{{ old('name', $dataTypeContent->name ?? '') }}">
                                            </div>
                                            <div class="form-group d-flex flex-column w-100 mr-4">
                                                <label for="email">{{ __('voyager::generic.email') }}</label>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('voyager::generic.email') }}"
                                                       value="{{ old('email', $dataTypeContent->email ?? '') }}">
                                            </div>
                                        </div>


                                        <div class="d-flex flex-row align-items-start flex-nowrap pt-4 px-4 pb-3 bg-light rounded-lg mb-3">

                                            <div class="form-group d-flex flex-column w-100 mr-4">
                                                <label for="password">{{ __('voyager::generic.password') }}</label>
                                                <input type="password" class="form-control" id="password" name="password" value="" autocomplete="new-password">
                                                @if(isset($dataTypeContent->password))
                                                    <small class="text-danger">{{ __('voyager::profile.password_hint') }}</small>
                                                @endif
                                            </div>

                                            @can('editRoles', $dataTypeContent)
                                                <div class="form-group d-flex flex-column w-100 mr-4">
                                                    <label for="default_role">{{ __('voyager::profile.role_default') }}</label>
                                                    @php
                                                        $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                                        $row     = $dataTypeRows->where('field', 'user_belongsto_role_relationship')->first();
                                                        $options = $row->details;
                                                    @endphp
                                                    @include('voyager::formfields.relationship')
                                                </div>
                                                <div class="form-group d-flex flex-column w-100 mr-4">
                                                    <label for="additional_roles">{{ __('voyager::profile.roles_additional') }}</label>
                                                    @php
                                                        $row     = $dataTypeRows->where('field', 'user_belongstomany_role_relationship')->first();
                                                        $options = $row->details;
                                                    @endphp
                                                    @include('voyager::formfields.relationship')
                                                </div>
                                            @endcan

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="panel panel panel-bordered panel-warning">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="p-3 bg-light rounded-lg align-items-center text-center">
                                                @if(isset($dataTypeContent->avatar))
                                                    <img src="{{ filter_var($dataTypeContent->avatar, FILTER_VALIDATE_URL) ? $dataTypeContent->avatar : Voyager::image( $dataTypeContent->avatar ) }}" class="img-fluid mb-4">
                                                @endif
                                            <input type="file" data-name="avatar" name="avatar">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex w-100 flex-row align-content-end">
                            <button type="submit" class="btn btn-pink px-5 mr-0 ml-auto">Save</button>
                        </div>

                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
                        {{ csrf_field() }}
                        <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
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
        });
    </script>
@stop
