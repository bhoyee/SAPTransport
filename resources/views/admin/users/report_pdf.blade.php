<!DOCTYPE html>
<html>
<head>
    <title>User Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px; /* Reduced font size */

        }
        .logo {
            width: 150px;
            margin-bottom: 20px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: auto; /* Ensure table rows break properly across pages */
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
        }
        .report-table tr {
            page-break-inside: avoid; /* Avoid breaking rows across pages */
        }
        .page-break {
            page-break-after: always; /* Ensures content is broken across pages */
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>{{ $companyName }} - {{ $roleDisplayName }} User Report</h1>
        <p>Generated at: {{ $generatedAt }}</p>
        <p>Report Date Range: From {{ $dateFrom }} to {{ $dateTo }}</p>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <!-- <th>Status</th> -->
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $index => $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ ucfirst($user->role) === 'Consultant' ? 'Staff' : ucfirst($user->role) }}</td>
                    <!-- <td>{{ ucfirst($user->status) }}</td> -->
                    <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div> <!-- Force a page break if needed -->
</body>
</html>
