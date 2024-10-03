<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\VehicleOwner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VehicleMaintenanceController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            // Retrieve all vehicle maintenance records with related models
            $vehicleMaintenances = VehicleMaintenance::with([
                'vehicle', 
                'driver', 
                'manager', 
                'authority', 
                'maintenanceProblem', 
                'entryBy',
                'garage'
            ])->get();

            if ($vehicleMaintenances->isEmpty()) {
                return response()->json(['message' => 'No vehicle maintenance records found.'], 200); // Not Found
            }

            return response()->json($vehicleMaintenances, 200); // OK
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve vehicle maintenance records.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Display a listing of the resource filtered by approval status.
     */
    public function indexByApprovalStatus(Request $request): JsonResponse
    {
        try {
            // Retrieve the approve status parameter from the request
            $approveStatus = $request->query('maintenance_approve_status');
    
            // Build the query
            $query = VehicleMaintenance::with([
                'vehicle', 
                'driver', 
                'manager', 
                'authority', 
                'maintenanceProblem', 
                'entryBy'
            ]);
    
            // Filter based on the approve status
            if ($approveStatus) {
                switch ($approveStatus) {
                    case 'Pending':
                        $query->where('maintenance_approve_status', 'Pending');
                        break;
                    case 'Approved':
                        $query->where('maintenance_approve_status', 'Approved');
                        break;
                    case 'Not Approved':
                        $query->where('maintenance_approve_status', 'Not Approved');
                        break;
                    default:
                        // Handle invalid status
                        return response()->json(['message' => 'Invalid approval status provided.'], 422); // Bad Request
                }
            }
    
            // Execute the query
            $vehicleMaintenances = $query->get();
    
            // Custom messages based on the status
            if ($vehicleMaintenances->isEmpty()) {
                $message = 'No vehicle maintenance records found.';
    
                if ($approveStatus) {
                    switch ($approveStatus) {
                        case 'Pending':
                            $message = 'No Pending vehicle maintenance records found.';
                            break;
                        case 'Approved':
                            $message = 'No Approved vehicle maintenance records found.';
                            break;
                        case 'Not Approved':
                            $message = 'No Not Approved vehicle maintenance records found.';
                            break;
                    }
                }
    
                return response()->json(['message' => $message], 200); // Not Found
            }
    
            return response()->json($vehicleMaintenances, 200); // OK
        } catch (\Exception $e) {
            // Ensure the error is caught and a message is returned in the response
            return response()->json(['message' => 'Failed to retrieve vehicle maintenance records.'], 500); // Internal Server Error
        }
    }


    public function ByApprovalStatus(Request $request): JsonResponse
    {
        try {
            // Retrieve the parameters from the request
            $approveStatus = $request->query('maintenance_approve_status');
            $authorityId = $request->query('authority_id');
            
            // Initialize the query
            $query = VehicleMaintenance::with([
                'vehicle', 
                'driver', 
                'manager', 
                'garage',
                'authority', 
                'maintenanceProblem', 
                'entryBy'
            ]);
    
            // Validate the request parameters
            if (!$approveStatus && !$authorityId) {
                return response()->json(['message' => 'Validation error: Either maintenance_approve_status or authority_id must be provided.'], 400); // Bad Request
            }
    
            // Apply the where conditions based on provided parameters
            if ($approveStatus) {
                $query->where('maintenance_approve_status', $approveStatus);
            }
            
            if ($authorityId) {
                $query->where('authority_id', $authorityId);
            }
            
            // Apply the where condition for active status
            $query->where('maintenance_status', 'Active');
    
            // Execute the query
            $vehicleMaintenances = $query->get();
            
            // Custom messages based on the status
            if ($vehicleMaintenances->isEmpty()) {
                $message = 'No active vehicle maintenance records found matching the given criteria.';
                return response()->json(['message' => $message], 404); // Not Found
            }
    
            return response()->json($vehicleMaintenances, 200); // OK
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve vehicle maintenance records.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
    
    
    
    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not needed for API controllers
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vehicle_id' => 'required|exists:vehicles,vehicle_id',
                'driver_id' => 'required|exists:users,user_id',
                'manager_id' => 'required|exists:users,user_id',
                'authority_id' => 'required|exists:users,user_id',
                'maintenance_problems_id' => 'required|exists:vehicle_problems,vehicle_problems_id',
                'maintenance_problems_other_details' => 'nullable|string',
                'maintenance_start_fuel_level' => 'nullable|numeric',
                'maintenance_end_fuel_level' => 'nullable|numeric',
                'maintenance_amount' => 'nullable|numeric',
                'maintenance_service_center_name' => 'required|exists:garages,garage_id',
                'maintenance_start_km_reading_by_manager' => 'nullable|integer',
                'maintenance_end_km_reading_by_manager' => 'nullable|integer',
                'maintenance_start_date' => 'required|date',
                'maintenance_start_time' => 'nullable|',
                'maintenance_end_date' => 'nullable|date',
                'exp_amt' => 'nullable',
                'maintenance_end_time' => 'nullable|',
                'maintenance_service_center_recept_file' => 'nullable|string',
                'maintenance_approve_status' => 'nullable|string',
                'maintenance_status' => 'required|string',
                'entry_by' => 'required|exists:users,user_id',
            ]);

            $vehicleMaintenance = VehicleMaintenance::create($validated);
            return response()->json(['message' => 'VehicleMaintenance created successfully.', 'data' => $vehicleMaintenance], 200);
            // return response()->json($vehicleMaintenance, 201); // Created status code
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create vehicle maintenance record.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleMaintenance $vehicleMaintenance): JsonResponse
    {
        try {
            $vehicleMaintenance = $vehicleMaintenance->load([
                'vehicle', 
                'driver', 
                'manager', 
                'authority', 
                'maintenanceProblem', 
                'entryBy'
            ]);

            return response()->json($vehicleMaintenance, 200); // OK
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle maintenance record not found.'], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve vehicle maintenance record.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleMaintenance $vehicleMaintenance)
    {
        // Not needed for API controllers
    }

    /**
     * Update the specified resource in storage.
     */
    public function VehicleMaintenanceupdate(Request $request, $id): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'vehicle_id' => 'sometimes|exists:vehicles,id', // Adjusted to match the correct field
            'maintenance_amount' => 'sometimes|numeric',
            'maintenance_status' => 'sometimes|', // Added validation rule
            'maintenance_end_date' => 'sometimes|date',
            'maintenance_end_fuel_level' => 'sometimes|numeric',
            'maintenance_end_km_reading_by_manager' => 'sometimes|integer',
            'maintenance_end_time' => 'sometimes',
            'maintenance_service_center_recept_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048', // Added validation for file
        ]);
    
        try {
            // Find the VehicleMaintenance record by ID or fail
            $vehicleMaintenance = VehicleMaintenance::findOrFail($id);
    
            // Handle file upload if present
            if ($request->hasFile('maintenance_service_center_recept_file')) {
                $file = $request->file('maintenance_service_center_recept_file');
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = uniqid() . '.' . $fileExtension;
                $filePath = 'maintenance_files/' . $fileName;
    
                // Optionally delete the old file if it exists
                if ($vehicleMaintenance->maintenance_service_center_recept_file && Storage::disk('public')->exists($vehicleMaintenance->maintenance_service_center_recept_file)) {
                    Storage::disk('public')->delete($vehicleMaintenance->maintenance_service_center_recept_file);
                }
    
                // Save the new file
                $filePath = $file->storeAs('maintenance_files', $fileName, 'public');
                $validated['maintenance_service_center_recept_file'] = $filePath;
            }
    
            // Update the VehicleMaintenance record
            $vehicleMaintenance->update($validated);
    
            return response()->json([
                'message' => 'Vehicle maintenance record updated successfully.',
                'data' => $vehicleMaintenance,
            ], 200); // OK
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            Log::error('Failed to update vehicle maintenance record: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update vehicle maintenance record.',
                'error' => 'An unexpected error occurred. Please try again later.',
            ], 500); // Internal Server Error
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleMaintenance $vehicleMaintenance): JsonResponse
    {
        try {
            $vehicleMaintenance->delete();
            return response()->json(['message' => 'Vehicle maintenance record deleted successfully.'], 204); // No Content
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete vehicle maintenance record.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }



    public function updateStatus(Request $request, $id): JsonResponse
    {
        // Validate that the field is required and its value is one of the allowed values
        $validatedData = $request->validate([
            'maintenance_approve_status' => 'required|string|in:Approved,Pending,Not Approved',
        ]);
    
        try {
            // Find the VehicleMaintenance record by ID
            $vehicleMaintenance = VehicleMaintenance::find($id);
    
            // Check if the record exists
            if (!$vehicleMaintenance) {
                return response()->json(['message' => 'Vehicle maintenance record not found.'], 404); // Not Found
            }
    
            // Normalize the case of the status field to Capital Case
            $status = ucfirst(strtolower($validatedData['maintenance_approve_status']));
    
            // Update only the status field
            $vehicleMaintenance->update([
                'maintenance_approve_status' => $status
            ]);
    
            // Return a success message along with the updated record
            return response()->json([
                'message' => 'Vehicle maintenance status updated successfully.',
                'data' => $vehicleMaintenance
            ], 200); // OK status code
        } catch (ValidationException $e) {
            // Handle validation exception
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'Failed to update vehicle maintenance record.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
    
    





        // Based on entry_by and maintenance_approve_status

        public function filterByEntryAndStatus(Request $request)
        {
            try {
                // Validate query parameters
                $validated = $request->validate([
                    'entry_by' => 'nullable|integer|exists:users,user_id',
                    'maintenance_approve_status' => 'nullable|string|in:Approved,Pending,Not Approved',
                ]);
        
                $entryBy = $validated['entry_by'] ?? null;
                $approveStatus = $validated['maintenance_approve_status'] ?? null;
        
                $query = VehicleMaintenance::with(['vehicle', 'driver', 'manager', 'authority', 'entryBy', 'maintenanceProblem'])
                    ->when($entryBy, function ($query, $entryBy) {
                        return $query->where('entry_by', $entryBy);
                    })
                    ->when($approveStatus, function ($query, $approveStatus) {
                        return $query->where('maintenance_approve_status', $approveStatus);
                    })
                    ->orderBy('created_at', 'desc'); // Order by creation date in descending order
        
                $maintenances = $query->get();
        
                // Check if any records are found
                if ($maintenances->isEmpty()) {
                    return response()->json(['message' => 'No records found for the given criteria.'], 404);
                }
        
                return response()->json($maintenances);
        
            } catch (ValidationException $e) {
                // Handle validation exceptions
                return response()->json(['error' => 'Invalid input data.', 'message' => $e->getMessage()], 422);
            } catch (\Exception $e) {
                // Handle other exceptions
                return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
            }
        }
        



        
        public function VehicleMaintenanceupdatesoemdata(Request $request)
        {
            try {
                // Validate request data
                $validated = $request->validate([
                    'vehicle_maintenance_id' => 'required|integer|exists:vehicle_maintenances,vehicle_maintenance_id',
                    'maintenance_start_km_reading_by_manager' => 'required|numeric',
                    'maintenance_start_fuel_level' => 'required|numeric',
                    'maintenance_start_time' => 'required|',
                ]);
        
                // Extract the data from the validated request
                $vehicleMaintenanceId = $validated['vehicle_maintenance_id'];
                $dataToUpdate = [
                    'maintenance_start_km_reading_by_manager' => $validated['maintenance_start_km_reading_by_manager'] ?? null,
                    'maintenance_start_fuel_level' => $validated['maintenance_start_fuel_level'] ?? null,
                    'maintenance_start_time' => $validated['maintenance_start_time'] ?? null,
                ];
        
                // Find the vehicle maintenance record
                $vehicleMaintenance = VehicleMaintenance::findOrFail($vehicleMaintenanceId);
        
                // Check if the maintenance status is 'Approved' or 'Approve'
                $approvedStatuses = ['Approved', 'Approve'];
                if (!in_array($vehicleMaintenance->maintenance_approve_status, $approvedStatuses)) {
                    return response()->json(['message' => 'The maintenance record is not approved. No updates allowed.'], 403);
                }
        
                // Check if the fields are already set
                $existingData = $vehicleMaintenance->only([
                    'maintenance_start_km_reading_by_manager',
                    'maintenance_start_fuel_level',
                    'maintenance_start_time'
                ]);
        
                $updateRequired = false;
        
                // Determine if update is required
                foreach ($dataToUpdate as $field => $value) {
                    if ($value !== null && $existingData[$field] === null) {
                        $updateRequired = true;
                        break;
                    }
                }
        
                if (!$updateRequired) {
                    return response()->json(['message' => 'No updates needed or fields are already set.'], 400);
                }
        
                // Update the record with new data
                $vehicleMaintenance->update($dataToUpdate);
        
                return response()->json(['message' => 'Vehicle maintenance record updated successfully.'], 200);
        
            } catch (ValidationException $e) {
                return response()->json(['error' => 'Invalid input data.', 'message' => $e->getMessage()], 422);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Vehicle maintenance record not found.', 'message' => $e->getMessage()], 404);
            } catch (\Exception $e) {
                return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
            }
        }
        
        

          // Range Wise Vehicle Maintenance Report 
        // Range Wise Vehicle Maintenance Report 
public function reportByDateRange(Request $request): JsonResponse
{
    try {
        // Define validation rules
        $rules = [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'vehicle_id' => 'nullable|exists:vehicles,vehicle_id', // Ensure vehicle_id exists in the vehicles table if provided
        ];

        // Apply validation
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        // Retrieve validated data
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $vehicleId = $request->input('vehicle_id');

        // Ensure both dates are provided if one is given
        if (($startDate && !$endDate) || (!$startDate && $endDate)) {
            return response()->json(['message' => 'Both start_date and end_date are required if one is provided.'], 400); // Bad Request
        }

        // Convert to Carbon instances if dates are provided
        if ($startDate && $endDate) {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
        }

        // Build query for vehicle maintenance records
        $query = VehicleMaintenance::query()
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                return $q->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('maintenance_start_date', [$startDate, $endDate])
                      ->orWhereBetween('maintenance_end_date', [$startDate, $endDate]);
                });
            })
            ->when($vehicleId, function ($q) use ($vehicleId) {
                return $q->where('vehicle_id', $vehicleId);
            })
            ->with(['vehicle', 'driver', 'manager', 'authority', 'entryBy', 'maintenanceProblem','garage']);

        // Execute the query
        $maintenanceRecords = $query->get();

        if ($maintenanceRecords->isEmpty()) {
            return response()->json(['message' => 'No maintenance records found for the given criteria.','data'=>[]], 200); // Not Found
        }

        // Return response
        return response()->json([
            'message' => 'Vehicle maintenance report generated successfully.',
            'data' => $maintenanceRecords
        ], 200); // OK

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to generate vehicle maintenance report.',
            'error' => $e->getMessage()
        ], 500); // Internal Server Error
    }
}




    public function getMaintenanceDataByManager(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'maintenance_approve_status' => 'required|string',
            'entry_by' => 'required|integer',
        ]);
    
        // Retrieve records with relationships for the given manager ID and maintenance_approve_status
        $maintenanceRecords = VehicleMaintenance::with([
            'vehicle',
            'driver',
            'manager',
            'authority',
            'entryBy',
            'maintenanceProblem'
        ])
        ->where('manager_id', $validatedData['entry_by'])
        ->where('maintenance_approve_status', $validatedData['maintenance_approve_status'])
        ->whereNotNull('maintenance_start_date')
        ->whereNotNull('maintenance_start_km_reading_by_manager')
        ->whereNotNull('maintenance_start_time')
        ->get();
    
        // Return the filtered records as a JSON response
        return response()->json([
            'message' => 'Maintenance data retrieved successfully',
            'data' => $maintenanceRecords,
        ], 200);
    }
    
    
    

    // Inactive Vehicle Data means whicb are avilable not in maintence
    // public function getVehiclesWhichAreNotInMaintenance()
    // {
    //     try {
    //         // Step 1: Retrieve all vehicle IDs that are currently in active maintenance
    //         $activeMaintenanceVehicleIds = VehicleMaintenance::where('maintenance_status', 'active')
    //             ->pluck('vehicle_id')
    //             ->toArray();
    
    //         // Step 2: Retrieve all vehicles that are not in active maintenance
    //         // $vehiclesNotInMaintenance = Vehicle::whereNotIn('vehicle_id', $activeMaintenanceVehicleIds)
    //         //     ->with(['category', 'owner', 'organization']) // Eager load related data
    //         //     ->get();
    //         $vehiclesNotInMaintenance = Vehicle::whereNotIn('vehicle_id', $activeMaintenanceVehicleIds)
    //         ->select('vehicle_id', 'vehicle_name') // Select only the required fields
    //         ->get();
    //         // Step 3: Return the list of vehicles as a JSON response
    //         return response()->json([
    //             'message' => 'Vehicles not in active maintenance retrieved successfully',
    //             'data' => $vehiclesNotInMaintenance,
    //         ], 200);
    
    //     } catch (\Exception $e) {
    //         // Handle any errors that may occur
    //         return response()->json([
    //             'message' => 'An error occurred while retrieving vehicles not in maintenance',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    


    public function getVehiclesWhichAreNotInMaintenance()
{
    try {
        // Step 1: Retrieve all vehicle IDs with maintenance status 'Inactive'
        $inactiveMaintenanceVehicleIds = VehicleMaintenance::where('maintenance_status', 'Inactive')
            ->pluck('vehicle_id')
            ->toArray();

        // Step 2: Retrieve all vehicles with IDs in the list above
        $vehiclesWithInactiveMaintenance = Vehicle::whereIn('vehicle_id', $inactiveMaintenanceVehicleIds)
            ->select('vehicle_id', 'vehicle_name') // Select only the required fields
            ->get();

        // Step 3: Return the list of vehicles as a JSON response
        return response()->json([
            'message' => 'Vehicles with inactive maintenance retrieved successfully',
            'data' => $vehiclesWithInactiveMaintenance,
        ], 200);

    } catch (\Exception $e) {
        // Handle any errors that may occur
        return response()->json([
            'message' => 'An error occurred while retrieving vehicles with inactive maintenance',
            'error' => $e->getMessage(),
        ], 500);
    }
}

//Based on driver id and current date(optional) take current date may date change based on request
public function basedonDriverandDateMaintenancae(Request $request)
{
    try {
        // Validate required parameters
        $request->validate([
            'driver_id' => 'required|integer',
            'date' => 'sometimes|date',
            'status' => 'sometimes|string',
        ]);

        // Retrieve request parameters with default values
        $driverId = $request->input('driver_id');
        $date = $request->input('date', now()->toDateString());
        $status = $request->input('status', 'Approved');

        // Convert status to lowercase for case-insensitive comparison
        $status = strtolower($status);

        // Query the VehicleMaintenance model
        $query = VehicleMaintenance::query();

        // Apply the driver filter
        $query->where('driver_id', $driverId);

        // Apply the date filter
        $query->whereDate('maintenance_start_date', $date);

        // Apply the status filter
        $query->whereRaw('LOWER(maintenance_approve_status) = ?', [$status]);

        // Debugging: Print the SQL query and bindings
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        // $formattedSql = vsprintf(str_replace('?', "'%s'", $sql), $bindings);
        // dd($formattedSql);

        // Eager load relationships
        $maintenances = $query->with(['vehicle', 'manager', 'driver', 'authority', 'entryBy', 'garage', 'maintenanceProblem'])->get();
      
        // Check if any records were found
        if ($maintenances->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No maintenance records found for the given criteria.',
                'data' => []
            ]);
        }

        // Return the results as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Maintenance records retrieved successfully.',
            'data' => $maintenances
        ]);

    } catch (\Illuminate\Database\QueryException $e) {
        // Handle database-related exceptions
        return response()->json([
            'success' => false,
            'error' => 'Database query error.',
            'message' => 'An error occurred while querying the database: ' . $e->getMessage()
        ], 500);

    } catch (\Exception $e) {
        // Handle general exceptions
        return response()->json([
            'success' => false,
            'error' => 'Server error.',
            'message' => 'An unexpected error occurred: ' . $e->getMessage()
        ], 500);
    }
}

    
}
