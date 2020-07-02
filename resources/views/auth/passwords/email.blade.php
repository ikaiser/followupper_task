@extends('layouts.app_login')

@section('content')

    <div class="card hoverable mt-8">
        <div class="card-content">
            <div class="row">
                <div class="col s6 offset-l3">
                    <span class="card-title"> @lang('Reset Password') </span>
                    <form method="POST" action="{{ route('password.email') }}">
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

                        <button type="submit" class="btn waves-effect waves-light"> @lang('Send Password Reset Link') </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
