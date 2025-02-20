<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>@yield('title')</title>
        <meta name="description" content="Report Store">
        <meta name="author" content="{{ env('APP_SS') }}">
        <link href="/css/report.css" rel="stylesheet">
        @yield('css')
    </head>
    <body>
        <div class="content">
            <div style="padding-bottom: 0.3rem">
                <img style="padding-bottom: 4px;" src="/images/logo_pluma.jpg"
                width="120" alt="Logo {{ env('APP_SS') }}"><br>
            </div>

            @yield('content')

            <div class="pie_pagina seis center" style="height: 20px;">
                <span class="uppercase">{{ env('APP_SS') }}</span><br>
                {{ env('APP_SS_ADDRESS') }} -
                Fono: {{ env('APP_SS_TELEPHONE') }} -
                {{ env('APP_SS_WEBSITE') }}
            </div>
        </div>
    </body>
</html>
