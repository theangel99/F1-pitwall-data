@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white">Race Calendar</h1>
        <p class="mt-2 text-gray-400">{{ date('Y') }} Formula 1 World Championship</p>
    </div>

    <!-- Upcoming Races -->
    @if($upcomingRaces->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-4">Upcoming Races</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingRaces as $race)
                    <a href="{{ route('races.show', $race) }}" class="block bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg shadow-lg overflow-hidden hover:shadow-red-600/20 hover:shadow-2xl transition transform hover:-translate-y-1">
                        <div class="p-6">
                            <div class="text-red-500 text-sm font-semibold mb-2">
                                {{ $race->date->format('M d, Y') }}
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">
                                {{ $race->name }}
                            </h3>
                            <div class="text-gray-400 text-sm">
                                {{ $race->circuit }}, {{ $race->country }}
                            </div>
                            @if($race->round_number)
                                <div class="mt-4 inline-block bg-red-600 text-white text-xs px-3 py-1 rounded-full">
                                    Round {{ $race->round_number }}
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Past Races -->
    @if($pastRaces->isNotEmpty())
        <div>
            <h2 class="text-2xl font-bold text-white mb-4">Past Races</h2>
            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="divide-y divide-gray-700">
                    @foreach($pastRaces as $race)
                        <a href="{{ route('races.show', $race) }}" class="block p-6 hover:bg-gray-700 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        @if($race->round_number)
                                            <div class="text-gray-400 font-bold text-lg">
                                                R{{ $race->round_number }}
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-lg font-semibold text-white">
                                                {{ $race->name }}
                                            </h3>
                                            <div class="text-gray-400 text-sm">
                                                {{ $race->circuit }}, {{ $race->country }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-gray-400 text-sm">
                                        {{ $race->date->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if($upcomingRaces->isEmpty() && $pastRaces->isEmpty())
        <div class="bg-gray-800 rounded-lg p-12 text-center">
            <p class="text-gray-400 text-lg">No races available yet.</p>
            <p class="text-gray-500 text-sm mt-2">Run <code class="bg-gray-900 px-2 py-1 rounded">php artisan openf1:sync meetings</code> to sync race data.</p>
        </div>
    @endif
</div>
@endsection
