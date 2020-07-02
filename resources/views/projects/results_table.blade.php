@extends('layouts.app')

@section('content')

    @include('projects/modal_remove')
    @include('projects/modal_search')

    <div class="row">
        <div class="col s6">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;Progetti</a>
                <a href="#" class="pointer">/ Ricerca</a>
            </h6>
        </div>
        <div class="col s6 right-align">
            <form method="POST" class="form-inline" action="{{ route('dce.search') }}">
                @csrf
                @foreach($req['text'] as $text)
                    <input type="hidden" name="search_text[]" value="{{$text}}">
                @endforeach
                @if(!is_null($req['rel']))
                    @foreach($req['rel'] as $rel)
                        <input type="hidden" name="search_text[]" value="{{$rel}}">
                    @endforeach
                @endif
                @if(!empty($req['project']))
                    <input type="hidden" name="project" value="{{$req['project']}}">
                @endif
                @if(!empty($req['tag']))
                    <input type="hidden" name="tag" id="tag" value="{{$req['tag']}}">
                @endif
                <input type="hidden" name="doc_type" value="{{$req['doc_type']}}">
                @if(!empty($req['authors']))
                    @foreach($req['authors'] as $authors)
                        <input type="hidden" name="authors[]" value="{{$authors}}">
                    @endforeach
                @endif
                <input type="hidden" name="start_date" value="{{$req['start_date']}}">
                <input type="hidden" name="start_date" value="{{$req['end_date']}}">

                <button id="search_button" type="button" class="btn btn-floating waves-effect waves-light modal-trigger" data-target="search_modal"><i class="material-icons">search</i></button>
                <button class="btn btn-floating waves-effect waves-light" title="Cambia Layout"><i class="material-icons">grid_on</i></button>
                <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
            </form>
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
                    <span class="card-title"> Risultati Ricerca </span>

                    <table class="table-responsive highlight display" id="results_table">
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
                                    <button type="button" class="btn btn-small waves-effect waves-light" onclick="document.location.href='{{route('dce.show', [$file->project_id, $file->id])}}'" title="view"><i class="material-icons">open_in_browser</i></button>
                                    @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                                        <button type="button" class="btn btn-small waves-effect waves-light" onclick="document.location.href='{{ route('dce.edit', [$file->project_id, $file->id])}}'" title="edit"><i class="material-icons">edit</i></button>
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

    <script src="{{ asset('js/projects.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#results_table').DataTable( {
                "lengthChange": false,
                fixedColumns:   {
                    heightMatch: 'none'
                }
            });
        } );

    </script>
@endsection
