@extends('layouts.master')

@section('title', 'Complaint Reports - Regional Health Colleges, Assam')

@section('sidebar-active-reports', 'active')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Complaint Analysis Report</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="collegeChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="statusChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <canvas id="timeChart" height="100"></canvas>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table table-striped">
                            <thead class="text-primary">
                                <tr>
                                    <th>Complaint ID</th>
                                    <th>Complainant</th>
                                    <th>College</th>
                                    <th>Submitted By</th>
                                    <th>Date Submitted</th>
                                    <th>Last Updated</th>
                                    <th>Update Count</th>
                                    <th>Days Open</th>
                                    <th>Status</th>
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
                                        <td>
                                            <span class="badge {{ $complaint->status == 'Pending' ? 'badge-danger' : ($complaint->status == 'In Progress' ? 'badge-warning' : 'badge-success') }}">
                                                {{ $complaint->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.reports.export') }}" class="btn btn-primary">Export as PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxCollege = document.getElementById('collegeChart').getContext('2d');
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        const ctxTime = document.getElementById('timeChart').getContext('2d');

        // Gradient for bar chart
        const gradient = ctxCollege.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, '#f96332');
        gradient.addColorStop(1, '#ffa26b');

        // Bar Chart: Complaints by College
        new Chart(ctxCollege, {
            type: 'bar',
            data: {
                labels: @json($collegeLabels),
                datasets: [{
                    label: 'Complaints by College',
                    data: @json($collegeCounts),
                    backgroundColor: gradient,
                    borderRadius: 5,
                    barThickness: 30,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Complaints Distribution by College',
                        font: { size: 16 }
                    },
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        title: {
                            display: true,
                            text: 'Number of Complaints'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Colleges'
                        }
                    }
                }
            }
        });

        // Pie Chart: Status Breakdown
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Resolved'],
                datasets: [{
                    label: 'Status Breakdown',
                    data: @json([$pendingComplaints, $inProgressComplaints, $resolvedComplaints]),
                    backgroundColor: ['#ff4444', '#ffbb33', '#00C851'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    title: {
                        display: true,
                        text: 'Complaint Status Overview',
                        font: { size: 16 }
                    },
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 15 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let value = context.raw;
                                let percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Line Chart: Complaints Over Time
        new Chart(ctxTime, {
            type: 'line',
            data: {
                labels: @json($timeLabels),
                datasets: [{
                    label: 'Complaints Over Time',
                    data: @json($timeCounts),
                    borderColor: '#f96332',
                    backgroundColor: 'rgba(249, 99, 50, 0.2)',
                    pointBackgroundColor: '#f96332',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Complaints Trend (Time Series)',
                        font: { size: 16 }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Complaints'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time (Weeks/Months)'
                        }
                    }
                }
            }
        });
    </script>
@endsection
