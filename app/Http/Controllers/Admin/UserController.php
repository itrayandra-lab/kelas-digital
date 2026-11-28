<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /** Roles the user form may give. Super-Admin is set up on the server only, never through the form. */
    private const ASSIGNABLE_ROLES = ['student', 'instructor', 'content-manager', 'admin'];

    /**
     * Display a listing of users.
     */
    public function index()
    {
        // Exclude Super Admin users from the list
        $users = User::with('roles')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Super-Admin');
            })
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::whereIn('name', self::ASSIGNABLE_ROLES)->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:20|unique:users|regex:/^[a-zA-Z0-9_-]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(self::ASSIGNABLE_ROLES)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role using Spatie
        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::with('roles')->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);

        // Prevent editing Super Admin users
        if ($user->hasRole('Super-Admin')) {
            return redirect()->route('admin.users.index')->with('error', 'Super Admin users cannot be edited.');
        }

        $roles = Role::whereIn('name', self::ASSIGNABLE_ROLES)->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Prevent updating Super Admin users
        if ($user->hasRole('Super-Admin')) {
            return redirect()->route('admin.users.index')->with('error', 'Super Admin users cannot be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:20|unique:users,username,'.$user->id.'|regex:/^[a-zA-Z0-9_-]+$/',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => ['required', Rule::in(self::ASSIGNABLE_ROLES)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Nobody changes their own role, so an admin can't promote or lock themselves.
        if ($user->is(auth()->user()) && ! $user->hasRole($request->role)) {
            return back()->withInput()->with('error', 'Kamu tidak bisa mengubah role akunmu sendiri.');
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting Super Admin users
        if ($user->hasRole('Super-Admin')) {
            return redirect()->back()->with('error', 'Super Admin users cannot be deleted.');
        }

        // Prevent deleting the current admin user
        if ($user->id == auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        // Remove all roles before deleting user
        $user->syncRoles([]);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
