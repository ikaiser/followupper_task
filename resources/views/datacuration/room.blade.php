@extends('layouts.app')

@section('content')

    @include('projects/modal_search')
    @include('datacuration/modal-remove')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects') </a>
                <a onclick="document.location.href='{{ route('dc.index', $project->id) }}'" class="pointer">/&nbsp;{{$project->name}} </a>
                @foreach($dcs as $single_dc)
                    <a onclick="document.location.href='{{ route('dc.get', [$project->id, $single_dc->id]) }}'" class="pointer">/ {{$single_dc->name}}&nbsp;</a>
                @endforeach
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button id="search_button" type="button" class="btn btn-floating waves-effect waves-light modal-trigger" data-target="search_modal"><i class="material-icons">search</i></button>
            @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('dc.create', [$project->id, $dc->id ]) }}'" title="@lang('Add Room')"><i class="material-icons">folder_open</i></button>
                <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('dce.create', [$project->id, $dc->id ]) }}'" title="@lang('Add File')"><i class="material-icons">insert_drive_file</i></button>
                <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('dc.edit', [$project->id, $dc->id ]) }}'"><i class="material-icons">edit</i></button>
                <button class="btn btn-floating waves-effect waves-light red modal-trigger" type="button" data-id="{{$dc->id}}" data-title="{{$single_dc->name}}" id="remove_dc" data-target="dc_remove_modal"><i class="material-icons">remove</i></button>

            @endif
            <!-- <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{route('dc.get', [$project->id, $dc->id])}}?layout=table'" title="Cambia Layout"><i class="material-icons">format_list_bulleted</i></button> -->
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
                    <div class="row mb-4">
                        <div class="col s12 m6">
                            <span class="card-title"> @lang('Data Curation Rooms') </span>
                        </div>
                        <div class="col s12 m6">
                            <input type="hidden" id="dc" value="{{$dc->id}}" />
                            <input type="hidden" id="project" value="{{$project->id}}" />
                            <select id="dc_sort">
                                <option value="" selected disabled> @lang('Sort Rooms by:') </option>
                                <option value="name"> @lang('Name') </option>
                                <option value="date"> @lang('Date') </option>
                            </select>
                        </div>
                    </div>
                    <div id="content_div">
                        <div class="row" id="dc_row">
                            @foreach($rooms as $room)
                                <div class="col s12 l4 m4">
                                    <div class="card mb-4 hoverable">
                                        <div style="background-image: url('{{Storage::url('dc/') . $room->thumbnail}}'); height: 150px; width: 100%; background-position: center;background-size: cover; background-repeat: no-repeat;"></div>
                                        <div class="card-content">
                                            <span class="card-title">{{$room->name}}</span>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-small waves-effect waves-light m-1" style="padding: 0 15px" onclick="document.location.href='{{route('dc.get', [$project->id, $room->id])}}'"> @lang('View') </button>
                                                @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                                                    <button type="button" class="btn btn-small waves-effect waves-light m-1" style="padding: 0 15px" onclick="document.location.href='{{route('dc.users', [$project->id, $room->id])}}'"> @lang('Assign Users') </button>
                                                    <button type="button" class="btn btn-small waves-effect waves-light m-1" style="padding: 0 15px" onclick="document.location.href='{{route('dc.edit', [$project->id, $room->id])}}'"> @lang('Edit') </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <span class="card-title"> @lang('File') </span>

                        <div class="row">
                            @foreach($files as $file)
                                <div class="col s12 l4 m4">
                                    <div class="card-panel border-radius-6 mt-10 card-animation-1" style="height: 320px">

                                        @if(!is_null($file->thumbnail))
                                            <img class="responsive-img border-radius-8 z-depth-4 image-n-margin" style="height: 150px" src="{{Storage::url("project/{$project->id}/files/thumbnails/") . $file->thumbnail}}">
                                        @else
                                            <img class="responsive-img border-radius-8 z-depth-4 image-n-margin" style="height: 150px" src="https://via.placeholder.com/700x200?text={{urlencode($file->name)}}">
                                        @endif
                                        <div class="card-content">
                                            <p class="truncate" style="font-weight: bold" title="{{$file->description}}"> {{$file->description}}</p>
                                            <p><a href="{{route('dce.show', [$project->id, $file->id])}}" class="mt-5 truncate" title="{{$file->name}}">{{$file->name}}</a></p>
                                            <div class="row mt-3">
                                                <div class="col s6 p-0 mt-2 left-align">
                                                    <span class="pt-2"> @lang('Uploaded:') {{date('d/m/Y', strtotime($file->created_at))}}</span>
                                                </div>
                                                <div class="col s6 mt-1 right-align">
                                                    <span class="material-icons light-blue-text lighten-2 ml-10">chat_bubble_outline</span>
                                                    <span class="ml-3 vertical-align-top">{{$file->comments->count()}}</span>
                                                    @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                                                        <span class="material-icons light-blue-text lighten-2 ml-10 pointer" onclick="document.location.href='{{route('dce.edit', [$project->id, $file->id])}}'" >edit</span>
                                                    @endif
                                                </div>
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
    </div>
@endsection

@section('js')
    @parent
    <script src="{{ asset('js/dc.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>
@endsection
