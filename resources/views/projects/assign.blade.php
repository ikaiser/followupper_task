@extends('layouts.app')

@section('content')

@php
    $role_users = $project->users;
@endphp

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects')</a>
                <a href="#" class="pointer"> / {{$project->name}} </a>
                <a href="#" class="pointer"> / @lang('Assign Users') </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title ml-2"> @lang('Assign Users to the Project') </span>
                    <form method="post" action="{{ route('project.save_users', $project->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col s12">
                                @foreach($users as $key => $user_group)
                                    @if($user_group['role_id'] == 2 && \Illuminate\Support\Facades\Auth::user()->roles->first()->id > 1)
                                        @continue
                                    @endif

                                    @if($user_group['role_id'] != 4 && \Illuminate\Support\Facades\Auth::user()->roles->first()->id == 3)
                                        @continue
                                    @endif
                                    <div class="card hoverable my-2">
                                        <div class="card-content">
                                            <span class="card-title ml-2"> @lang('Assign') {{$key}} </span>

                                            <div class="input-field my-3">
                                                <input type="text" name="users[]" autocomplete="off" class="w-50 my-2" data-role="{{$user_group['role_id']}}" value="">
                                                <button class="btn btn-small waves-effect waves-light red" name="deassign_user" style="display: none;"><i class="material-icons">delete</i></button>
                                            </div>
                                            <div class="w-50"></div>

                                            @foreach($role_users as $user)
                                                @if($user->roles->first()->name == $key)
                                                    <div class="input-field my-3">
                                                        <input type="text" name="users[]" autocomplete="off" class="w-50 my-2" data-role="{{$user_group['role_id']}}" value="{{$user->name}}">
                                                        <button class="btn btn-small waves-effect waves-light red" name="deassign_user"><i class="material-icons">delete</i></button>
                                                    </div>
                                                    <div class="w-50"></div>
                                                @endif
                                            @endforeach

                                        </div>
                                    </div>
                                @endforeach
                                <button type="submit" class="btn waves-effect waves-light mt-2"> @lang('Save') </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @parent
    <script src="{{ asset('js/users.js') }}"></script>
@endsection
