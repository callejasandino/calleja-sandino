<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Prevent caching of this page -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Admin - {{ config('app.name', 'Amy & Dave\'s Bakery') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Prevent back button after login -->
    <script type="text/javascript">
        window.onload = function() {
            // Disable browser back button
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function() {
                history.pushState(null, null, document.URL);
            });
        };
    </script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div id="app" class="min-h-screen bg-gray-100">
        <nav class="bg-indigo-800 border-b border-indigo-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/admin" class="text-white font-bold text-xl">
                                Bakery Admin
                            </a>
                        </div>
                        
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="/admin" class="{{ request()->is('admin') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            
                            <a href="/admin/opening-hours" class="{{ request()->is('admin/opening-hours') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Opening Hours
                            </a>
                            
                            <a href="/admin/appointments" class="{{ request()->is('admin/appointments') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Appointments
                            </a>
                        </div>
                    </div>
                    
                    <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-700 hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Logout
                            </button>
                        </form>
                        
                        <a href="/" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Back to Site
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900">
                    @yield('header')
                </h1>
            </div>
        </header>
        
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html> 