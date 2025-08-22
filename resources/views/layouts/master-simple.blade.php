<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Boleto')</title>
    <!-- CSS do AdminLTE -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte_custom.css?v=6') }}"> --}}
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/boleto.css?v=1') }}">
    <style>
        .row {
            display: block !important;
            /* força comportamento antigo */
            width: 100%;
            clear: both;
        }

        [class*="col-"] {
            float: left !important;
            min-height: 1px;
        }

        .col-1 {
            width: 8.33%;
        }

        .col-2 {
            width: 16.66%;
        }

        .col-3 {
            width: 25%;
        }

        .col-4 {
            width: 33.33%;
        }

        .col-5 {
            width: 41.66%;
        }

        .col-6 {
            width: 50%;
        }

        .col-7 {
            width: 58.33%;
        }

        .col-8 {
            width: 66.66%;
        }

        .col-9 {
            width: 75%;
        }

        .col-10 {
            width: 83.33%;
        }

        .col-11 {
            width: 91.66%;
        }

        .col-12 {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="" style="min-height: 346px;">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                {!! isset($title_page) ? '<h1 id="title-page">' . $title_page . '</h1>' : '' !!}
            </section>
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>
    </div>
    <!-- JS do AdminLTE -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
</body>

</html>
