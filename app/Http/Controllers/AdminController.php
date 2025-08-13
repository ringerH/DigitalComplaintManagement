<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\College;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\ComplaintStatusUpdated;
class AdminController extends Controller
{

    
    public function index(Request $request)
    {
        config(['session.cookie' => 'admin_session']); // Set admin session cookie

        // Define all variables before any early returns
        $query = Complaint::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('complaint_id', 'like', "%{$search}%")
                  ->orWhere('complainant_name', 'like', "%{$search}%")
                  ->orWhereHas('college', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($collegeId = $request->input('college')) {
            $query->where('college_id', $collegeId);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('submitted_at', $date);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $complaints = $query->with(['college', 'user'])->paginate(10);
        $colleges = College::all();
        $totalComplaints = Complaint::count();
        $pendingComplaints = Complaint::where('status', 'Pending')->count();
        $inProgressComplaints = Complaint::where('status', 'In Progress')->count();
        $resolvedComplaints = Complaint::where('status', 'Resolved')->count();

        // Check guard after variables are set
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        return view('admin.dashboard', compact(
            'complaints',
            'colleges',
            'totalComplaints',
            'pendingComplaints',
            'inProgressComplaints',
            'resolvedComplaints'
        ));
    }

    public function reports()
    {
        config(['session.cookie' => 'admin_session']);
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        $complaints = Complaint::with('college', 'updates')->get();
        $colleges = College::withCount('complaints')->get();

        $collegeLabels = $colleges->pluck('name')->toArray();
        $collegeCounts = $colleges->pluck('complaints_count')->toArray();

        $totalComplaints = Complaint::count();
        $pendingComplaints = Complaint::where('status', 'Pending')->count();
        $inProgressComplaints = Complaint::where('status', 'In Progress')->count();
        $resolvedComplaints = Complaint::where('status', 'Resolved')->count();

        $timeData = Complaint::selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->where('submitted_at', '>=', now()->subDays(30))
            ->get();
        $timeLabels = $timeData->pluck('date')->toArray();
        $timeCounts = $timeData->pluck('count')->toArray();

        return view('admin.reports', compact(
            'complaints',
            'collegeLabels',
            'collegeCounts',
            'totalComplaints',
            'pendingComplaints',
            'inProgressComplaints',
            'resolvedComplaints',
            'timeLabels',
            'timeCounts'
        ));
    }

    public function reportsExport()
    {
        config(['session.cookie' => 'admin_session']);
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        $complaints = Complaint::with('college', 'updates')->get();
        $pdf = Pdf::loadView('admin.reports-pdf', compact('complaints'));
        return $pdf->download('complaint_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function search(Request $request)
    {
        config(['session.cookie' => 'admin_session']);
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        $query = $request->input('query');

        $complaints = Complaint::where('complaint_id', 'like', "%{$query}%")
            ->orWhere('complainant_name', 'like', "%{$query}%")
            ->orWhere('complaint_text', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'complaint_id', 'complaint_text']);

        $colleges = College::where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name']);

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name']);

        return response()->json([
            'complaints' => $complaints,
            'colleges' => $colleges,
            'users' => $users,
        ]);
    }

    public function updateComplaint(Request $request)
    {
        config(['session.cookie' => 'admin_session']);
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        $request->validate([
            'complaint_id' => 'required|string|exists:complaints,complaint_id',
            'status' => 'required|in:Pending,In Progress,Resolved',
            'notes' => 'nullable|string',
        ]);

        $complaint = Complaint::where('complaint_id', $request->complaint_id)->firstOrFail();
        $complaint->status = $request->status;
        $complaint->save();

        if ($request->notes || $complaint->wasChanged()) {
            $complaint->updates()->create([
                'status' => $request->status,
                'notes' => $request->notes,
                'updated_by' => Auth::guard('admin')->id(),
            ]);
        }

        // Trigger real-time notification
        $complaint->user->notify(new ComplaintStatusUpdated($complaint));

        return redirect()->route('admin.dashboard')->with('success', 'Complaint status updated successfully!');
    }

    public function downloadPdf($id)
    {
        config(['session.cookie' => 'admin_session']);
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        $complaint = Complaint::with('college', 'updates')->findOrFail($id);
        $pdf = Pdf::loadView('admin.complaint-pdf', compact('complaint'));
        return $pdf->download('complaint_' . $complaint->complaint_id . '.pdf');
    }
}