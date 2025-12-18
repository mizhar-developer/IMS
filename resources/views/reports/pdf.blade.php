<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Diagnoses Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #f4f4f4;
        }
    </style>
</head>

<body>
    <h2>Diagnoses Report</h2>
    <p>From {{ $start }} to {{ $end }}</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Disease</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
                <tr>
                    <td>{{ $r['id'] }}</td>
                    <td>{{ $r['patient'] }}</td>
                    <td>{{ $r['doctor'] }}</td>
                    <td>{{ $r['disease_type'] }}</td>
                    <td>{{ $r['created_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>