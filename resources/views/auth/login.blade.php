@extends('layouts.app_login')

@section('content')

    <div class="card hoverable mt-8">
        <div class="card-content">
            <div class="row">
                <div class="col s12 m3">
                </div>
                <div class="col s12 m6 my-2">
                    <span class="card-title">
                        Login

                        <ul class="navbar-list right mt-0 mr-2">
                            {{language_switcher()}}
                        </ul>
                    </span>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @if ($errors->any())
                            <div class="card-alert card red lighten-5 my-4">
                                <div class="card-content red-text">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" class="close red-text" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        @endif
                        <div class="input-field">
                            <label for="email"> @lang('E-Mail Address') </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>

                        <div class="input-field">
                            <label for="password"> @lang('Password') </label>
                            <input id="password" type="password" style="width: 90%" name="password" required autocomplete="current-password">
                            <button type="button" id="show_password" class="btn btn-floating waves-effect waves-light ml-2"> <i class="material-icons">remove_red_eye</i> </button>
                        </div>
                        <p style="margin-bottom: 0.5rem">
                            <label>
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span> @lang('Remember Me') </span>
                            </label>
                        </p>

                        <button type="submit" class="btn waves-effect waves-light my-2" > Login </button>

                        @if (Route::has('password.request'))
                            <a class="btn waves-effect waves-light my-2" href="{{ route('password.request') }}"> @lang('Forgot Your Password?') </a>
                        @endif

                    </form>
                </div>
                <div class="col s12 m3 my-2">
                    <img style="max-width: 100%;" src="{{asset('logo.PNG')}}" />
                </div>
            </div>
        </div>
    </div>

@endsection


@section('js')
    @parent
    <script>
        $(document).ready(function(){
            $('#show_password').click(function () {
                var pass = $('[name="password"]');
                if(pass.attr('type') === 'password') {
                    pass.attr('type', 'text');
                } else {
                    pass.attr('type', 'password');
                }
            });
        });
    </script>
@endsection
