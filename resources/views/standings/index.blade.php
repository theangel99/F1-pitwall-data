@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white">{{ $currentSeason->year }} Championship Standings</h1>
        <p class="mt-2 text-gray-400">Current season rankings and statistics</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Driver Standings -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Driver Standings</h2>
            </div>

            <div class="p-6">
                @if($driverStandings->isEmpty())
                    <p class="text-gray-400 text-center py-8">No driver standings available yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($driverStandings as $index => $standing)
                            <div class="flex items-center justify-between bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="text-2xl font-bold text-gray-400 w-8">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <a href="{{ route('drivers.show', $standing['driver']) }}" class="text-white font-semibold hover:text-red-500 transition">
                                            {{ $standing['driver']->full_name }}
                                        </a>
                                        <div class="text-sm text-gray-400">
                                            {{ $standing['driver']->constructor?->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-red-500">
                                        {{ $standing['points'] }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $standing['wins'] }} wins
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Constructor Standings -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Constructor Standings</h2>
            </div>

            <div class="p-6">
                @if($constructorStandings->isEmpty())
                    <p class="text-gray-400 text-center py-8">No constructor standings available yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($constructorStandings as $index => $standing)
                            <div class="flex items-center justify-between bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="text-2xl font-bold text-gray-400 w-8">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        @if($standing['constructor']->color_hex)
                                            <div class="w-4 h-4 rounded-full" style="background-color: #{{ $standing['constructor']->color_hex }}"></div>
                                        @endif
                                        <div>
                                            <div class="text-white font-semibold">
                                                {{ $standing['constructor']->name }}
                                            </div>
                                            <div class="text-sm text-gray-400">
                                                {{ $standing['wins'] }} wins
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-2xl font-bold text-red-500">
                                    {{ $standing['points'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
