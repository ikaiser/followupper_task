@extends('layouts.app')

@section('content')

    @include('quotations/methodology/modal-remove')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('quotations.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Quotations')</a>
                <a onclick="document.location.href='{{ route('quotations_methodology.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Methodology')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('quotations_methodology.create') }}'" role="button" title="Aggiungi"><i class="material-icons">add</i></button>
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
                    <span class="card-title"> @lang('Methodology') </span>

                    <table class="table-responsive highlight stripe" id="quotation_methodologies_table">
                        <thead>
                        <tr>
                            <th> Id </th>
                            <th> @lang('Name') </th>
                            <th> @lang('Type') </th>
                            <th> @lang('Actions') </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($methodologies as $methodology)
                            <tr>
                                <td>{{$methodology->id}}</td>
                                <td>{{$methodology->name}}</td>
                                <td>{{$methodology->type}}</td>
                                <td>
                                    <a href="{{ route('quotations_methodology.edit', $methodology->id) }}"> @lang('Edit') </a>
                                    <a name="element_remove" class="mx-1 modal-trigger" href="#remove_modal" data-id="{{$methodology->id}}" data-type="quotation_methodology"> @lang('Delete') </a>
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
    <script src="{{ asset('js/quotations.js') }}"></script>
@endsection
