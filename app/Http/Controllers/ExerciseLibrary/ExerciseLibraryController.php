<?php

namespace App\Http\Controllers\ExerciseLibrary;

use App\Http\Controllers\Controller;
use App\Models\ExerciseLibrary;
use Illuminate\Http\Request;

class ExerciseLibraryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exercise = ExerciseLibrary::create([
            'name' => $request->name,
            'muscle_group' => null,
        ]);

        return response()->json($exercise, 201);
    }
}
