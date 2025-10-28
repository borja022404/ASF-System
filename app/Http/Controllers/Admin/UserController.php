<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        if (Gate::denies('admin-access')) {
            abort(403, 'Unauthorized access.');
        }

        $allusers = User::with('roles')->paginate(10);

        // Count users by role
        $adminCount = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->count();
        $vetCount = User::whereHas('roles', fn($q) => $q->where('name', 'vet'))->count();
        $farmerCount = User::whereHas('roles', fn($q) => $q->where('name', 'farmer'))->count();

        return view('admin.users.index', compact('allusers', 'adminCount', 'vetCount', 'farmerCount'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $role = Role::where('name', $request->role)->first();
        $user->roles()->attach($role->id);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        $user->load('roles'); // Good practice to eager load the roles
        return view('admin.users.edit', compact('user', 'roles'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|exists:roles,name',
        ];

        // Conditionally add a password validation rule if the field is present and not empty
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validatedData = $request->validate($rules);

        // Update user details
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        // Update password only if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Update user's role
        $role = Role::where('name', $validatedData['role'])->first();
        $user->roles()->sync([$role->id]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Example of user feedback page
     */
    public function userfeedback(): View
    {
        $allfeedbacks = DB::table('feedbacks')->paginate(10);
        return view('admin.users.feedbacks', compact('allfeedbacks'));
    }
}
