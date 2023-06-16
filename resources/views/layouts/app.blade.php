<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ $title }}</title>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link rel="icon" type="image/x-icon" href="{{asset('assets/images/favicon.png')}}">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,400;0,700;1,100;1,700;1,900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
        <link href="{{asset('plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{asset('plugins/perfectscroll/perfect-scrollbar.css')}}" rel="stylesheet">
        <link href="{{asset('plugins/pace/pace.css')}}" rel="stylesheet">
        <link href="{{asset('plugins/highlight/styles/github-gist.css')}}" rel="stylesheet">
        <link href="{{asset('css/admin/main.css')}}" rel="stylesheet">
        <link href="{{asset('css/admin/style.css') }}" rel="stylesheet">
        <script src="{{asset('plugins/jquery/jquery-3.5.1.min.js')}}"></script>
        <!-- Mix Scripts -->
        <script src="{{ asset('js/admin/main.js') }}" defer></script>
        <style>
            .swal-footer {
                text-align: center !important;
            }
        </style>
    </head>
    
    <body>
        <x-sidebar></x-sidebar>
        {{ $slot }}
        {{-- <x-footer></x-footer> --}}
        <!-- Javascripts -->

        <script src="{{asset('plugins/bootstrap/js/popper.min.js')}}"></script>
        <script src="{{asset('plugins/bootstrap/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('plugins/perfectscroll/perfect-scrollbar.min.js')}}"></script>
        <script src="{{asset('plugins/pace/pace.min.js')}}"></script>
        <script src="{{asset('plugins/highlight/highlight.pack.js')}}"></script>
        <script src="{{asset('plugins/blockUI/jquery.blockUI.min.js')}}"></script>
        <script src="{{asset('js/custom.js')}}"></script>
        <script src="{{asset('js/pages/blockui.js')}}"></script>
        
    </body>

</html>