@extends('layouts.app')

@section('content')

    @php
        $dc_users = $dc->users;
    @endphp

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects') </a>
                <a onclick="document.location.href='{{ route('dc.index', $project->id) }}'" class="pointer"> / {{$project->name}} </a>
                @foreach($dcs as $single_dc)
                    <a href="#" class="pointer" onclick="document.location.href='{{ route('dc.get', [$project->id, $single_dc->id]) }}'">&nbsp;/ {{$single_dc->name}}&nbsp;</a>
                @endforeach
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
                    <span class="card-title ml-2"> @lang('Assign Users to the Room') </span>
                    <form method="post" action="{{ route('dc.save_users', [$project->id, $dc->id]) }}">
                        @csrf
                        <div class="row">
                            <div class="col s12">
                                <div class="input-field my-3">
                                    <input type="text" name="users[]" autocomplete="off" class="w-50 my-2" data-role="4" data-project="{{$project->id}}" value="">
                                    <button class="btn btn-small waves-effect waves-light red" name="deassign_user" style="display: none;"><i class="material-icons">delete</i></button>
                                </div>
                                <div class="w-50"></div>

                                @foreach($dc_users as $dc_user)
                                    @if($dc_user->roles->first()->id == 4)
                                        <div class="input-field my-3">
                                            <input type="text" name="users[]" autocomplete="off" class="w-50 my-2" data-role="4" value="{{$dc_user->name}}">
                                            <button class="btn btn-small waves-effect waves-light red" name="deassign_user"><i class="material-icons">delete</i></button>
                                        </div>
                                        <div class="w-50"></div>
                                    @endif
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
