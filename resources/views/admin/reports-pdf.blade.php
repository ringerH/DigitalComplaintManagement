<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cumulative Complaint Report - {{ now()->format('Y-m-d') }}</title>
<style>
    @page { margin: 10mm; }
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
    h1, h3 { text-align: center; color: #333; }
    h1 { font-size: 16pt; margin-bottom: 10px; }
    h3 { font-size: 12pt; margin-bottom: 20px; }
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin: 0 auto; 
        page-break-inside: auto; 
    }
    th, td { 
        border: 1px solid #ddd; 
        padding: 6px; 
        text-align: left; 
        vertical-align: top; 
        font-size: 10pt;
        word-wrap: break-word; 
    }
    th { background-color: #f2f2f2; }
    tr { page-break-inside: avoid; page-break-after: auto; }

    .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #666; }

    /* Adjusted column widths to sum ~100% */
    .col-id { width: 7%; }
    .col-complainant { width: 13%; }
    .col-college { width: 13%; }
    .col-user { width: 13%; }
    .col-created { width: 10%; }
    .col-updated { width: 13%; }
    .col-updates { width: 8%; }
    .col-days { width: 10%; }
    .col-status { width: 13%; }
</style>

</head>
<body>
    <h1>Regional Health Colleges, Assam - Complaint Report</h1>
    <h3>Generated on {{ now()->format('Y-m-d') }}</h3>

    <table>
        <thead>
            <tr>
                <th class="col-id">Complaint ID</th>
                <th class="col-complainant">Complainant</th>
                <th class="col-college">College</th>
                <th class="col-user">Submitted By</th>
                <th class="col-created">Date Submitted</th>
                <th class="col-updated">Last Updated</th>
                <th class="col-updates">Update Count</th>
                <th class="col-days">Days Open</th>
                <th class="col-status">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($complaints as $complaint)
                <tr>
                    <td>{{ $complaint->complaint_id }}</td>
                    <td>{{ $complaint->complainant_name }}</td>
                    <td>{{ $complaint->college->name }}</td>
                    <td>{{ $complaint->user ? $complaint->user->name : 'Admin' }}</td>
                    <td>{{ $complaint->submitted_at->format('Y-m-d') }}</td>
                    <td>{{ $complaint->updated_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $complaint->updates->count() }}</td>
                    <td>{{ ceil($complaint->submitted_at->diffInDays(now())) }}</td>
                    <td>{{ $complaint->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Regional Health Colleges, Assam
    </div>
</body>
</html>