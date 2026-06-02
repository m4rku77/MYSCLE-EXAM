<?php

declare(strict_types=1);

namespace App\Http\Controllers\ExerciseLibrary;

use App\Http\Controllers\Controller;
use App\Repositories\ExerciseLibrary\ExerciseLibraryLogicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseLibraryController extends Controller
{
    public function __construct(
        private readonly ExerciseLibraryLogicRepository $logic
    ) {}

    // GET /exercise-library
    public function index(): JsonResponse
    {
        return response()->json($this->logic->getAll());
    }

    // POST /exercise-library
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $exercise = $this->logic->create($request->name);
            return response()->json($exercise, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}