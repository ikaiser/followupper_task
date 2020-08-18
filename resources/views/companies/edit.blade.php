@extends('layouts.app')

@section('content')


    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('companies.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Companies')</a>
                <a href="#" class="pointer"> / {{$company->name}}</a>
                <a href="#" class="pointer"> / @lang('Edit') </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title"> @lang('Edit Company') </span>

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
                        <form method="post" action="{{ route('companies.update', [$company->id]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-content">
                                <div class="row">
                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <label for="name"> @lang('Company Name') </label>
                                            <input type="text" name="name" id="name" value="{{$company->name}}">
                                        </div>
                                    </div>
                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <label for="code"> @lang('Company Code') </label>
                                            <input type="text" name="code" id="code" value="{{$company->code}}">
                                        </div>
                                    </div>
                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <select name="type" id="type">
                                                <option value="" disabled hidden selected> @lang('Select a Type') </option>
                                                <option value="customer" {{$company->type == 'customer' ? 'selected' : ''}}> @lang('Customer') </option>
                                                <option value="institute" {{$company->type == 'institute' ? 'selected' : ''}}> @lang('Institute') </option>
                                            </select>
                                            <label for="type"> @lang('Company Type') </label>
                                        </div>
                                    </div>

                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <label for="contact"> @lang('Company Contact') </label>

                                            @foreach($company->contacts as $contact)
                                                <input type="text" class="mb-2" name="contact[]" value="{{$contact->name}}"/>
                                            @endforeach

                                            <button type="button" class="btn btn-small waves-effect waves-light" id="add_contact"> <i class="material-icons">add</i> </button>
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
    <script src="{{ asset('js/companies.js') }}"></script>
@endsection
