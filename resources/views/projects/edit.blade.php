@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects') </a>
                <a href="#">/ {{$project->name}}</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    {{ Form::model( $project, ['route' => ['projects.update', $project->id], 'method' => 'put', 'role' => 'form', 'class' => 'forms-sample', 'files' => true, 'enctype' => 'multipart/form-data'] ) }}

    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title mb-4"> @lang('Edit Project') {{$project->name}} </span>
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
                    <div class="input-field my-3">
                        <label for="name"> @lang('Project Name') </label>
                        <input type="text" name="name" value="{{$project->name}}">
                    </div>
                    <div class="file-field input-field my-1">
                        <div class="btn">
                            <span> @lang('Logo') </span>
                            <input type="file" id="logo" name="logo">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path" type="text" value="{{$project->logo}}">
                        </div>
                    </div>
                    <button type="submit" class="btn waves-effect waves-light mt-3"> @lang('Save') </button>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}

@endsection

@section('js')
@parent
@endsection
