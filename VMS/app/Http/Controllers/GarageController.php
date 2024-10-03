<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GarageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $garages = Garage::all();
            return response()->json($garages);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve garages.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'garage_name' => 'required|string|max:255',
            'garage_owner' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_no' => 'required|string|max:15',
            'entry_by' => 'required|exists:users,user_id',
        ]);

        try {
            // Create a new garage record
            $garage = Garage::create($validatedData);

            return response()->json([
                'message' => 'Garage created successfully.',
                'data' => $garage
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create garage.',
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
            $garage = Garage::findOrFail($id);
            return response()->json([
                'message' => 'Garage retrieved successfully.',
                'data' => $garage
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Garage not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve garage.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'garage_name' => 'sometimes|string|max:255',
            'garage_owner' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'contact_person' => 'sometimes|string|max:255',
            'contact_no' => 'sometimes|string|max:15',
            'entry_by' => 'sometimes|exists:users,user_id',
        ]);

        try {
            // Find the garage by ID
            $garage = Garage::findOrFail($id);

            // Update the garage record
            $garage->update($validatedData);

            return response()->json([
                'message' => 'Garage updated successfully.',
                'data' => $garage
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Garage not found.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update garage.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the garage by ID
            $garage = Garage::findOrFail($id);

            // Delete the garage record
            $garage->delete();

            return response()->json([
                'message' => 'Garage deleted successfully.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Garage not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete garage.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
