<?php

namespace App\Http\Controllers;

use App\Models\VehiclePollution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VehiclePollutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  
     public function index(Request $request): JsonResponse
        {
         try {
             // Retrieve vehicle_id and organization_id from the request
             $vehicleId = $request->input('vehicle_id');
             $organizationId = $request->input('organization_id');
     
             // Build the query
             $query = VehiclePollution::query()->with(['vehicle', 'entryBy']);
     
             // Apply filters if vehicle_id is provided
             if ($vehicleId) {
                 $query->where('vehicle_id', $vehicleId);
             }
     
             // Apply organization_id filter if provided
             if ($organizationId) {
                 $query->whereHas('vehicle', function ($query) use ($organizationId) {
                     $query->where('organization_id', $organizationId);
                 });
             }
     
             // Execute the query
             $vehiclePollutions = $query->get();
     
             if ($vehiclePollutions->isEmpty()) {
                 return response()->json(['message' => 'No vehicle pollution records found.'], 404); // Not Found
             }
     
             return response()->json($vehiclePollutions, 200); // OK
         } catch (\Exception $e) {
             Log::error('Error retrieving vehicle pollution records: ' . $e->getMessage());
             return response()->json(['message' => 'Failed to retrieve vehicle pollution records.', 'error' => $e->getMessage()], 500); // Internal Server Error
         }
     }
    
     
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate request data
        $validatedData = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'vehicle_pollution_puc_no' => 'required|string|max:255',
            'vehicle_pollution_puc_file' => 'nullable|mimes:pdf,jpg,png|max:2048',
            'vehicle_pollution_start_date' => 'required|date',
            'vehicle_pollution_end_date' => 'required|date',
            'entry_by' => 'required|exists:users,user_id',
        ]);
    
        try {
            // Check for duplicate pollution number
            $existingRecord = VehiclePollution::where('vehicle_pollution_puc_no', $validatedData['vehicle_pollution_puc_no'])->first();
            if ($existingRecord) {
                return response()->json(['error' => 'Duplicate pollution number is not allowed.'], 400);
            }
    
            // Prepare data for database insertion
            $data = $validatedData;
    
            // Handle file upload
            if ($request->hasFile('vehicle_pollution_puc_file')) {
                $file = $request->file('vehicle_pollution_puc_file');
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = $validatedData['vehicle_pollution_puc_no'] . '.' . $fileExtension;
                $filePath = 'puc_files/' . $fileName;
    
                // Check for duplicate file
                if (Storage::disk('public')->exists($filePath)) {
                    return response()->json(['error' => 'File with the same name already exists.'], 400);
                }
    
                $filePath = $file->storeAs('puc_files', $fileName, 'public');
                $data['vehicle_pollution_puc_file'] = $filePath;
            }
    
            // Create vehicle pollution record
            $vehiclePollution = VehiclePollution::create($data);
    
            return response()->json([
                'message' => 'Vehicle pollution record has been successfully added.',
                'data' => $vehiclePollution
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create vehicle pollution record.'], 500);
        }
    }
    
    
    
    
    
    /**
     * Display the specified resource.
     */
    public function show(VehiclePollution $vehiclePollution): JsonResponse
    {
        try {
            return response()->json($vehiclePollution->load(['vehicle', 'entryBy']));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to retrieve vehicle pollution record.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
  /**
 * Update the specified resource in storage.
 */
public function Pollutionupdate(Request $request, $id): JsonResponse
{
    // Validate the request
    $validated = $request->validate([
        'vehicle_pollution_puc_no' => 'required|string|max:255',
        'vehicle_pollution_start_date' => 'required|date',
        'vehicle_pollution_end_date' => 'required|date',
        'entry_by' => 'required|integer|exists:users,user_id',
        'vehicle_id' => 'required|integer|exists:vehicles,vehicle_id',
        'vehicle_pollution_puc_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
    ]);

    try {
        // Find the VehiclePollution record by ID
        $vehiclePollution = VehiclePollution::findOrFail($id);

        // Handle file upload if present
        if ($request->hasFile('vehicle_pollution_puc_file')) {
            $file = $request->file('vehicle_pollution_puc_file');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = $validated['vehicle_pollution_puc_no'] . '.' . $fileExtension;
            $filePath = 'puc_files/' . $fileName;

            // Delete the old file if it exists
            if ($vehiclePollution->vehicle_pollution_puc_file && Storage::disk('public')->exists($vehiclePollution->vehicle_pollution_puc_file)) {
                Storage::disk('public')->delete($vehiclePollution->vehicle_pollution_puc_file);
            }

            // Store the new file
            $filePath = $file->storeAs('puc_files', $fileName, 'public');

            // Update the file path in the validated data
            $validated['vehicle_pollution_puc_file'] = $filePath;
        }

        // Update the VehiclePollution record
        $vehiclePollution->update($validated);

        return response()->json([
            'message' => 'Vehicle pollution record has been successfully updated.',
            'data' => $vehiclePollution,
        ], 200); // OK

    } catch (\Exception $e) {
        Log::error('Unable to update vehicle pollution record: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to update vehicle pollution record.'], 500);
    }
}




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehiclePollution $vehiclePollution): JsonResponse
    {
        try {
            $vehiclePollution->delete();
            return response()->json(['message' => 'Vehicle pollution record deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to delete vehicle pollution record.'], 500);
        }
    }
}