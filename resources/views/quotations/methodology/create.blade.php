@extends('layouts.app')

@section('content')


    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('quotations.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Quotations')</a>
                <a onclick="document.location.href='{{ route('quotations_methodology.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Methodology')</a>
                <a href="#" class="pointer"> / @lang('New Methodology') </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title"> @lang('Add Methodology') </span>

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
                        <form method="post" action="{{ route('quotations_methodology.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-content">
                                <div class="row">
                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <label for="name"> @lang('Name') </label>
                                            <input type="text" name="name" value="{{old('name')}}">
                                        </div>
                                    </div>
                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <label for="type"> @lang('Type') </label>
                                            <input type="text" name="type" value="{{old('type')}}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn waves-effect waves-light mt-3"> @lang('Add') </button>
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
