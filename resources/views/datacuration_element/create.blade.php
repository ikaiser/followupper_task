@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects') </a>
                <a onclick="document.location.href='{{ route('dc.index', $project->id) }}'" class="pointer"> / {{$project->name}} </a>
                @foreach($dcs as $single_dc)
                    <a href="#" class="pointer" onclick="document.location.href='{{ route('dc.get', [$project->id, $single_dc->id]) }}'">&nbsp;/ {{$single_dc->name}}&nbsp;</a>
                @endforeach
                <a href="#" class="pointer">&nbsp;/&nbsp;@lang('Add File') </a>
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
                    <span class="card-title ml-2"> @lang('Add File') </span>

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
                            <div class="card">
                                <form method="post" action="{{ route('dce.store', $project->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="card-content">
                                        <div class="row">
                                            <div class="col l6 m6 s12">
                                                <div class="input-field my-3">
                                                    <label for="description"> @lang('File Description') </label>
                                                    <textarea id="description" name="description" class="materialize-textarea">{{old('description')}}</textarea>
                                                </div>

                                                <div class="input-field my-3">
                                                    <label for="topic"> @lang('Topic') </label>
                                                    <textarea id="topic" name="topic" class="materialize-textarea">{{old('topic')}}</textarea>
                                                </div>
                                                <div class="file-field input-field my-1">
                                                    <div class="btn">
                                                        <span> @lang('Upload File') </span>
                                                        <input type="file" id="file" name="file">
                                                    </div>
                                                    <div class="file-path-wrapper">
                                                        <input class="file-path" type="text">
                                                    </div>
                                                </div>

                                                <div class="file-field input-field my-1">
                                                    <div class="btn">
                                                        <span> @lang('Thumbnail Image (optional)') </span>
                                                        <input type="file" id="thumbnail" name="thumbnail">
                                                    </div>
                                                    <div class="file-path-wrapper">
                                                        <input class="file-path" type="text">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col l6 m6 s12">
                                                <div class="input-field my-3 valign-wrapper" style="display:flex;">
                                                    <select id="rooms" name="rooms[]" class="room-select" data-level="1" multiple>
                                                        <option value="" disabled> @lang('Choose a Room') </option>
                                                        <option value="0"> @lang('Project Room') </option>
                                                        @foreach($rooms as $room)
                                                            <option value="{{$room->id}}"> {{$room->name}} </option>
                                                        @endforeach
                                                    </select>
                                                    <label for="rooms"> @lang('Choose a Room') </label>

                                                    <p class="mt-1 ml-2">
                                                        <label>
                                                            <input type="checkbox" data-level="1" checked>
                                                            <span>  </span>
                                                        </label>
                                                    </p>
                                                </div>

                                                <div class="input-field my-3" id="tags">
                                                    <label> @lang('Tags') </label>
                                                    <input type="text" class="mb-2" onkeyup="get_tags(this)" name="tags[]" autocomplete="off"/>
                                                    <div id="list_tags"></div>
                                                    <button type="button" class="btn btn-small waves-effect waves-light" id="add_tag"> <i class="material-icons">add</i> </button>

                                                    @if(!empty(old('tags')))
                                                        @foreach(old('tags') as $tag)
                                                            <input type="text" class="mb-2" onkeyup="get_tags(this)" name="tags[]" autocomplete="off" value="{{$tag}}"/>
                                                            <div id="list_tags"></div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="project" value="{{$project->id}}"/>

                                        <button type="submit" id="submit" class="btn waves-effect waves-light mt-3 mr-2"> @lang('Add') </button>

                                        <p class="mt-4">
                                            <label>
                                                <input type="checkbox" name="send_email" id="send_email" {{ old('send_email') == 1 ? 'checked' : ''}} value="1">
                                                <span> @lang('Send E-Mail') </span>
                                            </label>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @parent
    <script src="{{ asset('js/dce.js') }}"></script>
    <script> sort_rooms(); </script>
@endsection
