<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehiclesCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VehiclesCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $vehicleCategories = VehiclesCategory::with('users')->get();
            if ($vehicleCategories->isEmpty()) {
                return response()->json(['message' => 'No Vehicles Category Found.'], 404); // Not Found status code
            }
            return response()->json($vehicleCategories, 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve vehicle categories.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'vehicle_category_name' => 'required|string|max:255',
                'entry_by' => 'nullable|numeric',
            ]);

            // Create a new vehicle category
            $vehicleCategory = VehiclesCategory::create($validatedData);

            return response()->json(['message' => 'Vehicle category created successfully.', 'data' => $vehicleCategory], 201); // Created status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create vehicle category.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $vehicleCategory = VehiclesCategory::findOrFail($id);
            return response()->json($vehicleCategory, 200); // OK status code
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle category not found.'], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve vehicle category.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateVehicleCategory($id, Request $request): JsonResponse
    {
        try {
            // Find the vehicle category by ID
            $vehicleCategory = VehiclesCategory::findOrFail($id);
    
            // Validate the request data
            $validatedData = $request->validate([
                'vehicle_category_name' => 'required|string|max:255',
                'entry_by' => 'nullable|numeric',
            ]);
    
            // Update the vehicle category with validated data
            $vehicleCategory->update($validatedData);
    
            return response()->json([
                'message' => 'Vehicle category updated successfully.',
                'data' => $vehicleCategory
            ], 200); // OK status code
    
        } catch (Illuminate\Validation\ValidationException $e) {
            // Validation failed
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'provided_data' => $request->all() // Send the request data back for debugging
            ], 422); // Unprocessable Entity
    
        } catch (\Exception $e) {
            // General error
            return response()->json([
                'message' => 'Failed to update vehicle category.',
                'error' => $e->getMessage(),
                'provided_data' => $request->all() // Send the request data back for debugging
            ], 500); // Internal Server Error
        }
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            // Find the vehicle category by ID
            $vehicleCategory = VehiclesCategory::find($id);
    
            if (is_null($vehicleCategory)) {
                // Return a custom message if the vehicle category is not found
                return response()->json(['message' => 'Vehicle category not found.'], 404); // Not Found
            }
    
            // Delete the vehicle category
            $vehicleCategory->delete();
    
            return response()->json(['message' => 'Vehicle category deleted successfully.'], 204); // No Content status code
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['message' => 'Failed to delete vehicle category.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }



        // Report Based Vehicle Category
        public function reportByVehicleCategory(Request $request): JsonResponse
        {
            try {
                // Validate the request to ensure vehicle_category_id is present
                $validated = $request->validate([
                    'vehicle_category_id' => 'required|integer|exists:vehicles_categories,vehicle_category_id',
                    'vehicle_id' => 'nullable|integer|exists:vehicles,vehicle_id',
                    'organization_id' => 'nullable|integer|exists:organizations,organization_id',
                ]);
        
                $vehicleCategoryId = $validated['vehicle_category_id'];
        
                // Initialize the query for vehicles based on the category
                $query = Vehicle::with(['owner', 'category'])
                    ->where('vehicle_category_id', $vehicleCategoryId);
        
                // Check if vehicle_id is provided, and filter by it if so
                if (isset($validated['vehicle_id'])) {
                    $query->where('vehicle_id', $validated['vehicle_id']);
                }
        
                // Check if organization_id is provided, and filter by it if so
                if (isset($validated['organization_id'])) {
                    $query->where('organization_id', $validated['organization_id']);
                }
        
                // Retrieve vehicles based on the query
                $vehicles = $query->get();
        
                if ($vehicles->isEmpty()) {
                    return response()->json(['message' => 'No vehicles found for the given criteria.', 'data' => []], 200); // OK
                }
        
                return response()->json([
                    'message' => 'Vehicles retrieved successfully.',
                    'data' => $vehicles,
                ], 200); // OK
            } catch (\Exception $e) {
                Log::error('Error retrieving vehicles by category: ' . $e->getMessage());
                return response()->json(['message' => 'Failed to retrieve vehicles.', 'error' => $e->getMessage()], 500); // Internal Server Error
            }
        }
        
}