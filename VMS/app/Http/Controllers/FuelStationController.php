<?php

namespace App\Http\Controllers;
use App\Models\FuelStation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FuelStationController extends Controller
{
    /* /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $fuelStations = FuelStation::all();
            return response()->json($fuelStations);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve fuel stations.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'fuel_station_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'entry_by' => 'required|exists:users,user_id',
        ]);
    
        try {
            // Create a new fuel station record
            $fuelStation = FuelStation::create($validatedData);
    
            // Return a success response with a message and the created resource
            return response()->json([
                'message' => 'Fuel station created successfully.',
                'data' => $fuelStation
            ], 201);
        } catch (ValidationException $e) {
            // Return a validation error response with specific messages
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Return a general error response
            return response()->json([
                'message' => 'Failed to create fuel station.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $fuelStation = FuelStation::findOrFail($id);
            return response()->json($fuelStation);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Fuel station not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve fuel station.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'fuel_station_name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'entry_by' => 'sometimes|exists:users,user_id',
        ]);

        try {
            // Find the fuel station by ID
            $fuelStation = FuelStation::findOrFail($id);

            // Update the fuel station record
            $fuelStation->update($validatedData);

            return response()->json($fuelStation);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Fuel station not found.'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update fuel station.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the fuel station by ID
            $fuelStation = FuelStation::findOrFail($id);

            // Delete the fuel station record
            $fuelStation->delete();

            return response()->json(['message' => 'Fuel station deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Fuel station not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete fuel station.'], 500);
        }
    }
}
