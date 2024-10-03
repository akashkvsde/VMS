<?php

use App\Http\Controllers\AssignedNavigationController;
use App\Http\Controllers\Assignedrolecontroller;
use App\Http\Controllers\FuelExpenseController;
use App\Http\Controllers\FuelStationController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserroleController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleInsuranceController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\VehicleMovementController;
use App\Http\Controllers\VehiclePollutionController;
use App\Http\Controllers\VehiclesCategoryController;
use App\Http\Controllers\VehicleOwnerController;
use App\Http\Controllers\VehicleProblemController;
use App\Http\Controllers\OtherVehicleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\OvertimeController;
use App\Models\Garage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [LoginController::class, 'login']);
// Route::get('login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {

Route::apiResource('users', UserController::class);

Route::post('UserDetailsUpdate/{id}', [UserController::class,'UserDetailsUpdate']);
Route::apiResource('userroles', UserroleController::class);
Route::apiResource('organizations', OrganizationController::class);
Route::apiResource('navigations', NavigationController::class);
Route::apiResource('assignnavigations', AssignedNavigationController::class);
Route::apiResource('vehiclemaintences', VehicleMaintenanceController::class);

Route::get('basedonDriverandDateMaintenancae', [VehicleMaintenanceController::class, 'basedonDriverandDateMaintenancae']);
// Route::get('test', VehicleMaintenanceController::class,'test');
Route::apiResource('vehicles', VehicleController::class);
// Route::apiResource('vehiclepollutions', VehiclePollutionController::class);

Route::get('vehiclemaintenancesstatus', [VehicleMaintenanceController::class, 'indexByApprovalStatus']);
Route::get('ByApprovalStatus', [VehicleMaintenanceController::class, 'ByApprovalStatus']);

Route::apiResource('vehiclecategories', VehiclesCategoryController::class);

Route::put('updateVehicleCategory/{id}', [VehiclesCategoryController::class, 'updateVehicleCategory']);



Route::apiResource('vehicleproblems', VehicleProblemController::class);

Route::apiResource('vehiclemovements', VehicleMovementController::class);
Route::apiResource('vehicleinsurances', VehicleInsuranceController::class);

Route::get('OrgbasedOnrole', [OrganizationController::class, 'OrganizationBasedOnRole']);
Route::patch('vehicle-maintenance/{id}', [VehicleMaintenanceController::class, 'updateStatus']);


//Drivers Based On Org
Route::get('usersbasedonorg', [UserController::class, 'getDriverDataByOrgId']);
Route::get('authoritybasedonorg', [UserController::class, 'getAuthorityDataByOrgId']);


Route::apiResource('fuelstations', FuelStationController::class);
Route::apiResource('servicestation', GarageController::class);

Route::get('vehiclemaintenancebyentryBy', [VehicleMaintenanceController::class, 'filterByEntryAndStatus']);

// Route::get('vehiclemovementbyuseranddate', [VehicleMovementController::class, 'filterByUserAndDate']);
// Route::get('vehiclemovementbyuseranddatewhoassigned', [VehicleMovementController::class, 'filterByUserAndDatewhoassigned']);

Route::put('VehicleMaintenanceupdatesoemdata', [VehicleMaintenanceController::class, 'VehicleMaintenanceupdatesoemdata']);


Route::get('getVehicleMovementData', [VehicleMovementController::class, 'getVehicleMovementData']);

Route::apiResource('vehicleowners', VehicleOwnerController::class);
Route::apiResource('assignedRole', Assignedrolecontroller::class);

Route::delete('removeRole', [Assignedrolecontroller::class,'removeRole']);
Route::get('assinedroleuserData', [Assignedrolecontroller::class,'assinedroleuserData']);

Route::apiResource('vehiclepollutions', VehiclePollutionController::class);

// Route::post('vehiclepollutionsUpdate/{id}', [VehiclePollutionController::class,'vehiclepollutionsupdate']);

Route::post('VehicleMaintenanceupdate/{id}', [VehicleMaintenanceController::class,'VehicleMaintenanceupdate']);

Route::apiResource('fuelexpense', FuelExpenseController::class);
Route::post('fuelexpenseupdate/{id}', [FuelExpenseController::class,'fuelexpenseupdate']);
Route::post('updateVehicle/{id}', [VehicleController::class,'updateVehicle']);


Route::get('alluserroles', [UserroleController::class,'alluserroles']);


Route::post('Pollutionupdate/{id}', [VehiclePollutionController::class,'Pollutionupdate']);

Route::post('Insuranceupdate/{id}', [VehicleInsuranceController::class,'Insuranceupdate']);

// Report date Range Wise
// From Frpm and TO 
Route::get('daterangeFuelExpenseReport', [FuelExpenseController::class,'daterangeFuelExpenseReport']);

Route::get('reportByDateRangeVehicleMaintenance', [VehicleMaintenanceController::class,'reportByDateRange']);
Route::get('reportByDateRangeofMovement', [VehicleMovementController::class,'reportByDateRangeofMovement']);

Route::get('reportByVehicleCategory', [VehiclesCategoryController::class, 'reportByVehicleCategory']);

Route::get('FuelFillingReport', [FuelExpenseController::class, 'FuelFillingReport']);



Route::get('vehicleAssignments', [VehicleMovementController::class, 'getVehicleMovementassigned']);
Route::get('getVehicleswhichareinmovment', [VehicleMovementController::class, 'getVehicleswhichareinmovment']);

Route::get('vehiclesassignmentsCount', [VehicleMovementController::class, 'getVehicleMovementAssignments']);

// updatekm reading by driver
Route::patch('updateKmReadingsByDriver', [VehicleMovementController::class, 'updateKmReadingsByDriver']);
Route::patch('updateByManager', [VehicleMovementController::class, 'updateByManager']);


// from and to date an duserid request fuel expenc data 
Route::get('getFilteredFuelExpensesbyusridfromto', [FuelExpenseController::class, 'getFilteredFuelExpenses']);

Route::get('getFilteredFuelExpensesbasedonorg', [FuelExpenseController::class, 'getFilteredFuelExpensesbasedonorg']);

// Free Vehicle 
Route::get('FreeVehicle', [VehicleMovementController::class, 'FreeVehicle']);

// Free Driver
Route::get('FreeDriver', [VehicleMovementController::class, 'FreeDriver']);

Route::get('getMaintenanceDataByManager', [VehicleMaintenanceController::class, 'getMaintenanceDataByManager']);
Route::get('getVehiclesWhichAreNotInMaintenance', [VehicleMaintenanceController::class, 'getVehiclesWhichAreNotInMaintenance']);

Route::get('getStatistics', [VehicleController::class, 'getStatistics']);


Route::get('LogDetails', [LoginController::class, 'LogDetails']);

Route::get('basedOnDriverFuelFilling', [UserController::class, 'basedOnDriverFuelFilling']);
Route::get('getSomedataofmovementbasedonentryby', [VehicleMovementController::class, 'getLast5DataVehicleMovementBasedOnEntryBy']);


Route::post('OtherVehiclestore', [OtherVehicleController::class, 'OtherVehiclestore']);
Route::post('OtherVehiclesFuelStore', [OtherVehicleController::class, 'OtherVehiclesFuelStore']);
Route::get('OtherVehiclesFuelGet', [OtherVehicleController::class, 'OtherVehiclesFuelGet']);
Route::get('OtherVehicleGet', [OtherVehicleController::class, 'OtherVehicleGet']);
Route::post('OtherVehiclesFuelUpdate/{id}', [OtherVehicleController::class, 'OtherVehiclesFuelUpdate']);

Route::get('OtherVehiclesFuelGetBasedONEntryBy', [OtherVehicleController::class, 'OtherVehiclesFuelGetBasedONEntryBy']);
Route::get('UnofficialFuelAExpense', [OtherVehicleController::class, 'UnofficialFuelAExpense']);




Route::post('storeAttenDance', [AttendanceController::class, 'storeAttenDance']);
Route::get('getTodayAttendancebasedonuserid', [AttendanceController::class, 'getTodayAttendancebasedonuserid']);
Route::put('checkOut/{id}', [AttendanceController::class, 'checkOut']);
Route::get('getAttendanceByDateRange', [AttendanceController::class, 'getAttendanceByDateRange']);


// Chart Controller
Route::get('monthly-fuel-expenses', [ChartController::class, 'getMonthlyFuelExpenses']);
Route::get('daily-fuel-expenses', [ChartController::class, 'getDailyFuelExpenses']);
Route::get('other-fuels', [ChartController::class, 'getOtherFuels']);

// OverTime Duty fro driver
Route::post('assignOvertimeDutytoDriver', [OvertimeController::class, 'assignOvertimeDutytoDriver']);
Route::put('handleOvertime', [OvertimeController::class, 'handleOvertime']);
Route::get('getOvertimeDuty', [OvertimeController::class, 'getOvertimeDuty']);
Route::get('getmyOverdutybasedondriver', [OvertimeController::class, 'getmyOverdutybasedondriver']);
Route::get('getRecentUpdatedData', [OvertimeController::class, 'getRecentUpdatedData']);

Route::put('updateFuelExpenseIdInMovement/{id}', [VehicleMovementController::class, 'updateFuelExpenseIdInMovement']);

Route::get('getAllDriversReport', [UserController::class, 'getDriverReport']);
Route::get('filterAttendance', [AttendanceController::class, 'filterAttendance']);

Route::get('allvehicles', [VehicleController::class,'allvehiclesinactiveinactive']);

});