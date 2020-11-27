@extends('layouts.app')

@section('content')


    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('users.index') }}'" class="pointer">/&nbsp;@lang('Users') </a>
                <a href="#">/ {{$user->name}}</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title mb-4"> @lang('Edit User') {{$user->name}} </span>

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

            {{ Form::model( $user, ['route' => ['users.update', $user->id], 'method' => 'put', 'role' => 'form', 'class' => 'forms-sample', 'enctype' => 'multipart/form-data'] ) }}
                @csrf
                <div class="input-field my-4">
                    <label for="name"> @lang('Username') </label>
                    <input id="name" type="text" name="name" value="{{$user->name}}" autocomplete="no">
                </div>
                <div class="input-field my-4">
                    <label for="email"> @lang('E-Mail Address')</label>
                    <input id="email" type="email" name="email" value="{{$user->email}}" autocomplete="email">
                </div>
                <div class="input-field my-4">
                    <label for="company">@lang('Company')</label>
                    <input id="company" type="text" name="company" value="{{$user->company}}" autocomplete="no">
                    <div id="list_company"></div>
                </div>
                <div class="input-field my-4">
                    <label for="password"> @lang('Password') </label>
                    <input type="password" class="form-control" style="width: 80%" id="exampleInputPassword1" name="password" autocomplete="new-password">
                    <button type="button" id="show_password" class="btn btn-floating waves-effect waves-light ml-2"> <i class="material-icons">remove_red_eye</i> </button>
                    <!-- <input type="text" name="clear_password" class="mb-3" value="{{$user->clear_password}}" readonly> -->
                </div>
                <div class="input-field my-4">
                    <select name="role" id="role">
                        <option value="" disabled hidden selected> @lang('Choose a Role') </option>
						<?php foreach ($roles as $key => $role) {  ?>
                            <?php if($role->id > \Illuminate\Support\Facades\Auth::user()->roles->first()->id || \Illuminate\Support\Facades\Auth::user()->roles->first()->id == '1') { ?>
								<option value="<?php echo $role->id; ?>" <?php echo($user->hasRole($role->name) ? 'selected' : '') ?> > <?php echo $role->name; ?> </option>
							<?php } ?>
						<?php } ?>
                    </select>
                    <label for="role"> @lang('Role') </label>
                </div>
                <div class="file-field input-field my-4">
                    <div class="btn btn-small">
                        <span> @lang('Profile Image') </span>
                        <input type="file" id="user_img" name="user_img">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" value="{{!empty($user->user_img) ? $user->user_img : ''}}">
                    </div>
                </div>
                <button type="submit" class="btn waves-effect waves-light" > @lang('Save') </button>
            {{ Form::close() }}
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

        $('#company').keyup(function(){
            var query = $(this).val();
            $('#list_company').fadeOut();

            if(query != '' && query.length > 3)
            {
                // var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"/users/company/fetch",
                    method:"GET",
                    data:{query:query},
                    success:function(data){
                        $('#list_company').fadeIn();
                        $('#list_company').html(data);
                    }
                });
            }
        });

        $(document).on('click', 'li', function() {
            if ($(this).data('ref') === 'company') {
                $('#company').val($(this).text());
                $('#list_company').fadeOut();
                return;
            }
        });
    </script>
@endsection
