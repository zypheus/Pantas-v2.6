<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {
        // Show the createuser form
        return view('accounts.create');
    }

    public function store(Request $request, AdminActivityLogger $activities)
    {
        // Validate input
        $validated = $request->validate([
            'lname' => 'required|string|max:255',
            'fname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:super_admin,library_admin,library_staff,attendance_admin,attendance_staff',
        ]);

        // Create user
        $user = User::create([
            'lname' => $validated['lname'],
            'fname' => $validated['fname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);
        $user->syncRoles([$validated['role']]);
        $activities->log('super-admin', 'staff.created', 'Staff account created', $user->email, $user);

        return redirect()->route('users.create')->with('success', 'User account created successfully!');
    }

    // show user
    public function index()
    {
        $users = User::where('role', '!=', 'developer')->orderBy('lname')->orderBy('fname')->get();

        return view('accounts.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        abort_if($this->isDeveloperAccount($user), 403);

        return view('accounts.edit', compact('user'));
    }

    public function update(Request $request, $id, AdminActivityLogger $activities)
    {
        $request->validate([
            'fname' => 'required|string',
            'lname' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|in:super_admin,library_admin,library_staff,attendance_admin,attendance_staff',
        ]);

        $user = User::findOrFail($id);
        abort_if($this->isDeveloperAccount($user), 403);
        $user->update($request->only(['fname', 'lname', 'email', 'role']));
        $user->syncRoles([$request->string('role')->toString()]);
        $activities->log('super-admin', 'staff.updated', 'Staff account updated', $user->email, $user);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id, AdminActivityLogger $activities)
    {
        $user = User::findOrFail($id);
        abort_if($this->isDeveloperAccount($user), 403);
        $email = $user->email;
        $user->delete();
        $activities->log('super-admin', 'staff.deleted', 'Staff account deleted', $email);

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    private function isDeveloperAccount(User $user): bool
    {
        return $user->hasRole('developer') || $user->getRawOriginal('role') === 'developer';
    }
}
