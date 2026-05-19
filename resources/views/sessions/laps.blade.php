@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-gray-400 text-sm mb-2">
            <a href="{{ route('races.show', $session->race) }}" class="hover:text-white transition">
                {{ $session->race->name }}
            </a>
            <span>/</span>
            <span class="text-white">{{ $session->type->label() }}</span>
        </div>
        <h1 class="text-4xl font-bold text-white">Lap Times</h1>
        <p class="mt-2 text-gray-400">{{ $session->starts_at->format('F d, Y - H:i') }}</p>
    </div>

    <!-- Lap Times Table -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">Fastest Laps</h2>
        </div>

        <div class="overflow-x-auto">
            @if($fastestLaps->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-gray-400 text-lg">No lap data available yet.</p>
                    <p class="text-gray-500 text-sm mt-2">Sync lap data for this session to see timings.</p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Pos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Driver
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Team
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Lap Time
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Sector 1
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Sector 2
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Sector 3
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @foreach($fastestLaps as $index => $lap)
                            <tr class="hover:bg-gray-700 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold {{ $index === 0 ? 'text-yellow-500' : ($index <= 2 ? 'text-gray-300' : 'text-gray-400') }}">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-red-500 mr-3">
                                            {{ $lap->driver->openf1_driver_number }}
                                        </div>
                                        <a href="{{ route('drivers.show', $lap->driver) }}" class="text-sm text-white hover:text-red-500 transition">
                                            {{ $lap->driver->full_name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        @if($lap->driver->constructor?->color_hex)
                                            <div class="w-3 h-3 rounded-full" style="background-color: #{{ $lap->driver->constructor->color_hex }}"></div>
                                        @endif
                                        <span class="text-sm text-gray-400">
                                            {{ $lap->driver->constructor?->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-bold {{ $index === 0 ? 'text-yellow-500' : 'text-white' }}">
                                        {{ $lap->lap_duration ? number_format($lap->lap_duration, 3) . 's' : 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-400">
                                        {{ $lap->sector_1 ? number_format($lap->sector_1, 3) . 's' : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-400">
                                        {{ $lap->sector_2 ? number_format($lap->sector_2, 3) . 's' : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-400">
                                        {{ $lap->sector_3 ? number_format($lap->sector_3, 3) . 's' : '-' }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
