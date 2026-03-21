@extends('layouts.dashboard')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Role Specific Summary Icons -->
    @if(auth()->user()->role !== 'member')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @if(auth()->user()->role === 'admin')
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Total Members</p>
                <p class="text-3xl font-black text-gray-800 font-outfit">{{ $total_users ?? 0 }}</p>
            </div>
        @endif

        @if(auth()->user()->role === 'leader')
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Division Members</p>
                <p class="text-3xl font-black text-gray-800 font-outfit">{{ $members_count ?? $total_members ?? 0 }}</p>
            </div>
        @endif

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Attendance Rate</p>
            <p class="text-3xl font-black text-gray-800 font-outfit">{{ $attendance_rate ?? $avg_attendance ?? 0 }}{{ is_numeric($attendance_rate ?? $avg_attendance ?? 0) ? '%' : '' }}</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Active Sessions</p>
            <p class="text-3xl font-black text-gray-800 font-outfit">{{ $active_sessions ?? $sessions_count ?? $total_sessions ?? 0 }}</p>
        </div>
    </div>
    @endif

    @if(auth()->user()->role === 'member')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Today's Sessions -->
        <div class="lg:col-span-2 bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-orens rounded-full"></span>
                Jadwal Hari Ini
            </h3>
            <div class="space-y-4">
                @forelse($today_sessions ?? [] as $session)
                    @php
                        $startTime = \Carbon\Carbon::parse($session->session_date . ' ' . $session->start_time);
                        $endTime = \Carbon\Carbon::parse($session->session_date . ' ' . $session->end_time);
                        $now = now();
                        $isTooEarly = $now->lt($startTime->copy()->subMinutes(30));
                        $isHappening = $now->between($startTime->copy()->subMinutes(30), $endTime);
                    @endphp
                    <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between hover:border-orens/20 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-orens transition-all group-hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-1">{{ $session->organisation->name ?? 'Organisation' }}</p>
                            <h3 class="text-sm font-bold text-gray-800 leading-tight group-hover:text-orens transition-colors">{{ $session->title }}</h3>
                        </div>
                        </div>

                        @if($session->attendances->isNotEmpty())
                            <span class="px-5 py-2.5 bg-green-50 text-green-500 rounded-xl text-xs font-bold border border-green-100 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Sudah Absen
                            </span>
                        @elseif($isHappening)
                            <form action="{{ route('sessions.checkin', $session) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 bg-orens text-white rounded-xl text-xs font-bold hover:bg-orens-light transition-all shadow-md shadow-orens/10">Check-in</button>
                            </form>
                        @elseif($isTooEarly)
                            <div class="flex flex-col items-end gap-1">
                                <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-full text-[10px] font-bold uppercase border border-gray-200">Sesi Belum Dimulai</span>
                                <p class="text-[9px] text-gray-400 font-medium tracking-tight">Buka jam {{ $startTime->copy()->subMinutes(30)->format('H:i') }}</p>
                            </div>
                        @else
                            <span class="px-3 py-1 bg-red-50 text-red-400 rounded-full text-[10px] font-bold uppercase border border-red-100">Sesi Berakhir</span>
                        @endif
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p class="text-gray-400 font-medium text-sm">Tidak ada jadwal untuk hari ini.</p>
                    </div>
                @endforelse
            </div>
            
            @if(count($upcoming_sessions ?? []) > 0)
            <div class="mt-8 pt-8 border-t border-gray-50">
                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Mendatang</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($upcoming_sessions as $session)
                        <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">{{ $session->title }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($session->session_date)->format('d M') }} • {{ $session->start_time }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-blue-50 text-blue-500 rounded-lg text-[9px] font-bold uppercase tracking-tighter">Scheduled</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Participation Card -->
        <div class="bg-orens rounded-[32px] p-8 flex flex-col justify-between text-white shadow-xl shadow-orens/20 relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
            <div>
                <p class="font-bold uppercase tracking-widest text-[10px] mb-2 opacity-80">Total Partisipasi</p>
                <h4 class="text-7xl font-black font-outfit mb-4">{{ $total_join ?? 0 }}</h4>
            </div>
            <div>
                <p class="text-sm font-medium opacity-90 mb-4 italic text-balance leading-relaxed text-orens-light/20">
                    "Kedisiplinan adalah jembatan antara cita-cita dan pencapaian."
                </p>
                <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-orens rounded-2xl font-bold text-sm hover:scale-105 transition-all shadow-lg">
                    Lihat Riwayat
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>
        </div>
    </div>
    @endif


    <!-- Table Section -->
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">
                {{ auth()->user()->role === 'member' ? 'My Recent Attendance' : 'Recent Activity' }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-25 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                        <th class="px-8 py-4">{{ auth()->user()->role === 'member' ? 'Session' : 'User/Session' }}</th>
                        <th class="px-8 py-4">Date</th>
                        <th class="px-8 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recent_activity ?? [] as $item)
                        @php
                            $isAttendance = $item instanceof \App\Models\Attendance;
                            $title = $isAttendance ? ($item->session->title ?? 'Untitled') : ($item->title ?? 'Untitled');
                            $date = $isAttendance ? ($item->session->session_date ?? '-') : ($item->session_date ?? '-');
                            $subtext = $isAttendance ? ($item->session->start_time ?? '') : ($item->division->name ?? 'Global');
                            $status = $isAttendance ? ($item->status) : ($item->is_active ? 'active' : 'inactive');
                        @endphp
                        <tr class="hover:bg-gray-25 transition-all">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800">{{ $attendance->session->title ?? 'Session' }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium">{{ $attendance->session->organisation->name ?? 'Organisation' }}</span>
                                    </div>
                                </td>
</div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-gray-600 block">{{ $date }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ in_array($status, ['hadir', 'completed', 'active']) ? 'bg-green-50 text-green-600' : '' }}
                                    {{ $status === 'sakit' ? 'bg-blue-50 text-blue-600' : '' }}
                                    {{ $status === 'izin' ? 'bg-yellow-50 text-yellow-600' : '' }}
                                    {{ in_array($status, ['alpha', 'inactive']) ? 'bg-red-50 text-red-600' : '' }}">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center text-gray-400">No recent activity.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }
</style>
@endsection
