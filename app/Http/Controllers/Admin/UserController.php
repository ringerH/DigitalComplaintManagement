<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('complaints')->where('usertype', '!=', 'Admin');

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }


        // Filter by multiple complaints
        if ($request->has('multiple_complaints') && $request->multiple_complaints) {
            $query->having('complaints_count', '>=', 2);
        }

        // Sort by usertype
        if ($request->has('sort_usertype') && in_array($request->sort_usertype, ['asc', 'desc'])) {
            $query->orderBy('usertype', $request->sort_usertype);
        } else {
            $query->orderBy('usertype', 'asc'); // Default sort
        }

        $users = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'users' => $users,
                'pagination' => $users->links()->toHtml()
            ]);
        }

        return view('admin.users', compact('users'));
    }

    public function complaints(Request $request)
    {
        $userId = $request->input('user_id');
        $complaints = User::findOrFail($userId)->complaints()->with('college')->get();
        return response()->json(['complaints' => $complaints]);
    }
}