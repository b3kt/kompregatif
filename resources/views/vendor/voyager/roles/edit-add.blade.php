@extends('voyager::master')

@section('page_title', __('voyager::generic.' . (isset($dataTypeContent->id) ? 'edit' : 'add')) . ' ' .
    $dataType->getTranslatedAttribute('display_name_singular'))

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.' . (isset($dataTypeContent->id) ? 'edit' : 'add')) . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form class="form-edit-add" role="form"
                        action="@if (isset($dataTypeContent->id)) {{ route('voyager.' . $dataType->slug . '.update', $dataTypeContent->id) }}@else{{ route('voyager.' . $dataType->slug . '.store') }} @endif"
                        method="POST" enctype="multipart/form-data">

                        <!-- PUT Method if we are editing -->
                        @if (isset($dataTypeContent->id))
                            {{ method_field('PUT') }}
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

                            @foreach ($dataType->addRows as $row)
                                <div class="form-group">
                                    <label for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>

                                    {!! Voyager::formField($row, $dataType, $dataTypeContent) !!}

                                </div>
                            @endforeach

                            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                                <li role="presentation"><a href="#permission" id="permission-tab" role="tab"
                                        data-toggle="tab" aria-controls="permission" aria-expanded="true">Permissions</a>
                                </li>
                                <li role="presentation" class="active"><a href="#menu" role="tab" id="menu-tab"
                                        data-toggle="tab" aria-controls="menu">Menus</a></li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade" role="tabpanel" id="permission" aria-labelledby="permission-tab">
                                    {{-- <label for="permission">{{ __('voyager::generic.permissions') }}</label><br> --}}
                                    <a href="#"
                                        class="permission-select-all">{{ __('voyager::generic.select_all') }}</a> /
                                    <a href="#"
                                        class="permission-deselect-all">{{ __('voyager::generic.deselect_all') }}</a>
                                    <ul class="permissions checkbox">
                                        <?php
                                        $role_permissions = isset($dataTypeContent) ? $dataTypeContent->permissions->pluck('key')->toArray() : [];
                                        $menu_permissions = [];
                                        foreach (\App\Models\PermissionMenu::select('menu_item_id')->get() as $key => $value) {
                                            array_push($menu_permissions, $value->menu_item_id);
                                        }
                                        ?>
                                        @foreach (Voyager::model('Permission')->all()->groupBy('table_name') as $table => $permission)
                                            <li>
                                                <input type="checkbox" id="{{ $table }}" class="permission-group">
                                                <label
                                                    for="{{ $table }}"><strong>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $table)) }}</strong></label>
                                                <ul>
                                                    @foreach ($permission as $perm)
                                                        <li>
                                                            <input type="checkbox" id="permission-{{ $perm->id }}"
                                                                name="permissions[{{ $perm->id }}]"
                                                                class="the-permission" value="{{ $perm->id }}"
                                                                @if (in_array($perm->key, $role_permissions)) checked @endif>
                                                            <label
                                                                for="permission-{{ $perm->id }}">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $perm->key)) }}</label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="tab-pane fade in active" role="tabpanel" id="menu"
                                    aria-labelledby="menu-tab">
                                    {{-- <label for="permission">{{ __('voyager::generic.permissions') }}</label><br> --}}
                                    <a href="#" class="menu-select-all">{{ __('voyager::generic.select_all') }}</a> /
                                    <a href="#"
                                        class="menu-deselect-all">{{ __('voyager::generic.deselect_all') }}</a>

                                    <ul class="menu checkbox">
                                        <?php
                                        $role_permissions = isset($dataTypeContent) ? $dataTypeContent->permissions->pluck('key')->toArray() : [];
                                        ?>
                                        @foreach (Voyager::model('Menu')->all() as $menu)
                                            <li>
                                                <input type="checkbox" id="{{ $menu->name }}" class="menu-group">
                                                <label
                                                    for="{{ $menu->name }}"><strong>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $menu->name)) }}</strong></label>
                                                <ul>
                                                    @foreach (Voyager::model('MenuItem')->where('menu_id', $menu->id)->where('parent_id', null)->get() as $menuItem)
                                                        <li>
                                                            <input type="checkbox" id="menu-{{ $menuItem->id }}"
                                                                name="menus[{{ $menuItem->id }}]"
                                                                class="the-menu" value="{{ $menuItem->id }}"
                                                                @if (in_array($menuItem->id, $menu_permissions)) checked @endif>
                                                            <label
                                                                for="menu-{{ $menuItem->id }}">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $menuItem->title)) }}</label>

                                                            <ul>
                                                                @foreach (Voyager::model('MenuItem')->where('menu_id', $menu->id)->where('parent_id', $menuItem->id)->get() as $menuItem1)
                                                                    <li>
                                                                        <input type="checkbox"
                                                                            id="menu1-{{ $menuItem1->id }}"
                                                                            name="menus[{{ $menuItem1->id }}]"
                                                                            class="the-menu1"
                                                                            value="{{ $menuItem1->id }}"
                                                                            @if (in_array($menuItem1->id, $menu_permissions)) checked @endif>
                                                                        <label
                                                                            for="menu1-{{ $menuItem1->id }}">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $menuItem1->title)) }}</label>

                                                                        <ul>
                                                                            @foreach (Voyager::model('MenuItem')->where('menu_id', $menu->id)->where('parent_id', $menuItem1->id)->get() as $menuItem2)
                                                                                <li>
                                                                                    <input type="checkbox"
                                                                                        id="menu2-{{ $menuItem2->id }}"
                                                                                        name="menus[{{ $menuItem2->id }}]"
                                                                                        class="the-menu2"
                                                                                        value="{{ $menuItem2->id }}"
                                                                                        @if (in_array($menuItem2->id, $menu_permissions)) checked @endif>
                                                                                    <label
                                                                                        for="menu2-{{ $menuItem2->id }}">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $menuItem2->title)) }}</label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </li>
                                                                @endforeach
                                                            </ul>

                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        </div><!-- panel-body -->
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary">{{ __('voyager::generic.submit') }}</button>
                        </div>
                    </form>

                    <div style="display:none">
                        <input type="hidden" id="upload_url" value="{{ route('voyager.upload') }}">
                        <input type="hidden" id="upload_type_slug" value="{{ $dataType->slug }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $('document').ready(function() {
            $('.toggleswitch').bootstrapToggle();

            $('.permission-group').on('change', function() {
                $(this).siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
            });

            $('.permission-select-all').on('click', function() {
                $('ul.permissions').find("input[type='checkbox']").prop('checked', true);
                return false;
            });

            $('.permission-deselect-all').on('click', function() {
                $('ul.permissions').find("input[type='checkbox']").prop('checked', false);
                return false;
            });

            function parentChecked() {
                $('.permission-group').each(function() {
                    var allChecked = true;
                    $(this).siblings('ul').find("input[type='checkbox']").each(function() {
                        if (!this.checked) allChecked = false;
                    });
                    $(this).prop('checked', allChecked);
                });
            }

            parentChecked();

            $('.the-permission').on('change', function() {
                parentChecked();
            });


            // --------------------------

            $('.menu-group').on('change', function() {
                $(this).siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
            });

            $('.menu-select-all').on('click', function() {
                $('ul.menus').find("input[type='checkbox']").prop('checked', true);
                return false;
            });

            $('.menu-deselect-all').on('click', function() {
                $('ul.menus').find("input[type='checkbox']").prop('checked', false);
                return false;
            });

            function menuParentChecked() {
                $('.menu-group').each(function() {
                    var allChecked = true;
                    $(this).siblings('ul').find("input[type='checkbox']").each(function() {
                        if (!this.checked) allChecked = false;
                    });
                    $(this).prop('checked', allChecked);
                });
            }

            menuParentChecked();

            $('.the-menu').on('change', function() {
                menuParentChecked();
            });

            $('.the-menu1').on('change', function() {
                menuParentChecked();
            });

            $('.the-menu2').on('change', function() {
                menuParentChecked();
            });
        });
    </script>
@stop
