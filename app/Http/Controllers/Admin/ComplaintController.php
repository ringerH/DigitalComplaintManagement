<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function update(Request $request)
    {
        try {
            $request->validate([
                'complaint_id' => 'required|exists:complaints,id',
                'status' => 'required|in:Pending,In Progress,Resolved',
                'notes' => 'nullable|string'
            ]);

            $complaint = Complaint::findOrFail($request->complaint_id);
            $complaint->status = $request->status;
            $complaint->notes = $request->notes;
            $complaint->save();

            return response()->json(['success' => 'Complaint updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Error updating complaint: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update complaint'], 500);
        }
    }
}