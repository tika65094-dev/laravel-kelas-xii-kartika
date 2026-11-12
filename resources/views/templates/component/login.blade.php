<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Sign In & Sign Up
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
      </div>
      <section>
        <div class="modal-body">
          <div class="w3_login_module">
            <div class="module form-module">
              <div class="toggle"><i class="fa fa-times fa-pencil"></i>
                <div class="tooltip">Click Me</div>
              </div>

              <!-- Login Form -->
              <div class="form">
                <h3>Login to your account</h3>

                @if($errors->login->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; padding: 10px; font-size: 13px; text-align: left;">
                    @foreach($errors->login->all() as $error)
                      <div><i class="fa fa-exclamation-circle"></i> {{ $error }}</div>
                    @endforeach
                  </div>
                @endif

                <form action="{{ route('login') }}" method="post">
                  @csrf
                  <input type="hidden" name="redirect_to" value="{{ old('redirect_to', request()->fullUrl()) }}">
                  <input type="text" name="login" placeholder="Email atau Username" value="{{ old('login') }}" required="">
                  <input type="password" name="password" placeholder="Password" required="">
                  <div style="text-align: left; margin-bottom: 15px;">
                    <label style="font-weight: normal; font-size: 13px; cursor: pointer; color: #555;">
                      <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Ingat Saya
                    </label>
                  </div>
                  <input type="submit" value="Login">
                </form>
              </div>

              <!-- Register Form -->
              <div class="form">
                <h3>Create an account</h3>

                @if($errors->register->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; padding: 10px; font-size: 13px; text-align: left;">
                    @foreach($errors->register->all() as $error)
                      <div><i class="fa fa-exclamation-circle"></i> {{ $error }}</div>
                    @endforeach
                  </div>
                @endif

                <form action="{{ route('register') }}" method="post">
                  @csrf
                  <input type="text" name="name" placeholder="Nama / Username" value="{{ old('name') }}" required="">
                  <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required="">
                  <input type="password" name="password" placeholder="Password (Min. 8 karakter)" required="">
                  <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required="">
                  <input type="submit" value="Register">
                </form>
              </div>

              <div class="cta"><a href="#" onclick="alert('Fitur lupa password belum tersedia. Silakan hubungi administrator.'); return false;">Forgot your password?</a></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<script>
  $(document).ready(function(){
    $('.toggle').click(function(){
      // Switches the Icon
      $(this).children('i').toggleClass('fa-pencil');
      // Switches the forms  
      $('.form').animate({
        height: "toggle",
        'padding-top': 'toggle',
        'padding-bottom': 'toggle',
        opacity: "toggle"
      }, "slow");
    });

    @if(session('active_modal') === 'register' || $errors->register->any())
      $('#myModal').modal('show');
      $('.toggle').trigger('click');
    @elseif(session('active_modal') === 'login' || $errors->login->any())
      $('#myModal').modal('show');
    @endif
  });
</script>
