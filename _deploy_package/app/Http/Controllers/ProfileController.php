<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Determine which profile view to show based on user role
        switch ($user->user_type) {
            case 'parent':
                $students = [];
                if ($user->parentProfile) {
                    $students = $user->parentProfile->students()->get();
                }
                return view('profile.parent-profile', [
                    'user' => $user,
                    'students' => $students,
                ]);
            case 'administrator':
                return view('profile.admin-profile', [
                    'user' => $user,
                ]);
            case 'principal':
                return view('profile.principal-profile', [
                    'user' => $user,
                ]);
            case 'teacher':
                return view('profile.teacher-profile', [
                    'user' => $user,
                ]);
            default:
                return view('profile.edit', [
                    'user' => $user,
                ]);
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($user->user_type === 'parent') {
            $hasEmergencyColumns = Schema::hasColumn('users', 'emergency_contact_name')
                && Schema::hasColumn('users', 'emergency_contact_phone');

            if ($hasEmergencyColumns) {
                $user->forceFill(Arr::only($validated, [
                    'emergency_contact_name',
                    'emergency_contact_phone',
                ]));
            }
        } else {
            $user->fill(Arr::except($validated, [
                'emergency_contact_name',
                'emergency_contact_phone',
            ]));

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
