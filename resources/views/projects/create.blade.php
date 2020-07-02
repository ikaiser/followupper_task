@extends('layouts.app')

@section('content')


    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects')</a>
                <a href="#" class="pointer"> / @lang('New Project') </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

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

    <div class="row">
        <div class="col s12">
            <div class="card hoverable">
                <div class="card-content">
                    <span class="card-title ml-2"> @lang('Add Project') </span>
                    <div class="divider"></div>
                    {{ Form::model( '', ['route' => ['projects.update', $project->id], 'method' => 'POST', 'role' => 'form', 'class' => 'forms-sample', 'files' => true, 'enctype' => 'multipart/form-data']) }}

                    <div class="input-field my-3">
                        <label for="name"> @lang('Project Name') </label>
                        <input type="text" name="name" value="">
                    </div>

                    <div class="file-field input-field my-1">
                        <div class="btn">
                            <span> @lang('Logo') </span>
                            <input type="file" id="logo" name="logo">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path" type="text">
                        </div>
                    </div>

                    <button type="submit" class="btn waves-effect waves-light mt-3"> @lang('Save') </button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
@parent
@endsection
