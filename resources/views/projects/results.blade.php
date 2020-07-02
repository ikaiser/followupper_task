@extends('layouts.app')

@section('content')

    @include('projects/modal_remove')
    @include('projects/modal_search')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;@lang('Projects') </a>
                <a href="#" class="pointer">/ @lang('Search') </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <form method="POST" class="form-inline" action="{{ route('dce.search') }}?layout=table">
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
                <!-- <button class="btn btn-floating waves-effect waves-light" title="Cambia Layout"><i class="material-icons">format_list_bulleted</i></button> -->
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
                    <span class="card-title"> @lang('Search Results') </span>

                    <div class="row">
                        @foreach($files as $file)
                            <div class="col s12 l4 m4">
                                <div class="card-panel border-radius-6 mt-10 card-animation-1" style="height: 300px">

                                    @if(!is_null($file->thumbnail))
                                        <img class="responsive-img border-radius-8 z-depth-4 image-n-margin" style="height: 150px" src="{{Storage::url("project/{$file->project_id}/files/thumbnails/") . $file->thumbnail}}">
                                    @else
                                        <img class="responsive-img border-radius-8 z-depth-4 image-n-margin" style="height: 150px" src="https://via.placeholder.com/700x200?text={{urlencode($file->name)}}">
                                    @endif
                                    <div class="card-content">
                                        <p class="truncate" style="font-weight: bold" title="{{$file->description}}"> {{$file->description}}</p>
                                        <p><a href="{{route('dce.show', [$file->project_id, $file->id])}}" class="mt-5 truncate" title="{{$file->name}}">{{$file->name}}</a></p>
                                        <div class="row mt-3">
                                            <div class="col s6 p-0 mt-2 left-align">
                                                <span class="pt-2"> @lang('Uploaded:') {{date('d/m/Y', strtotime($file->created_at))}}</span>
                                            </div>
                                            <div class="col s6 mt-1 right-align">
                                                <span class="material-icons light-blue-text lighten-2 ml-10">chat_bubble_outline</span>
                                                <span class="ml-3 vertical-align-top">{{$file->comments->count()}}</span>
                                                @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                                                    <span class="material-icons light-blue-text lighten-2 ml-10 pointer" onclick="document.location.href='{{route('dce.edit', [$file->project_id, $file->id])}}'" >edit</span>
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

@endsection

@section('js')
    @parent
    <script src="{{ asset('js/projects.js')}}"></script>
    <script src="{{ asset('js/search.js') }}"></script>

@endsection
