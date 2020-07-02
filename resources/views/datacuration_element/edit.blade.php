@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects') </a>
                <a onclick="document.location.href='{{ route('dc.index', $project->id) }}'" class="pointer"> / @lang('Data Curation') </a>
                <a href="#" class="pointer"> / Modifica File </a>
                <a href="#" class="pointer"> / {{$file->name}} </a>
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
                    <span class="card-title ml-2"> @lang('Edit File') </span>

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
                                <form method="post" action="{{ route('dce.update', [$project->id, $file->id]) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="card-content">
                                        <div class="row">
                                            <div class="col l6 m6 s12">
                                                <div class="input-field my-3">
                                                    <label for="description"> @lang('File Description') </label>
                                                    <textarea id="description" name="description" class="materialize-textarea">{{is_null(old('description')) ? $file->description : old('description')}}</textarea>
                                                </div>

                                                <div class="input-field my-3">
                                                    <label for="topic"> @lang('Topic') </label>
                                                    <textarea id="topic" name="topic" class="materialize-textarea">{{is_null(old('topic')) ? $file->topic : old('topic')}}</textarea>
                                                </div>

                                                <div class="file-field input-field my-1">
                                                    <div class="btn">
                                                        <span> @lang('Upload File') </span>
                                                        <input type="file" id="file" name="file">
                                                    </div>
                                                    <div class="file-path-wrapper">
                                                        <input class="file-path" type="text" value="{{$file->name}}">
                                                    </div>
                                                </div>

                                                <div class="file-field input-field my-1">
                                                    <div class="btn">
                                                        <span> @lang('Thumbnail Image (optional)') </span>
                                                        <input type="file" id="thumbnail" name="thumbnail">
                                                    </div>
                                                    <div class="file-path-wrapper">
                                                        <input class="file-path" type="text" value="{{$file->thumbnail}}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col l6 m6 s12">
                                                @foreach($select_rooms as $key => $select_level)
                                                    <div class="input-field my-3 valign-wrapper" style="display:flex;">
                                                        <select id="rooms" name="rooms[]" class="room-select" data-level="{{$key}}" multiple>
                                                            <option value="" disabled> @lang('Choose a Room') </option>
                                                            @if($key == 1)
                                                                <option value="0"> @lang('Project Room') </option>
                                                            @endif
                                                            @foreach($select_level as $room)
                                                                @php
                                                                    $id = $room->id;
                                                                    $search = $file_rooms[$key]['rooms']->search(function ($item, $key) use ($id)
                                                                    {
                                                                        return $item->id == $id;
                                                                    });
                                                                    $checked = false;
                                                                    if(!is_bool($search))
                                                                    {
                                                                        $checked = true;
                                                                    }
                                                                @endphp
                                                                <option value="{{$room->id}}" {{$checked ? 'selected' : ''}}> {{$room->name}} </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="rooms"> @lang('Choose a Room') </label>

                                                        <p class="mt-1 ml-2">
                                                            <label>
                                                                <input type="checkbox" data-level="1" {{$file_rooms[$key]['checked'] ? 'checked' : ''}}>
                                                                <span>  </span>
                                                            </label>
                                                        </p>
                                                    </div>
                                                @endforeach

                                                <div class="input-field my-3" id="tags">
                                                    <label> @lang('Tags') </label>
                                                    @if($tags->isEmpty())
                                                        <input type="text" class="mb-2" onkeyup="get_tags(this)" name="tags[]" autocomplete="off"/>
                                                        <div id="list_tags"></div>
                                                    @endif
                                                    @if(!empty(old('tags')))
                                                        @foreach(old('tags') as $tag)
                                                            <input type="text" class="mb-2" onkeyup="get_tags(this)" name="tags[]" autocomplete="off" value="{{$tag}}"/>
                                                            <div id="list_tags"></div>
                                                        @endforeach
                                                    @else
                                                        @foreach($tags as $tag)
                                                            <input type="text" class="mb-2" onkeyup="get_tags(this)" name="tags[]" autocomplete="off" value="{{$tag->tag}}"/>
                                                            <div id="list_tags"></div>
                                                        @endforeach
                                                    @endif
                                                    <button type="button" class="btn btn-small waves-effect waves-light" id="add_tag"> <i class="material-icons">add</i> </button>

                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="project" value="{{$project->id}}"/>

                                        <button type="submit" id="submit" class="btn waves-effect waves-light mt-3 mr-2"> @lang('Update') </button>

                                        <p class="mt-4">
                                            <label>
                                                <input type="checkbox" name="send_email" id="send_email" {{ $file->send_email == 1 ? 'checked' : ''}} value="1">
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

    <script src="{{ asset('js/custom.js') }}"></script>
@endsection
