<?php

namespace App\Http\Controllers\Friends;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FriendController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $friends = DB::table('friends')
            ->join('users', 'friends.friend_id', '=', 'users.id')
            ->where('friends.user_id', $user->id)
            ->select('users.id', 'users.name', 'users.profile_photo')
            ->get();

        return response()->json($friends);
    }

    public function add(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();

        DB::table('friends')->insert([
            'user_id' => $user->id,
            'friend_id' => $request->friend_id,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Friend request sent']);
    }

    public function remove($id)
    {
        $user = Auth::user();

        DB::table('friends')
            ->where('user_id', $user->id)
            ->where('friend_id', $id)
            ->delete();

        return response()->json(['message' => 'Friend removed']);
    }

    public function search(Request $request)
    {
        $search = $request->query('search');
        $userId = Auth::id();

        if (! $search) {
            return response()->json([]);
        }

        $friendIds = DB::table('friends')
            ->where('user_id', $userId)
            ->pluck('friend_id');

        $users = User::where('name', 'like', '%'.$search.'%')
            ->where('id', '!=', $userId)
            ->whereNotIn('id', $friendIds)
            ->select('id', 'name', 'profile_photo')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => $user->profile_photo,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return response()->json([
            'message' => 'Profile updated',
            'user' => $user,
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        $path = $request->file('photo')->store('profiles', 'public');

        $user->profile_photo = $path;
        $user->save();

        return response()->json([
            'message' => 'Photo uploaded',
            'photo' => $path,
        ]);
    }

    public function requests()
    {
        $userId = Auth::id();

        $requests = DB::table('friends')
            ->join('users', 'friends.user_id', '=', 'users.id')
            ->where('friends.friend_id', $userId)
            ->where('friends.status', 'pending')
            ->select('users.id', 'users.name', 'users.profile_photo')
            ->get();

        return response()->json($requests);
    }

    public function accept($id)
    {
        $userId = Auth::id();

        DB::table('friends')
            ->where('user_id', $id)
            ->where('friend_id', $userId)
            ->update(['status' => 'accepted']);

        DB::table('friends')->insert([
            'user_id' => $userId,
            'friend_id' => $id,
            'status' => 'accepted',
        ]);

        return response()->json(['message' => 'Friend added']);
    }

    public function decline($id)
    {
        $userId = Auth::id();

        DB::table('friends')
            ->where('user_id', $id)
            ->where('friend_id', $userId)
            ->delete();

        return response()->json(['message' => 'Request declined']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Wrong current password'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated']);
    }
}
