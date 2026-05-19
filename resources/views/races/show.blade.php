@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white">{{ $race->name }}</h1>
                <p class="mt-2 text-gray-400">{{ $race->circuit }}, {{ $race->country }}</p>
            </div>
            <div class="text-right">
                <div class="text-red-500 text-sm font-semibold">
                    {{ $race->date->format('F d, Y') }}
                </div>
                @if($race->round_number)
                    <div class="text-gray-400 text-sm mt-1">
                        Round {{ $race->round_number }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sessions -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white">Sessions</h2>
                </div>

                <div class="p-6">
                    @if($race->sessions->isEmpty())
                        <p class="text-gray-400 text-center py-8">No sessions available yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($race->sessions->sortBy('starts_at') as $session)
                                <div class="bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-white font-semibold">
                                                {{ $session->type->label() }}
                                            </h3>
                                            <div class="text-gray-400 text-sm">
                                                {{ $session->starts_at->format('M d, Y - H:i') }}
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('sessions.laps', $session) }}" class="inline-block bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded transition">
                                                View Laps
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Race Results -->
        <div class="lg:col-span-1">
            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Race Results</h2>
                </div>

                <div class="p-6">
                    @if($race->raceResults->isEmpty())
                        <p class="text-gray-400 text-sm text-center py-8">Results not available yet.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($race->raceResults->sortBy('position') as $result)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-gray-400 font-bold w-6">
                                            {{ $result->position ?? 'DNF' }}
                                        </div>
                                        <div>
                                            <a href="{{ route('drivers.show', $result->driver) }}" class="text-white text-sm hover:text-red-500 transition">
                                                {{ $result->driver->full_name }}
                                            </a>
                                            <div class="text-xs text-gray-500">
                                                {{ $result->driver->constructor?->name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-red-500 font-bold text-sm">
                                        {{ $result->points }} pts
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
