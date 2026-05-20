<?php

namespace App\Http\Controllers\AdminUser;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminUserController
{
    public function index()
    {
        return User::all();
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->hasFile('profile_photo')) {

            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return response()->json($user);
    }

    public function subscriptions()
    {
        $subscriptions = \App\Models\Subscription::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($subscriptions);
    }
}
