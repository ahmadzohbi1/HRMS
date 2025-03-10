<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
class RolesController extends Controller
{
    // Display the list of roles
    public function index()
    {
        // Fetch all roles from the database
        $roles = Role::all();  // You can also use pagination if needed, e.g., Role::paginate(10);

        return view('admin.roles.index', compact('roles'));
    }

    // Show the form for creating a new role
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    // Store a newly created role in the database
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'role' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        if (strtolower($request->role) === 'super admin') {
            return redirect()->back()->with('error', 'Creating a Super Admin role is not allowed.');
        }
        // Create the role
        $role = Role::create(['name' => $validatedData['role']]);

        // Assign permissions to the role
        if ($request->filled('permissions')) {
            $permissionNames = Permission::whereIn('id', $validatedData['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        }

        // Assign role and permissions to a user for demonstration purposes
        $user = User::find(1); // Replace this with the dynamic user as per your logic
        if ($user) {
            $user->assignRole($role->name);
            $user->syncPermissions(Permission::whereIn('id', $validatedData['permissions'])->get());
        }

        return redirect()->route('roles.index')->with('success', 'Role and permissions assigned successfully!');
    }

    // Show the specific role by its ID
    public function show($id)
    {
        $role = Role::findOrFail($id);

        // Return the show view with the role
        return view('admin.roles.show', compact('role'));
    }

    // Show the form for editing the specified role
    public function edit($id)
    {
        $permissions = Permission::all();
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact(['role', 'permissions']), );
    }

    // Update the specified role in the database
    public function update(Request $request, $id)
    {
        $superAdminRoleName = 'Super Admin'; // Define Super Admin role name

        $role = Role::findOrFail($id);

        // Check if it's the Super Admin role
        if ($role->name === $superAdminRoleName) {
            return redirect()->back()->with('error', 'The Super Admin role cannot be edited.');
        }

        $request->validate([
            'role' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->name = $request->role;
        $role->save();

        if ($request->has('permissions')) {
            $permissionNames = Permission::whereIn('id', $request->permissions ?? [])->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Role and permissions updated successfully!');
    }


    // Remove the specified role from the database
    public function destroy($id)
    {
        $superAdminRoleName = 'Super Admin';

        $role = Role::findOrFail($id);

        if ($role->name === $superAdminRoleName) {
            return redirect()->back()->with('error', 'The Super Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }

}
