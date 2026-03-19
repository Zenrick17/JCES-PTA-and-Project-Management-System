<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Split the name into first and last name
        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate username from email
        $username = explode('@', $request->email)[0];
        $baseUsername = $username;
        $counter = 1;

        // Ensure username is unique
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'username' => $username,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'plain_password' => $request->password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'user_type' => 'parent', // Default user type for registration
            'is_active' => true,
            'created_date' => now(),
            'password_changed_date' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Update last login
        $user->updateLastLogin();

        return redirect(route('dashboard', absolute: false));
    }
}
