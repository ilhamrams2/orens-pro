@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Mark Attendance</h1>
            <p class="text-gray-500 font-medium">{{ $session->title }} • {{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sessions.index') }}" class="px-6 py-3 bg-white border border-gray-100 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition-all text-sm">Cancel</a>
            <button form="marking-form" type="submit" class="px-8 py-3 bg-orens text-white rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20 text-sm">Save Changes</button>
        </div>
    </div>

    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden p-8 animate-fade-in">
        <form id="marking-form" action="{{ route('sessions.submit-mark', $session) }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                            <th class="px-4 py-4">Member</th>
                            <th class="px-4 py-4 text-center w-32 tracking-wider">Hadir</th>
                            <th class="px-4 py-4 text-center w-32 tracking-wider">Sakit</th>
                            <th class="px-4 py-4 text-center w-32 tracking-wider">Izin</th>
                            <th class="px-4 py-4 text-center w-32 tracking-wider">Alpha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($members as $member)
                            @php $status = $attendances[$member->id]->status ?? 'alpha'; @endphp
                            <tr class="hover:bg-gray-25/50 transition-all">
                                <td class="px-4 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-orange-50 text-orens flex items-center justify-center font-bold text-sm">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-700 block">{{ $member->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $member->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-6 text-center">
                                    <input type="radio" name="attendance[{{ $member->id }}]" value="hadir" 
                                        {{ $status === 'hadir' ? 'checked' : '' }}
                                        class="w-5 h-5 accent-green-500 cursor-pointer">
                                </td>
                                <td class="px-4 py-6 text-center">
                                    <input type="radio" name="attendance[{{ $member->id }}]" value="sakit"
                                        {{ $status === 'sakit' ? 'checked' : '' }}
                                        class="w-5 h-5 accent-blue-500 cursor-pointer">
                                </td>
                                <td class="px-4 py-6 text-center">
                                    <input type="radio" name="attendance[{{ $member->id }}]" value="izin"
                                        {{ $status === 'izin' ? 'checked' : '' }}
                                        class="w-5 h-5 accent-yellow-500 cursor-pointer">
                                </td>
                                <td class="px-4 py-6 text-center">
                                    <input type="radio" name="attendance[{{ $member->id }}]" value="alpha"
                                        {{ $status === 'alpha' ? 'checked' : '' }}
                                        class="w-5 h-5 accent-red-500 cursor-pointer">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection
