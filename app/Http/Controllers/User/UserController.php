<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\User\UserLogicRepository;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserLogicRepository $logic
    ) {}

    // GET /users/{id}
    public function show(int $id): JsonResponse
    {
        try {
            return response()->json($this->logic->getWithStats($id));
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    // DELETE /admin/users/{id}
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->logic->delete($id);
            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }
}