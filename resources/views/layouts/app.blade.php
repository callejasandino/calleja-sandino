<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Amy & Dave\'s Bakery') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div id="app">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/" class="text-indigo-600 font-bold text-2xl">
                                Amy & Dave's Bakery
                            </a>
                        </div>
                        
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="/" class="{{ request()->is('/') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Home
                            </a>
                            
                            <a href="/schedule" class="{{ request()->is('schedule') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Schedule a Visit
                            </a>
                        </div>
                    </div>
                    
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <a href="/admin" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Admin
                        </a>
                    </div>
                </div>
            </div>
        </header>
        
        <main>
            @yield('content')
        </main>
        
        <footer class="bg-white mt-12 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Amy & Dave's Bakery. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</body>
</html> 