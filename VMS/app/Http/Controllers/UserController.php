<?php

namespace App\Http\Controllers;

use App\Models\AssignedRole;
use App\Models\DriverDetail;
use App\Models\FuelExpense;
use App\Models\LoginCredential;
use App\Models\User;
use App\Models\Userrole;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $roleId = $request->query('role_id');
            $orgId = $request->query('user_organization_id');
            
            $usersQuery = User::query();
            
            // Exclude 'superadmin' role
            $usersQuery->whereHas('role', function ($query) {
                $query->where('role_name', '!=', 'superadmin');
            });
            
            // Filter by role_id if provided
            if ($roleId) {
                $usersQuery->whereHas('role', function ($query) use ($roleId) {
                    $query->where('role_id', $roleId);
                });
            }
    
            // Filter by user_organization_id if provided
            if ($orgId) {
                $usersQuery->where('user_organization_id', $orgId);
            }
    
            // Fetch users with their roles, assigned roles, and organization
            $users = $usersQuery->with(['organizationByUserId', 'role', 'assignedRole','driverDetails'])->get();
            
            if ($users->isEmpty()) {
                return response()->json(['message' => 'No users found.'], 404);
            }
            
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to retrieve users.'], 500);
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
    // public function store(Request $request): JsonResponse
    // {
    //     try {
    //         // Determine the role
    //         $role = UserRole::find($request->input('role_id'));
    //         if (!$role) {
    //             return response()->json(['message' => 'Role not found.'], 404); // Not Found
    //         }
    
    //         $roleName = strtolower($role->role_name);
    
    //         // Base validation rules
    //         $rules = [
    //             'role_id' => 'required|exists:userroles,role_id',
    //             'user_organization_id' => 'required|exists:organizations,organization_id',
    //             'user_name' => 'required|string|max:255',
    //             'user_1st_mobile_no' => 'required|string|max:15',
    //             'user_2nd_mobile_no' => 'nullable|string|max:15',
    //             'user_wp_no' => 'nullable|string|max:15',
    //             'doj' => 'required|date',
    //             'dob' => 'required|date',
    //             'gender' => 'required|string|max:10',
    //             'aadhar_no' => 'nullable|string|max:12|unique:users,aadhar_no', // Unique constraint for aadhar_no
    //             'address' => 'required|string',
    //             'photo' => 'nullable|image|mimes:jpeg,png,jpg',
    //             'status' => 'boolean',
    //             'entry_by' => 'required|exists:users,user_id',
    //         ];
    
    //         // Conditionally add role-specific rules
    //         if ($roleName === 'driver') {
    //             $rules['dl_no'] = 'required|string|max:255|unique:driver_details,dl_no'; // Unique constraint for dl_no
    //             $rules['dl_file'] = 'required|file|mimes:jpeg,png,jpg,pdf'; // Driver-specific file
    //         }
    
    //         // Validate the request
    //         $validatedData = $request->validate($rules);
    
    //         // Check for existing user with same Aadhaar number
    //         $existingUser = User::where('aadhar_no', $validatedData['aadhar_no'])->first();
    
    //         if ($existingUser) {
    //             return response()->json(['message' => 'A user with the same Aadhaar number already exists.'], 409); // Conflict
    //         }
    
    //         // Normalize the user_name to Title Case
    //         $validatedData['user_name'] = ucwords(strtolower($validatedData['user_name']));
    
    //         $rolePrefix = strtoupper(substr($role->role_name, 0, 1)); // Get the first letter of the role name
    //         $organizationId = $validatedData['user_organization_id'];
    
    //         // Generate the sequential number
    //         $latestUser = User::where('user_organization_id', $organizationId)
    //             ->where('user_login_id', 'like', $rolePrefix . $organizationId . '%')
    //             ->latest('user_login_id')
    //             ->first();
    
    //         $nextNumber = $latestUser ? (int)substr($latestUser->user_login_id, -3) + 1 : 1;
    //         $nextNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // Pad the number to 4 digits
    
    //         // Create the user login ID
    //         $userLoginId = $rolePrefix . $organizationId . $nextNumber;
    
    //         // Add the user login ID to the validated data
    //         $validatedData['user_login_id'] = $userLoginId;
    
    //         // Determine the password
    //         $password = $validatedData['user_1st_mobile_no']; // Use phone number as password
    
    //         // Handle photo upload
    //         $photoPath = $request->hasFile('photo') ? $request->file('photo')->storeAs('user_photos', $userLoginId . '_photo.' . $request->file('photo')->extension()) : null;
    
    //         // Add the photo path to validated data if present
    //         $validatedData['photo'] = $photoPath;
    
    //         // Create a new user
    //         $user = User::create($validatedData);
    
    //         // Save login credentials
    //         LoginCredential::create([
    //             'user_id' => $user->user_id,
    //             'login_id' => $userLoginId, // Use the generated user_login_id
    //             'user_password' => bcrypt($password), // Encrypt the password
    //             'is_active' => true, // Set a default value for is_active
    //             'entry_by' => $validatedData['entry_by'],
    //         ]);
    
    //         // Handle role-specific profile creation
    //         if ($roleName === 'driver') {
    //             // Store driver DL file
    //             $dlFilePath = $validatedData['dl_file']->storeAs('driver_dl_files', $userLoginId . '_dl_file.' . $validatedData['dl_file']->extension());
    
    //             DriverDetail::create([
    //                 'user_id' => $user->user_id,
    //                 'dl_no' => $validatedData['dl_no'],
    //                 'dl_file' => $dlFilePath,
    //                 'entry_by' => $validatedData['entry_by'],
    //             ]);
    //         }
    
    //         // Insert into assigned_roles table
    //         AssignedRole::create([
    //             'user_id' => $user->user_id,
    //             'role_id' => $validatedData['role_id'],
    //             'entry_by' => $validatedData['entry_by'],
    //         ]);
    
    //         return response()->json([
    //             'message' => 'User and login credentials created successfully.',
    //             'data' => $user
    //         ], 201); // Created status code
    
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'message' => 'Validation failed.',
    //             'errors' => $e->errors()
    //         ], 422); // Unprocessable Entity
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Failed to create user and login credentials.',
    //             'error' => $e->getMessage()
    //         ], 500); // Internal Server Error
    //     }
    // }

// /last updated  at 16th auguast
public function store(Request $request): JsonResponse
{
    try {
        // Determine the role
        $role = UserRole::find($request->input('role_id'));
        if (!$role) {
            return response()->json(['message' => 'Role not found.'], 404); // Not Found
        }

        // Normalize role name to lowercase and remove spaces
        $roleName = str_replace(' ', '', strtolower($role->role_name));

        // Base validation rules
        $rules = [
            'role_id' => 'required|exists:userroles,role_id',
            'user_organization_id' => 'required|exists:organizations,organization_id',
            'user_name' => 'required|string|max:255',
            'user_1st_mobile_no' => 'required|string|max:15|unique:users,user_1st_mobile_no',
            'user_2nd_mobile_no' => 'nullable|string|max:15',
            'user_wp_no' => 'nullable|string|max:15',
            'doj' => 'nullable|date',
            'dob' => 'required|date',
            'gender' => 'required|string|max:10',
            'aadhar_no' => 'nullable|string|max:12|unique:users,aadhar_no', // Unique constraint for aadhar_no
            'address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'status' => 'boolean',
            'entry_by' => 'required|exists:users,user_id',
        ];

        // Conditionally add role-specific rules
        if ($roleName === 'driver' || $roleName === 'managementdriver') {
            $rules['dl_no'] = 'required|string|max:255|unique:driver_details,dl_no'; // Unique constraint for dl_no
            $rules['dl_file'] = 'required|file|mimes:jpeg,png,jpg,pdf'; // Driver-specific file
        }

        // Validate the request
        $validatedData = $request->validate($rules);

        // Check for existing user with same Aadhaar number
        $existingUser = User::where('aadhar_no', $validatedData['aadhar_no'])->first();

        if ($existingUser) {
            return response()->json(['message' => 'A user with the same Aadhaar number already exists.'], 409); // Conflict
        }

        // Normalize the user_name to Title Case
        $validatedData['user_name'] = ucwords(strtolower($validatedData['user_name']));

        // Determine role prefix
        $validRoles = ['admin', 'manager', 'driver', 'superadmin'];
        $rolePrefix = in_array($roleName, $validRoles)
            ? strtoupper(substr($roleName, 0, 1)) // Get the first letter of the role name
            : strtoupper(substr($roleName, 0, 2)); // Get the first two letters of the role name

        $organizationId = $validatedData['user_organization_id'];

        // Generate the sequential number
        $latestUser = User::where('user_organization_id', $organizationId)
            ->where('user_login_id', 'like', $rolePrefix . $organizationId . '%')
            ->latest('user_login_id')
            ->first();

        $nextNumber = $latestUser ? (int)substr($latestUser->user_login_id, -3) + 1 : 1;
        $nextNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // Pad the number to 3 digits

        // Create the user login ID
        $userLoginId = $rolePrefix . $organizationId . $nextNumber;

        // Add the user login ID to the validated data
        $validatedData['user_login_id'] = $userLoginId;

        // Determine the password
        $password = $validatedData['user_1st_mobile_no']; // Use phone number as password

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $photoExtension = $photoFile->getClientOriginalExtension();
            $photoName = $userLoginId . '_photo.' . $photoExtension;
            $photoPath = 'user_photos/' . $photoName;

            // Check for duplicate photo
            if (Storage::disk('public')->exists($photoPath)) {
                return response()->json(['error' => 'Photo with the same name already exists.'], 400);
            }

            $photoPath = $photoFile->storeAs('user_photos', $photoName, 'public');
            $validatedData['photo'] = $photoPath;
        }

        // Handle DL file upload for drivers and management drivers
        if ($roleName === 'driver' || $roleName === 'managementdriver') {
            if ($request->hasFile('dl_file')) {
                $dlFile = $request->file('dl_file');
                $dlFileExtension = $dlFile->getClientOriginalExtension();
                $dlFileName = $userLoginId . '_dl_file.' . $dlFileExtension;
                $dlFilePath = 'driver_dl_files/' . $dlFileName;

                // Check for duplicate DL file
                if (Storage::disk('public')->exists($dlFilePath)) {
                    return response()->json(['error' => 'DL file with the same name already exists.'], 400);
                }

                $dlFilePath = $dlFile->storeAs('driver_dl_files', $dlFileName, 'public');
                $validatedData['dl_file'] = $dlFilePath;
            }
        }

        // Create a new user
        $user = User::create($validatedData);

        // Save login credentials
        LoginCredential::create([
            'user_id' => $user->user_id,
            'login_id' => $userLoginId, // Use the generated user_login_id
            'user_password' => bcrypt($password), // Encrypt the password
            'is_active' => true, // Set a default value for is_active
            'entry_by' => $validatedData['entry_by'],
        ]);

        // Handle role-specific profile creation
        if ($roleName === 'driver' || $roleName === 'managementdriver') {
            DriverDetail::create([
                'user_id' => $user->user_id,
                'dl_no' => $validatedData['dl_no'],
                'dl_file' => $validatedData['dl_file'],
                'entry_by' => $validatedData['entry_by'],
            ]);
        }

        // Insert into assigned_roles table
        AssignedRole::create([
            'user_id' => $user->user_id,
            'role_id' => $validatedData['role_id'],
            'entry_by' => $validatedData['entry_by'],
        ]);

        return response()->json([
            'message' => 'User and login credentials created successfully.',
            'data' => $user
        ], 201); // Created status code

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
            'message' => 'An error occurred while creating user and login credentials.',
            'error' => 'Error details: ' . $e->getMessage()
        ], 500); // Internal Server Error
    }
}


//     public function store(Request $request): JsonResponse
// {
//     try {
//         // Determine the role
//         $role = UserRole::find($request->input('role_id'));
//         if (!$role) {
//             return response()->json(['message' => 'Role not found.'], 404); // Not Found
//         }

//         $roleName = strtolower($role->role_name);

//         // Base validation rules
//         $rules = [
//             'role_id' => 'required|exists:userroles,role_id',
//             'user_organization_id' => 'required|exists:organizations,organization_id',
//             'user_name' => 'required|string|max:255',
//             'user_1st_mobile_no' => 'required|string|max:15|unique:users,user_1st_mobile_no',
//             'user_2nd_mobile_no' => 'nullable|string|max:15',
//             'user_wp_no' => 'nullable|string|max:15',
//             'doj' => 'required|date',
//             'dob' => 'required|date',
//             'gender' => 'required|string|max:10',
//             'aadhar_no' => 'nullable|string|max:12|unique:users,aadhar_no', // Unique constraint for aadhar_no
//             'address' => 'required|string',
//             'photo' => 'nullable|image|mimes:jpeg,png,jpg',
//             'status' => 'boolean',
//             'entry_by' => 'required|exists:users,user_id',
//         ];

//         // Conditionally add role-specific rules
//         if ($roleName === 'driver') {
//             $rules['dl_no'] = 'required|string|max:255|unique:driver_details,dl_no'; // Unique constraint for dl_no
//             $rules['dl_file'] = 'required|file|mimes:jpeg,png,jpg,pdf'; // Driver-specific file
//         }

//         // Validate the request
//         $validatedData = $request->validate($rules);

//         // Normalize the user_name to Title Case
//         $validatedData['user_name'] = ucwords(strtolower($validatedData['user_name']));

//         // Determine role prefix
//         $validRoles = ['admin', 'manager', 'driver', 'super admin'];
//         if (in_array($roleName, $validRoles)) {
//             $rolePrefix = strtoupper(substr($roleName, 0, 1)); // Get the first letter of the role name
//         } else {
//             $rolePrefix = strtoupper(substr($roleName, 0, 2)); // Get the first two letters of the role name
//         }

//         $organizationId = $validatedData['user_organization_id'];

//         // Generate the sequential number and ensure unique user_login_id
//         do {
//             $latestUser = User::where('user_organization_id', $organizationId)
//                 ->where('user_login_id', 'like', $rolePrefix . $organizationId . '%')
//                 ->latest('user_login_id')
//                 ->first();

//             $nextNumber = $latestUser ? (int)substr($latestUser->user_login_id, -3) + 1 : 1;
//             $nextNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // Pad the number to 3 digits

//             $userLoginId = $rolePrefix . $organizationId . $nextNumber;
//         } while (LoginCredential::where('login_id', $userLoginId)->exists());

//         // Add the user login ID to the validated data
//         $validatedData['user_login_id'] = $userLoginId;

//         // Determine the password
//         $password = $validatedData['user_1st_mobile_no']; // Use phone number as password

//         // Handle photo upload
//         if ($request->hasFile('photo')) {
//             $photoFile = $request->file('photo');
//             $photoExtension = $photoFile->getClientOriginalExtension();
//             $photoName = $userLoginId . '_photo.' . $photoExtension;
//             $photoPath = 'user_photos/' . $photoName;

//             // Check for duplicate photo
//             if (Storage::disk('public')->exists($photoPath)) {
//                 return response()->json(['error' => 'Photo with the same name already exists.'], 400);
//             }

//             $photoPath = $photoFile->storeAs('user_photos', $photoName, 'public');
//             $validatedData['photo'] = $photoPath;
//         }

//         // Handle DL file upload for drivers
//         if ($roleName === 'driver' && $request->hasFile('dl_file')) {
//             $dlFile = $request->file('dl_file');
//             $dlFileExtension = $dlFile->getClientOriginalExtension();
//             $dlFileName = $userLoginId . '_dl_file.' . $dlFileExtension;
//             $dlFilePath = 'driver_dl_files/' . $dlFileName;

//             // Check for duplicate DL file
//             if (Storage::disk('public')->exists($dlFilePath)) {
//                 return response()->json(['error' => 'DL file with the same name already exists.'], 400);
//             }

//             $dlFilePath = $dlFile->storeAs('driver_dl_files', $dlFileName, 'public');
//             $validatedData['dl_file'] = $dlFilePath;
//         }

//         // Create a new user
//         $user = User::create($validatedData);

//         // Save login credentials
//         LoginCredential::create([
//             'user_id' => $user->user_id,
//             'login_id' => $userLoginId, // Use the generated user_login_id
//             'user_password' => bcrypt($password), // Encrypt the password
//             'is_active' => true, // Set a default value for is_active
//             'entry_by' => $validatedData['entry_by'],
//         ]);

//         // Handle role-specific profile creation
//         if ($roleName === 'driver') {
//             DriverDetail::create([
//                 'user_id' => $user->user_id,
//                 'dl_no' => $validatedData['dl_no'],
//                 'dl_file' => $validatedData['dl_file'],
//                 'entry_by' => $validatedData['entry_by'],
//             ]);
//         }

//         // Insert into assigned_roles table
//         AssignedRole::create([
//             'user_id' => $user->user_id,
//             'role_id' => $validatedData['role_id'],
//             'entry_by' => $validatedData['entry_by'],
//         ]);

//         return response()->json([
//             'message' => 'User and login credentials created successfully.',
//             'data' => $user
//         ], 201); // Created status code

//     } catch (ValidationException $e) {
//         $errors = $e->errors();
//         $formattedErrors = [];
    
//         foreach ($errors as $field => $messages) {
//             $formattedErrors[] =  implode(', ', $messages);
//         }
    
//         return response()->json([
//             'message' => 'Validation failed. Please correct the following errors:',
//             'errors' => $formattedErrors
//         ], 422); // Unprocessable Entity
    
//     } catch (\Exception $e) {
//         return response()->json([
//             'message' => 'An error occurred while creating user and login credentials.',
//             'error' => 'Error details: ' . $e->getMessage()
//         ], 500); // Internal Server Error
//     }
// }

    
    
    
    
    

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Return user data as JSON
            return response()->json(['data' => $user], 200); // OK status code
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found.', 'error' => $e->getMessage()], 404); // Not Found
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function UserDetailsUpdate(Request $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);
    
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404); // Not Found
            }
    
            // Check if only `status` is provided
            if ($request->has('status') && !$request->has(['user_organization_id', 'user_name', 'user_1st_mobile_no', 'doj', 'dob', 'gender', 'aadhar_no', 'address'])) {
                // Update only the status
                $status = $request->input('status') ? 1 : 0;
                $user->update(['status' => $status]);
    
                return response()->json(['message' => 'User status updated successfully.'], 200);
            }
    
            // Update all user data
            $validatedData = $request->validate([
                'role_id' => 'sometimes|exists:userroles,role_id',
                'user_organization_id' => 'sometimes|exists:organizations,organization_id',
                'user_name' => 'sometimes|string|max:255',
                'user_1st_mobile_no' => 'sometimes|string|max:15',
                'user_wp_no' => 'nullable|string|max:15',
                'doj' => 'sometimes|date',
                'dob' => 'sometimes|date',
                'gender' => 'sometimes|string|max:10',
                'aadhar_no' => 'nullable|string|max:12|unique:users,aadhar_no,' . $id, // Ignore unique constraint for current user
                'address' => 'sometimes|string',
                'photo' => 'sometimes|image|mimes:jpeg,png,jpg',
                'status' => 'boolean',
                'entry_by' => 'sometimes|exists:users,user_id',
            ]);
    
            // Handle photo upload if present
            if ($request->hasFile('photo')) {
                $photoFile = $request->file('photo');
                $photoExtension = $photoFile->getClientOriginalExtension();
                $photoName = $user->user_login_id . '_photo.' . $photoExtension;
                $photoPath = 'user_photos/' . $photoName;
    
                // Check for duplicate file
                if (Storage::disk('public')->exists($photoPath)) {
                    return response()->json(['error' => 'Photo file with the same name already exists.'], 400);
                }
    
                $photoFile->storeAs('public/user_photos', $photoName);
                $validatedData['photo'] = $photoPath;
            }
    
            $user->update($validatedData);
    
            return response()->json(['message' => 'User data updated successfully.', 'data' => $user], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update user.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    
    
    
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }





    
   
    public function getDriverDataByOrgId(Request $request): JsonResponse
    {
        try {
            // Step 1: Retrieve and validate the organization ID from the query string
            $orgId = $request->query('user_organization_id');
            
            if (empty($orgId)) {
                return response()->json(['error' => 'Organization ID is required.'], 400);
            }
    
            // Step 2: Retrieve users based on the organization ID along with their organization
            $users = User::where('user_organization_id', $orgId)
                            ->where('status', 1)
                         ->with('organization') // Ensure organization relationship is loaded
                         ->get();
    
            if ($users->isEmpty()) {
                return response()->json(['message' => 'No users found for the given organization ID.'], 404);
            }
    
            // Step 3: Fetch roles using the AssignedRole model
            $drivers = $users->filter(function($user) {
                // Fetch all assigned roles for the user
                $assignedRoles = AssignedRole::where('user_id', $user->user_id)
                                             ->with('role') // Ensure the role relationship is loaded
                                             ->get();
                                             
                // Check if the user has 'driver' or 'management driver' role
                return $assignedRoles->contains(function($assignedRole) {
                    return in_array(strtolower($assignedRole->role->role_name), ['driver', 'management driver']);
                });
            });
    
            if ($drivers->isEmpty()) {
                return response()->json(['message' => 'No drivers found for the given organization ID.'], 404);
            }
    
            // Step 4: Transform data into a clean array with required fields
            $driverData = $drivers->map(function($driver) {
                // Get all roles for the user
                $assignedRoles = AssignedRole::where('user_id', $driver->user_id)
                                             ->with('role')
                                             ->get();
    
                // Extract relevant roles for display
                $roles = $assignedRoles->map(function($assignedRole) {
                    return [
                        'role_id' => $assignedRole->role->role_id,
                        'role_name' => $assignedRole->role->role_name
                    ];
                });
    
                return [
                    'user_id' => $driver->user_id,
                    'user_name' => $driver->user_name,
                    'roles' => $roles, // Include all the user's roles
                    'organization_name' => $driver->organization ? $driver->organization->organization_name : null
                ];
            })->values(); // Ensure the collection is indexed from 0
    
            // Store the formatted data in a variable
            $responseData = $driverData;
    
            // Return the response with the formatted data
            return response()->json($responseData);
    
        } catch (ModelNotFoundException $e) {
            // Handle case where model or record is not found
            return response()->json(['error' => 'Resource not found: ' . $e->getMessage()], 404);
            
        } catch (QueryException $e) {
            // Handle database query exceptions
            return response()->json(['error' => 'Database query error: ' . $e->getMessage()], 500);
            
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
    



    public function getAuthorityDataByOrgId(Request $request): JsonResponse
    {
        try {
            // Step 1: Retrieve and validate the organization ID from the query string
            $orgId = $request->query('user_organization_id');
            
            if (empty($orgId)) {
                return response()->json(['error' => 'Organization ID is required.'], 400);
            }
    
            // Step 2: Retrieve users based on the organization ID along with their roles and organization
            $users = User::where('user_organization_id', $orgId)
                         ->with(['role', 'organization']) // Ensure relationships are defined correctly
                         ->get();
    
            if ($users->isEmpty()) {
                return response()->json(['message' => 'No users found for the given organization ID.'], 404);
            }
    
            // Step 3: Filter users to get only those with the role 'Authority', case-insensitive
            $authorities = $users->filter(function($user) {
                return $user->role && strcasecmp($user->role->role_name, 'Authority') === 0;
            });
    
            if ($authorities->isEmpty()) {
                return response()->json(['message' => 'No Authority found for the given organization ID.'], 404);
            }
    
            // Step 4: Transform data into a clean array with required fields
            $authorityData = $authorities->map(function($authority) {
                return [
                    'user_id' => $authority->user_id,
                    'role_id' => $authority->role->role_id, // Role ID
                    'user_name' => $authority->user_name,
                    'role_name' => $authority->role->role_name, // Role Name
                    'organization_name' => $authority->organization ? $authority->organization->organization_name : null // Organization Name
                ];
            })->values(); // Ensure the collection is indexed from 0
    
            // Store the formatted data in a variable
            $responseData = $authorityData;
    
            // Return the response with the formatted data
            return response()->json($responseData);
    
        } catch (QueryException $e) {
            // Handle database query exceptions
            return response()->json(['error' => 'Database query error: ' . $e->getMessage()], 500);
    
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
    





    public function basedOnDriverFuelFilling(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'driver_id' => 'required|integer|exists:users,user_id',
                'date' => 'nullable|date',
            ]);
    
            // Get the driver_id from the validated data
            $driverId = $validatedData['driver_id'];
    
            // Get the date from the validated data or default to today's date
            $startDate = $validatedData['date'] ?? date('Y-m-d');
    
            // Log the parameters to check
            // Log::info('Driver ID:', ['driver_id' => $driverId]);
            // Log::info('Start Date:', ['start_date' => $startDate]);
    
            // Initialize the query with eager loading
            $query = FuelExpense::with(['fuelStation', 'driver', 'entryBy', 'vehicle'])
                                ->where('driver_id', $driverId)
                                ->where('filling_date', '=', $startDate);
    
            // Log the query for debugging
            // Log::info('Query:', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);
    
            // Execute the query and get the results
            $fuelFillingData = $query->get();
    
            // Log the results to check
            // Log::info('Fuel Filling Data:', $fuelFillingData->toArray());
    
            // Check if data is empty
            if ($fuelFillingData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No fuel filling data found for the given driver and date range.',
                    'data' => [],
                ], 404);
            }
    
            // Return the results as a JSON response
            return response()->json([
                'success' => true,
                'data' => $fuelFillingData,
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->errors(), // Provide detailed validation errors
            ], 422);
    
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    
    public function getDriverReport(Request $request)
    {
        // Validate input parameters
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);
    
        $userId = $validated['user_id'];
        $dateFrom = $validated['date_from'];
        $dateTo = $validated['date_to'];
        $movements = DB::select('CALL get_driver_movements(?, ?, ?)', [
            $userId,
            $dateFrom,
            $dateTo,
        ]);

        return response()->json($movements);
      
    }
    
}
