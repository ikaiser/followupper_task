@extends('layouts.app')

@section('content')


    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('quotations.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Quotations')</a>
                <a onclick="document.location.href='{{ route('quotations_status.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Status')</a>
                <a href="#" class="pointer"> / @lang('Edit Status') </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title"> @lang('Edit Status') </span>

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
                        <form method="post" action="{{ route('quotations_status.update', [$status->id]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-content">
                                <div class="row">
                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <label for="status"> @lang('Status') </label>
                                            <input type="text" name="status" value="{{$status->name}}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn waves-effect waves-light mt-3"> @lang('Update') </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @parent
@endsection
