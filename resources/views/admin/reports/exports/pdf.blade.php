<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reports Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
        }

        h2 {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <h1>Reports Export</h1>

    <table>
        <thead>
            <tr>
                <th>Report ID</th>
                <th>User</th>
                <th>Health Status</th>
                <th>Risk Level</th>
                <th>Location</th>
                <th>Date</th>
                <th>Specilaist</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->report_id }}</td>
                    <td>{{ $report->user->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($report->pig_health_status) }}</td>
                    <td>{{ ucfirst($report->risk_level) }}</td>
                    <td>{{ $report->barangay }}, {{ $report->city }}</td>
                    <td>{{ $report->created_at->format('M d, Y') }}</td>
                    <td>
                        @if ($report->vetAssessments->isNotEmpty())
                            @foreach ($report->vetAssessments as $assessment)
                                <small>{{ $assessment->assessor->name }}</small>
                            @endforeach
                        @else
                            <em>N/A</span>
                        @endif
                    </td>
                    <td>
                        @forelse ($report->notes as $note)
                            <strong>{{ strtoupper($note->note_type) }}</strong>
                            by {{ $note->user->name ?? 'Unknown' }}:
                            {{ $note->content }}
                            <br>
                            <small>({{ $note->created_at->format('M d, Y h:i A') }})</small>
                            <hr>
                        @empty
                            <em>No notes.</em>
                        @endforelse
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>