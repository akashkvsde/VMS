<?php

namespace App\Http\Controllers;

use App\Models\VehicleInsurance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VehicleInsuranceController extends Controller
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
            $query = VehicleInsurance::query()->with('vehicle');
    
            // Apply vehicle_id filter if provided
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
            $vehicleInsurances = $query->get();
    
            if ($vehicleInsurances->isEmpty()) {
                return response()->json(['message' => 'No vehicle insurances found.'], 200); // Not Found
            }
    
            return response()->json($vehicleInsurances, 200); // OK
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle insurances: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve vehicle insurances.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Typically, this would return a view to display a form.
        // For API-only apps, you might not need this.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'insurance_company_name' => 'required|string|max:255',
            'vehicle_insurance_agent_name' => 'required|string|max:255',
            'vehicle_insurance_agent_mobile_no' => 'required|string|max:15',
            'vehicle_insurance_no' => 'required|string|max:255',
            'vehicle_insurance_file' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'vehicle_insurance_start_date' => 'required|date',
            'vehicle_insurance_end_date' => 'required|date|after_or_equal:vehicle_insurance_start_date',
            'entry_by' => 'required|integer|exists:users,user_id',
        ]);
    
        try {
            // Handle file upload if present
            $filePath = null;
            if ($request->hasFile('vehicle_insurance_file')) {
                $file = $request->file('vehicle_insurance_file');
                $fileExtension = $file->getClientOriginalExtension();
                $insuranceNo = $validated['vehicle_insurance_no']; // Retrieve insurance number
                $fileName = 'insurance_' . $insuranceNo . '.' . $fileExtension; // Use insurance number in the file name
                $filePath = 'insurance_files/' . $fileName;
    
                // Store the file
                $filePath = $file->storeAs('insurance_files', $fileName, 'public');
            }
    
            // Create the VehicleInsurance record
            $vehicleInsurance = VehicleInsurance::create(array_merge($validated, [
                'vehicle_insurance_file' => $filePath,
            ]));
    
            return response()->json([
                'message' => 'Vehicle insurance record has been successfully created.',
                'data' => $vehicleInsurance,
            ], 201); // Created
    
        } catch (\Exception $e) {
            Log::error('Unable to create vehicle insurance record: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to create vehicle insurance record.'], 500);
        }
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(VehicleInsurance $vehicleInsurance): JsonResponse
    {
        try {
            return response()->json($vehicleInsurance->load('vehicle'), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to retrieve vehicle insurance.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleInsurance $vehicleInsurance)
    {
        // Typically, this would return a view to display an edit form.
        // For API-only apps, you might not need this.
    }

    /**
     * Update the specified resource in storage.
     */
    public function Insuranceupdate(Request $request, $id): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'vehicle_id' => 'sometimes|exists:vehicles,vehicle_id',
            'insurance_company_name' => 'sometimes|string|max:255',
            'vehicle_insurance_agent_name' => 'sometimes|string|max:255',
            'vehicle_insurance_agent_mobile_no' => 'sometimes|string|max:15',
            'vehicle_insurance_no' => 'sometimes|string|max:255',
            'vehicle_insurance_file' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'vehicle_insurance_start_date' => 'sometimes|date',
            'vehicle_insurance_end_date' => 'sometimes|date|after_or_equal:vehicle_insurance_start_date',
            'entry_by' => 'sometimes|integer|exists:users,user_id',
        ]);
    
        try {
            // Find the VehicleInsurance record by ID
            $vehicleInsurance = VehicleInsurance::findOrFail($id);
    
            // Handle file upload if present
            if ($request->hasFile('vehicle_insurance_file')) {
                $file = $request->file('vehicle_insurance_file');
                $fileExtension = $file->getClientOriginalExtension();
                
                // Get the insurance number if provided, otherwise use the current one
                $insuranceNo = $request->input('vehicle_insurance_no', $vehicleInsurance->vehicle_insurance_no);
    
                $fileName = 'insurance_' . $insuranceNo . '.' . $fileExtension;
                $filePath = 'insurance_files/' . $fileName;
    
                // Delete the old file if it exists
                if ($vehicleInsurance->vehicle_insurance_file && Storage::disk('public')->exists($vehicleInsurance->vehicle_insurance_file)) {
                    Storage::disk('public')->delete($vehicleInsurance->vehicle_insurance_file);
                }
    
                // Store the new file
                $filePath = $file->storeAs('insurance_files', $fileName, 'public');
    
                // Update the file path in the validated data
                $validated['vehicle_insurance_file'] = $filePath;
            }
    
            // Update the VehicleInsurance record
            $vehicleInsurance->update($validated);
    
            return response()->json([
                'message' => 'Vehicle insurance record has been successfully updated.',
                'data' => $vehicleInsurance,
            ], 200); // OK
    
        } catch (\Exception $e) {
            Log::error('Unable to update vehicle insurance record: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to update vehicle insurance record.'], 500);
        }
    }
    
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleInsurance $vehicleInsurance): JsonResponse
    {
        try {
            $vehicleInsurance->delete();

            return response()->json(['message' => 'Vehicle insurance deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to delete vehicle insurance.'], 500);
        }
    }
}
