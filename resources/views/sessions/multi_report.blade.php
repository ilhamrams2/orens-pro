@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Laporan Kehadiran Kumulatif</h1>
            <p class="text-gray-600">Berdasarkan {{ $sessions->count() }} sesi yang dipilih</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="btn-premium px-6 py-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                </svg>
                Cetak PDF
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($divisionStats as $name => $stats)
        <div class="glass-card p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ $name }}</h3>
            <div class="flex items-end gap-2 mt-2">
                <span class="text-3xl font-bold text-primary">{{ $stats['percentage'] }}%</span>
                <span class="text-sm text-gray-500 mb-1">Kehadiran</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                <div class="bg-primary h-2 rounded-full" style="width: {{ $stats['percentage'] }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $stats['present'] }} dari {{ $stats['total'] }} total kehadiran</p>
        </div>
        @endforeach
    </div>

    <!-- Detailed Table -->
    <div class="glass-card overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-bold">Detail Kehadiran Anggota</h2>
            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-semibold">
                {{ count($reportData) }} Anggota Terdaftar
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Anggota</th>
                        <th class="px-6 py-4 font-semibold">Divisi</th>
                        <th class="px-6 py-4 font-semibold text-center">Hadir</th>
                        <th class="px-6 py-4 font-semibold text-center">Total Sesi</th>
                        <th class="px-6 py-4 font-semibold text-right">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reportData as $data)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $data['name'] }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                {{ $data['division'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-primary font-bold">{{ $data['present'] }}</td>
                        <td class="px-6 py-4 text-center text-gray-500">{{ $data['total'] }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <div class="w-24 bg-gray-100 rounded-full h-1.5 hidden md:block">
                                    <div class="bg-primary h-1.5 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                                <span class="font-bold {{ $data['percentage'] < 50 ? 'text-red-500' : ($data['percentage'] < 80 ? 'text-orange-500' : 'text-green-500') }}">
                                    {{ $data['percentage'] }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Selected Sessions Info -->
    <div class="mt-8">
        <h3 class="text-lg font-bold mb-4">Sesi yang Disertakan:</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($sessions as $session)
            <div class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm shadow-sm">
                <div class="font-bold text-gray-800">{{ $session->title }}</div>
                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    @media print {
        .btn-premium, .navbar, footer { display: none !important; }
        .glass-card { border: 1px solid #eee !important; box-shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection
