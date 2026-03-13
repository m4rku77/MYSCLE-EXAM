<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TrainingPlan;
use Illuminate\Http\Request;

class TrainingPlanController extends Controller
{
    public function index()
    {
        return TrainingPlan::all();
    }

    public function show(int $id)
    {
        return TrainingPlan::findOrFail($id);
    }

    public function store(Request $request)
    {
        $trainingPlan = TrainingPlan::create($request->all());

        return response()->json($trainingPlan, 201);
    }

    public function update(Request $request, int $id)
    {
        $trainingPlan = TrainingPlan::findOrFail($id);

        $trainingPlan->update($request->all());

        return response()->json($trainingPlan);
    }

    public function destroy(int $id)
    {
        $trainingPlan = TrainingPlan::findOrFail($id);

        $trainingPlan->delete();

        return response()->json(null, 204);
    }
}