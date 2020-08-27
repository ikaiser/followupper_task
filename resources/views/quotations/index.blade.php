@extends('layouts.app')

@section('content')

    @include('quotations/modal-remove')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('quotations.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Quotations')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 2)
                <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('quotations.export') }}'" role="button" title="Download"><i class="material-icons">file_download</i></button>
            @endif
            <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('quotations.create') }}'" role="button" title="Aggiungi"><i class="material-icons">add</i></button>
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
                    <span class="card-title"> @lang('Quotations') </span>

                    <table class="table-responsive highlight stripe" id="quotations_table">
                        <thead>
                        <tr>
                            <th> Id </th>
                            <th> @lang('Name') </th>
                            <th> @lang('Sequential Number') </th>
                            <th> @lang('User') </th>
                            <th> @lang('Company') </th>
                            <th> @lang('Actions') </th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($quotations as $quotation)
                            <tr>
                                <td>{{get_code($quotation)}}</td>
                                <td>{{$quotation->name}}</td>
                                <td>{{$quotation->sequential_number}}</td>
                                <td>{{$quotation->user->name}}</td>
                                <td>{{$quotation->company->name}}</td>
                                <td>
                                    <a class="mx-1" href="{{ route('quotations.edit', $quotation->id) }}"> @lang('Edit') </a>
                                    <a name="quotation_remove" class="mx-1 modal-trigger" href="#quotation_remove_modal" data-id="{{$quotation->id}}"> @lang('Delete') </a>
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
