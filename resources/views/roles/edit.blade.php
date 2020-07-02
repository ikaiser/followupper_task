@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('users.index') }}'" class="pointer">/&nbsp;@lang('Roles') </a>
                <a href="#">/ {{$role->name}}</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title mb-4"> @lang('Edit Role') {{$role->name}} </span>

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

            {{ Form::model( $role, ['route' => ['roles.update', $role->id], 'method' => 'put', 'role' => 'form', 'class' => 'forms-sample'] ) }}
            @csrf
            <div class="input-field my-3">
                <label for="name">@lang('Name')</label>
                <input type="text" id="name" name="name" value="{{$role->name}}">
            </div>
            <button type="submit" class="btn waves-effect waves-light" >@lang('Save')</button>
            {{ Form::close() }}
        </div>
    </div>
@endsection

@section('js')
@parent
@endsection
