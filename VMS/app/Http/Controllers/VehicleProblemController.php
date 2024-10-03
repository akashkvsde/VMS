<?php

namespace App\Http\Controllers;

use App\Models\VehicleProblem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehicleProblemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            // Retrieve all vehicle problems
            $vehicleProblems = VehicleProblem::all();
    
            if ($vehicleProblems->isEmpty()) {
                return response()->json(['message' => 'No vehicle problems found.'], 404);
            }
    
            return response()->json($vehicleProblems, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to retrieve vehicle problems.'], 500);
        }
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Typically, this method is not used in API controllers.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_problems_name' => 'required|string|max:255',
            'entry_by' => 'required|exists:users,user_id',
        ]);

        try {
            $vehicleProblem = VehicleProblem::create($validated);

            return response()->json([
                'message' => 'Vehicle problem record has been successfully created.',
                'data' => $vehicleProblem
            ], 201); // Created status code
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create vehicle problem record.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleProblem $vehicleProblem): JsonResponse
    {
        return response()->json($vehicleProblem, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleProblem $vehicleProblem)
    {
        // Typically, this method is not used in API controllers.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleProblem $vehicleProblem): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_problems_name' => 'sometimes|required|string|max:255',
            'entry_by' => 'sometimes|required|exists:users,user_id',
        ]);

        try {
            $vehicleProblem->update($validated);

            return response()->json([
                'message' => 'Vehicle problem record has been successfully updated.',
                'data' => $vehicleProblem
            ], 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to update vehicle problem record.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleProblem $vehicleProblem): JsonResponse
    {
        try {
            $vehicleProblem->delete();

            return response()->json(['message' => 'Vehicle problem record has been successfully deleted.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to delete vehicle problem record.'], 500);
        }
    }
}
