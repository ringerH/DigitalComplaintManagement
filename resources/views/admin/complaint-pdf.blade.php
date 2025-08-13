<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Complaint Report - {{ $complaint->complaint_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 20px; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Regional Health Colleges, Assam - Complaint Report</h1>
        <p><strong>Complaint ID:</strong> {{ $complaint->complaint_id }}</p>
    </div>

    <table>
        <tr>
            <th>Complainant</th>
            <td>{{ $complaint->complainant_name }}</td>
        </tr>
        <tr>
            <th>College</th>
            <td>{{ $complaint->college->name }}</td>
        </tr>
        <tr>
            <th>Date Submitted</th>
            <td>{{ $complaint->submitted_at->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <th>Complaint</th>
            <td>{{ $complaint->complaint_text }}</td>
        </tr>
        <tr>
            <th>Current Status</th>
            <td>{{ $complaint->status }}</td>
        </tr>
    </table>

    @if ($complaint->updates->count() > 0)
        <h3>Update History</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Updated By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($complaint->updates as $update)
                    <tr>
                        <td>{{ $update->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $update->status }}</td>
                        <td>{{ $update->notes ?? 'N/A' }}</td>
                        <td>{{ $update->updatedBy ? $update->updatedBy->name : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generated on {{ now()->format('Y-m-d') }} | Regional Health Colleges, Assam
    </div>
</body>
</html>