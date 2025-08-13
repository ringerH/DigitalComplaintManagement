@extends('layouts.master')

@section('title', 'User Management')

@section('sidebar-active-users', 'active')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Users</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search-users">Search by Name</label>
                                <input type="text" class="form-control" id="search-users" placeholder="Enter user name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort-usertype">Sort by User Type</label>
                                <select class="form-control" id="sort-usertype">
                                    <option value="asc">Patient First</option>
                                    <option value="desc">Student First</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="multiple-complaints-filter">
                        <label class="form-check-label" for="multiple-complaints-filter">Show Users with Multiple Complaints</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="users-table">
                            <thead class="text-primary">
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>User Type</th>
                                    <th>Complaint Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="users-tbody">
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->usertype }}</td>
                                        <td>{{ $user->complaints_count }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info view-complaints" 
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}">View Complaints</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" id="pagination">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing User's Complaints -->
    <div class="modal fade" id="complaintsModal" tabindex="-1" role="dialog" aria-labelledby="complaintsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="complaintsModalLabel">Complaints by <span id="modal-user-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="user-complaints-table">
                            <thead class="text-primary">
                                <tr>
                                    <th>Complaint ID</th>
                                    <th>College</th>
                                    <th>Date Submitted</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="user-complaints-tbody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Individual Complaint Details -->
    <div class="modal fade" id="complaintModal" tabindex="-1" role="dialog" aria-labelledby="complaintModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="complaintModalLabel">Complaint Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal-complaint-id-input">
                    <div class="mb-3">
                        <label class="font-bold">Complaint ID:</label>
                        <span id="modal-complaint-id" class="ml-2"></span>
                    </div>
                    <div class="mb-3">
                        <label class="font-bold">Complainant:</label>
                        <span id="modal-complainant" class="ml-2"></span>
                    </div>
                    <div class="mb-3">
                        <label class="font-bold">College:</label>
                        <span id="modal-college" class="ml-2"></span>
                    </div>
                    <div class="mb-3">
                        <label class="font-bold">Date Submitted:</label>
                        <span id="modal-date" class="ml-2"></span>
                    </div>
                    <div class="mb-3">
                        <label class="font-bold">Complaint Text:</label>
                        <p id="modal-text" class="ml-2"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            function fetchUsers() {
                var search = $('#search-users').val();
                var usertype = $('#usertype-filter').val();
                var sortUsertype = $('#sort-usertype').val();
                var multipleComplaints = $('#multiple-complaints-filter').is(':checked') ? 1 : 0;

                $.ajax({
                    url: '{{ route('admin.users') }}',
                    method: 'GET',
                    data: {
                        search: search,
                        usertype: usertype,
                        sort_usertype: sortUsertype,
                        multiple_complaints: multipleComplaints
                    },
                    success: function(response) {
                        $('#users-tbody').empty();
                        response.users.data.forEach(function(user) {
                            var row = `
                                <tr>
                                    <td>${user.id}</td>
                                    <td>${user.name}</td>
                                    <td>${user.usertype}</td>
                                    <td>${user.complaints_count}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-complaints" 
                                                data-user-id="${user.id}"
                                                data-user-name="${user.name}">View Complaints</button>
                                    </td>
                                </tr>
                            `;
                            $('#users-tbody').append(row);
                        });
                        $('#pagination').html(response.pagination);
                    },
                    error: function() {
                        alert('Error fetching users. Please try again.');
                    }
                });
            }

            $('#search-users').on('keyup', function() {
                fetchUsers();
            });

            $('#usertype-filter, #sort-usertype, #multiple-complaints-filter').on('change', function() {
                fetchUsers();
            });

            $(document).on('click', '.view-complaints', function() {
                var userId = $(this).data('user-id');
                var userName = $(this).data('user-name');

                $.ajax({
                    url: '{{ route('admin.users.complaints') }}',
                    method: 'GET',
                    data: { user_id: userId },
                    success: function(response) {
                        $('#modal-user-name').text(userName);
                        $('#user-complaints-tbody').empty();
                        response.complaints.forEach(function(complaint) {
                            var row = `
                                <tr>
                                    <td>${complaint.id}</td>
                                    <td>${complaint.college.name}</td>
                                    <td>${complaint.created_at}</td>
                                    <td><span class="badge badge-${complaint.status === 'Pending' ? 'danger' : complaint.status === 'In Progress' ? 'info' : 'success'}">${complaint.status}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-details" 
                                                data-complaint-id="${complaint.id}"
                                                data-complainant="${complaint.complainant_name}"
                                                data-college="${complaint.college.name}"
                                                data-date="${complaint.created_at}"
                                                data-text="${complaint.complaint_text}"
                                                data-status="${complaint.status}">View</button>
                                    </td>
                                </tr>
                            `;
                            $('#user-complaints-tbody').append(row);
                        });
                        $('#complaintsModal').modal('show');
                    },
                    error: function() {
                        alert('Error fetching complaints. Please try again.');
                    }
                });
            });

            $(document).on('click', '.view-details', function() {
                var complaintId = $(this).data('complaint-id');
                var complainant = $(this).data('complainant');
                var college = $(this).data('college');
                var date = $(this).data('date');
                var text = $(this).data('text');
                var status = $(this).data('status');

                $('#modal-complaint-id').text(complaintId);
                $('#modal-complaint-id-input').val(complaintId);
                $('#modal-complainant').text(complainant);
                $('#modal-college').text(college);
                $('#modal-date').text(date);
                $('#modal-text').text(text);
                $('#modal-status').val(status);
                $('#complaintModal').modal('show');
            });
        });
    </script>
    <style>
        .view-complaints { transition: all 0.3s ease; }
        .view-complaints:hover { background-color: #007bff; color: white; }
    </style>
@endsection