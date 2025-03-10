<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.companies.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.companies.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:companies,username',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email',
            'address' => 'required|string',
            'pin' => 'required|string|max:10',
        ]);

        // Create company
        $company = Company::create([
            'name' => $request->name,
            'username' => $request->username,
            'dash_pin' => $request->pin,
        ]);


        // Create the user
        $user = User::create([
            'name' => $request->username, // Or another field for the admin's name
            'is_admin' => 2,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password (change as needed)
            'phone' => $request->phone,
            'address' => $request->address,
            'role_id' => null,
            'company_id' => $company->id, // Make sure this is passed correctly
        ]);

        // Log the created user
    
        return redirect()->route('companies.index')->with('success', 'Company and admin user created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
