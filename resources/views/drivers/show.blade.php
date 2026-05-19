@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Driver Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-lg shadow-lg p-8 mb-8">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center space-x-4 mb-4">
                    <div class="text-6xl font-bold text-gray-700">
                        {{ $driver->openf1_driver_number }}
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-white">
                            {{ $driver->full_name }}
                        </h1>
                        <div class="text-gray-400 mt-1">
                            {{ $driver->nationality ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                @if($driver->constructor)
                    <div class="flex items-center space-x-3 mt-4">
                        @if($driver->constructor->color_hex)
                            <div class="w-4 h-4 rounded-full" style="background-color: #{{ $driver->constructor->color_hex }}"></div>
                        @endif
                        <span class="text-white font-semibold">
                            {{ $driver->constructor->name }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="bg-red-600 text-white text-xl font-bold px-6 py-3 rounded">
                {{ $driver->code }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Race Results -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Recent Results</h2>
            </div>

            <div class="p-6">
                @if($driver->raceResults->isEmpty())
                    <p class="text-gray-400 text-center py-8">No race results available yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($driver->raceResults as $result)
                            <div class="bg-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <a href="{{ route('races.show', $result->race) }}" class="text-white font-semibold hover:text-red-500 transition">
                                            {{ $result->race->name }}
                                        </a>
                                        <div class="text-sm text-gray-400">
                                            {{ $result->race->date->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold {{ $result->position == 1 ? 'text-yellow-500' : ($result->position <= 3 ? 'text-gray-300' : 'text-gray-400') }}">
                                            P{{ $result->position ?? 'DNF' }}
                                        </div>
                                        <div class="text-sm text-red-500 font-semibold">
                                            {{ $result->points }} pts
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Statistics</h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-white">
                            {{ $driver->raceResults->count() }}
                        </div>
                        <div class="text-gray-400 text-sm mt-1">Races</div>
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-yellow-500">
                            {{ $driver->raceResults->where('position', 1)->count() }}
                        </div>
                        <div class="text-gray-400 text-sm mt-1">Wins</div>
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-gray-300">
                            {{ $driver->raceResults->whereIn('position', [1, 2, 3])->count() }}
                        </div>
                        <div class="text-gray-400 text-sm mt-1">Podiums</div>
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-red-500">
                            {{ $driver->raceResults->sum('points') }}
                        </div>
                        <div class="text-gray-400 text-sm mt-1">Total Points</div>
                    </div>
                </div>

                @if($driver->laps->isNotEmpty())
                    <div class="mt-6 bg-gray-700 rounded-lg p-4">
                        <div class="text-gray-400 text-sm mb-2">Recent Laps</div>
                        <div class="text-white font-semibold">
                            {{ $driver->laps->count() }} laps recorded
                        </div>
                        @php
                            $fastestLap = $driver->laps->where('is_pit_out_lap', false)->whereNotNull('lap_duration')->sortBy('lap_duration')->first();
                        @endphp
                        @if($fastestLap)
                            <div class="text-sm text-red-500 mt-2">
                                Fastest: {{ number_format($fastestLap->lap_duration, 3) }}s
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
