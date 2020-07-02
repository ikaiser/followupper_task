<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- BEGIN: Head-->

<head>
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

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/css/custom/custom.css') }}">
    <!-- END: Custom CSS-->

    @section('css') @show

</head>
<!-- END: Head-->

<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns   " data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">

@if (Auth::check()) @include('template.topbar') @endif

@if (Auth::check()) @include('template.sidebar') @endif

<div class="row">
    <div class="col s12">
        <div class="container">
            <div class="section">
                @yield('content')
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
<script src="{{ asset('js/custom.js') }}"></script>

<!-- END PAGE LEVEL JS-->

@section('js') @show
<script>
    $(document).ready(function () {
        $('.dropdown-trigger').dropdown();
        $('.modal').modal();

        $('select').formSelect();
        bsCustomFileInput.init();

        $('a.lang-button').click(function () {
            var lang = $(this).attr('data-lang');

            $.ajax({
                url:    'locale',
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
