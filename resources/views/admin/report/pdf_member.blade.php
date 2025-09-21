<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Member</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Member Gym</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Pertemuan</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Harga</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $member)
            <tr>
                <td>{{ $member->name_member }}</td>
                <td>{{ $member->pertemuan }}</td>
                <td>{{ \Carbon\Carbon::parse($member->start_training)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($member->end_training)->format('d-m-Y') }}</td>
                <td>Rp {{ number_format($member->price_member, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($member->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
