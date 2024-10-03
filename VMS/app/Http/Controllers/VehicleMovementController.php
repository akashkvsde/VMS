<?php

namespace App\Http\Controllers;

use App\Models\AssignedRole;
use App\Models\User;
use App\Models\Userrole;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VehicleMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Use Eloquent's `with` method to eager load related models
            $vehicleMovements = VehicleMovement::with(['vehicle', 'driver', 'manager', 'entryBy'])->orderBy('created_at', 'desc') // Add order by created_at
            ->get();
    
            if ($vehicleMovements->isEmpty()) {
                return response()->json(['message' => 'No vehicle movements found.'], 404);
            }
    
            return response()->json($vehicleMovements, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve vehicle movements. Please try again later.'], 500);
        }
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This method can be used to return a form view for creating a new resource if needed.
        // For API-only applications, you can omit this method.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'driver_id' => 'required|exists:users,user_id',
            'manager_id' => 'required|exists:users,user_id',
            'movement_start_from' => 'required|string|max:255',
            'movement_destination' => 'required|string|max:255',
            'purpose_of_visit' => 'nullable|string|max:255',
            'purpose' => 'required|string|max:255',
            'taken_by' => 'required|string|max:255',
            'movement_start_date' => 'required|date',
            'movement_start_time' => 'required',
            'movement_end_date' => 'nullable|date',
            'movement_end_time' => 'nullable|',
            'movement_start_km_reading_by_manager' => 'required|integer',
            'movement_end_km_reading_by_manager' => 'nullable|integer',
            'movement_start_km_reading_by_driver' => 'nullable|integer',
            'movement_start_time_by_driver' => 'nullable|string',
            'movement_end_km_reading_by_driver' => 'nullable|integer',
            'movement_distance_covered' => 'nullable|numeric',
            'movement_status' => 'required|boolean',
            'entry_by' => 'required|exists:users,user_id',
        ]);

        try {
            $vehicleMovement = VehicleMovement::create($validatedData);
            return response()->json(['message' => 'Vehicle movement created successfully.', 'data' => $vehicleMovement], 201);
        } catch (\Exception $e) {
            return response()->json(['message'=>'Failed','error' => $e->getMessage(),], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleMovement $vehicleMovement): JsonResponse
    {
        try {
            return response()->json(['data' => $vehicleMovement], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Vehicle movement not found.'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleMovement $vehicleMovement)
    {
        // This method can be used to return a form view for editing a resource if needed.
        // For API-only applications, you can omit this method.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleMovement $vehicleMovement): JsonResponse
    {
        $validatedData = $request->validate([
            'vehicle_id' => 'sometimes|exists:vehicles,vehicle_id',
            'driver_id' => 'sometimes|exists:users,user_id',
            'manager_id' => 'sometimes|exists:users,user_id',
            'movement_start_from' => 'sometimes|string|max:255',
            'movement_destination' => 'sometimes|string|max:255',
            'purpose_of_visit' => 'sometimes|string|max:255',
            'taken_by' => 'sometimes|string|max:255',
            'movement_start_date' => 'sometimes|date',
            'movement_start_time' => 'sometimes|date_format:H:i:s',
            'movement_end_date' => 'nullable|date',
            'movement_end_time' => 'nullable|date_format:H:i:s',
            'movement_start_km_reading_by_manager' => 'sometimes|integer',
            'movement_end_km_reading_by_manager' => 'nullable|integer',
            'movement_start_km_reading_by_driver' => 'sometimes|integer',
            'movement_end_km_reading_by_driver' => 'nullable|integer',
            'movement_distance_covered' => 'nullable|numeric',
            'movement_status' => 'sometimes|boolean',
            'entry_by' => 'sometimes|exists:users,user_id',
        ]);

        try {
            $vehicleMovement->update($validatedData);
            return response()->json(['message' => 'Vehicle movement updated successfully.', 'data' => $vehicleMovement], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update vehicle movement. Please try again later.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleMovement $vehicleMovement): JsonResponse
    {
        try {
            $vehicleMovement->delete();
            return response()->json(['message' => 'Vehicle movement deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete vehicle movement. Please try again later.'], 500);
        }
    }



    public function getVehicleMovementassigned(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $startDate = $request->input('start_date');
    
            // Validate inputs
            if (empty($userId) || empty($startDate)) {
                return response()->json([
                    'error' => 'Missing required parameters: user_id or start_date.'
                ], 400);
            }
    
            // Retrieve vehicle movements with sorting
            $movements = VehicleMovement::with(['vehicle', 'driver', 'manager', 'entryBy'])
                ->where('entry_by', $userId)
                ->where('movement_start_date', '=', $startDate)
                ->orderBy('created_at', 'desc') // Ensure latest records come first
                ->get();
                if ($movements->isEmpty()) {
                    return response()->json([
                        'data'=>[],
                        'message' => 'No vehicle movements found for the provided user and date.'
                    ], 404);
                }
    
            return response()->json($movements);
    
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Error retrieving vehicle movements: ' . $e->getMessage());
    
            // Return a generic error response
            return response()->json([
                'error' => 'An error occurred while retrieving vehicle movements. Please try again later.'
            ], 500);
        }
    }


    // all vehicle which are in moivement means movement status =1 those vehgile will come 
    public function getVehicleswhichareinmovment(Request $request){
        try {
            $userId = $request->input('user_id');
    
            // Validate inputs
            if (empty($userId) ) {
                return response()->json([
                    'error' => 'Missing required parameters: user_id or .'
                ], 400);
            }
    
            // Retrieve vehicle movements with sorting
            $movements = VehicleMovement::with(['vehicle', 'driver', 'manager', 'entryBy'])
                ->where('entry_by', $userId)
                ->where('movement_status', '=', '1')
                ->orderBy('created_at', 'desc') // Ensure latest records come first
                ->get();
                if ($movements->isEmpty()) {
                    return response()->json([
                        'data'=>[],
                        'message' => 'No vehicle movements found for the provided user and date.'
                    ], 404);
                }
    
            return response()->json($movements);
    
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Error retrieving vehicle movements: ' . $e->getMessage());
    
            // Return a generic error response
            return response()->json([
                'error' => 'An error occurred while retrieving vehicle movements. Please try again later.'
            ], 500);
        }
    }
    



// /Vehicle Assoignments
public function getVehicleMovementAssignments(Request $request)
{
    try {
        // Retrieve request parameters with default values
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date', now()->toDateString()); // Default to today's date if not provided

        // Validate inputs
        if (empty($userId)) {
            return response()->json([
                'error' => 'Missing required parameter: user_id.'
            ], 400);
        }

        // Retrieve vehicle movements for a specific driver on the given start date
        $movements = VehicleMovement::with(['vehicle', 'driver', 'manager', 'entryBy'])
            ->where('driver_id', $userId)
            ->whereDate('movement_start_date', $startDate)
            ->get();

        // Count how many unique vehicles are assigned to the driver on that date
        $vehicleCount = $movements->pluck('vehicle_id')->unique()->count();

        return response()->json([
            'movements' => $movements,
            'vehicle_count' => $vehicleCount
        ]);

    } catch (\Exception $e) {
        // Log the error message for debugging
        Log::error('Error retrieving vehicle movements: ' . $e->getMessage());

        // Return a generic error response
        return response()->json([
            'error' => 'An error occurred while retrieving vehicle movements. Please try again later.'
        ], 500);
    }
}


    

// Report dateRange Vise Movement report
public function reportByDateRangeofMovement(Request $request): JsonResponse
{
    // Define validation rules
    $rules = [
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date|after_or_equal:from_date',
        'start_time' => 'nullable|date_format:H:i:s',
        'end_time' => 'nullable|date_format:H:i:s|after_or_equal:start_time',
        'vehicle_id' => 'nullable|exists:vehicles,vehicle_id', 
        'organization_id' => 'nullable|exists:organizations,organization_id', 
      
    ];

    // Apply validation
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422); // Unprocessable Entity
    }

    try {
        // Retrieve validated data
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $vehicleId = $request->input('vehicle_id');
        $organizationId = $request->input('organization_id');

        // Initialize the query
        $query = VehicleMovement::query();

        // Apply filters based on provided criteria
        if ($fromDate && $toDate) {
            $query->whereBetween('movement_start_date', [$fromDate, $toDate]);

            if ($startTime && $endTime) {
                $query->where(function ($query) use ($startTime, $endTime, $fromDate, $toDate) {
                    $query->where(function ($query) use ($startTime, $endTime, $fromDate) {
                        $query->whereDate('movement_start_date', '=', $fromDate)
                              ->whereTime('movement_start_time', '>=', $startTime)
                              ->whereTime('movement_end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($query) use ($startTime, $endTime, $toDate) {
                        $query->whereDate('movement_start_date', '=', $toDate)
                              ->whereTime('movement_start_time', '>=', $startTime)
                              ->whereTime('movement_end_time', '<=', $endTime);
                    });
                });
            }
        } elseif ($startTime && $endTime) {
            $query->whereTime('movement_start_time', '>=', $startTime)
                  ->whereTime('movement_end_time', '<=', $endTime);
        }

        // Apply vehicle_id filter if provided
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }
        if ($organizationId) {
            $query->whereHas('vehicle', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });
        }
        $query->orderBy('created_at', 'desc');

        // Load related models
        $vehicleMovements = $query->with(['vehicle', 'driver', 'manager', 'entryBy'])->get();

        // Check if there are results
        if ($vehicleMovements->isEmpty()) {
            return response()->json([
                'message' => 'No vehicle movement records found for the given criteria.',
                'data' => [],
            ], 404); // Not Found
        }

        return response()->json([
            'message' => 'Vehicle movement report generated successfully.',
            'data' => $vehicleMovements,
        ], 200); // OK

    } catch (\Exception $e) {
        Log::error('Unable to generate vehicle movement report: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to generate vehicle movement report.,', $e->getMessage()], 500); // Internal Server Error
    }
}







public function updateKmReadingsByDriver(Request $request): JsonResponse
{
    // Validate the request
    $validated = $request->validate([
        'vehicle_movement_id' => 'required|integer|exists:vehicle_movements,vehicle_movement_id',
        'movement_start_km_reading_by_driver' => 'nullable|integer',
        'movement_end_km_reading_by_driver' => 'nullable|integer',
        'movement_start_time_by_driver' => 'nullable|date_format:H:i',
        'movement_end_time_by_driver' => 'nullable|date_format:H:i',
    ]);

    try {
        // Extract the ID from the request
        $id = $validated['vehicle_movement_id'];

        // Find the VehicleMovement record by ID
        $vehicleMovement = VehicleMovement::findOrFail($id);

        // Update the start kilometer reading if present
        if (array_key_exists('movement_start_km_reading_by_driver', $validated)) {
            $vehicleMovement->movement_start_km_reading_by_driver = $validated['movement_start_km_reading_by_driver'];
        }

        // Update the end kilometer reading if present
        if (array_key_exists('movement_end_km_reading_by_driver', $validated)) {
            $vehicleMovement->movement_end_km_reading_by_driver = $validated['movement_end_km_reading_by_driver'];
        }

        // Update the start time if present
        if (array_key_exists('movement_start_time_by_driver', $validated)) {
            $vehicleMovement->movement_start_time_by_driver = $validated['movement_start_time_by_driver'];
        }

        // Update the end time if present
        if (array_key_exists('movement_end_time_by_driver', $validated)) {
            $vehicleMovement->movement_end_time_by_driver = $validated['movement_end_time_by_driver'];
        }

        // Save the updated record
        $vehicleMovement->save();

        return response()->json([
            'message' => 'Kilometer readings and times by driver updated successfully.',
            'data' => $vehicleMovement,
        ], 200); // OK

    } catch (\Exception $e) {
        Log::error('Unable to update kilometer readings and times by driver: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to update kilometer readings and times by driver.'], 500);
    }
}


// vehicle Movement strat or end m reading By Manger
public function updateByManager(Request $request): JsonResponse
{
    try {
        // Extract the ID from the request
        $id = $request->input('vehicle_movement_id');

        // Find the VehicleMovement record by ID
        $vehicleMovement = VehicleMovement::findOrFail($id);

        // Update the end kilometer reading by manager if present
        if ($request->has('movement_end_km_reading_by_manager')) {
            $vehicleMovement->movement_end_km_reading_by_manager = $request->input('movement_end_km_reading_by_manager');
        }

        // Update the distance covered if present
        if ($request->has('movement_distance_covered')) {
            $vehicleMovement->movement_distance_covered = $request->input('movement_distance_covered');
        }

        // Update the end date if present
        if ($request->has('movement_end_date')) {
            $vehicleMovement->movement_end_date = $request->input('movement_end_date');
        }

        // Update the end time if present
        if ($request->has('movement_end_time')) {
            $vehicleMovement->movement_end_time = $request->input('movement_end_time');
        }

        // Conditionally update `movement_end_km_reading_by_driver` and `movement_end_time_by_driver`
        if ($request->has('movement_end_km_reading_by_driver')) {
            $vehicleMovement->movement_end_km_reading_by_driver = $request->input('movement_end_km_reading_by_driver');
        }

        if ($request->has('movement_end_time_by_driver')) {
            $vehicleMovement->movement_end_time_by_driver = $request->input('movement_end_time_by_driver');
        }

        // Update the movement status if present (should be either 1 or 0)
        if ($request->has('movement_status')) {
            $vehicleMovement->movement_status = (bool) $request->input('movement_status');
        }

        // Save the updated record
        $vehicleMovement->save();

        return response()->json([
            'message' => 'Vehicle movement record updated successfully by manager.',
            'data' => $vehicleMovement,
        ], 200); // OK

    } catch (\Exception $e) {
        Log::error('Unable to update vehicle movement record by manager: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to update vehicle movement record by manager.'], 500);
    }
}




// Free Driver
public function FreeDriver(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'organization_id' => 'required|integer|exists:organizations,organization_id',
        ]);

        $organizationId = $request->input('organization_id');

        // Get the role ID for the 'Driver' role, case-insensitive
        $driverRoleId = Userrole::whereRaw('LOWER(role_name) = ?', ['driver'])->value('role_id');

        // Get the driver IDs that are currently in use (movement_status = 1)
        $usedDriverIds = VehicleMovement::where('movement_status', 1)
                                        ->pluck('driver_id');

        // Get the free drivers based on organization_id and role
        $freeDrivers = User::where('user_organization_id', $organizationId)
                           ->where('status', 1)
                           ->whereNotIn('user_id', $usedDriverIds)
                           ->whereHas('assignedRole', function($query) use ($driverRoleId) {
                               $query->where('role_id', $driverRoleId);
                           })
                           ->get();

        return response()->json($freeDrivers);

    } catch (ValidationException $e) {
        // Return validation errors directly
        return response()->json([
            'message' => 'Validation failed.',
            'errors' => $e->getMessage(),
        ], 422);

    } catch (\Exception $e) {
        // Handle all other exceptions
        return response()->json([
            'message' => 'An error occurred while fetching free drivers.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


// Free Vehicle
public function FreeVehicle(Request $request)
{
    try {
        // Validate the request to ensure the organization_id is provided and exists
        $request->validate([
            'organization_id' => 'required|integer|exists:organizations,organization_id',
        ]);

        $organizationId = $request->input('organization_id');

        // Get the IDs of vehicles that are currently running (movement_status = 1)
        $usedVehicleIds = VehicleMovement::where('movement_status', 1)
                                         ->pluck('vehicle_id')
                                         ->toArray();

        // Fetch vehicles that belong to the specified organization and are not in use
        $freeVehicles = Vehicle::where('organization_id', $organizationId)
                                ->where('is_active', 1)
                               ->where(function ($query) use ($usedVehicleIds) {
                                   // Select vehicles that are not in the list of used vehicle IDs
                                   $query->whereNotIn('vehicle_id', $usedVehicleIds)
                                         ->orWhereNotIn('vehicle_id', $usedVehicleIds);
                               })
                               ->get();

        // Return the list of free vehicles
        return response()->json($freeVehicles);

    } catch (\Exception $e) {
        // Handle the exception and return an error response
        return response()->json([
            'message' => 'An error occurred while fetching free vehicles.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


// when movement_end_km_reading_by_driver
// movement_end_km_reading_by_manager are null
// ----------------------------------------------------
// public function FreeVehicle(Request $request)
// {
//     try {
//         // Validate the request
//         $request->validate([
//             'organization_id' => 'required|integer|exists:organizations,organization_id',
//         ]);

//         $organizationId = $request->input('organization_id');

//         // Get the vehicle IDs where either movement_end_km_reading_by_driver or movement_end_km_reading_by_manager is null
//         $freeVehicleIds = VehicleMovement::whereNull('movement_end_km_reading_by_driver')
//                                          ->orWhereNull('movement_end_km_reading_by_manager')
//                                          ->pluck('vehicle_id');

//         // Get the free vehicles based on organization_id
//         $freeVehicles = Vehicle::where('organization_id', $organizationId)
//                                ->whereIn('vehicle_id', $freeVehicleIds)
//                                ->get();

//         return response()->json($freeVehicles);

//     } catch (\Exception $e) {
//         // Handle the exception and return an error response
//         return response()->json([
//             'message' => 'An error occurred while fetching free vehicles.',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }



public function getLast5DataVehicleMovementBasedOnEntryBy(Request $request)
{
    try {
        // Validate the request parameter
        $request->validate([
            'entry_by' => 'required|integer|exists:users,user_id',
        ]);

        // Get the entry_by parameter from the request
        $entryBy = $request->query('entry_by');

        // Query the VehicleMovement model with the given entry_by and get the last 5 records
        $vehicleMovements = VehicleMovement::with(['vehicle', 'driver', 'manager'])
            ->where('entry_by', $entryBy)
            ->orderBy('created_at', 'desc') // or use 'id' if you prefer
            ->limit(5)
            ->get();

        // Return the data
        return response()->json([
            'status' => 'success',
            'data' => $vehicleMovements
        ]);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'message' => 'No records found for the given entry_by.'
        ], 404);
    } catch (QueryException $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'message' => 'Error executing the query.'
        ], 500);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'message' => 'An unexpected error occurred.'
        ], 500);
    }
}



public function updateFuelExpenseIdInMovement(Request $request, $id): JsonResponse
{
    // Validate the input
    $validatedData = $request->validate([
        'fuel_expenses_id' => 'sometimes|exists:fuel_expenses,fuel_expenses_id',
    ]);

    try {
        // Find the vehicle movement by ID
        $vehicleMovement = VehicleMovement::findOrFail($id);

        // Update the vehicle movement with the validated data
        $vehicleMovement->update($validatedData);

        return response()->json([
            'message' => 'Vehicle movement updated successfully.',
            'data' => $vehicleMovement
        ], 200);
    } catch (\Exception $e) {
        // Handle the exception and return an error response
        return response()->json([
            'error' => 'Failed to update vehicle movement. Please try again later.'
        ], 500);
    }
}


}



