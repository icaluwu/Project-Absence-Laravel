<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px; }
        th { background: #f3f4f6; }
        .text-center { text-align: center; }
    </style>
    <title>{{ $title }}</title>
</head>
<body>
    <h3 style="margin:0 0 10px;">{{ $title }}</h3>
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama</th>
                <th>Departemen</th>
                <th>Jabatan</th>
                @foreach($days as $d)
                    <th class="text-center">{{ $d }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($users as $u)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->departemen }}</td>
                    <td>{{ $u->jabatan }}</td>
                    @foreach($days as $d)
                        @php
                            $cell = '-';
                            if (isset($map[$u->id][$d])) {
                                $ci = $map[$u->id][$d]['in'] ?? null;
                                $co = $map[$u->id][$d]['out'] ?? null;
                                if ($ci || $co) {
                                    $cell = ($ci ? substr($ci,0,5) : '-') . '/' . ($co ? substr($co,0,5) : '-');
                                }
                            }
                        @endphp
                        <td class="text-center">{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
