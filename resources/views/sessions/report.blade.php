<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report - {{ $session->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; }
            .print-container { box-shadow: none; border: none; width: 100%; max-width: 100%; }
        }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="p-4 md:p-12">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100 print-container">
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-gray-100 pb-8 mb-8">
            <div>
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">{{ $session->organisation->name ?? 'Orens Pro' }}</h1>
                <p class="text-gray-500 font-medium text-sm mt-1">Attendance Report Summary</p>
            </div>
            <div class="text-right">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-orange-50 text-orens rounded-full text-[10px] font-bold uppercase tracking-wider mb-2" style="color: #FF6B00;">
                    Official Report
                </div>
                <p class="text-xs text-gray-400 font-medium">{{ now()->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <!-- Session Details -->
        <div class="grid grid-cols-2 gap-8 mb-12">
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Session Title</label>
                <p class="text-lg font-bold text-gray-800">{{ $session->title }}</p>
                <p class="text-sm text-gray-500 font-medium mt-1">{{ $session->division->name ?? 'Global Division' }}</p>
            </div>
            <div class="text-right">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Schedule</label>
                <p class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($session->session_date)->format('d F Y') }}</p>
                <p class="text-sm text-gray-500 font-medium mt-1">{{ $session->start_time }} - {{ $session->end_time }}</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-4 gap-4 mb-12">
            @php
                $tags = [
                    'hadir' => ['label' => 'Total Present', 'color' => 'bg-green-50 text-green-600'],
                    'sakit' => ['label' => 'Sakit', 'color' => 'bg-blue-50 text-blue-600'],
                    'izin' => ['label' => 'Izin', 'color' => 'bg-yellow-50 text-yellow-600'],
                    'alpha' => ['label' => 'Alpha', 'color' => 'bg-red-50 text-red-600'],
                ];
            @endphp
            @foreach($tags as $status => $meta)
                <div class="{{ $meta['color'] }} p-4 rounded-2xl border border-current border-opacity-10">
                    <p class="text-[10px] font-bold uppercase tracking-wider opacity-60">{{ $meta['label'] }}</p>
                    <p class="text-2xl font-black mt-1">{{ $members->filter(fn($m) => ($attendances[$m->id]->status ?? 'alpha') === $status)->count() }}</p>
                </div>
            @endforeach
        </div>

        <!-- Attendance Table -->
        <div class="overflow-hidden border border-gray-100 rounded-2xl mb-12">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Member Name</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Check-in Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @foreach($members as $member)
                        @php $att = $attendances[$member->id] ?? null; @endphp
                        <tr>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $member->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $member->email }}</td>
                            <td class="px-6 py-4">
                                <span class="capitalize font-bold {{ ($att->status ?? 'alpha') === 'hadir' ? 'text-green-500' : ( ($att->status ?? 'alpha') === 'alpha' ? 'text-red-500' : 'text-orange-500' ) }}">
                                    {{ $att->status ?? 'Alpha' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400 font-medium">
                                {{ $att && $att->checkin_time ? \Carbon\Carbon::parse($att->checkin_time)->format('H:i:s') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-100 pt-8 flex justify-between items-center bg-gray-50/50 -mx-8 -mb-8 p-8 mt-auto">
            <p class="text-[10px] text-gray-400 font-medium">Generated by Orens Pro Management System</p>
            <div class="flex gap-4 no-print">
                <button onclick="window.print()" class="px-6 py-3 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 transition-all shadow-lg shadow-gray-200">
                    Print Report / Save as PDF
                </button>
                <a href="{{ route('sessions.index') }}" class="px-6 py-3 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto trigger print if needed
        // window.onload = () => window.print();
    </script>
</body>
</html>
