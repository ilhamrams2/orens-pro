@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <x-ui.header title="Log Audit" subtitle="Pantau semua perubahan sistem dan aktivitas administratif.">
    </x-ui.header>

    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-fixed">
                <thead>
                    <tr class="bg-gray-25 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                        <th class="px-6 lg:px-8 py-4 w-48">Waktu</th>
                        <th class="px-6 lg:px-8 py-4 w-52">Administrator</th>
                        <th class="px-6 lg:px-8 py-4 w-32">Aktivitas</th>
                        <th class="px-6 lg:px-8 py-4 w-48">Target</th>
                        <th class="px-6 lg:px-8 py-4 w-80">Detail</th>
                        <th class="px-6 lg:px-8 py-4 w-48">IP / User Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                        @php
                            $className = $log->auditable_type ? class_basename($log->auditable_type) : '-';
                            $className = match($className) {
                                'User' => 'Pengguna',
                                'Organisation' => 'Organisasi',
                                'Division' => 'Divisi',
                                'AttendanceSession' => 'Sesi Presensi',
                                'Attendance' => 'Presensi',
                                default => $className,
                            };
                            $event = strtolower($log->event);
                            $badgeClass = match($event) {
                                'created' => 'bg-green-50 text-green-600 border-green-100',
                                'updated' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'deleted' => 'bg-red-50 text-red-600 border-red-100',
                                default => 'bg-purple-50 text-purple-600 border-purple-100',
                            };
                            $eventText = match($event) {
                                'created' => 'Dibuat',
                                'updated' => 'Diperbarui',
                                'deleted' => 'Dihapus',
                                default => ucfirst($event),
                            };
                        @endphp
                        <tr class="hover:bg-gray-25/50 transition-all">
                            <td class="px-6 lg:px-8 py-5 text-sm font-medium text-gray-500">
                                {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-6 lg:px-8 py-5">
                                <span class="font-bold text-gray-700 block text-sm">{{ $log->user->name ?? 'Sistem' }}</span>
                                <span class="text-xs text-gray-400 truncate block">{{ $log->user->email ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 lg:px-8 py-5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $badgeClass }}">
                                    {{ $eventText }}
                                </span>
                            </td>
                            <td class="px-6 lg:px-8 py-5">
                                <span class="text-sm font-bold text-gray-700 block">{{ $className }}</span>
                                <span class="text-xs text-gray-400 font-medium">Ref ID: #{{ $log->auditable_id ?? '-' }}</span>
                            </td>
                            <td class="px-6 lg:px-8 py-5">
                                @if($event === 'updated')
                                    @php
                                        $changes = [];
                                        if ($log->old_values && $log->new_values) {
                                            foreach ($log->new_values as $key => $val) {
                                                // Convert non-scalar values to string for visual representation
                                                $oldValStr = is_array($log->old_values[$key] ?? null) ? json_encode($log->old_values[$key]) : ($log->old_values[$key] ?? 'null');
                                                $newValStr = is_array($val) ? json_encode($val) : ($val ?? 'null');

                                                if (isset($log->old_values[$key]) && $log->old_values[$key] !== $val) {
                                                    $changes[] = "<strong>" . e($key) . "</strong>: <span class='text-red-500'>" . e($oldValStr) . "</span> &rarr; <span class='text-green-600'>" . e($newValStr) . "</span>";
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(empty($changes))
                                        <span class="text-gray-400 italic text-xs">Tidak ada perubahan bidang yang terlihat</span>
                                    @else
                                        <ul class="text-xs text-gray-500 list-disc list-inside space-y-1">
                                            @foreach($changes as $change)
                                                <li>{!! $change !!}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                @elseif($event === 'created')
                                    @if($log->new_values)
                                        @php
                                            $nonNull = array_filter($log->new_values, fn($v) => !is_null($v) && $v !== '');
                                        @endphp
                                        <div class="text-[11px] text-gray-500 font-medium">
                                            <span class="font-bold">Data Awal:</span>
                                            <ul class="list-disc list-inside mt-0.5 space-y-0.5 max-h-[80px] overflow-y-auto">
                                                @foreach(array_slice($nonNull, 0, 4) as $key => $val)
                                                    <li><strong>{{ $key }}</strong>: {{ is_array($val) ? json_encode($val) : $val }}</li>
                                                @endforeach
                                                @if(count($nonNull) > 4)
                                                    <li class="italic text-gray-400">+ {{ count($nonNull) - 4 }} bidang lainnya</li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                @elseif($event === 'deleted')
                                    <span class="text-xs text-red-500 font-medium italic">Data dihapus permanen</span>
                                @else
                                    @if($log->new_values)
                                        <pre class="text-[10px] bg-gray-50 p-2 rounded-lg font-mono truncate max-w-xs">{{ json_encode($log->new_values) }}</pre>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 lg:px-8 py-5">
                                <span class="font-mono text-xs text-gray-600 block">{{ $log->ip_address ?? '-' }}</span>
                                <span class="text-[10px] text-gray-400 truncate block max-w-[150px]" title="{{ $log->user_agent }}">{{ $log->user_agent ?? '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center text-gray-400 font-medium italic">Belum ada log audit yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
