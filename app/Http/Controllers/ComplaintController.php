<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\College;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    /**
     * Get category-specific fields for form rendering and validation.
     */
    private function getCategoryFields($usertype, $categoryName)
    {
        $fields = [];

        if ($usertype === 'hospital_patient') {
            if ($categoryName === 'Dispensary Services') {
                $fields = [
                    ['name' => 'medicine_name', 'label' => 'Medicine Name', 'type' => 'text', 'required' => true],
                    ['name' => 'date_of_issue', 'label' => 'Date of Issue', 'type' => 'date', 'required' => true],
                ];
            } elseif ($categoryName === 'Patient Care') {
                $fields = [
                    ['name' => 'ward_number', 'label' => 'Ward Number', 'type' => 'text', 'required' => true],
                    ['name' => 'nurse_name', 'label' => 'Nurse Name', 'type' => 'text', 'required' => false],
                ];
            } elseif ($categoryName === 'Medical Treatment') {
                $fields = [
                    ['name' => 'treatment_date', 'label' => 'Treatment Date', 'type' => 'date', 'required' => true],
                    ['name' => 'doctor_name', 'label' => 'Doctor Name', 'type' => 'text', 'required' => true],
                ];
            } elseif ($categoryName === 'Visitor Experience') {
                $fields = [
                    ['name' => 'visitor_name', 'label' => 'Visitor Name', 'type' => 'text', 'required' => true],
                    ['name' => 'visit_date', 'label' => 'Visit Date', 'type' => 'date', 'required' => true],
                ];
            } elseif ($categoryName === 'Hospital Security') {
                $fields = [
                    ['name' => 'incident_location', 'label' => 'Incident Location', 'type' => 'text', 'required' => true],
                    ['name' => 'incident_date', 'label' => 'Incident Date', 'type' => 'date', 'required' => true],
                ];
            }
        } elseif ($usertype === 'student') {
            if ($categoryName === 'Transportation') {
                $fields = [
                    ['name' => 'bus_number', 'label' => 'Bus Number', 'type' => 'text', 'required' => true],
                    ['name' => 'incident_date', 'label' => 'Incident Date', 'type' => 'date', 'required' => true],
                    ['name' => 'route_number', 'label' => 'Route Number', 'type' => 'text', 'required' => false],
                ];
            } elseif ($categoryName === 'Staff Behavior') {
                $fields = [
                    ['name' => 'staff_name', 'label' => 'Staff Name', 'type' => 'text', 'required' => true],
                    ['name' => 'incident_date', 'label' => 'Incident Date', 'type' => 'date', 'required' => true],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'required' => false],
                    ['name' => 'room_number', 'label' => 'Room Number', 'type' => 'text', 'required' => false],
                    ['name' => 'block', 'label' => 'Block', 'type' => 'text', 'required' => false],
                ];
            } elseif ($categoryName === 'Hygiene') {
                $fields = [
                    ['name' => 'area_name', 'label' => 'Area Name', 'type' => 'text', 'required' => true],
                    ['name' => 'incident_date', 'label' => 'Incident Date', 'type' => 'date', 'required' => true],
                    ['name' => 'severity_level', 'label' => 'Severity Level', 'type' => 'select', 'required' => false, 'options' => ['minor' => 'Minor', 'moderate' => 'Moderate', 'severe' => 'Severe']],
                    ['name' => 'room_number', 'label' => 'Room Number', 'type' => 'text', 'required' => false],
                    ['name' => 'block', 'label' => 'Block', 'type' => 'text', 'required' => false],
                ];
            } elseif ($categoryName === 'Payment') {
                $fields = [
                    ['name' => 'transaction_id', 'label' => 'Transaction ID', 'type' => 'text', 'required' => true],
                    ['name' => 'payment_date', 'label' => 'Payment Date', 'type' => 'date', 'required' => true],
                    ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'required' => false],
                ];
            } elseif ($categoryName === 'Infrastructure') {
                $fields = [
                    ['name' => 'facility_name', 'label' => 'Facility Name', 'type' => 'text', 'required' => true],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'required' => true],
                    ['name' => 'reported_date', 'label' => 'Reported Date', 'type' => 'date', 'required' => false],
                    ['name' => 'room_number', 'label' => 'Room Number', 'type' => 'text', 'required' => false],
                    ['name' => 'block', 'label' => 'Block', 'type' => 'text', 'required' => false],
                ];
            } elseif ($categoryName === 'Academic Support') {
                $fields = [
                    ['name' => 'resource_type', 'label' => 'Resource Type', 'type' => 'text', 'required' => true],
                    ['name' => 'exam_date', 'label' => 'Exam Date', 'type' => 'date', 'required' => false],
                    ['name' => 'request_date', 'label' => 'Request Date', 'type' => 'date', 'required' => false],
                    ['name' => 'room_number', 'label' => 'Room Number', 'type' => 'text', 'required' => false],
                    ['name' => 'block', 'label' => 'Block', 'type' => 'text', 'required' => false],
                ];
            } elseif ($categoryName === 'Security') {
                $fields = [
                    ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'required' => true],
                    ['name' => 'incident_date', 'label' => 'Incident Date', 'type' => 'date', 'required' => true],
                    ['name' => 'incident_time', 'label' => 'Incident Time', 'type' => 'time', 'required' => false],
                    ['name' => 'room_number', 'label' => 'Room Number', 'type' => 'text', 'required' => false],
                    ['name' => 'block', 'label' => 'Block', 'type' => 'text', 'required' => false],
                ];
            }
        }

        return $fields;
    }

    public function index()
    {
        $user = Auth::guard('web')->user();
        $categories = $user->usertype === 'student'
            ? Category::whereIn('name', ['Transportation', 'Staff Behavior', 'Hygiene', 'Payment', 'Infrastructure', 'Academic Support', 'Security'])->get()
            : Category::whereIn('name', ['Dispensary Services', 'Patient Care', 'Medical Treatment', 'Visitor Experience', 'Hospital Security'])->get();
        $colleges = College::all();
        $complaints = Complaint::where('user_id', $user->id)
            ->when(request('status'), fn($query, $status) => $query->where('status', $status))
            ->with(['college', 'category'])
            ->get();

        // Prepare category-specific fields for the front-end
        $formFields = ['category_specific' => []];
        foreach ($categories as $category) {
            $formFields['category_specific'][$category->name] = $this->getCategoryFields($user->usertype, $category->name);
        }

        return view('home', compact('complaints', 'categories', 'colleges', 'formFields'));
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            $commonRules = [
                'category_id' => 'required|exists:categories,id',
                'college_id' => 'required|exists:colleges,id',
                'title' => 'required|string|max:255',
                'priority' => 'required|in:low,medium,high',
                'description' => 'required|string',
            ];

            $category = Category::findOrFail($request->category_id);
            $categoryName = $category->name;
            $categoryFields = $this->getCategoryFields($user->usertype, $categoryName);

            // Build validation rules for category-specific fields
            $categoryRules = [];
            foreach ($categoryFields as $field) {
                $rule = $field['required'] ? 'required' : 'nullable';
                if ($field['type'] === 'text') {
                    $rule .= '|string|max:255';
                } elseif ($field['type'] === 'date') {
                    $rule .= '|date';
                } elseif ($field['type'] === 'time') {
                    $rule .= '|date_format:H:i';
                } elseif ($field['type'] === 'number') {
                    $rule .= '|numeric|min:0';
                } elseif ($field['type'] === 'select') {
                    $rule .= '|in:' . implode(',', array_keys($field['options']));
                }
                $categoryRules[$field['name']] = $rule;
            }

            $request->validate(array_merge($commonRules, $categoryRules));

            $complaint = Complaint::create([
                'complaint_id' => 'CMP-' . time() . '-' . rand(1000, 9999),
                'complainant_name' => $user->name,
                'user_id' => $user->id,
                'college_id' => $request->college_id,
                'category_id' => $request->category_id,
                'category' => $categoryName,
                'complaint_text' => $request->description,
                'title' => $request->title,
                'priority' => $request->priority,
                'status' => 'Pending',
                'submitted_at' => now(),
                'additional_data' => array_diff_key($request->all(), array_flip(['_token', 'category_id', 'college_id', 'title', 'priority', 'description'])),
            ]);

            return redirect()->route('home')->with('success', 'Complaint submitted successfully!');
        } catch (\Exception $e) {
            Log::error('Complaint submission failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit complaint. Please try again.')->withInput();
        }
    }

    public function followUp(Request $request)
    {
        try {
            $request->validate([
                'complaint_id' => 'required|exists:complaints,id',
                'follow_up_note' => 'required|string',
            ]);

            $complaint = Complaint::findOrFail($request->complaint_id);
            $complaint->updates()->create([
                'status' => $complaint->status,
                'notes' => $request->follow_up_note,
                'updated_by' => Auth::id(), // Simplified from Auth::guard('web')->id()
            ]);

            return redirect()->route('home')->with('success', 'Follow-up submitted successfully!');
        } catch (\Exception $e) {
            Log::error('Follow-up submission failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit follow-up. Please try again.');
        }
    }
}