<?php

namespace App\Http\Controllers;

use App\Models\AssignedRole;
use App\Models\Organization;
use App\Models\User;
use App\Models\Userrole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $organizations = Organization::all();
             if ($organizations->isEmpty()) {
                return response()->json(['message' => 'organizations not found.'], 404); // Not Found status code
            }
          
            return response()->json($organizations, 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve organizations.', 'error' => $e->getMessage()], 500); // Internal Server Error
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
                'organization_name' => 'required|string|max:255',
                'organization_location' => 'required|string|max:255',
                'organization_inclusion_date' => 'nullable|date',
                'organization_status' => 'nullable|boolean',
                'entry_by' => 'nullable|numeric',
            ]);

            // Create a new organization
            $organization = Organization::create($validatedData);

            return response()->json(['message' => 'Organization created successfully.', 'data' => $organization], 201); // Created status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create organization.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $organization = Organization::findOrFail($id);
            return response()->json($organization, 200); // OK status code
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Organization not found.'], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve organization.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization): JsonResponse
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'organization_name' => 'required|string|max:255',
                'organization_location' => 'required|string|max:255',
                'organization_inclusion_date' => 'nullable|date',
                'organization_status' => 'nullable|boolean',
                'entry_by' => 'nullable|numeric',
            ]);

            // Check if the organization exists
            if (!$organization) {
                return response()->json(['message' => 'Organization not found.'], 404); // Not Found
            }

            // Update the organization
            $organization->update($validatedData);

            return response()->json(['message' => 'Organization updated successfully.', 'data' => $organization], 200); // OK status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update organization.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            // Find the organization by ID
            $organization = Organization::find($id);
    
            if (is_null($organization)) {
                // Return a custom message if the organization is not found
                return response()->json(['message' => 'Organization not found.'], 404); // Not Found
            }
    
            // Delete the organization
            $organization->delete();
    
            return response()->json(['message' => 'Organization deleted successfully.'], 204); // No Content status code
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['message' => 'Failed to delete organization.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }




    public function OrganizationBasedOnRole(Request $request): JsonResponse
    {
        try {
            // Check if user_id is present in the request
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json(['message' => 'user_id is required.'], 400); // Bad Request status code
            }
    
            // Fetch the user by user_id
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404); // Not Found status code
            }
    
            // Fetch the user's assigned role
            $assignedRole = AssignedRole::where('user_id', $userId)->first();
            if (!$assignedRole) {
                return response()->json(['message' => 'Assigned role not found.'], 404); // Not Found status code
            }
    
            // Fetch the role details
            $role = Userrole::find($assignedRole->role_id);
            if (!$role) {
                return response()->json(['message' => 'Role not found.'], 404); // Not Found status code
            }
    
          // Check if the role is 'superadmin'
            $isSuperAdmin = strtolower($role->role_name) === 'superadmin';  
    
    
            // Fetch organizations based on user role
            $organizations = $isSuperAdmin ? Organization::all() : Organization::where('organization_id', $user->user_organization_id)->get();
    
            if ($organizations->isEmpty()) {
                return response()->json(['message' => 'No organizations found.'], 404); // Not Found status code
            }
    
            return response()->json($organizations, 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve organizations.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
}
