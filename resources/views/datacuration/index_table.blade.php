@extends('layouts.app')

@section('content')

    @include('projects/modal_search')

    <div class="row">
        <div class="col s6">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;Progetti</a>
                <a onclick="document.location.href='{{ route('dc.index', $project->id) }}'" class="pointer">/&nbsp;DataCuration </a>
            </h6>
        </div>
        <div class="col s6 right-align">
            <button id="search_button" type="button" class="btn btn-floating waves-effect waves-light modal-trigger" data-target="search_modal"><i class="material-icons">search</i></button>
            @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{Request::url()}}/create'" title="Aggiungi Stanza"><i class="material-icons">folder_open</i></button>
                <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{Request::url()}}/file/add'" title="Aggiungi File"><i class="material-icons">insert_drive_file</i></button>
            @endif
            <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('dc.index', $project->id) }}'" title="Cambia Layout"><i class="material-icons">grid_on</i></button>
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="card-alert card green lighten-5">
            <div class="card-content green-text">
                <p>{{ session()->get('message') }}</p>
            </div>
            <button type="button" class="close green-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="card-alert card red lighten-5 my-4">
            <div class="card-content red-text">
                {{ session()->get('error') }}
            </div>
            <button type="button" class="close red-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title"> Stanze Data Curation </span>

                    <table class="table-responsive highlight display" id="dc_table">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tags</th>
                            <th>Azioni</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dcs as $dc)
                            <tr>
                                <td>{{$dc->name}}</td>
                                <td>
                                    @if(!empty($dc->tags))
                                        @foreach(explode(';', $dc->tags) as $tag)
                                            <b>{{$tag}}</b>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-small waves-effect waves-light" onclick="document.location.href='{{route('dc.get', [$project->id, $dc->id])}}'" title="view"><i class="material-icons">open_in_browser</i></button>
                                    @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                                        <button type="button" class="btn btn-small waves-effect waves-light" onclick="document.location.href='{{route('dc.users', [$project->id, $dc->id])}}'" title="assign user"><i class="material-icons">folder_shared</i></button>
                                        <button type="button" class="btn btn-small waves-effect waves-light" onclick="document.location.href='{{route('dc.edit', [$project->id, $dc->id])}}'" title="edit"><i class="material-icons">edit</i></button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <span class="card-title"> File </span>

                    <table class="table-responsive highlight display" id="dce_table">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Autore</th>
                            <th>Tags</th>
                            <th>Azioni</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>{{$file->name}}</td>
                                <td>{{$file->author->name}}</td>
                                <td>
                                    @if(!empty($file->tags))
                                        @foreach(explode(';', $file->tags) as $tag)
                                            <b>{{$tag}}</b>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-small waves-effect waves-light" onclick="document.location.href='{{route('dce.show', [$project->id, $file->id])}}'" title="view"><i class="material-icons">open_in_browser</i></button>
                                    @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                                        <button type="button" class="btn btn-small waves-effect waves-light" onclick="document.location.href='{{ route('dce.edit', [$project->id, $file->id])}}'" title="edit"><i class="material-icons">edit</i></button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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

    <script src="{{ asset('js/dc.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#dc_table').DataTable( {
                "lengthChange": false,
                fixedColumns:   {
                    heightMatch: 'none'
                }
            });

            $('#dce_table').DataTable( {
                "lengthChange": false,
                fixedColumns:   {
                    heightMatch: 'none'
                }
            });
        } );

    </script>


@endsection
