<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <x-head>
        </x-head>
        <!-- Scripts -->
        @routes
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    {{-- <body class="font-sans antialiased">

    </body> --}}
    <body class="m-0 font-sans antialiased font-normal dark:bg-slate-900 text-sm leading-default bg-gray-50 text-slate-600">
        <x-scripts>
        </x-scripts>
        <div class="min-h-screen bg-gray-100">
            @inertia
        </div>
    </body>
</html>
