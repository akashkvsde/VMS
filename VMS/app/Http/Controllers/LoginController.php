<?php

namespace App\Http\Controllers;

use App\Models\AssignedNavigation;
use App\Models\AssignedRole;
use App\Models\LoginCredential;
use App\Models\Organization;
use App\Models\User;
use App\Models\Userrole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    //


    public function login(Request $request)
    {

        // if ($request->isMethod('get')) {
        //     return response()->json([
        //         'message' => 'Unauthorized',
        //         'error' => 'Your requests are not allowed on this route.'
        //     ], 401); // Unauthorized
        // }
        try {
            // Validate incoming request
            $request->validate([
                'login_id' => 'required|string',
                'user_password' => 'required|string',
            ]);
    
            // Fetch login credentials by login ID
            $loginCredentials = LoginCredential::where('login_id', $request->login_id)->first();
    
            // Fetch user by ID if login credentials are found
            $user = $loginCredentials ? User::find($loginCredentials->user_id) : null;
    
            // Validate credentials and check if both user and login credentials are active
            if (!$user || !$loginCredentials || !Hash::check($request->user_password, $loginCredentials->user_password) || !$loginCredentials->is_active || $user->status != 1) {
                throw ValidationException::withMessages([
                    'login_id' => ['The provided credentials are incorrect or the user is inactive.'],
                ]);
            }
    
            // Fetch all roles associated with the user
            $assignedRoles = AssignedRole::where('user_id', $user->user_id)->pluck('role_id')->toArray();
    
            if (empty($assignedRoles)) {
                throw ValidationException::withMessages([
                    'login_id' => ['No role assigned to the user.'],
                ]);
            }
    
            // Fetch assigned navigation pages for all roles, ensuring unique navigations
            $assignedNavigations = AssignedNavigation::whereIn('role_id', $assignedRoles)
                ->with('navigation') // Eager load navigation relationship
                ->get()->unique('nav_id');
    
            // Group the navigations by 'nav_title' and format them
            $groupedNavigations = $assignedNavigations->groupBy('navigation.nav_title')->map(function ($navGroup, $navTitle) {
                return [
                    'nav_title' => $navTitle,
                    'nav_icon' => $navGroup->first()->navigation->nav_icon, // Get the nav_icon for the group
                    'nav_items' => $navGroup->map(function ($assignNavigation) {
                        return [
                            'nav_id' => $assignNavigation->nav_id,
                            'nav_name' => $assignNavigation->navigation->nav_name,
                            'nav_url' => $assignNavigation->navigation->nav_url,
                        ];
                    })
                ];
            })->values();
    
            // Fetch the user's organization
            $organization = Organization::find($user->user_organization_id);
    
            // Get the first assigned role (optional)
            $firstAssignedRole = UserRole::find($assignedRoles[0]);
    
            // Create personal access token
            $token = $user->createToken('Personal Access Token')->plainTextToken;
    
            // Return response
            return response()->json([
                'token' => $token,
                'user' => $user,
                'role' => [
                    'role_id' => $firstAssignedRole->role_id,
                    'role_name' => $firstAssignedRole->role_name,
                ],
                'roles' => UserRole::whereIn('role_id', $assignedRoles)->pluck('role_name'),
                'organization' => $organization,
                'navigations' => $groupedNavigations, // Return grouped navigations by nav_title
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login failed.', 'error' => $e->getMessage()], 500); // Internal Server Error
        }
    }



public function LogDetails(Request $request)
{
    $clientIp = $request->ip();
    
    // Make an external request to get the public IP address
    $publicIp = file_get_contents('https://api.ipify.org?format=json');
    $publicIpData = json_decode($publicIp, true);
    $publicIpAddress = $publicIpData['ip'];
    
    // Optionally, you could also log or return the IP addresses
    // dd($clientIp, $publicIpAddress);
    return response()->json([$clientIp, $publicIpAddress]);
}


    
    
}
