<div class="side-menu sidebar-inverse">
    <nav class="navbar navbar-default" role="navigation">
        <div class="side-menu-container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <div class="logo-icon-container">
                        <img src="" alt="Logo Icon">
                    </div>
                    <div class="title">{{ setting('admin_title', 'ADMIN')}}</div>
                </a>
            </div><!-- .navbar-header -->

            <div class="panel widget center bgimage"
                 style="background-image:url({{ image(setting('admin_bg_image'), config('assets_path') . '/images/bg.jpg' ) }});">
                <div class="dimmer"></div>
                <div class="panel-content">
                    @php $user = Auth::user(); @endphp
                    @if($user)
                        <img src="" class="avatar" alt="{{ Auth::user()->name }} avatar">
                        <h4>{{ ucwords(Auth::user()->name) }}</h4>
                        <p>{{ Auth::user()->email }}</p>
                    @else
                        <img src="" class="avatar" alt="avatar">
                        <h4>临时登录</h4>
                    @endif
                    <a href="{{ route('profile') }}" class="btn btn-primary">Profile</a>
                    <div style="clear:both"></div>
                </div>
            </div>

        </div>

        {!! menu('admin', 'admin_menu') !!}
    </nav>
</div>
