<?php

namespace App\Http\Controllers;

use App\Models\VehicleOwner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class VehicleOwnerController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $vehicleOwners = VehicleOwner::with(['entryBy', 'organization'])->get();
            
            if ($vehicleOwners->isEmpty()) {
                return response()->json(['message' => 'No vehicle owners found.'], 404); // Not Found
            }
            
            return response()->json($vehicleOwners, 200); // OK
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle owners: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve vehicle owners.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'vehicle_owner_name' => 'required|string|max:255',
                'organization_id' => 'required|exists:organizations,organization_id',
               'vehicle_owner_mobile_no_1' => 'required|string|max:15|unique:vehicle_owners,vehicle_owner_mobile_no_1',
                'vehicle_owner_mobile_no_2' => 'nullable|string|max:15',
                'entry_by' => 'required|exists:users,user_id',
            ]);

            $vehicleOwner = VehicleOwner::create($validatedData);
            
            return response()->json(['message' => 'Vehicle owner created successfully.', 'data' => $vehicleOwner], 201); // Created
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            Log::error('Error creating vehicle owner: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create vehicle owner.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $vehicleOwner = VehicleOwner::with(['entryBy', 'organization'])->findOrFail($id);
            return response()->json(['message' => 'Vehicle owner retrieved successfully.', 'data' => $vehicleOwner], 200); // OK
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle owner not found.'], 404); // Not Found
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle owner: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve vehicle owner.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'vehicle_owner_name' => 'required|string|max:255',
                'organization_id' => 'required|exists:organizations,organization_id',
                'vehicle_owner_mobile_no_1' => 'required|string|max:15',
                'vehicle_owner_mobile_no_2' => 'nullable|string|max:15',
                'entry_by' => 'required|exists:users,user_id',
            ]);

            $vehicleOwner = VehicleOwner::findOrFail($id);
            $vehicleOwner->update($validatedData);
            
            return response()->json(['message' => 'Vehicle owner updated successfully.', 'data' => $vehicleOwner], 200); // OK
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle owner not found.'], 404); // Not Found
        } catch (\Exception $e) {
            Log::error('Error updating vehicle owner: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update vehicle owner.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $vehicleOwner = VehicleOwner::findOrFail($id);
            $vehicleOwner->delete();
            
            return response()->json(['message' => 'Vehicle owner deleted successfully.'], 200); // OK
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle owner not found.'], 404); // Not Found
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle owner: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete vehicle owner.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
}
