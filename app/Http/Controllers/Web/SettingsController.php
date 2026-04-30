<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function profile()
    {
        return view('settings.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'timezone' => ['required', 'string', 'max:50'],
            'language' => ['required', 'string', 'max:10'],
        ]);

        Auth::user()->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function apiTokens()
    {
        return view('settings.api-tokens', [
            'user' => Auth::user(),
            'tokens' => Auth::user()->tokens,
        ]);
    }

    public function createApiToken(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token = Auth::user()->createToken($request->name);

        return back()->with('token', $token->plainTextToken);
    }

    public function deleteApiToken(Request $request, $tokenId)
    {
        Auth::user()->tokens()->where('id', $tokenId)->delete();

        return back()->with('success', 'API token revoked.');
    }
}
