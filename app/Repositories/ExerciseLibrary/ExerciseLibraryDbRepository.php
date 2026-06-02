<?php

declare(strict_types=1);

namespace App\Repositories\ExerciseLibrary;

use App\Models\ExerciseLibrary;
use Illuminate\Database\Eloquent\Collection;

class ExerciseLibraryDbRepository
{
    public function getAll(): Collection
    {
        return ExerciseLibrary::all();
    }

    public function create(array $data): ExerciseLibrary
    {
        return ExerciseLibrary::create($data);
    }
}