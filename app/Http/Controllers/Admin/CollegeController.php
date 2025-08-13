<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::withCount('complaints')->paginate(10);
        return view('admin.colleges', compact('colleges'));
    }

    public function complaints(Request $request)
    {
        try {
            $collegeId = $request->input('college_id');
            $complaints = College::findOrFail($collegeId)->complaints()->with('user')->get();
            return response()->json(['complaints' => $complaints]);
        } catch (\Exception $e) {
            Log::error('Error fetching complaints: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch complaints'], 500);
        }
    }
}