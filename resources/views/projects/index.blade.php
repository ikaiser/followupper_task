@extends('layouts.app')

@section('content')

    @include('projects/modal_remove')
    @include('projects/modal_search')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            @if($role->id <= 2)
                <button onclick="document.location.href='{{ route('projects.create') }}'" type="button" class="btn btn-floating waves-effect waves-light"><i class="material-icons">add</i></button>
            @endif
            <button id="search_button" type="button" class="btn btn-floating waves-effect waves-light modal-trigger" data-target="search_modal"><i class="material-icons">search</i></button>
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title ml-2"> @lang('Projects') </span>

                    @if(session()->has('message'))
                        <div class="card-alert card green lighten-5">
                            <div class="card-content green-text">
                                <p>{{ Session::get('message') }}</p>
                            </div>
                            <button type="button" class="close green-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    @endif

                    @if ($message = Session::get('success'))
                        <div class="card-alert card green lighten-5">
                            <div class="card-content green-text">
                                <p>{{ $message }}</p>
                            </div>
                            <button type="button" class="close green-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        @foreach ($projects as $project)
                            <div class="col s12 l4 m4">
                                <div class="card mb-4 hoverable">
                                    @if(!is_null($project->logo))
                                        <div style="background-image: url('{{Storage::url("/project/{$project->id}/") . $project->logo}}'); height: 150px; width: 100%; background-position: center;background-size: cover; background-repeat: no-repeat;"></div>
                                    @else
                                        <div style="height: 150px; width: 100%;"></div>
                                    @endif
                                    <div class="card-content">
                                        <span class="card-title">{{$project->name}}</span>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-small waves-effect waves-light my-1" onclick="document.location.href='{{route('dc.index', $project->id)}}'" title="@lang('Data Curation')"><i class="material-icons">folder</i></button>
                                            @if($role->id <= 2)
                                                <button type="button" class="btn btn-small waves-effect waves-light my-1" onclick="document.location.href='{{route('projects.edit', $project->id)}}'" title="@lang('Edit')"><i class="material-icons">edit</i></button>
                                            @endif
                                            @if($role->id <= 3)
                                                <button type="button" class="btn btn-small waves-effect waves-lightmy-1 " onclick="document.location.href='{{route('projects.users', $project->id)}}'" title="@lang('Assign')"><i class="material-icons">folder_shared</i></button>
                                            @endif
                                            @if($role->id == 1)
                                                <button type="button" class="btn btn-small waves-effect waves-light red modal-trigger my-1" title="Elimina" data-route="{{route('projects.remove', $project->id)}}" data-id="{{$project->id}}" name="remove_project" data-target="project_remove"><i class="material-icons">delete</i></button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/flag-icon/css/flag-icon.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/data-tables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/data-tables/css/select.dataTables.min.css') }}">
@endsection

@section('js')
    @parent

    <script src="{{asset('templates/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('templates/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('templates/vendors/data-tables/js/dataTables.select.min.js')}}"></script>

    <script src="{{ asset('js/projects.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#user_table').DataTable( {
                "lengthChange": false,
                "responsive"  : true,
                fixedColumns:   {
                    heightMatch: 'none'
                }
            });
        } );

    </script>
@endsection

