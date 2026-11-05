<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN USERS LIST
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        // Show administrators and regular users
        $users = User::orderBy('name')
            ->get();

        return view('admin-users.index', compact('users'));
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE ADMIN FORM
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin-users.create');
    }


    /*
    |--------------------------------------------------------------------------
    | STORE NEW ADMIN
    |--------------------------------------------------------------------------
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


        /*
        |--------------------------------------------------------------------------
        | Create administrator
        |--------------------------------------------------------------------------
        |
        | is_admin is explicitly set to true here.
        |
        */

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => true,
        ]);


        return redirect()
            ->route('admin-users.index')
            ->with(
                'success',
                'Administrator account created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT USER
    |--------------------------------------------------------------------------
    */

    public function edit(User $user)
    {
        // Allow editing administrators and regular users
        return view(
            'admin-users.edit',
            compact('user')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

    
public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            'unique:users,email,' . $user->id,
        ],

        'password' => [
            'nullable',
            'confirmed',
            'min:8',
        ],

        'is_admin' => [
            'nullable',
            'boolean',
        ],
    ]);

    // Update basic account details.
    $user->name = $validated['name'];
    $user->email = $validated['email'];

    // Save administrator permission from the checkbox.
    $user->is_admin = $request->boolean('is_admin');

    // Only change the password if a new one was entered.
    if (!empty($validated['password'])) {
        $user->password = $validated['password'];
    }

    $user->save();

    return redirect()
        ->route('admin-users.index')
        ->with('success', 'User account updated successfully.');
}



    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD
    |--------------------------------------------------------------------------
    */

    public function resetPassword(Request $request, User $user)
    {
        // Allow resetting passwords for administrators and regular users

        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
        ]);


        $user->update([
            'password' => $validated['password'],
        ]);


        return redirect()
            ->route('admin-users.index')
            ->with(
                'success',
                'User password updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent deleting currently logged-in user
        |--------------------------------------------------------------------------
        */

        if ($user->id === auth()->id()) {

            return redirect()
                ->route('admin-users.index')
                ->with(
                    'error',
                    'You cannot delete your own account.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent deleting the last administrator
        |--------------------------------------------------------------------------
        */

        if ($user->is_admin) {

            $adminCount = User::where('is_admin', true)->count();

            if ($adminCount <= 1) {

                return redirect()
                    ->route('admin-users.index')
                    ->with(
                        'error',
                        'The last administrator account cannot be deleted.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Delete user
        |--------------------------------------------------------------------------
        */

        $user->delete();


        return redirect()
            ->route('admin-users.index')
            ->with(
                'success',
                'User account deleted successfully.'
            );
    }
}
