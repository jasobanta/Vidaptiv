<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('favicon/favicon-32x32.png') }}" type="image/x-icon">
        <script type="text/javascript">
            var base_url = "{!! url('/') !!}";
        </script>

        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons"   rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
        <!--Responsive Extension Datatables CSS-->
        <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">

        <!-- Scripts -->
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="{{ asset('js/jquery-ui.min.js') }}"></script>
        
        <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>

        <!--Datatables -->
        <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>

        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/edi_email.js') }}"></script>
        <script src="{{ asset('js/rules.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        @if (in_array(Route::currentRouteName(), ['show.file.action', 'edi.action-form']))
        <link rel="stylesheet" href="{{ asset('css/bootstrap-switch-button.css') }}">
        <script src="{{asset('js/bootstrap-switch-button.js')}}"></script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
