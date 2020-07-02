@extends('layouts.app')

@section('content')

    @php
        $project = \App\Project::find($dc->project_id);
    @endphp

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects') </a>
                <a onclick="document.location.href='{{ route('dc.index', $dc->project_id) }}'" class="pointer"> / {{$project->name}} </a>
                @foreach($dcs as $single_dc)
                    <a onclick="document.location.href='{{ route('dc.get', [$dc->project_id, $single_dc->id]) }}'" class="pointer">&nbsp;/ {{$single_dc->name}}&nbsp;</a>
                @endforeach
                <a href="#" class="pointer"> / @lang('Edit') </a>
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
                    <span class="card-title ml-2"> @lang('Edit Room') </span>

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
                                <form method="post" action="{{ route('dc.update', $dc->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="card-content">

                                        <div class="input-field my-3">
                                            <label for="name"> @lang('Room Name') </label>
                                            <input id="name" type="text" name="name" value="{{$dc->name}}" autocomplete="no">
                                        </div>

                                        <div class="input-field my-3">
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

                                        <div class="file-field input-field my-1">
                                            <div class="btn">
                                                <span> @lang('Thumbnail Image') </span>
                                                <input type="file" id="file" name="file">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text" value="{{$dc->thumbnail}}">
                                            </div>
                                        </div>

                                        <button type="submit" class="btn waves-effect waves-light my-2"> @lang('Update') </button>

                                        <p class="mt-3">
                                            <label>
                                                <input type="checkbox" name="send_email" id="send_email" {{$dc->send_email == 1 ? 'checked' : ''}} value="1">
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
    <script src="{{ asset('js/dc.js') }}"></script>
@endsection
