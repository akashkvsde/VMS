<?php

namespace App\Http\Controllers;

use App\Models\FuelExpense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FuelExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            // Eager load related models to avoid N+1 query problem
            $fuelExpenses = FuelExpense::with(['vehicle', 'driver', 'entryBy', 'fuelStation','vehicle.owner'])->orderBy('created_at', 'desc') // Order by created_at in descending order
            ->get();
    
            // Calculate the sum of filling_amount and filling_quantity
            $totalFillingAmount = $fuelExpenses->sum('filling_amount');
            $totalFillingQuantity = $fuelExpenses->sum('filling_quantity');
    
            return response()->json([
                'data' => $fuelExpenses,
                'totals' => [
                    'total_filling_amount' => $totalFillingAmount,
                    'total_filling_quantity' => $totalFillingQuantity,
                ]
            ], 200); // OK
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve fuel expenses.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }
    
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'vehicle_id' => 'required|exists:vehicles,vehicle_id',
                'driver_id' => 'required|exists:users,user_id',
                'filling_date' => 'required|date',
                'fuel_station_id' => 'required|exists:fuel_stations,fuel_station_id',
                'filling_quantity' => 'required|numeric',
                'filling_amount' => 'nullable|numeric',
                'last_km_reading' => 'required|numeric',
                'filling_bill' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'entry_by' => 'required|exists:users,user_id',
                'other_owner_name' => 'nullable|string',
                'other_vehicle_no' => 'nullable|string',
            ]);
    
            // Check for duplicate entries
            $existingRecord = FuelExpense::where('vehicle_id', $validated['vehicle_id'])
                ->where('driver_id', $validated['driver_id'])
                ->whereDate('filling_date', $validated['filling_date'])
                ->where('last_km_reading', $validated['last_km_reading'])
                ->first();
    
            if ($existingRecord) {
                return response()->json([
                    'message' => 'Duplicate entry:(The fuel expense record already exists) ଏଇ ରେକର୍ଡ ଆଗରୁ ନିଆ ଯାଇ ସାରିଛି ଦୟା କରି ପରଖି ନିୟନ୍ତୁ !!.'
                ], 200); // Conflict
            }
    
            // Handle file upload if present
            if ($request->hasFile('filling_bill')) {
                $file = $request->file('filling_bill');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('filling_bills', $fileName, 'public');
                $validated['filling_bill'] = $filePath;
            }
    
            // Create a new FuelExpense record
            $fuelExpense = FuelExpense::create($validated);
    
            return response()->json([
                'message' => 'Fuel expense record created successfully.',
                'data' => $fuelExpense
            ], 201); // Created
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
    
        } catch (QueryException $e) {
            // Handle specific database errors, e.g., duplicate entry
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'Failed to create fuel expense record due to a duplicate entry.',
                    'error' => $e->getMessage()
                ], 409); // Conflict
            }
    
            return response()->json([
                'message' => 'Failed to create fuel expense record due to a database error.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create fuel expense record.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FuelExpense $fuelExpense): JsonResponse
    {
        try {
            return response()->json([
                'data' => $fuelExpense
            ], 200); // OK
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve fuel expense record.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    /**
     * Update the specified resource in storage.
     */
     /**
     * Update the specified resource in storage.
     */
    public function fuelexpenseupdate(Request $request, $id): JsonResponse
    {
        try {
            // Find the fuel expense record by ID
            $fuelExpense = FuelExpense::findOrFail($id);
    
            // Define validation rules only for the fields that are allowed to be updated
            $validated = $request->validate([
                'vehicle_id' => 'sometimes|exists:vehicles,vehicle_id',
                'driver_id' => 'sometimes|exists:users,user_id',
                'filling_date' => 'sometimes|date',
                'fuel_station_id' => 'sometimes|exists:fuel_stations,fuel_station_id',
                'filling_quantity' => 'sometimes|numeric',
                'filling_amount' => 'sometimes|numeric',
                'last_km_reading' => 'sometimes|numeric',
                'filling_bill' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'updated_by' => 'nullable|exists:users,user_id',
            ]);
    
            // Handle file upload if present
            if ($request->hasFile('filling_bill')) {
                // Delete the old file if it exists
                if ($fuelExpense->filling_bill) {
                    Storage::disk('public')->delete($fuelExpense->filling_bill);
                }
    
                $file = $request->file('filling_bill');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('filling_bills', $fileName, 'public');
                $validated['filling_bill'] = $filePath;
            }
    
            // Update only the specified fields
            $fuelExpense->update($validated);
    
            return response()->json([
                'message' => 'Fuel expense record updated successfully.',
                'data' => $fuelExpense
            ], 200); // OK
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update fuel expense record.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FuelExpense $fuelExpense): JsonResponse
    {
        try {
            // Delete the file if it exists
            if ($fuelExpense->filling_bill) {
                Storage::disk('public')->delete($fuelExpense->filling_bill);
            }

            // Delete the record
            $fuelExpense->delete();

            return response()->json([
                'message' => 'Fuel expense record deleted successfully.'
            ], 200); // OK
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete fuel expense record.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    public function daterangeFuelExpenseReport(Request $request): JsonResponse
    {
        // Define validation rules
        $rules = [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'vehicle_id' => 'nullable|exists:vehicles,vehicle_id', // Ensure vehicle_id exists in the vehicles table if provided
            'organization_id' => 'required|integer|exists:organizations,organization_id'
        ];
    
        // Apply validation
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 400); // Bad Request
        }
    
        // Retrieve validated data
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $vehicleId = $request->input('vehicle_id');
        $organizationId = $request->input('organization_id');
        // Ensure date range is valid if provided
        if (($startDate && !$endDate) || (!$startDate && $endDate)) {
            return response()->json(['message' => 'Both start_date and end_date are required if one is provided.'], 400); // Bad Request
        }
    
        // Convert to Carbon instances if dates are provided
        if ($startDate && $endDate) {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
        }
    
        // Build query for fuel expenses
        $query = FuelExpense::query()
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('filling_date', [$startDate, $endDate]);
            })
            ->with(['vehicle.owner', 'driver', 'fuelStation']);
    
        // Apply vehicle_id filter if provided
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }
        if ($organizationId) {
            $query->whereHas('vehicle', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }
       // Add order by created_at
       $fuelExpenses = $query->orderBy('created_at', 'desc')->get();
        // Execute the query
        $fuelExpenses = $query->get();
    
        if ($fuelExpenses->isEmpty()) {
            return response()->json(['message' => 'No fuel expenses found for the given criteria.'], 404); // Not Found
        }
    
        // Calculate the sums of filling_amount and filling_quantity, ignoring null values
        $totalFillingAmount = round($fuelExpenses->whereNotNull('filling_amount')->sum('filling_amount'), 2);
        $totalFillingQuantity = round($fuelExpenses->whereNotNull('filling_quantity')->sum('filling_quantity'), 2);
    
        return response()->json([
            'data' => $fuelExpenses,
            'totals' => [
                'total_filling_amount' => $totalFillingAmount,
                'total_filling_quantity' => $totalFillingQuantity,
            ]
        ], 200); // OK
    }
    
    

    
    
    
    
    


// Filling data based on user_id from and to date 
public function getFilteredFuelExpenses(Request $request): JsonResponse
{
    // Validate the request
    $validated = $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'user_id' => 'required|integer|exists:users,user_id'
    ]);

    try {
        $query = FuelExpense::with('vehicle.owner', 'driver', 'fuelStation')
            ->where('entry_by', $validated['user_id']);

        // Apply filters based on start and end dates if provided
        if (isset($validated['start_date']) && isset($validated['end_date'])) {
            $query->whereBetween('filling_date', [$validated['start_date'], $validated['end_date']]);
        } else {
            $query->latest('created_at')->take(4);
        }

        // Fetch the fuel expenses
        $fuelExpenses = $query->orderBy('created_at', 'desc')->get();

        // Log for debugging
        Log::info('Fetched fuel expenses:', $fuelExpenses->toArray());

        if ($fuelExpenses->isEmpty()) {
            return response()->json([
                'message' => 'No fuel expenses found for the given criteria.',
                'data' => [],
            ], 404); // Not Found
        }

        return response()->json([
            'message' => 'Fuel expenses retrieved successfully.',
            'data' => $fuelExpenses
        ], 200); // OK

    } catch (\Exception $e) {
        Log::error('Unable to retrieve fuel expenses: ' . $e->getMessage());
        return response()->json([
            'error' => 'Unable to retrieve fuel expenses.',
            'details' => $e->getMessage()
        ], 500); // Internal Server Error
    }
}









    // // Filling data based on user_id from and to date and org_id
public function getFilteredFuelExpensesbasedonorg(Request $request): JsonResponse
{
// Validate the request
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'organization_id' => 'required|integer|exists:organizations,organization_id'
    ]);

    try {
        // Fetch the fuel expenses related to the specified organization
        $fuelExpenses = FuelExpense::whereBetween('filling_date', [$validated['start_date'], $validated['end_date']])
            ->whereHas('vehicle', function ($query) use ($validated) {
                $query->where('organization_id', $validated['organization_id']);
            })
            ->with(['vehicle.owner', 'driver', 'fuelStation'])
            ->orderBy('created_at', 'desc') // Add order by created_at
            ->get();

        return response()->json([
            'message' => 'Fuel expenses retrieved successfully.',
            'data' => $fuelExpenses,
        ], 200); // OK

    } catch (\Exception $e) {
        Log::error('Unable to retrieve fuel expenses: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to retrieve fuel expenses.'], 500);
    }
}

public function FuelFillingReport(Request $request): JsonResponse
{
    try {
        // Validate the incoming request data
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'vehicle_id' => 'nullable|integer|exists:vehicles,vehicle_id',
            'vehicle_owner_id' => 'nullable|integer|exists:vehicle_owners,vehicle_owner_id', // New validation rule for vehicle owner ID
            'organization_id' => 'nullable|exists:organizations,organization_id', 
        ]);

        // Build the query with optional filters based on validated input
        $query = FuelExpense::with(['vehicle.owner', 'driver', 'entryBy', 'fuelStation']);

        if (!empty($validated['from_date'])) {
            $query->whereDate('filling_date', '>=', $validated['from_date']);
        }

        if (!empty($validated['to_date'])) {
            $query->whereDate('filling_date', '<=', $validated['to_date']);
        }

        if (!empty($validated['vehicle_id'])) {
            $query->where('vehicle_id', $validated['vehicle_id']);
        }

        // Check if vehicle_owner_id is provided and filter based on that
        if (!empty($validated['vehicle_owner_id'])) {
            $query->whereHas('vehicle', function($q) use ($validated) {
                $q->where('vehicle_owner_id', $validated['vehicle_owner_id']);
            });
        }
        if (!empty($validated['organization_id'])) {
            $query->whereHas('vehicle', function ($q) use ($validated) {
                $q->where('organization_id', $validated['organization_id']);
            });
        }

        // Execute the query and get the results
        $fuelExpenses = $query->get();

        // Return the data directly without manually constructing each field
        return response()->json(['data' => $fuelExpenses], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        // Handle other exceptions
        return response()->json([
            'message' => 'Failed to retrieve fuel expenses.',
            'error' => $e->getMessage()
        ], 500);
    }
}





}
