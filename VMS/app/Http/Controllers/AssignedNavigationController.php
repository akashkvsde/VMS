<?php

namespace App\Http\Controllers;

use App\Models\AssignedNavigation;
use App\Models\Navigation;
use App\Models\Userrole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignedNavigationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get the role_id from the query parameter
            $roleId = $request->input('role_id');
            // return response()->json($roleId);
        
            if (!$roleId) {
                return response()->json(['message' => 'Role ID is required.'], 400); // Bad Request
            }
    
            // Fetch assigned navigations for the given role_id
            $assignNavigations = AssignedNavigation::with(['navigation', 'userrole', 'user'])
                ->where('role_id', $roleId)
                ->get();
    
            // Check if there are any results
            if ($assignNavigations->isEmpty()) {
                return response()->json(['message' => 'No pages are assigned for the given role.'], 404); // Not Found
            }
    
            // Format the results
            $result = [
                'role_id' => $roleId,
                'role_name' => $assignNavigations->first()->userrole->role_name,
                'navigation_count' => $assignNavigations->count(),
                'navigations' => $assignNavigations->map(function ($item) {
                    return [
                        'nav_id' => $item->nav_id,
                        'assign_nav_id' => $item->assign_nav_id,
                        'nav_name' => $item->navigation->nav_name,
                        'entry_by' => $item->entry_by,
                        'entry_by_user' => $item->user->name, // Assuming user has a 'name' attribute
                    ];
                }),
            ];
        
            return response()->json($result, 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve assigned navigations.', 'error' => $e->getMessage()], 500); // Internal Server Error
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
            $validatedData = $request->validate([
                '*.nav_id' => 'required|exists:navigations,nav_id',
                '*.role_id' => 'required|exists:userroles,role_id',
                '*.entry_by' => 'required|exists:users,user_id',
            ]);
    
            if (empty($validatedData)) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => ['navigation_entries' => 'The request body must contain at least one navigation entry.']
                ], 422);
            }
    
            foreach ($validatedData as $nav) {
                if (empty($nav['nav_id'])) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => ['nav_id' => 'The nav_id cannot be null.']
                    ], 422);
                }
    
                if (empty($nav['role_id'])) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => ['role_id' => 'The role_id is required.']
                    ], 422);
                }
    
                if (empty($nav['entry_by'])) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => ['entry_by' => 'The entry_by is required.']
                    ], 422);
                }
    
                $exists = AssignedNavigation::where('nav_id', $nav['nav_id'])
                    ->where('role_id', $nav['role_id'])
                    ->exists();
    
                if ($exists) {
                    $roleName = Userrole::where('role_id', $nav['role_id'])->value('role_name');
                    $navName = Navigation::where('nav_id', $nav['nav_id'])->value('nav_name');
    
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => ["The navigation '{$navName}' is already assigned to role '{$roleName}'."]
                    ], 422);
                }
    
                AssignedNavigation::create($nav);
            }
    
            return response()->json(['message' => 'Navigations assigned successfully.'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create assigned navigations.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            // Find the assigned navigation by ID and eager load related models
            $assignNavigation = AssignedNavigation::with(['navigation', 'userrole', 'user'])->findOrFail($id);
    
            // Structure the response data
            $responseData = [
                'id' => $assignNavigation->id,
                'role_id' => $assignNavigation->role_id,
                'role_name' => $assignNavigation->userrole->role_name,
                'nav_id' => $assignNavigation->nav_id,
                'nav_name' => $assignNavigation->navigation->nav_name,
                'entry_by' => $assignNavigation->entry_by,
                'entry_by_user' => $assignNavigation->user->name, // Assuming user has a 'name' attribute
            ];
    
            return response()->json($responseData, 200); // OK status code
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Assigned navigation not found.'], 404); // Not Found
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve assigned navigation.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignedNavigation $assignedNavigation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignedNavigation $assignNavigation): JsonResponse
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'nav_id' => 'required|exists:navigations,nav_id',
                'role_id' => 'required|exists:userroles,role_id',
                'entry_by' => 'required|exists:users,user_id',
            ]);

            // Update the assigned navigation
            $assignNavigation->update($validatedData);

            return response()->json(['message' => 'Assigned navigation updated successfully.', 'data' => $assignNavigation], 200); // OK status code
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update assigned navigation.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $assignNavigation = AssignedNavigation::find($id);
    
            if (is_null($assignNavigation)) {
                return response()->json(['message' => 'Assigned navigation not found.'], 404); // Not Found
            }
    
            // Delete the assigned navigation
            $assignNavigation->delete();
    
            return response()->json(['message' => 'Assigned navigation deleted successfully.'], 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete assigned navigation.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }
}
