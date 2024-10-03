<?php

namespace App\Http\Controllers;

use App\Models\AssignedRole;
use App\Models\User;
use App\Models\Userrole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Assignedrolecontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate the incoming request to ensure 'user_id' is provided
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed. Please correct the following errors:',
                'errors' => $validator->errors()->all(),
            ], 422); // Unprocessable Entity
        }

        try {
            // Retrieve the user by user_id
            $user = User::findOrFail($request->user_id);

            // Retrieve all roles assigned to this user
            $assignedRoles = AssignedRole::where('user_id', $request->user_id)
                ->with('role') // Eager load the related role
                ->get();

            // Format the roles data
            $roles = $assignedRoles->map(function ($assignedRole) use ($user) {
                return [
                    'assigned_role_id' => $assignedRole->assigned_role_id,
                    'role_id' => $assignedRole->role->role_id,
                    'role_name' => $assignedRole->role->role_name,
                    'user_id' => $user->user_id,
                    'user_name' => $user->user_name,
                ];
            });

            return response()->json([
                'message' => 'Roles retrieved successfully.',
                'roles' => $roles,
            ], 200); // OK
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $formattedErrors = [];

            foreach ($errors as $field => $messages) {
                $formattedErrors[] = implode(', ', $messages);
            }

            return response()->json([
                'message' => 'Validation failed. Please correct the following errors:',
                'errors' => $formattedErrors
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving roles.',
                'errors' => [$e->getMessage()],
            ], 500); // Internal Server Error
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
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'role_id' => 'required|exists:userroles,role_id',
            'entry_by' => 'required',
        ]);

        try {
            $validator->validate();

            // Check if the role is already assigned to the given user
            $existingRole = AssignedRole::where('user_id', $request->user_id)
                ->where('role_id', $request->role_id)
                ->first();

            if ($existingRole) {
                $user = User::find($request->user_id);
                return response()->json([
                    'message' => "The role is already assigned to user {$user->user_name}.",
                ], 200);
            }

            // Create the new role assignment
            $assignedRole = AssignedRole::create($request->only('user_id', 'role_id', 'entry_by'));

            return response()->json([
                'message' => 'Role assigned successfully.',
                'data' => $assignedRole,
            ], 201);
        } catch (ValidationException $e) {
            // Format validation errors
            $errors = $e->errors();
            $formattedErrors = [];
        
            foreach ($errors as $field => $messages) {
                $formattedErrors[] = implode(', ', $messages);
            }
        
            return response()->json([
                'message' => 'Validation failed. Please correct the following errors:',
                'errors' => $formattedErrors
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            // Handle any other errors
            return response()->json([
                'message' => 'An error occurred while assigning the role.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignedRole $assignedRole)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignedRole $assignedRole)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignedRole $assignedRole)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignedRole $assignedRole)
    {
        //
    }

    public function removeRole(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'role_id' => 'required|exists:userroles,role_id',
            'assigned_role_id' => 'required|exists:assigned_roles,assigned_role_id',
        ]);

        try {
            $validator->validate();

            // Retrieve the user and check if the role_id is the user's primary role
            $user = User::findOrFail($request->user_id);

            // Retrieve the role name based on role_id
            $role = Userrole::findOrFail($request->role_id);

            if ($user->role_id == $request->role_id) {
                return response()->json([
                    'message' => "Cannot remove the primary role '{$role->role_name}' from user '{$user->user_name}'.",
                ], 200); // Forbidden
            }

            // If not the primary role, proceed to delete the role assignment
            $assignedRole = AssignedRole::where('assigned_role_id', $request->assigned_role_id)
                ->where('user_id', $request->user_id)
                ->where('role_id', $request->role_id)
                ->first();

            if ($assignedRole) {
                $assignedRole->delete();
                return response()->json([
                    'message' => "Role '{$role->role_name}' removed successfully from user '{$user->user_name}'.",
                ], 200); // OK
            } else {
                return response()->json([
                    'message' => 'Assigned role not found or does not match the provided user and role.',
                ], 404); // Not Found
            }
        } catch (ValidationException $e) {
            // Format validation errors
            $errors = $e->errors();
            $formattedErrors = [];
        
            foreach ($errors as $field => $messages) {
                $formattedErrors[] = implode(', ', $messages);
            }
        
            return response()->json([
                'message' => 'Validation failed. Please correct the following errors:',
                'errors' => $formattedErrors,
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            // Handle any other errors
            return response()->json([
                'message' => 'An error occurred while removing the role.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

      // Here based on org_id and user_id data will come
      public function assinedroleuserData(Request $request)
      {
          try {
              // Validate the request
              $request->validate([
                  'user_organization_id' => 'nullable|integer',
                  'user_id' => 'nullable|integer',
              ]);
      
              // Initialize the query
              $query = User::query();
      
              // Filter by organization if user_organization_id is provided
              if ($request->has('user_organization_id')) {
                  $query->where('user_organization_id', $request->user_organization_id);
              }
      
              // Filter by user if user_id is provided
              if ($request->has('user_id')) {
                  $query->where('user_id', $request->user_id);
              }
      
              // Get the users with their assigned roles
              $users = $query->with(['assignedRoles.role' => function($query) {
                  $query->select('role_id', 'role_name'); // Only select required fields
              }])
              ->get()
              ->filter(function($user) {
                  // Exclude 'superadmin' users
                  return !$user->assignedRoles->contains(function($assignedRole) {
                      return strtolower($assignedRole->role->role_name) === 'superadmin';
                  });
              });
      
              // Prepare the response data
              $response = $users->map(function($user) {
                  // Aggregate role names into a comma-separated list
                  $roleNames = $user->assignedRoles->map(function($assignedRole) {
                      return $assignedRole->role->role_name;
                  })->implode(', ');
      
                  // Generate full URL for the photo if it exists
                  $photoUrl = $user->photo ? Storage::url($user->photo) : null;
      
                  return [
                      'user_id' => $user->user_id,
                      'user_name' => $user->user_name,
                      'role_id' => $user->role_id,
                      'photo' => $photoUrl, // Include the full photo URL
                      'roles' => $roleNames,
                  ];
              })->values(); // Ensure the response is a proper array of objects
      
              return response()->json([
                  'status' => 'success',
                  'data' => $response
              ], 200); // 200 OK
      
          } catch (\Illuminate\Validation\ValidationException $e) {
              // Handle validation exceptions
              $errors = $e->errors();
              $formattedErrors = [];
      
              foreach ($errors as $field => $messages) {
                  $formattedErrors[$field] = implode(', ', $messages);
              }
      
              return response()->json([
                  'status' => 'error',
                  'message' => 'Validation failed. Please correct the following errors:',
                  'errors' => $formattedErrors
              ], 422); // 422 Unprocessable Entity
          } catch (\Exception $e) {
              // Handle other exceptions
              return response()->json([
                  'status' => 'error',
                  'error_code' => 'INTERNAL_SERVER_ERROR',
                  'message' => 'An error occurred while retrieving the data.',
                  'details' => $e->getMessage()
              ], 500); // 500 Internal Server Error
          }
      }
}
