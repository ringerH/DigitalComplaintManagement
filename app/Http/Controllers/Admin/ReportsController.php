<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $collegeId = $request->query('college');
            $usertype = $request->query('usertype');
            $status = $request->query('status');

            // Debug: Log colleges count
            $colleges = College::all();
            Log::info('Colleges fetched: ' . $colleges->count());

            // Base query
            $query = Complaint::with(['college', 'user']);

            // Apply filters
            if ($collegeId) {
                $query->where('college_id', $collegeId);
            }
            if ($usertype) {
                if ($usertype === 'Unknown') {
                    $query->whereHas('user', function ($q) {
                        $q->whereNull('usertype');
                    })->orWhereNull('user_id');
                } else {
                    $query->whereHas('user', function ($q) use ($usertype) {
                        $q->where('usertype', $usertype);
                    });
                }
            }
            if ($status) {
                $query->where('status', $status);
            }

            // Complaints for table
            $complaints = $query->get()->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'complainant_name' => $complaint->complainant_name,
                    'college_name' => $complaint->college->name,
                    'user_name' => $complaint->user ? $complaint->user->name : 'Admin',
                    'created_at' => $complaint->created_at->toDateTimeString(),
                    'updated_at' => $complaint->updated_at->toDateTimeString(),
                    'notes' => $complaint->notes,
                    'days_open' => $complaint->status === 'Resolved'
                        ? $complaint->created_at->diffInDays($complaint->updated_at)
                        : $complaint->created_at->diffInDays(now()),
                    'status' => $complaint->status,
                ];
            });

            // Chart data: Complaints by College
            $collegeData = Complaint::groupBy('college_id')
                ->selectRaw('college_id, count(*) as count')
                ->get()
                ->mapWithKeys(function ($item) {
                    $college = College::find($item->college_id);
                    return [$college->name => $item->count];
                });
            $collegeLabels = $collegeData->keys()->toArray();
            $collegeCounts = $collegeData->values()->toArray();

            // Chart data: Status Breakdown
            $pendingComplaints = $query->clone()->where('status', 'Pending')->count();
            $inProgressComplaints = $query->clone()->where('status', 'In Progress')->count();
            $resolvedComplaints = $query->clone()->where('status', 'Resolved')->count();

            // Chart data: Complaints Over Time
            $timeData = Complaint::selectRaw("DATE(created_at) as date, count(*) as count")
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            $timeLabels = $timeData->pluck('date')->toArray();
            $timeCounts = $timeData->pluck('count')->toArray();

            // Chart data: Role and Sub-Category
            $subcategoryMap = [
                1 => 'Medical Error',
                2 => 'Treatment',
                3 => 'Hygiene',
                4 => 'Staff Behavior'
            ];
            $usertypes = ['Patient', 'Student', 'Unknown'];
            $subcategoryLabels = array_values($subcategoryMap);
            $roleSubcategoryDatasets = [];

            foreach ($usertypes as $usertype) {
                $dataset = [
                    'label' => $usertype,
                    'data' => [],
                    'backgroundColor' => $usertype === 'Patient' ? '#ff4444' : ($usertype === 'Student' ? '#ffbb33' : '#00C851')
                ];
                foreach ($subcategoryMap as $categoryId => $subcategory) {
                    $countQuery = $query->clone()->where('category_id', $categoryId);
                    if ($usertype === 'Unknown') {
                        $countQuery->whereHas('user', function ($q) {
                            $q->whereNull('usertype');
                        })->orWhereNull('user_id');
                    } else {
                        $countQuery->whereHas('user', function ($q) use ($usertype) {
                            $q->where('usertype', $usertype);
                        });
                    }
                    $dataset['data'][] = $countQuery->count();
                }
                $roleSubcategoryDatasets[] = $dataset;
            }

            // AJAX response
            if ($request->ajax()) {
                return response()->json([
                    'complaints' => $complaints,
                    'collegeLabels' => $collegeLabels,
                    'collegeCounts' => $collegeCounts,
                    'pendingComplaints' => $pendingComplaints,
                    'inProgressComplaints' => $inProgressComplaints,
                    'resolvedComplaints' => $resolvedComplaints,
                    'timeLabels' => $timeLabels,
                    'timeCounts' => $timeCounts,
                    'subcategoryLabels' => $subcategoryLabels,
                    'roleSubcategoryDatasets' => $roleSubcategoryDatasets,
                ]);
            }

            // Pass all variables to view
            return view('admin.reports', compact(
                'complaints',
                'colleges',
                'collegeLabels',
                'collegeCounts',
                'pendingComplaints',
                'inProgressComplaints',
                'resolvedComplaints',
                'timeLabels',
                'timeCounts',
                'subcategoryLabels',
                'roleSubcategoryDatasets'
            ));
        } catch (\Exception $e) {
            Log::error('Error fetching reports: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to fetch reports'], 500);
        }
    }

    public function export() {
    try {
        $complaints = Complaint::with(['college', 'user'])->get()->map(function ($complaint) {
            return [
                'id' => $complaint->id,
                'complainant_name' => $complaint->complainant_name ?: 'Unknown',
                'college_name' => $complaint->college->name,
                'user_name' => $complaint->user ? $complaint->user->name : 'Admin',
                'created_at' => $complaint->created_at->format('Y-m-d'),
                'updated_at' => $complaint->updated_at->format('Y-m-d H:i'),
                'notes' => $complaint->notes,
                'days_open' => $complaint->status === 'Resolved'
                    ? $complaint->created_at->diffInDays($complaint->updated_at)
                    : $complaint->created_at->diffInDays(now()),
                'status' => $complaint->status,
            ];
        });
        $pdf = Pdf::loadView('admin.reports_pdf', compact('complaints'));
        return $pdf->download('complaint_report.pdf');
    } catch (\Exception $e) {
        Log::error('Error exporting PDF: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to export PDF');
    }
}
    public function exportCsv()
    {
        try {
            $complaints = Complaint::with(['college', 'user'])->get()->map(function ($complaint) {
                return [
                    'Complaint ID' => $complaint->id,
                    'Complainant' => $complaint->complainant_name ?: 'Unknown',
                    'College' => $complaint->college->name,
                    'Submitted By' => $complaint->user ? $complaint->user->name : 'Admin',
                    'Date Submitted' => $complaint->created_at->format('Y-m-d'),
                    'Last Updated' => $complaint->updated_at->format('Y-m-d H:i'),
                    'Update Count' => $complaint->notes ? 1 : 0,
                    'Days Open' => $complaint->status === 'Resolved'
                        ? $complaint->created_at->diffInDays($complaint->updated_at)
                        : $complaint->created_at->diffInDays(now()),
                    'Status' => $complaint->status,
                ];
            });

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="complaint_report.csv"',
            ];

            $callback = function() use ($complaints) {
                $file = fopen('php://output', 'w');
                fputcsv($file, array_keys($complaints->first()));
                foreach ($complaints as $complaint) {
                    fputcsv($file, $complaint);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting CSV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export CSV');
        }
    }
}