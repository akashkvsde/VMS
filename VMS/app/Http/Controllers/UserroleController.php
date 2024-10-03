<?php

namespace App\Http\Controllers;

use App\Models\Userrole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserroleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            // Fetch all user roles except 'superadmin' (case-insensitive)
            $userRoles = Userrole::select('role_id', 'role_name')
            ->whereRaw('LOWER(role_name) != ?', ['superadmin'])
            ->get();
        
    
            if ($userRoles->isEmpty()) {
                return response()->json(['message' => 'No user roles found.'], 404); // Not Found status code
            }
    
            return response()->json($userRoles, 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user roles.', 'error' => $e->getMessage()], 500); // Internal Server Error
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
                'role_name' => 'required|string|max:255',
                'entry_by' => 'nullable|numeric',
            ]);
    
            // Capitalize the first letter of role_name
            $validatedData['role_name'] = ucfirst(strtolower($validatedData['role_name']));
    
            // Check if the role already exists
            $existingRole = Userrole::where('role_name', $validatedData['role_name'])->first();
    
            if ($existingRole) {
                return response()->json(['message' => 'Role name already exists.'], 409); // Conflict status code
            }
    
            // Create a new user role
            $userRole = Userrole::create($validatedData);
    
            return response()->json(['message' => 'User role created successfully.', 'data' => $userRole], 201); // Created status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create user role.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $userrole = Userrole::findOrFail($id);
            return response()->json($userrole, 200); // OK status code
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User role not found.'], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user role.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Userrole $userrole)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Userrole $userrole): JsonResponse
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'role_name' => 'required|string|max:255',
                'entry_by' => 'nullable|numeric',
            ]);

            // Check if the userrole exists
            if (!$userrole) {
                return response()->json(['message' => 'User role not found.'], 404); // Not Found
            }

            // Update the user role
            $userrole->update($validatedData);

            return response()->json(['message' => 'User role updated successfully.', 'data' => $userrole], 200); // OK status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update user role.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Find the user role by ID
            $userrole = Userrole::find($id);
    
            if (is_null($userrole)) {
                // Return a custom message if the user role is not found
                return response()->json(['message' => 'User role not found.'], 404); // Not Found
            }
    
            // Delete the user role
            $userrole->delete();
    
            return response()->json(['message' => 'User role deleted successfully.'], 204); // No Content status code
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['message' => 'Failed to delete user role.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }



    public function alluserroles(): JsonResponse
    {
        try {
            // Fetch all user roles except 'superadmin' (case-insensitive)
            $userRoles = Userrole::select('role_id', 'role_name')->get();

    
            if ($userRoles->isEmpty()) {
                return response()->json(['message' => 'No user roles found.'], 404); // Not Found status code
            }
    
            return response()->json($userRoles, 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user roles.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
}
