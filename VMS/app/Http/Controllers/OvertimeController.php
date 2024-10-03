<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OvertimeController extends Controller
{
    //Assigned Overtime
    public function assignOvertimeDutytoDriver(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'driver_id' => 'required|exists:users,user_id',
                'start_date' => 'required|date',
                'entry_by' => 'required|exists:users,user_id',
            ]);
            // Create a new Overtime record using Eloquent model
            $overtime = Overtime::create($request->only([
                'driver_id',
                'start_date',
                'entry_by'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Overtime record created successfully',
                'data' => $overtime,
               
            ], 201);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating overtime record: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create overtime record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

// check IN Chekc oUt
public function handleOvertime(Request $request)
{
    try {
        // Validate incoming request
        $request->validate([
            'overtime_id' => 'required|exists:overtimes,overtime_id',
        ]);

        // Find the existing overtime record
        $overtime = Overtime::findOrFail($request->input('overtime_id'));

        // Update check-in time only if it's not already set
        if (!$overtime->check_in_time) {
            $overtime->check_in_time = now()->format('H:i'); // Set current server time as check-in
        }

        // Check if 'check_out_time' is in the request, and only then update check-out time and end date
        if ($request->has('check_out_time')) {
            // Ensure that check-in time is already set before setting check-out time
            $overtime->check_out_time = now()->format('H:i');
            if (!$overtime->check_in_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in time must be recorded before check-out time.',
                ], 400);
            }

            // Update check-out time
            $overtime->check_out_time = now()->format('H:i'); // Set current server time as check-out

            // Update end date to now if provided or use the current date
            $overtime->end_date = now(); // Set current date as end date
        }

        // Save the updated record
        $overtime->save();

        return response()->json([
            'success' => true,
            'message' => 'Overtime record updated successfully',
            'data' => $overtime,
        ], 200);

    } catch (\Exception $e) {
        // Log the error
        Log::error('Error updating overtime record: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to update overtime record',
            'error' => $e->getMessage(),
        ], 500);
    }
}




    // gTE oVERTINE DUTY
    public function getOvertimeDuty(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date',
                'user_id' => 'required|exists:users,user_id',
            ]);
    
            // Start building the query
            $query = Overtime::query();
    
            // Apply date range filter if provided
            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereBetween('start_date', [
                    $request->input('from_date'),
                    $request->input('to_date'),
                ]);
            }
    
            // Apply entry_by filter if provided
            if ($request->has('user_id')) {
                $query->where('entry_by', $request->input('user_id'));
            }
    
            // Fetch the data
            $overtimeDuties = $query
                ->with(['driver', 'entryBy'])
                ->orderBy('created_at', 'desc')
                ->get();
    
            // Check if data is empty
            if ($overtimeDuties->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No overtime duties found for the given criteria',
                    'data' => [],
                ], 200);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Overtime duties retrieved successfully',
                'data' => $overtimeDuties,
            ], 200);
    
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error retrieving overtime duties: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve overtime duties',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
 //    getmyOverdutybasedondriver and date range
 public function getmyOverdutybasedondriver(Request $request)
 {
     try {
         // Validate incoming request
         $request->validate([
             'driver_id' => 'required|exists:users,user_id', // Ensure the driver_id exists in the drivers table
         ]);
 
         // Start building the query
         $query = Overtime::query();
 
         // Apply driver_id filter
         $query->where('driver_id', $request->input('driver_id'));
 
         // Filter records where check_in_time and check_out_time are null or blank
        //  $query->where(function ($subQuery) {
        //      $subQuery->whereNull('check_out_time')
        //               ->orWhere('check_out_time', '');
        //  });
 
         // Fetch the data
         $overtimeDuties = $query
             ->with(['driver', 'entryBy'])
             ->orderBy('created_at', 'desc')
             ->get()->take(5);
 
         // Check if data is empty
         if ($overtimeDuties->isEmpty()) {
             return response()->json([
                 'success' => true,
                 'message' => 'No overtime duties found for the given driver with missing check-in or check-out records',
                 'data' => [],
             ], 200);
         }
 
         return response()->json([
             'success' => true,
             'message' => 'Overtime duties retrieved successfully',
             'data' => $overtimeDuties,
         ], 200);
 
     } catch (\Exception $e) {
         // Log the error
         Log::error('Error retrieving overtime duties based on driver: ' . $e->getMessage());
 
         return response()->json([
             'success' => false,
             'message' => 'Failed to retrieve overtime duties',
             'error' => $e->getMessage(),
         ], 500);
     }
 }




 //  recent Updated data
 public function getRecentUpdatedData(Request $request)
 {
     try {
         // Initialize query with relationships
         $query = Overtime::with(['driver', 'entryBy']);
 
         // Apply where clause for filtering by driver_id if provided in the request
         if ($request->has('driver_id')) {
             $query->where('driver_id', $request->input('driver_id'));
         }
 
         // Fetch the most recent updated records, ordered by updated_at
         $recentData = $query->orderBy('updated_at', 'desc')->get();
 
         // Return the data with a success response
         return response()->json([
             'success' => true,
             'message' => 'Recent updated overtime records retrieved successfully',
             'data' => $recentData,
         ], 200);
     } catch (\Exception $e) {
         // Log the error
         Log::error('Error fetching recent updated overtime records: ' . $e->getMessage());
 
         return response()->json([
             'success' => false,
             'message' => 'Failed to fetch recent updated overtime records',
             'error' => $e->getMessage(),
         ], 500);
     }
 }
 
    






}
