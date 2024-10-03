<?php

namespace App\Http\Controllers;

use App\Models\OtherVehicle;
use App\Models\OtherFuel;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OtherVehicleController extends Controller
{
    //
    public function OtherVehicleStore(Request $request): JsonResponse
    {
        try {
            // Define validation rules
            $rules = [
                'other_vehicle_number' => 'required|string|max:255|unique:other_vehicles,other_vehicle_number',
                'organization_id' => 'required|integer|exists:organizations,organization_id',
                'other_owner_name' => 'required|string|max:255',
                'entry_by' => 'required|integer',
            ];
    
            // Validate the request
            $validatedData = $request->validate($rules);
    
            // Create and save the other vehicle
            $otherVehicle = OtherVehicle::create($validatedData);
    
            // Return a successful response
            return response()->json([
                'message' => 'Other vehicle created successfully.',
                'data' => $otherVehicle
            ], 201); // Created status code
    
        } catch (ValidationException $e) {
            // Handle validation errors
            $formattedErrors = [];

            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $formattedErrors[] = $message; // Flatten the errors into an array
                }
            }
            return response()->json([
                'message' => 'Validation failed. Please correct the following errors:',
                'errors' => $formattedErrors
            ], 422); // Unprocessable Entity
    
        } catch (QueryException $e) {
            // Handle database query errors
            return response()->json([
                'message' => 'A database error occurred while creating the other vehicle.',
                'error' => 'Error details: ' . $e->getMessage()
            ], 500); // Internal Server Error
    
        } catch (\Exception $e) {
            // Handle any other types of errors
            return response()->json([
                'message' => 'An unexpected error occurred while creating the other vehicle.',
                'error' => 'Error details: ' . $e->getMessage()
            ], 500); // Internal Server Error
        }
    }
      



    public function OtherVehiclesFuelStore(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'other_vehicle_id' => 'required|integer|exists:other_vehicles,other_vehicle_id',
            'organization_id' => 'required|integer|exists:organizations,organization_id',
            'amount' => 'nullable|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'entry_by' => 'required|integer',
            'filling_station' => 'required|string|max:255',
            'approved_by' => 'nullable|string',
            'last_km_reading' => 'nullable|string',
            'filling_date' => 'required|date',
            'filling_bill' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048', // Validate image file
        ]);
    
        try {
            // Validate the data, throws ValidationException on failure
            $validator->validate();
            
            // Create a new OtherFuel instance and fill it with validated data
            $otherFuel = new OtherFuel();
            $otherFuel->other_vehicle_id = $request->input('other_vehicle_id');
            $otherFuel->organization_id = $request->input('organization_id');
            $otherFuel->amount = $request->input('amount');
            $otherFuel->quantity = $request->input('quantity');
            $otherFuel->entry_by = $request->input('entry_by');
            $otherFuel->filling_station = $request->input('filling_station');
            $otherFuel->approved_by = $request->input('approved_by');
            $otherFuel->filling_date = $request->input('filling_date');
            $otherFuel->last_km_reading = $request->input('last_km_reading');
    
            // Handle file upload
            if ($request->hasFile('filling_bill')) {
                $file = $request->file('filling_bill');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension(); // Create a unique filename
                $path = $file->storeAs('public/filling_bills', $filename); // Store the file
                $otherFuel->filling_bill = $filename; // Save filename in the database
            } else {
                $otherFuel->filling_bill = null; // No file provided
            }
    
            // Save the data
            $otherFuel->save();
    
            // Return a successful response
            return response()->json([
                'status' => 'success',
                'message' => 'Other fuel entry created successfully.',
                'data' => $otherFuel
            ], 201);
    
        } catch (ValidationException $e) {
            // Format validation errors
            $formattedErrors = [];

            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $formattedErrors[] = $message; // Flatten the errors into an array
                }
            }
    
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please correct the following errors:',
                'errors' => $formattedErrors
            ], 422); // Unprocessable Entity
    
        } catch (QueryException $e) {
            // Handle database query errors
            return response()->json([
                'status' => 'error',
                'message' => 'A database error occurred while processing your request.',
                'error' => 'Error details: ' . $e->getMessage(),
            ], 500); // Internal Server Error
    
        } catch (\Exception $e) {
            // Handle any other general errors
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => 'Error details: ' . $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }
    
    

    public function OtherVehicleGet(Request $request)
    {
        try {
            // Retrieve organization_id from the request
            $organizationId = $request->input('organization_id');
    
            // Build the query with optional filtering by organization_id
            $otherVehiclesQuery = OtherVehicle::with('organization');
    
            // Apply the organization_id filter if provided
            if ($organizationId) {
                $otherVehiclesQuery->where('organization_id', $organizationId);
            }
    
            // Get the result of the query
            $otherVehicles = $otherVehiclesQuery->get();
    
            // Return a successful response with the data
            return response()->json([
                'status' => 'success',
                'message' => 'Other vehicles retrieved successfully.',
                'data' => $otherVehicles
            ], 200);
    
        } catch (QueryException $e) {
            // Handle database query errors
            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred.',
                'error' => $e->getMessage(),
            ], 500);
    
        } catch (\Exception $e) {
            // Handle any other types of errors
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function OtherVehiclesFuelGet(Request $request)
     {
        try {
            // Get the organization_id from the request if it exists
            $organizationId = $request->input('organization_id');
    
            // Build the query
            $query = OtherFuel::with(['otherVehicle', 'garage', 'entry_by']);
    
            // Apply filter if organization_id is provided
            if ($organizationId) {
                $query->where('organization_id', $organizationId);
            }
    
            // Retrieve the data
            $otherFuels = $query->get();
    
            // Check if data was found
            if ($otherFuels->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $organizationId ? 'No data found for the provided organization ID.' : 'No data available.',
                    'data' => []
                ], $organizationId ? 404 : 200);
            }
    
            // Return a successful response with the data
            return response()->json([
                'status' => 'success',
                'message' => 'Other fuels retrieved successfully.',
                'data' => $otherFuels
            ], 200);
    
        } catch (QueryException $e) {
            // Handle database query errors
            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred.',
                'error' => $e->getMessage(),
            ], 500);
    
        } catch (\Exception $e) {
            // Handle any other types of errors
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    public function OtherVehiclesFuelUpdate(Request $request, $id)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'amount' => 'required|numeric|min:0',
        'filling_bill' => 'required|mimes:jpeg,png,jpg,pdf|max:2048', // Validate image file
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors()->toArray();
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages;
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Validation errors occurred.',
            'errors' => $formattedErrors
        ], 422);
    }

    try {
        // Find the OtherFuel instance by ID
        $otherFuel = OtherFuel::findOrFail($id);

        // Update the amount field if provided
        if ($request->has('amount')) {
            $otherFuel->amount = $request->input('amount');
        }

        // Handle file upload if a new file is provided
        if ($request->hasFile('filling_bill')) {
            // Delete the old file if it exists
            if ($otherFuel->filling_bill && Storage::exists('public/filling_bills/' . $otherFuel->filling_bill)) {
                Storage::delete('public/filling_bills/' . $otherFuel->filling_bill);
            }

            // Store the new file
            $file = $request->file('filling_bill');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension(); // Create unique filename
            $file->storeAs('public/filling_bills', $filename); // Store the file
            $otherFuel->filling_bill = $filename; // Save new filename in the database
        }

        // Save the updated record
        $otherFuel->save();

        // Return a successful response
        return response()->json([
            'status' => 'success',
            'message' => 'Other fuel entry updated successfully.',
            'data' => $otherFuel
        ], 200);

    } catch (QueryException $e) {
        // Handle database query errors
        return response()->json([
            'status' => 'error',
            'message' => 'Database error occurred.',
            'error' => $e->getMessage(),
        ], 500);

    } catch (\Exception $e) {
        // Handle any other types of errors
        return response()->json([
            'status' => 'error',
            'message' => 'An unexpected error occurred.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



// based on entryBy and 10 daata only return 
public function OtherVehiclesFuelGetBasedONEntryBy(Request $request)
{
    try {
        // Get the filters from the request
        $organizationId = $request->input('entry_by');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Build the query
        $query = OtherFuel::with(['otherVehicle', 'fillingstations', 'organization', 'entry_by']); // Adjust relationships as needed

        // Apply filter if entry_by is provided
        if ($organizationId) {
            $query->where('entry_by', $organizationId);
        }

        // Apply date range filter if both from_date and to_date are provided
        if ($fromDate && $toDate) {
            $query->whereBetween('filling_date', [$fromDate, $toDate]);
        }

        // Retrieve the data
        $otherFuels = $query->get();

        // Check if data was found
        if ($otherFuels->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => ($organizationId || $fromDate || $toDate) 
                    ? 'No data found for the provided filters.' 
                    : 'No data available.',
                'data' => []
            ], 404);
        }

        // Return a successful response with the data
        return response()->json([
            'status' => 'success',
            'message' => 'Other fuels retrieved successfully.',
            'data' => $otherFuels
        ], 200);

    } catch (QueryException $e) {
        // Handle database query errors
        return response()->json([
            'status' => 'error',
            'message' => 'Database error occurred.',
            'error' => $e->getMessage(),
        ], 500);

    } catch (\Exception $e) {
        // Handle any other types of errors
        return response()->json([
            'status' => 'error',
            'message' => 'An unexpected error occurred.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

// Unofficial Date range FuelExpense
// UnofficialFuelExpense
// Unofficail fuel
public function UnofficialFuelAExpense(Request $request): JsonResponse
{

    try {
        // Define validation rules
        $rules = [
            'organization_id' => 'required|exists:organizations,organization_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'other_vehicle_id' => 'nullable|exists:other_vehicles,other_vehicle_id',
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
        $organizationId = $request->input('organization_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $otherVehicleId = $request->input('other_vehicle_id'); 

        // Initialize query builder for fetching fuel expenses
        $query = OtherFuel::with(['otherVehicle', 'fillingstations', 'entry_by', 'organization'])
            ->where('organization_id', $organizationId);

        // Convert to Carbon instances if dates are provided
        if ($fromDate) {
            $fromDate = \Carbon\Carbon::parse($fromDate)->startOfDay();
        }

        if ($toDate) {
            $toDate = \Carbon\Carbon::parse($toDate)->endOfDay();
        }

        // Ensure date range is valid if both dates are provided
        if ($fromDate && $toDate && $fromDate > $toDate) {
            return response()->json([
                'message' => 'from_date cannot be after to_date.'
            ], 400); // Bad Request
        }

        // Apply date range filter if both dates are provided
        if ($fromDate && $toDate) {
            $query->whereBetween('filling_date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            // Only from_date provided
            $query->whereDate('filling_date', '>=', $fromDate);
        } elseif ($toDate) {
            // Only to_date provided
            $query->whereDate('filling_date', '<=', $toDate);
        }

        
        if ($otherVehicleId) {
            $query->where('other_vehicle_id', $otherVehicleId);
        }

        // Execute query and get results
        $otherFuelExpenses = $query->get();

        // Check if data is empty
        if ($otherFuelExpenses->isEmpty()) {
            return response()->json([
                'message' => 'No fuel expenses found for the given criteria.'
            ], 404); // Not Found
        }

        // Calculate the total amount and quantity
        $totalAmount = $otherFuelExpenses->sum('amount');
        $totalQuantity = $otherFuelExpenses->sum('quantity');

        // Return the response
        return response()->json([
            'data' => $otherFuelExpenses,
            'totals' => [
                'total_amount' => $totalAmount,
                'total_quantity' => $totalQuantity,
            ]
        ], 200); // OK
    } catch (\Exception $e) {
        // Handle any errors that may occur
        return response()->json([
            'message' => 'Failed to retrieve other fuel expenses.',
            'error' => $e->getMessage()
        ], 500); // Internal Server Error
    }
}



}
