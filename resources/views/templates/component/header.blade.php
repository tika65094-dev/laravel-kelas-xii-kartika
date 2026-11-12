<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="#" method="post">
        <input type="text" name="Search" placeholder="Search" required="">
        <input type="submit" value="Go">
      </form>
    </div>
    <div class="w3l_sign_in_register" style="width: auto; max-width: 45%;">
      <ul>
        @auth
          <li style="padding-right: 10px; color: #212121; font-weight: bold;">
            <i class="fa fa-user" style="color: #FF8D1B;"></i> {{ Auth::user()->name }}
          </li>
          @if(Auth::user()->isAdmin())
            <li>
              <a href="{{ route('admin.dashboard') }}" style="padding: 6px 15px; margin-right: 5px;">Admin</a>
            </li>
          @endif
          <li>
            <form action="{{ route('logout') }}" method="POST" style="display: inline-block; margin: 0;">
              @csrf
              <button type="submit" style="background: #212121; color: #fff; border: none; padding: 6px 20px; font-weight: bold; text-transform: uppercase; font-size: 1em; cursor: pointer; transition: 0.5s ease-in;">
                Logout
              </button>
            </form>
          </li>
        @else
          <li><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li>
        @endauth
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>