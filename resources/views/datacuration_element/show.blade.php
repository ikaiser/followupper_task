@extends('layouts.app')

@section('content')

    @include('datacuration_element/modal_remove')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('projects.index') }}'" class="pointer">/&nbsp;{{$project->name}}</a>
                <a onclick="document.location.href='{{ route('dc.index', $project->id) }}'" class="pointer"> / @lang('Data Curation') </a>
                <a href="#" class="pointer">&nbsp;/ {{$file->name}} </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('dce.edit', [$project->id, $file->id]) }}'"><i class="material-icons">edit</i></button>
                <button class="btn btn-floating waves-effect waves-light red modal-trigger" id="remove_file" data-id="{{$file->id}}" data-target="file_remove_modal"><i class="material-icons">remove</i></button>
            @endif
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
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
            <input type="hidden" id="file_id" value="{{$file->id}}" />

            <div class="card">
                <div class="card-content">
                    <span class="card-title"> @lang('File') </span>
                    <div class="row">
                        <div class="col l6 offset-l6 s12 left-align">
                            <span> File In: </span>
                            <ul>
                                @foreach($rooms as $room)
                                    <li>
                                        {{$room}}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col l12">
                            @if(!is_null($file->thumbnail))
                                <img class="responsive-img" src="{{Storage::url("project/{$project->id}/files/thumbnails/") . $file->thumbnail}}">
                            @endif
                            <p class="my-4">
                                {!! nl2br(e($file->topic)) !!}
                            </p>
                            <a class="btn waves-effect waves-light mb-4" href="{{Storage::url("project/{$project->id}/files/") . $file->name}}" target="_blank" download> @lang('Download File') </a>
                            <a class="btn waves-effect waves-light mb-4" href="{{Storage::url("project/{$project->id}/files/") . $file->name}}" target="_blank"> @lang('Open in Another Tab') </a>

                            <div class="card-action">
                                @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 5)
                                    <span class="card-title"> @lang('Add Comment:') </span>
                                    <div class="input-field">
                                        @csrf
                                        <label for="comment"> @lang('Comment') </label>
                                        <textarea name="comment" id="comment" class="materialize-textarea" rows="3"></textarea>
                                    </div>
                                    <button id="add_comment" class="btn waves-effect waves-light"> @lang('Add') </button>
                                @endif
                            </div>

                            <div id="comment_div">
                                @foreach($comments as $comment)
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="row" name="comment_row">
                                                <div class="col s3 l1" style="width: auto">
                                                    @if(!is_null($comment->user->user_img))
                                                        <div class="circle" style="background-image: url('{{ Storage::url("users/") . $comment->user->user_img}}'); height: 50px; width: 50px; background-position: center;background-size: cover; background-repeat: no-repeat;"></div>
                                                    @endif
                                                </div>
                                                <div class="col s8 l11 left-align" style="padding-left: 0">
                                                    <span class="black-text"> {{$comment->user->name}} </span> <br>
                                                    <p class="mt-4">{{$comment->comment}}</p>
                                                </div>
                                            </div>
                                            <div class="row valign-wrapper">
                                                <div class="col s12 mt-3 ml-6" style="padding-left: 0">
                                                    @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4)
                                                        <button name="edit_comment" data-id="{{$comment->id}}" class="btn btn-small waves-effect waves-light m-1" title="Modifica Commento"> @lang('Edit') </button>
                                                    @endif
                                                    @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 5)
                                                        <button name="reply_comment" data-id="{{$comment->id}}" class="btn btn-small waves-effect waves-light m-1" title="Rimuovi Commento"> @lang('Reply') </button>
                                                    @endif
                                                    @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id < 4 || \Illuminate\Support\Facades\Auth::user()->id == $comment->user->id)
                                                        <button name="remove_comment" data-id="{{$comment->id}}" class="btn btn-small waves-effect waves-light red m-1" title="Rimuovi Commento"> @lang('Remove') </button>
                                                    @endif
                                                    {{get_comment_childrens($comment)}}
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
    </div>

@endsection

@section('js')
    @parent
    <script src="{{ asset('js/dce.js') }}"></script>
@endsection
