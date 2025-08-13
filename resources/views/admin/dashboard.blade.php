@extends('layouts.master')

@section('title', 'Complaint Management Dashboard')
@section('sidebar-active-dashboard', 'active')

@section('content')
<div class="row">
    @php
        $statuses = [
            [null, 'Total Complaints', 'now-ui-icons files_paper text-warning', $totalComplaints],
            ['Pending', 'Pending', 'now-ui-icons ui-1_simple-remove text-danger', $pendingComplaints],
            ['In Progress', 'In Progress', 'now-ui-icons loader_gear text-info', $inProgressComplaints],
            ['Resolved', 'Resolved', 'now-ui-icons ui-1_check text-success', $resolvedComplaints],
        ];
    @endphp

    @foreach($statuses as [$statusKey, $label, $icon, $count])
        <div class="col-lg-3 col-md-6">
            <div class="card card-stats clickable-card {{ request('status') === $statusKey ? 'active-card' : '' }}" data-status="{{ $statusKey }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icon-big text-center">
                                <i class="{{ $icon }}"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">{{ $label }}</p>
                                <h4 class="card-title" id="{{ strtolower(str_replace(' ', '-', $label)) }}-complaints">{{ $count }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Registered Complaints</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="complaints-table">
                        <thead class="text-primary">
                            <tr>
                                <th>Complaint ID</th>
                                <th>Complainant</th>
                                <th>College</th>
                                <th>Submitted By</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="complaints-tbody">
                            @include('admin.complaints-table', ['complaints' => $complaints])
                        </tbody>
                    </table>
                </div>
                <div class="mt-3" id="pagination">
                    {!! $complaints->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="complaintModal" tabindex="-1" role="dialog" aria-labelledby="complaintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.complaint.update') }}" id="update-complaint-form">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Complaint Details</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="complaint_id" id="modal-complaint-id-input">
                    <p><strong>Complaint ID:</strong> <span id="modal-complaint-id"></span></p>
                    <p><strong>Complainant:</strong> <span id="modal-complainant"></span></p>
                    <p><strong>College:</strong> <span id="modal-college"></span></p>
                    <p><strong>Date Submitted:</strong> <span id="modal-date"></span></p>
                    <p><strong>Complaint:</strong> <span id="modal-text"></span></p>
                    <div class="form-group">
                        <label for="modal-status">Status</label>
                        <select class="form-control" name="status" id="modal-status">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    function fetchComplaints() {
        const status = $('.clickable-card.active-card').data('status') || '';
        $.ajax({
            url: '{{ route('admin.dashboard') }}',
            method: 'GET',
            data: {
                status: status,
                search: $('#search-complaints').val(),
                college: $('#college-filter').val(),
                date: $('#date-filter').val()
            },
            success: function(response) {
                $('#complaints-tbody').html($(response).find('#complaints-tbody').html());
                $('#pagination').html($(response).find('#pagination').html());
            },
            error: function() {
                alert('Error loading complaints.');
            }
        });
    }

    $('#search-complaints, #college-filter, #date-filter').on('change keyup', fetchComplaints);
    $('.clickable-card').on('click', function() {
        $('.clickable-card').removeClass('active-card');
        $(this).addClass('active-card');
        fetchComplaints();
    });

    $(document).on('click', '.view-details', function() {
        $('#modal-complaint-id').text($(this).data('complaint-id'));
        $('#modal-complaint-id-input').val($(this).data('complaint-id'));
        $('#modal-complainant').text($(this).data('complainant'));
        $('#modal-college').text($(this).data('college'));
        $('#modal-date').text($(this).data('date'));
        $('#modal-text').text($(this).data('text'));
        $('#modal-status').val($(this).data('status'));
        $('#complaintModal').modal('show');
    });

    Echo.channel('admin-dashboard').listen('ComplaintSubmitted', (e) => {
        $('#complaints-tbody').prepend(`
            <tr>
                <td>${e.complaint_id}</td>
                <td>${e.complainant_name}</td>
                <td>${e.college_name}</td>
                <td>${e.submitted_by}</td>
                <td>${e.submitted_at}</td>
                <td><span class="badge badge-danger">${e.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-info view-details" 
                        data-complaint-id="${e.complaint_id}"
                        data-complainant="${e.complainant_name}"
                        data-college="${e.college_name}"
                        data-date="${e.submitted_at}"
                        data-text="${e.complaint_text}"
                        data-status="${e.status}">View</button>
                    <a href="/admin/complaint/${e.complaint_id}/pdf" class="btn btn-sm btn-primary download-pdf">PDF</a>
                </td>
            </tr>
        `);
        $('#total-complaints').text(parseInt($('#total-complaints').text()) + 1);
        if (e.status === 'Pending') {
            $('#pending-complaints').text(parseInt($('#pending-complaints').text()) + 1);
        }
    });
});
</script>

<style>
    .clickable-card { cursor: pointer; transition: all 0.3s ease; }
    .clickable-card:hover { box-shadow: 0 7px 14px rgba(119, 119, 204, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08); }
    .active-card { border: 2px solid #3192d2; background-color: #f0f9ff; }
    .pagination { justify-content: center; }
    .pagination .page-item .page-link {
        font-size: 0.9rem;
        padding: 6px 12px;
    }
</style>
@endsection
