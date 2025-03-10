<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class AdminsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Query to fetch the users data
                $data = User::with('role')->withTrashed()->admin()->get();

                // Return the data in DataTables format
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->setRowClass(fn($row) => 'align-middle')
                    ->addColumn('role', fn($row) => $row->role ? $row->role->name : 'N/A')
                    ->addColumn('action', fn($row) => '<div class="d-flex">
                                                            <a href="' . route('admins.show', $row->id) . '" class="btn btn-sm btn-primary waves-effect waves-light me-1">View</a>
                                                            <a href="' . route('admins.edit', $row->id) . '" class="btn btn-sm btn-warning waves-effect waves-light me-1">Edit</a>
                                                        </div>')
                    ->addColumn('status', function ($row) {
                        $status = $row->deleted_at ? 'checked' : '';
                        return '<div class="form-check form-switch">
                                    <input class="form-check-input ban-toggle" type="checkbox" data-id="' . $row->id . '" ' . $status . ' value="1" role="switch">
                                </div>';
                    })
                    ->editColumn('created_at', fn($row) => formatDate($row->created_at))
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            } catch (\Exception $e) {
                // Log the error if there is an exception
                Log::error('Error fetching admin data: ' . $e->getMessage());

                // Return an error response
                return response()->json(['error' => 'An error occurred while fetching the data.'], 500);
            }
        }

        return view('admin.users.index');
    }
    public function show(Request $request, User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all(['id', 'name']); // Fetch roles to display in dropdown
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',  // Validate the password field
        ]);

        // Create new user and assign role
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role_id' => $validated['role_id'],
            'is_admin' => true,
            'password' => Hash::make($validated['password'])  // Hash the password before storing
        ]);

        // Assign the selected role to the user
        $role = Role::findById($validated['role_id']);
        $user->assignRole($role);

        // Create a password reset token (if needed for sending password reset email)
        $token = Password::broker()->createToken($user);
        $user->sendPasswordResetNotification($token);

        // Redirect with a success message
        return redirect()->route('admins.index')->with('success', __('translation.user.user_added_successfully'));
    }


    public function edit(User $user)
    {
        $roles = Role::all(['id', 'name']);
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|unique:users,phone,' . $id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed', // Password is optional
        ]);

        // Find the user
        $user = User::findOrFail($id);

        // Update user data
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->role_id = $validated['role_id'];

        // Only update the password if it is provided
        if ($request->has('password') && $request->password) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admins.index')->with('success', __('translation.user.user_updated_successfully'));
    }


    public function toggleBan(Request $request)
    {
        $admin = User::withTrashed()->findOrFail($request->id);

        if ($request->ban) {
            $admin->delete(); // Soft delete (ban)
        } else {
            $admin->restore(); // Restore (unban)
        }

        return response()->json(['success' => true]);
    }


}
