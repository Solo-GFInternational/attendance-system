<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display all administrators.
     */
    public function index()
    {
        $admins = User::where('is_admin', true)
            ->orderBy('name')
            ->get();

        return view('admin.index', compact('admins'));
    }

    /**
     * Show create administrator form.
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Store a new administrator.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => true,
        ]);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Administrator created successfully.');
    }

    /**
     * Show edit administrator form.
     */
    public function edit(User $user)
    {
        abort_unless($user->is_admin, 404);

        return view('admin.edit', compact('user'));
    }

    /**
     * Update administrator.
     */
    public function update(Request $request, User $user)
    {
        abort_unless($user->is_admin, 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'confirmed',
                'min:8',
            ],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.index')
            ->with('success', 'Administrator updated successfully.');
    }

    /**
     * Delete administrator.
     */
    public function destroy(User $user)
    {
        abort_unless($user->is_admin, 404);

        /*
         * Prevent an administrator from deleting
         * their own account.
         */
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'You cannot delete your own administrator account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.index')
            ->with('success', 'Administrator deleted successfully.');
    }
}