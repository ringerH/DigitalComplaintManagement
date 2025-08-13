@forelse ($complaints as $complaint)
    <tr>
        <td>{{ $complaint->complaint_id }}</td>
        <td>{{ $complaint->complainant_name }}</td>
        <td>{{ $complaint->college->name }}</td>
        <td>{{ $complaint->user ? $complaint->user->name : 'Admin' }}</td>
        <td>{{ $complaint->submitted_at ? $complaint->submitted_at->format('Y-m-d') : 'N/A' }}</td>
        <td>
            <span class="badge {{ $complaint->status == 'Pending' ? 'badge-danger' : ($complaint->status == 'In Progress' ? 'badge-warning' : 'badge-success') }}">
                {{ $complaint->status }}
            </span>
        </td>
        <td>
            <button class="btn btn-sm btn-info view-details" 
                    data-complaint-id="{{ $complaint->complaint_id }}"
                    data-complainant="{{ $complaint->complainant_name }}"
                    data-college="{{ $complaint->college->name }}"
                    data-date="{{ $complaint->submitted_at ? $complaint->submitted_at->format('Y-m-d') : 'N/A' }}"
                    data-text="{{ $complaint->complaint_text }}"
                    data-status="{{ $complaint->status }}">View</button>
            <a href="{{ route('admin.complaint.pdf', $complaint->id) }}" class="btn btn-sm btn-primary download-pdf">PDF</a>
            @if ($complaint->updates()->whereNotNull('updated_by')->exists())
                <span class="badge badge-warning ml-1" title="User Followed Up"><i class="fas fa-bell"></i></span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">No complaints found.</td>
    </tr>
@endforelse