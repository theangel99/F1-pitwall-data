<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'PitWall' }} - Formula 1 Data</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* F1 Theme Colors */
        :root {
            --f1-red: #E10600;
            --f1-dark: #15151E;
            --f1-gray: #38383F;
        }

        body {
            background-color: var(--f1-dark);
            color: #FFFFFF;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-black border-b border-red-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-red-600">
                                🏎️ PitWall
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-10 sm:flex sm:space-x-8">
                            <a href="{{ route('home') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-red-600 text-white' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-300' }} text-sm font-medium">
                                Standings
                            </a>

                            <a href="{{ route('races.index') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('races.*') ? 'border-red-600 text-white' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-300' }} text-sm font-medium">
                                Races
                            </a>

                            <a href="{{ route('drivers.index') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('drivers.*') ? 'border-red-600 text-white' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-300' }} text-sm font-medium">
                                Drivers
                            </a>
                        </div>
                    </div>

                    <!-- Season Year -->
                    <div class="flex items-center">
                        <span class="text-gray-400 text-sm">Season {{ date('Y') }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-black border-t border-gray-800 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="text-center text-gray-500 text-sm">
                    <p>Data provided by <a href="https://openf1.org" target="_blank" class="text-red-600 hover:text-red-500">OpenF1 API</a></p>
                    <p class="mt-2">Built with Laravel 13 &amp; Alpine.js</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
