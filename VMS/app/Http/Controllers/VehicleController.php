<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Userrole;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request): JsonResponse
    // {
    //     try {
    //         // Retrieve the 'organization_id' from the request query parameters
    //         $organizationId = $request->query('organization_id');
    
    //         // Build the query based on the presence of 'organization_id'
    //         $query = Vehicle::with(['category', 'owner', 'organization']);
            
    //         if ($organizationId) {
    //             // Filter vehicles by 'organization_id' if provided
    //             $query->where('organization_id', $organizationId);
    //         }
    
    //         // Execute the query
    //         $vehicles = $query->get(); 
    
    //         if ($vehicles->isEmpty()) {
    //             return response()->json(['message' => 'No vehicles found.'], 404); // Not Found status code
    //         }
    
    //         return response()->json($vehicles, 200); // OK status code
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Failed to retrieve vehicles.', 'error' => $e->getMessage()], 500); // Internal Server Error
    //     }
    // }


    public function index(Request $request): JsonResponse
    {
        try {
            $organizationId = $request->query('organization_id');
            $userId = $request->query('user_id');
    
            $query = Vehicle::with(['category', 'owner', 'organization'])
                                ->where('is_active', 1); 
            if ($organizationId) {
                $query->where('organization_id', $organizationId);
            } elseif ($userId) {
                $query->where('entry_by', $userId);
            }
    
            // Order by 'created_at' in descending order
            $vehicles = $query->orderBy('created_at', 'asc')->get(); 
    
            if ($vehicles->isEmpty()) {
                return response()->json(['message' => 'No vehicles found.'], 404);
            }
    
            return response()->json($vehicles, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve vehicles.', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function allvehiclesinactiveinactive(Request $request): JsonResponse
    {
        try {
            $organizationId = $request->query('organization_id');
            $userId = $request->query('user_id');
    
            $query = Vehicle::with(['category', 'owner', 'organization']);
                               
            if ($organizationId) {
                $query->where('organization_id', $organizationId);
            } elseif ($userId) {
                $query->where('entry_by', $userId);
            }
    
            // Order by 'created_at' in descending order
            $vehicles = $query->orderBy('created_at', 'asc')->get(); 
    
            if ($vehicles->isEmpty()) {
                return response()->json(['message' => 'No vehicles found.'], 404);
            }
    
            return response()->json($vehicles, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve vehicles.', 'error' => $e->getMessage()], 500);
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
                'vehicle_category_id' => 'required|exists:vehicles_categories,vehicle_category_id',
                'vehicle_owner_id' => 'required|exists:vehicle_owners,vehicle_owner_id',
                'vehicle_name' => 'required|string|max:255',
                'vehicle_model' => 'nullable|string|max:255',
                'vehicle_purchase_date' => 'required|date',
                'vehicle_rc_no' => 'nullable|string|max:255|unique:vehicles,vehicle_rc_no',
                'vehicle_rto_no' => 'nullable|string|max:255|unique:vehicles,vehicle_rto_no',
                'vehicle_rc_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'vehicle_fastag_no' => 'nullable|string|max:255',
                'vehicle_fitness_end' => 'nullable|date',
                'vehicle_chassis_no' => 'nullable|string|max:255',
                'vehicle_engine_no' => 'nullable|string|max:255',
                'vehicle_fuel_type' => 'required|string|max:255',
                'organization_id' => 'required|exists:organizations,organization_id',
                'is_active' => 'boolean',
                'entry_by' => 'required|exists:users,user_id',
            ]);
    
            // Convert specified fields to uppercase
            $validatedData['vehicle_engine_no'] = strtoupper($validatedData['vehicle_engine_no']);
            $validatedData['vehicle_chassis_no'] = strtoupper($validatedData['vehicle_chassis_no']);
            $validatedData['vehicle_fastag_no'] = strtoupper($validatedData['vehicle_fastag_no']);
            $validatedData['vehicle_rto_no'] = strtoupper($validatedData['vehicle_rto_no']);
    
            // Handle file upload
            if ($request->hasFile('vehicle_rc_file')) {
                $file = $request->file('vehicle_rc_file');
                $extension = $file->getClientOriginalExtension();
                $fileName = $validatedData['vehicle_rto_no'] ? $validatedData['vehicle_rto_no'] . '.' . $extension : 'rc_file.' . $extension; // Use vehicle_rto_no or default name
                $filePath = $file->storeAs('vehicle_rc_files', $fileName, 'public'); // Ensure the 'public' disk is used
    
                // Add the file path to validated data
                $validatedData['vehicle_rc_file'] = $filePath;
            } else {
                // Handle the case where no file is provided
                $validatedData['vehicle_rc_file'] = null;
            }
    
            // Create a new vehicle
            $vehicle = Vehicle::create($validatedData);
    
            return response()->json(['message' => 'Vehicle created successfully.', 'data' => $vehicle], 201); // Created status code
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $formattedErrors = [];
        
            foreach ($errors as $field => $messages) {
                $formattedErrors[] =  implode(', ', $messages);
            }
        
            return response()->json([
                'message' => 'Validation failed. Please correct the following errors:',
                'errors' => $formattedErrors
            ], 422); // Unprocessable Entity
        
        }  catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create vehicle.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            // Eager load 'category', 'owner', and 'organization' relationships
            $vehicle = Vehicle::with(['category', 'owner', 'organization'])->findOrFail($id);
    
            return response()->json($vehicle, 200); // OK status code
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle not found.'], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve vehicle.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();

            return response()->json(['message' => 'Vehicle deleted successfully.'], 204); // No Content status code
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle not found.'], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete vehicle.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }




    public function getStatistics(Request $request)
    {
        $organizationId = $request->input('org_id');

        // 1. Total Number of Vehicles
        $totalVehicles = Vehicle::where('organization_id', $organizationId)->count();

        // 2. Total Active Drivers
        $driverRoleId = Userrole::where('role_name', 'Driver')->value('role_id');
        $totalActiveDrivers = User::whereHas('assignedRole', function ($query) use ($driverRoleId) {
            $query->where('role_id', $driverRoleId);
        })->where('status', 1)
          ->where('user_organization_id', $organizationId)
          ->count();

        // 3. Active Vehicles
        // Get all vehicles with active movements (no end date for movement)
        $activeVehicleIds = VehicleMovement::whereNull('movement_end_date')
            ->pluck('vehicle_id')->unique();
            
        $activeVehicles = Vehicle::whereIn('vehicle_id', $activeVehicleIds)
            ->where('organization_id', $organizationId)
            ->count();

        // 4. Vehicles Under Maintenance
        $vehiclesUnderMaintenance = Vehicle::where('organization_id', $organizationId)
            ->whereHas('maintenances', function ($query) {
                $query->where('maintenance_status', 'active');
            })
            ->count();

        return response()->json([
            'total_vehicles' => $totalVehicles,
            'total_active_drivers' => $totalActiveDrivers,
            'active_vehicles' => $activeVehicles,
            'vehicles_under_maintenance' => $vehiclesUnderMaintenance,
        ]);
    }



    public function updateVehicle(Request $request, $vehicleId): JsonResponse
    {
        try {
            // Find the vehicle by ID
            $vehicle = Vehicle::findOrFail($vehicleId);
    
            // Validate the request (use 'sometimes' for optional fields)
            $validatedData = $request->validate([
                'vehicle_category_id' => 'sometimes|exists:vehicles_categories,vehicle_category_id',
                'vehicle_owner_id' => 'sometimes|exists:vehicle_owners,vehicle_owner_id',
                'vehicle_name' => 'sometimes|string|max:255',
                'vehicle_model' => 'sometimes|string|max:255',
                'vehicle_purchase_date' => 'sometimes|date',
                'vehicle_rc_no' => 'sometimes|max:255',
                'vehicle_rto_no' => 'sometimes|max:255',
                'vehicle_rc_file' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'vehicle_fastag_no' => 'sometimes|max:255',
                'vehicle_fitness_end' => 'sometimes',
                'vehicle_chassis_no' => 'sometimes|max:255',
                'vehicle_engine_no' => 'sometimes|max:255',
                'vehicle_fuel_type' => 'sometimes|string|max:255',
                'organization_id' => 'sometimes|exists:organizations,organization_id',
                'is_active' => 'sometimes|boolean', // Ensure it is validated as a boolean
                'entry_by' => 'sometimes|exists:users,user_id',
            ]);
    
            // Convert specified fields to uppercase if they exist in the validated data
            $fieldsToUppercase = ['vehicle_engine_no', 'vehicle_chassis_no', 'vehicle_fastag_no', 'vehicle_rto_no'];
            foreach ($fieldsToUppercase as $field) {
                if (isset($validatedData[$field])) {
                    $validatedData[$field] = strtoupper($validatedData[$field]);
                }
            }
    
            // Handle file upload if provided
            if ($request->hasFile('vehicle_rc_file')) {
                // Delete the old file if it exists
                if ($vehicle->vehicle_rc_file) {
                    Storage::disk('public')->delete($vehicle->vehicle_rc_file);
                }
    
                $file = $request->file('vehicle_rc_file');
                $extension = $file->getClientOriginalExtension();
                $fileName = $validatedData['vehicle_rto_no'] ?? 'rc_file'; // Use vehicle_rto_no or default name
                $fileName .= '.' . $extension;
                $filePath = $file->storeAs('vehicle_rc_files', $fileName, 'public'); // Ensure the 'public' disk is used
    
                // Add the file path to validated data
                $validatedData['vehicle_rc_file'] = $filePath;
            }
    
            // Update the vehicle
            $vehicle->update($validatedData); // Directly update without array_filter
    
            return response()->json(['message' => 'Vehicle updated successfully.', 'data' => $vehicle], 200); // OK status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' =>  $e->getMessage()], 422); // Unprocessable Entity
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle not found.', 'error' => $e->getMessage()], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update vehicle.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
    
}

