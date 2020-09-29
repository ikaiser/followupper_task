@extends('layouts.app')

@section('content')

    <div class="row" id="breadcrumb_row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('quotations.index') }}'" class="pointer">&nbsp;/&nbsp;Reports </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
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
        @csrf
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title"> Report </span>

                    <table class="table-responsive highlight stripe">
                        <thead>
                        <tr>
                            <th> @lang('Name') </th>
                            <th>  </th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Report Admin </th>
                                <th class="center-align"> <button class="btn btn-primary generate_report" data-report="admin"> Genera Report </button> </th>
                            </tr>
                            <tr>
                                <th>Report Scadenze </th>
                                <th class="center-align"> <button class="btn btn-primary generate_report" data-report="deadlines"> Genera Report </button> </th>
                            </tr>
                            <tr>
                                <th>Report Importi Mancanti </th>
                                <th class="center-align"> <button class="btn btn-primary generate_report" data-report="amounts"> Genera Report </button> </th>
                            </tr>
                            <tr>
                                <th>Report giornaliero status A1 ( Ricercatori, utenti, operatori )</th>
                                <th class="center-align"> <button class="btn btn-primary generate_report" data-report="status_a1"> Genera Report </button> </th>
                            </tr>
                            <tr>
                                <th>Report settimanale status B1 ( Ricercatori, utenti, operatori )</th>
                                <th class="center-align"> <button class="btn btn-primary generate_report" data-report="status_b1"> Genera Report </button> </th>
                            </tr>
                            <tr>
                                <th>Report importi mancanti per status diverso da A1 ( Ricercatori, utenti, operatori )</th>
                                <th class="center-align"> <button class="btn btn-primary generate_report" data-report="operators_amount"> Genera Report </button> </th>
                            </tr>
                            <tr>
                                <th>Report preventivi non chiusi ( Ricercatori, utenti, operatori )</th>
                                <th class="center-align"> <button class="btn btn-primary generate_report" data-report="operators_not_delivered"> Genera Report </button> </th>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @parent

    <script>
        $('.generate_report').click(function() {
           var report = $(this).attr('data-report');

            $.ajax({
                url:"/quotations/report",
                method:"POST",
                data:{
                    _token : $('input[name="_token"]').val(),
                    report:report
                },
                success:function(){
                    $('.report-alert-success').remove();
                    $('#breadcrumb_row').after('<div class="report-alert-success card-alert card green lighten-5"> <div class="card-content green-text"> <p> Report Inviato </p> </div> <button type="button" class="close green-text" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button> </div>');
                }
            });
        });
    </script>

@endsection
