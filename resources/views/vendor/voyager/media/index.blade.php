@extends('voyager::master')

@section('page_title', __('voyager::generic.media'))

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">

                <div class="admin-section-title">
                    <h3><i class="voyager-images"></i> {{ __('voyager::generic.media') }}</h3>
                </div>
                <div class="clear"></div>
                <div id="filemanager">
                    @php
                        $user = Auth::user();
                        $role = strtolower(str_replace(' ','-',preg_replace('/[^A-Za-z0-9\-]/', '', $user->role->name)));
                        $basepath = $role == 'admin' ? config('voyager.media.path', '/') : config('voyager.media.path', '').'/'.$role.'/';
                        $isadmin = Auth::user()->role->name == 'admin';
                    @endphp
                    <media-manager
                        base-path="{{$basepath}}"
                        :show-folders="{{ $isadmin || config('voyager.media.show_folders', true) ? 'true' : 'false' }}"
                        :allow-upload="{{ $isadmin || config('voyager.media.allow_upload', true) ? 'true' : 'false' }}"
                        :allow-move="{{ $isadmin || config('voyager.media.allow_move', true) ? 'true' : 'false' }}"
                        :allow-delete="{{ $isadmin || config('voyager.media.allow_delete', true) ? 'true' : 'false' }}"
                        :allow-create-folder="{{ $isadmin || config('voyager.media.allow_create_folder', true) ? 'true' : 'false' }}"
                        :allow-rename="{{ $isadmin || config('voyager.media.allow_rename', true) ? 'true' : 'false' }}"
                        :allow-crop="{{ $isadmin || config('voyager.media.allow_crop', true) ? 'true' : 'false' }}"
                        :details="{{ json_encode(['thumbnails' => config('voyager.media.thumbnails', []), 'watermark' => config('voyager.media.watermark', (object)[])]) }}"
                        ></media-manager>
                </div>
            </div><!-- .row -->
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
