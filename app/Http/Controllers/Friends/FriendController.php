<?php

namespace App\Http\Controllers\Friends;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $friends = DB::table('friends')
            ->join('users', 'friends.friend_id', '=', 'users.id')
            ->where('friends.user_id', $user->id)
            ->select('users.id', 'users.name')
            ->get();

        return response()->json($friends);
    }

    public function add(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id'
        ]);

        $user = Auth::user();

        if ($user->id == $request->friend_id) {
            return response()->json(['message' => 'Cannot add yourself'], 400);
        }

        DB::table('friends')->insertOrIgnore([
            'user_id' => $user->id,
            'friend_id' => $request->friend_id
        ]);

        return response()->json(['message' => 'Friend added']);
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

        if (!$search) {
            return response()->json([]);
        }

        $friendIds = DB::table('friends')
            ->where('user_id', $userId)
            ->pluck('friend_id');

        $users = User::where('name', 'like', '%' . $search . '%')
            ->where('id', '!=', $userId) 
            ->whereNotIn('id', $friendIds) 
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}