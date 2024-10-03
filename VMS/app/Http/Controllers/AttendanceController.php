<?php
// TEST
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class AttendanceController extends Controller
{
    //
    public function storeAttenDance(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,user_id',
                'location' => 'required|string|max:255',
                'status' => 'required|string|max:50',
            ]);
    
            // Check if the user has already submitted attendance for today
            $existingAttendance = Attendance::where('user_id', $validatedData['user_id'])
                ->whereDate('date', now()->toDateString())
                ->first();
    
            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance has already been recorded for today.',
                ], 200); // Conflict status code
            }
    
            // Fetch the user's name from the users table
            $user = User::findOrFail($validatedData['user_id']);
            $userName = $user->user_name;
    
            // Prepare data for insertion
            $dataToStore = [
                'user_id' => $validatedData['user_id'],
                'location' => $validatedData['location'],
                'date' => now()->toDateString(), // Set current date
                'check_in_time' => now()->format('H:i'), // Set current time as check-in time
                'check_out_time' => null, // Default to null; update later as needed
                'status' => $validatedData['status'],
            ];
    
            // Create a new attendance record
            $attendance = Attendance::create($dataToStore);
    
            // Return a response with user name and current time
            return response()->json([
                'success' => true,
                'message' => $userName . ', Thank you! Your attendance has been recorded successfully at ' . now()->toDateTimeString() . '!!',
                'data' => [
                    'attendance' => $attendance,
                    'user_name' => $userName,
                    'current_time' => now()->toDateTimeString()
                ]
            ], 201);
    
        } catch (ValidationException $e) {
            // Handle validation errors
            $formattedErrors = [];
    
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $formattedErrors[] = $message; // Flatten the errors into an array
                }
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please correct the following errors:',
                'errors' => $formattedErrors
            ], 422);
    
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


    public function getTodayAttendancebasedonuserid(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,user_id',
            ]);

            // Fetch attendance for the user on today's date
            $attendance = Attendance::where('user_id', $validatedData['user_id'])
                ->whereDate('date', now()->toDateString()) // Filter by today's date
                ->first();

            if ($attendance) {
                // Return attendance details if found
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance record for today found.',
                    'data' => $attendance
                ], 200);
            } else {
                // No attendance found for today
                return response()->json([
                    'success' => false,
                    'message' => 'No attendance record found for today.',
                ], 404);
            }
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



//   uSER cHECKOUT ATTENDANCE
public function checkOut(Request $request, $attendance_id)
{
    try {
        // Validate that the attendance_id exists in the database
        $attendance = Attendance::find($attendance_id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found.',
            ], 404); // Not Found status code
        }

        // Check if the attendance already has a check-out time
        if (!is_null($attendance->check_out_time)) {
            return response()->json([
                'success' => false,
                'message' => 'Check-out has already been recorded for this attendance.',
            ], 409); // Conflict status code
        }

        // Update the check_out_time to the current time
        $attendance->update([
            'check_out_time' => now()->format('H:i'), // Set current time as check-out time
        ]);

        // Return a success response with updated attendance data
        return response()->json([
            'success' => true,
            'message' => 'Check-out recorded successfully at ' . now()->toDateTimeString(),
            'data' => [
                'attendance_id' => $attendance->attendance_id,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'status' => $attendance->status
            ]
        ], 200);

    } catch (\Exception $e) {
        // Handle general exceptions
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred.',
            'error' => $e->getMessage()
        ], 500);
    }
}



public function getAttendanceByDateRange(Request $request)
{
    try {
        // Validate the request
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        // Fetch attendance records within the specified date range along with user data
        $attendanceRecords = Attendance::with('user') // Assuming there's a relationship defined
            ->where('user_id', $validatedData['user_id'])
            ->whereBetween('date', [$validatedData['from_date'], $validatedData['to_date']])
            ->get();

        // Format the data to include user information with each attendance record
        // $formattedRecords = $attendanceRecords->map(function ($attendance) {
        //     return [
        //         'attendance_id' => $attendance->id,
        //         'user_id' => $attendance->user_id,
        //         'user_name' => $attendance->user->user_name, // Accessing related user data
        //         'location' => $attendance->location,
        //         'date' => $attendance->date,
        //         'check_in_time' => $attendance->check_in_time,
        //         'check_out_time' => $attendance->check_out_time,
        //         'status' => $attendance->status,
        //     ];
        // });

        // Return a response with the attendance records and user data
        return response()->json([
            'success' => true,
            'message' => 'Attendance records with user data fetched successfully.',
            'data' => $attendanceRecords
        ], 200);

    } catch (ValidationException $e) {
        // Handle validation errors
        $formattedErrors = [];
        
        foreach ($e->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $formattedErrors[] = $message; // Flatten the errors into an array
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Validation failed. Please correct the following errors:',
            'errors' => $formattedErrors
        ], 422);

    } catch (\Exception $e) {
        // Handle general exceptions
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred.',
            'error' => $e->getMessage()
        ], 500);
    }
}



public function filterAttendance(Request $request)
{
    try {
        // Initialize the query for filtering attendance
        $query = Attendance::with('user');  // Load user relationship

        // Optional: Filter by date range (if provided)
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('date', [$request->input('from_date'), $request->input('to_date')]);
        }

        // Optional: Filter by role_id (if provided)
        if ($request->has('role_id')) {
            // Join with the User model to filter by role_id
            $query->whereHas('user', function ($userQuery) use ($request) {
                $userQuery->where('role_id', $request->input('role_id'));
            });
        }

        // Optional: Filter by user_id (if provided)
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Fetch the filtered or full data
        $attendanceRecords = $query->get();

        // Check if data is found
        if ($attendanceRecords->isEmpty()) {
            return response()->json([
                'message' => 'No attendance records found.',
                'data' => []
            ], 404);
        }

        // Format the attendance data with user_name from the related user
        $attendanceData = $attendanceRecords->map(function ($attendance) {
            return [
                'attendance_id' => $attendance->attendance_id,
                'user_id' => $attendance->user_id,
                'user_name' => $attendance->user->user_name ?? 'N/A', // Get user_name from related User model
                'location' => $attendance->location,
                'date' => $attendance->date,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'status' => $attendance->status,
            ];
        });

        // Return the formatted data
        return response()->json([
            'message' => 'Attendance records retrieved successfully.',
            'data' => $attendanceData
        ], 200);

    } catch (\Exception $e) {
        // Log the error for debugging purposes
        Log::error('Error filtering attendance records: ' . $e->getMessage());

        // Return a generic error message
        return response()->json([
            'message' => 'An error occurred while retrieving attendance records.',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
