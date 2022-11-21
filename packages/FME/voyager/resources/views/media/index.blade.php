@extends('voyager::master')

@section('page_title', __('voyager::generic.media'))

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-md-12">
                    <div>
                        @include('voyager::alerts')
                    </div>
                    <div class="admin-section-title mb-3">
                        <h1 class="page-title">
                            <i class="h3 voyager-images"></i> {{ __('voyager::generic.media') }}
                        </h1>
                    </div>
                    <div class="clear"></div>
                    <div id="filemanager">
                        <media-manager
                            base-path="{{ config('voyager.media.path', '/') }}"
                            :show-folders="{{ config('voyager.media.show_folders', true) ? 'true' : 'false' }}"
                            :allow-upload="{{ config('voyager.media.allow_upload', true) ? 'true' : 'false' }}"
                            :allow-move="{{ config('voyager.media.allow_move', true) ? 'true' : 'false' }}"
                            :allow-delete="{{ config('voyager.media.allow_delete', true) ? 'true' : 'false' }}"
                            :allow-create-folder="{{ config('voyager.media.allow_create_folder', true) ? 'true' : 'false' }}"
                            :allow-rename="{{ config('voyager.media.allow_rename', true) ? 'true' : 'false' }}"
                            :allow-crop="{{ config('voyager.media.allow_crop', true) ? 'true' : 'false' }}"
                            :details="{{ json_encode(['thumbnails' => config('voyager.media.thumbnails', []), 'watermark' => config('voyager.media.watermark', (object)[])]) }}"
                            ></media-manager>
                    </div>
                </div><!-- .row -->
            </div>

        </div><!-- .col-md-12 -->
    </div><!-- .page-content container-fluid -->
@stop

@section('javascript')
<script>
    new Vue({
        el: '#filemanager'
    });
</script>
@endsection
