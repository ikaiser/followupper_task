@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
              <a href="#" class="pointer">Home</a>
              <a onclick="document.location.href='{{ route('activities.index') }}'" class="pointer">/&nbsp;@lang('Activities')</a>
              <a href="#" class="pointer">@Lang('Create')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title"> @lang('Add activity') </span>

            @include("partials/flashdata")

            <form method="post" action="{{ route('activities.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">

                  <div class="col l12 s12">
                    <div class="input-field my-3">
                        <label for="name"> @lang('Name') </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="no">
                    </div>
                  </div>

                  <div class="col l12 s12">
                    <div class="input-field my-3">
                        <label for="description"> @lang('Description') </label>
                        <textarea name="description" id="description" class="materialize-textarea">{{ old('description') }}</textarea>
                    </div>
                  </div>

                </div>

                <button type="submit" class="btn waves-effect waves-light" > @lang('Save') </button>
            </form>
        </div>
    </div>

@endsection

@section('js')
    @parent
@endsection
