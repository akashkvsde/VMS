<?php

namespace App\Http\Controllers;

use App\Models\Navigation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $navigations = Navigation::all();

            if ($navigations->isEmpty()) {
                return response()->json(['message' => 'No navigations found.'], 404); // Not Found status code
            }

            return response()->json($navigations, 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve navigations.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'nav_name' => 'required|string|max:255',
                'nav_url' => 'required|string|max:255',
                'entry_by' => 'required|numeric',
            ]);

            // Create a new navigation
            $navigation = Navigation::create($validatedData);

            return response()->json(['message' => 'Navigation created successfully.', 'data' => $navigation], 201); // Created status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create navigation.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $navigation = Navigation::findOrFail($id);
            return response()->json($navigation, 200); // OK status code
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Navigation not found.'], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve navigation.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Navigation $navigation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Navigation $navigation): JsonResponse
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'nav_name' => 'required|string|max:255',
                'nav_url' => 'required|string|max:255',
                'entry_by' => 'required|numeric',
            ]);

            // Update the navigation
            $navigation->update($validatedData);

            return response()->json(['message' => 'Navigation updated successfully.', 'data' => $navigation], 200); // OK status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update navigation.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $navigation = Navigation::find($id);

            if (is_null($navigation)) {
                return response()->json(['message' => 'Navigation not found.'], 404); // Not Found
            }

            // Delete the navigation
            $navigation->delete();

            return response()->json(['message' => 'Navigation deleted successfully.'], 204); // No Content status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete navigation.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
}
