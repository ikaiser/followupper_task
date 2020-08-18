@extends('layouts.app')

@section('content')

    @include('companies/modal-remove')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('companies.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Companies')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('companies.create') }}'" role="button" title="Aggiungi"><i class="material-icons">add</i></button>
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
                    <span class="card-title"> @lang('Companies') </span>

                    <table class="table-responsive highlight stripe" id="quotations_table">
                        <thead>
                        <tr>
                            <th> @lang('Name') </th>
                            <th> @lang('Code') </th>
                            <th> @lang('Type') </th>
                            <th> @lang('Actions') </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td>{{$company->name}}</td>
                                <td>{{$company->code}}</td>
                                <td>{{$company->type}}</td>
                                <td>
                                    <a href="{{ route('companies.edit', $company->id) }}"> @lang('Edit') </a>
                                    <a name="company_remove" class="mx-1 modal-trigger" href="#company_remove_modal" data-id="{{$company->id}}"> @lang('Delete') </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @parent
    <script src="{{ asset('js/companies.js') }}"></script>
    <script src="{{ asset('js/quotations.js') }}"></script>
@endsection
