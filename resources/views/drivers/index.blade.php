@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white">Drivers</h1>
        <p class="mt-2 text-gray-400">{{ date('Y') }} Formula 1 Driver Lineup</p>
    </div>

    <!-- Drivers Grid -->
    @if($drivers->isEmpty())
        <div class="bg-gray-800 rounded-lg p-12 text-center">
            <p class="text-gray-400 text-lg">No drivers available yet.</p>
            <p class="text-gray-500 text-sm mt-2">Sync a session to import driver data.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($drivers as $driver)
                <a href="{{ route('drivers.show', $driver) }}" class="block bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg shadow-lg overflow-hidden hover:shadow-red-600/20 hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="p-6">
                        <!-- Driver Number & Code -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="text-5xl font-bold text-gray-700">
                                {{ $driver->openf1_driver_number }}
                            </div>
                            <div class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded">
                                {{ $driver->code }}
                            </div>
                        </div>

                        <!-- Driver Name -->
                        <h3 class="text-xl font-bold text-white mb-2">
                            {{ $driver->full_name }}
                        </h3>

                        <!-- Constructor -->
                        @if($driver->constructor)
                            <div class="flex items-center space-x-2 mt-3">
                                @if($driver->constructor->color_hex)
                                    <div class="w-3 h-3 rounded-full" style="background-color: #{{ $driver->constructor->color_hex }}"></div>
                                @endif
                                <span class="text-gray-400 text-sm">
                                    {{ $driver->constructor->name }}
                                </span>
                            </div>
                        @endif

                        <!-- Nationality -->
                        @if($driver->nationality)
                            <div class="text-gray-500 text-xs mt-2">
                                {{ $driver->nationality }}
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
