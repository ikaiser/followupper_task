@extends('layouts.app_login')

@section('content')
    <div class="card hoverable mt-8">
        <div class="card-content">
            <div class="row">
                <div class="col s6 offset-l3">
                    <span class="card-title"> @lang('Reset Password') </span>
                    <form method="POST" action="{{ route('password.update') }}">
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

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="input-field">
                            <label for="email"> @lang('E-Mail Address') </label>
                            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                        </div>

                        <div class="input-field">
                            <label for="password"> @lang('Password') </label>
                            <input id="password" type="password" name="password" required autocomplete="new-password">
                        </div>

                        <div class="input-field">
                            <label for="password-confirm"> @lang('Confirm Password') </label>
                            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn waves-effect waves-light"> @lang('Reset Password') </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
