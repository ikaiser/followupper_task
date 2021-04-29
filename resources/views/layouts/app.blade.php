<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- BEGIN: Head-->

<head>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- BEGIN: VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/vendors.min.css') }}">
    <!-- END: VENDOR CSS-->

    <!-- BEGIN: Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/css/themes/vertical-dark-menu-template/materialize.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/css/themes/vertical-dark-menu-template/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/css/pages/dashboard.css') }}">
    <!-- END: Page Level CSS-->

    <!-- BEGIN: DATATABLE CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/flag-icon/css/flag-icon.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/data-tables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/vendors/data-tables/css/select.dataTables.min.css') }}">
    <!-- END: DATATABLE CSS -->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/css/custom/custom.css') }}">
    <!-- END: Custom CSS-->

    @section('css') @show
</head>
<!-- END: Head-->

<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns   " data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">

    @if (Auth::check()) @include('template.topbar') @endif

    @if (Auth::check()) @include('template.sidebar') @endif

    <div id="main">
        <div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="section">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('template.footer')

    <!-- BEGIN VENDOR JS-->
    <script src="{{ asset('templates/js/vendors.min.js') }}"></script>
    <!-- BEGIN VENDOR JS-->

    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{ asset('templates/vendors/chartjs/chart.min.js') }}"></script>
    <!-- END PAGE VENDOR JS-->

    <!-- BEGIN THEME  JS-->
    <script src="{{ asset('templates/js/plugins.js') }}"></script>
    <script src="{{ asset('templates/js/search.js') }}"></script>
    <script src="{{ asset('templates/js/custom/custom-script.js') }}"></script>
    <!-- END THEME  JS-->

    <!-- BEGIN PAGE LEVEL JS-->
    <!-- <script src="{{ asset('templates/js/scripts/dashboard-ecommerce.js') }}"></script> -->
    <script src="{{ asset('templates/js/scripts/ui-alerts.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap_file.js') }}"></script>
    <!-- END PAGE LEVEL JS-->

    <!-- BEGIN DATATABLE JS -->
    <script src="{{asset('templates/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('templates/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('templates/vendors/data-tables/js/dataTables.select.min.js')}}"></script>
    <!-- END DATATABLE JS -->

    @section('js') @show
    <script>
        $(document).ready(function () {
            $('.dropdown-trigger').dropdown();
            $('.modal').modal();

            $('.datepicker').datepicker({
                'format' : 'dd-mm-yyyy',
            });

            $('select').formSelect({});
            bsCustomFileInput.init();

            $('a.lang-button').click(function () {
                var lang = $(this).attr('data-lang');

                $.ajax({
                    url:    '/locale',
                    type:   'POST',
                    data:   ({
                        '_token' : $('[name="csrf-token"]').val(),
                        'lang': lang
                    }),
                    success: function (data) {
                        location.reload();
                    }
                });
            });
        })
    </script>
</body>
</html>
