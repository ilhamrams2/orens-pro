<!DOCTYPE html>
<html>
<head>
    <title>Member Attendance Report</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin-bottom: 5px; }
        .header p { color: #666; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; font-size: 12px; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .grade-a { color: green; font-weight: bold; }
        .grade-b { color: blue; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEHADIRAN MEMBER</h1>
        <p>{{ $organisation }}</p>
        <p>Tanggal: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Divisi</th>
                <th>Total Hadir</th>
                <th>Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $u)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->division->name ?? '-' }}</td>
                <td>{{ $u->attendances_count }} Sesi</td>
                <td>
                    <span class="{{ $u->grade === 'A' ? 'grade-a' : ($u->grade === 'B' ? 'grade-b' : '') }}">
                        {{ $u->grade }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh OrensPro pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
