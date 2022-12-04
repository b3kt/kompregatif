@inject('menuHelper', 'App\Helpers\MenuHelper')

<div class="side-menu sidebar-inverse">
    <nav class="navbar navbar-default" role="navigation">
        <div class="side-menu-container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('voyager.dashboard') }}">
                    <div class="logo-icon-container">
                        <?php $admin_logo_img = Voyager::setting('admin.icon_image', ''); ?>
                        @if($admin_logo_img == '')
                            <img src="{{ voyager_asset('images/logo-icon-light.png') }}" alt="Logo Icon">
                        @else
                            <img src="{{ Voyager::image($admin_logo_img) }}" alt="Logo Icon">
                        @endif
                    </div>
                    <div class="title">{{Voyager::setting('admin.title', 'AVMS')}}</div>
                </a>
            </div><!-- .navbar-header -->

            <div class="panel widget center bgimage"
                 style="background-image:url({{ Voyager::image( Voyager::setting('admin.bg_image'), voyager_asset('images/bg.jpg') ) }}); background-size: cover; background-position: 0px;">
                <div class="dimmer"></div>
                <div class="panel-content">
                    <img src="{{ $user_avatar }}" class="avatar" alt="{{ Auth::user()->name }} avatar">
                    <h4>{{ ucwords(Auth::user()->name) }}</h4>
                    <p>{{ Auth::user()->email }}</p>

                    <a href="{{ route('voyager.profile') }}" class="btn btn-primary">{{ __('voyager::generic.profile') }}</a>
                    <div style="clear:both"></div>
                </div>
            </div>

        </div>
        <div id="adminmenu">
            <ul class="nav navbar-nav">
                @php
                    $menus = $menuHelper->display('admin');
                @endphp

                @foreach($menus as $item)
                    <li class="{{ $item->active ? 'active' : '' }} {{ count($item->children) > 0 ? 'dropdown' : '' }}">

                        <a target="{{$item->target}}" href="{{ count($item->children) > 0 ? '#'.$item->id.'-dropdown-element' : $item->url }}"
                            style="{{$item->color}}" data-toggle="{{ count($item->children) > 0 ? 'collapse' : false }}"
                            aria-expanded="{{ count($item->children) > 0 ? $item->active : false }}">
                            <span class="icon {{$item->icon_class}}"></span>
                            <span class="title">{{ $item->title }}</span>
                        </a>
                        @if (count($item->children) > 0)
                            <div id="{{$item->id}}-dropdown-element" class="panel-collapse collapse {{$item->active ? ' in' : ''}}">
                                <div class="panel-body">
                                    <ul class="nav navbar-nav">
                                        @foreach($item->children as $child)
                                            <li class="{{ $child->active ? 'active' : '' }} {{ count($child->children) > 0 ? 'dropdown' : '' }}">

                                                <a target="{{$child->target}}" href="{{ count($child->children) > 0 ? '#'.$child->id.'-dropdown-element' : $child->url }}"
                                                    style="{{$child->color}}" data-toggle="{{ count($child->children) > 0 ? 'collapse'.$child->id : false }}"
                                                    aria-expanded="{{ count($child->children) > 0 ? $child->active : false }}">
                                                    <span class="icon {{$child->icon_class}}"></span>
                                                    <span class="title">{{ $child->title }}</span>
                                                </a>
                                                @if (count($child->children) > 0)
                                                    <div id="{{$child->id}}-dropdown-element" class="panel-collapse collapse{{$child->id}} {{$child->active ? ' in' : ''}}">
                                                        <div class="panel-body">
                                                            <ul class="nav navbar-nav" style="padding-left:32px;" >
                                                                @foreach($child->children as $grandchild)
                                                                    <li class="{{ $grandchild->active ? 'active' : '' }} {{ count($grandchild->children) > 0 ? 'dropdown' : '' }}">
                                                                        <a target="{{$grandchild->target}}" href="{{ count($grandchild->children) > 0 ? '#'.$grandchild->id.'-dropdown-element' : $grandchild->url }}"
                                                                            style="{{$grandchild->color}}" data-toggle="{{ count($grandchild->children) > 0 ? 'collapse' : false }}"
                                                                            aria-expanded="{{ count($grandchild->children) > 0 ? $grandchild->active : false }}">
                                                                            <span class="icon {{$grandchild->icon_class}}"></span>
                                                                            <span class="title">{{ $grandchild->title }}</span>
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                    </li>
                @endforeach
            </ul>


            {{-- @if (Auth::user()->hasRole('admin'))
                <admin-menu :items="{{ menu('admin', '_json')->filter(function ($item) { return !$item->hidden; }) }}"></admin-menu>
            @endif --}}
        </div>
    </nav>
</div>
