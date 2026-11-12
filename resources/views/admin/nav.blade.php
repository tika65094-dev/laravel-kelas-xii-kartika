<div class="row" style="margin-bottom: 25px;">
    <div class="col-md-12">
        <div style="background: #212121; padding: 12px 18px; border-radius: 4px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; border-left: 4px solid #FF8D1B;">
            <ul class="nav nav-pills" style="margin: 0; display: inline-block;">
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" style="{{ request()->routeIs('admin.dashboard') ? 'background: #FF8D1B;' : 'color: #fff;' }}">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.films.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.films.index') }}" style="{{ request()->routeIs('admin.films.*') ? 'background: #FF8D1B;' : 'color: #fff;' }}">
                        <i class="fa fa-film"></i> Kelola Film
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.film-cast') ? 'active' : '' }}">
                    <a href="{{ route('admin.film-cast') }}" style="{{ request()->routeIs('admin.film-cast') ? 'background: #FF8D1B;' : 'color: #fff;' }}">
                        <i class="fa fa-star"></i> Kelola Cast
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" style="{{ request()->routeIs('admin.users.*') ? 'background: #FF8D1B;' : 'color: #fff;' }}">
                        <i class="fa fa-users"></i> Kelola Pengguna
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.comments.index') }}" style="{{ request()->routeIs('admin.comments.*') ? 'background: #FF8D1B;' : 'color: #fff;' }}">
                        <i class="fa fa-comments"></i> Kelola Komentar
                    </a>
                </li>
            </ul>

            <div style="margin-top: 5px; float: right;">
                <a href="{{ route('home') }}" class="btn btn-sm btn-default" style="margin-right: 8px;">
                    <i class="fa fa-globe"></i> Lihat Website
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline-block; margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger" style="background: #e74c3c; font-weight: bold;">
                        <i class="fa fa-sign-out"></i> Logout
                    </button>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

