@extends('layouts.master')

@section('title', 'College Management')

@section('sidebar-active-colleges', 'active')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Colleges</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="colleges-table">
                            <thead class="text-primary">
                                <tr>
                                    <th>College ID</th>
                                    <th>Name</th>
                                    <th>Complaint Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="colleges-tbody">
                                @foreach ($colleges as $college)
                                    <tr>
                                        <td>{{ $college->id }}</td>
                                        <td>{{ $college->name }}</td>
                                        <td>{{ $college->complaints_count }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-complaints" 
                                                    data-college-id="{{ $college->id }}"
                                                    data-college-name="{{ $college->name }}">View Complaints</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" id="pagination">
                        {{ $colleges->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="complaintsModal" tabindex="-1" role="dialog" aria-labelledby="complaintsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="complaintsModalLabel">Complaints for <span id="modal-college-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="college-complaints-table">
                            <thead class="text-primary">
                                <tr>
                                    <th>Complaint ID</th>
                                    <th>Complainant</th>
                                    <th>Date Submitted</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="college-complaints-tbody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="complaintModal" tabindex="-1" role="dialog" aria-labelledby="complaintModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="complaintModalLabel">Complaint Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="update-complaint-form">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="modal-body">
                        <input type="hidden" name="complaint_id" id="modal-complaint-id-input">
                        <p><strong>Complaint ID:</strong> <span id="modal-complaint-id"></span></p>
                        <p><strong>Complainant:</strong> <span id="modal-complainant"></span></p>
                        <p><strong>College:</strong> <span id="modal-college"></span></p>
                        <p><strong>Date Submitted:</strong> <span id="modal-date"></span></p>
                        <p><strong>Complaint:</strong> <span id="modal-text"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '.view-complaints', function() {
                var collegeId = $(this).data('college-id');
                var collegeName = $(this).data('college-name');
                console.log('Fetching complaints for college ID:', collegeId); // Debug

                $.ajax({
                    url: '{{ route('admin.colleges.complaints') }}',
                    method: 'GET',
                    data: { college_id: collegeId },
                    success: function(response) {
                        $('#modal-college-name').text(collegeName);
                        $('#college-complaints-tbody').empty();
                        response.complaints.forEach(function(complaint) {
                            var row = `
                                <tr>
                                    <td>${complaint.id}</td>
                                    <td>${complaint.complainant_name || 'Unknown'}</td>
                                    <td>${complaint.created_at}</td>
                                    <td><span class="badge badge-${complaint.status === 'Pending' ? 'danger' : complaint.status === 'In Progress' ? 'info' : 'success'}">${complaint.status}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-details" 
                                                data-complaint-id="${complaint.id}"
                                                data-complainant="${complaint.complainant_name || 'Unknown'}"
                                                data-college="${collegeName}"
                                                data-date="${complaint.created_at}"
                                                data-text="${complaint.complaint_text || ''}"
                                                data-status="${complaint.status}"
                                                data-notes="${complaint.notes || ''}">View</button>
                                    </td>
                                </tr>
                            `;
                            $('#college-complaints-tbody').append(row);
                        });
                        $('#complaintsModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error fetching complaints:', xhr.status, xhr.responseText);
                        alert('Error fetching complaints. Please try again.');
                    }
                });
            });

            $(document).on('click', '.view-details', function() {
                console.log('View details clicked:', $(this).data()); // Debug
                var complaintId = $(this).data('complaint-id');
                var complainant = $(this).data('complainant');
                var college = $(this).data('college');
                var date = $(this).data('date');
                var text = $(this).data('text');
                var status = $(this).data('status');
                var notes = $(this).data('notes');

                $('#modal-complaint-id').text(complaintId);
                $('#modal-complaint-id-input').val(complaintId);
                $('#modal-complainant').text(complainant);
                $('#modal-college').text(college);
                $('#modal-date').text(date);
                $('#modal-text').text(text);
                $('#modal-status').val(status);
                $('#modal-notes').val(notes);
                $('#complaintModal').modal('show');
            });

            $(document).on('submit', '#update-complaint-form', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                console.log('Submitting complaint update:', formData); // Debug

                $.ajax({
                    url: '{{ route('admin.complaint.update') }}',
                    method: 'POST', // Laravel handles PATCH via _method
                    data: formData,
                    success: function(response) {
                        alert('Complaint updated successfully');
                        $('#complaintModal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error('Error updating complaint:', xhr.status, xhr.responseText);
                        alert('Error updating complaint. Please try again.');
                    }
                });
            });
        });
    </script>
    <style>
        .view-complaints, .view-details { transition: all 0.3s ease; }
        .view-complaints:hover, .view-details:hover { background-color: #007bff; color: white; }
    </style>
@endsection