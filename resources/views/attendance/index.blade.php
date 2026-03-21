@extends('layouts.dashboard')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 font-outfit">My Attendance History</h1>
            <p class="text-gray-500 font-medium">Track your participation across all sessions.</p>
        </div>
        <div class="px-6 py-3 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 bg-orens/10 text-orens rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Sessions</p>
                <p class="text-lg font-bold text-gray-800">{{ $attendances->total() }}</p>
            </div>
        </div>
    </div>

    <!-- History Card -->
    <div class="bg-white rounded-[40px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                        <th class="px-8 py-5">Session Details</th>
                        <th class="px-8 py-5">Date & Time</th>
                        <th class="px-8 py-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendances as $item)
                        <tr class="hover:bg-gray-25/50 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 transition-all group-hover:bg-orens/10 group-hover:text-orens">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-700 block transition-all group-hover:text-gray-900">{{ $item->session->title ?? 'Untitled' }}</span>
                                        <span class="text-xs text-gray-400 font-medium">{{ $item->session->division->name ?? 'Global' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-gray-600 block">{{ $item->session->session_date ?? '-' }}</span>
                                <span class="text-xs text-gray-400 font-medium">{{ $item->session->start_time ?? '' }} - {{ $item->session->end_time ?? '' }}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @php 
                                    $status = strtolower($item->status);
                                    $colorClass = match($status) {
                                        'hadir' => 'bg-green-50 text-green-600',
                                        'sakit' => 'bg-blue-50 text-blue-600',
                                        'izin' => 'bg-yellow-50 text-yellow-600',
                                        'alpha' => 'bg-red-50 text-red-600',
                                        default => 'bg-gray-50 text-gray-600',
                                    };
                                @endphp
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $colorClass }} shadow-sm">
                                    {{ $item->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-200">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <p class="text-gray-400 font-bold">No attendance records found yet.</p>
                                <a href="{{ route('dashboard') }}" class="text-orens font-bold text-sm hover:underline mt-2 inline-block">Go to Dashboard to Check-in</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection
