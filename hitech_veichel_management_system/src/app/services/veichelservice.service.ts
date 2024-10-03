import { HttpClient, HttpErrorResponse ,HttpHeaders} from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, catchError, delay, retryWhen, take, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class VeichelserviceService {
  apiUrl='http://yourapiurlputhere:5002/api';
  constructor(private http:HttpClient) {}

 // <!-----------------------ALL DATAS------------------------------------>

 //login============
 login(data: any) {
  // Extract the form data from the FormGroup
  const formData = data.value; 

  return this.http.post(`${this.apiUrl}/login`, formData);
}
 //ASSIGN NAVIGATION
 searchAssignNav(role_id:number){
   return this.http.get(`${this.apiUrl}/assignnavigations`,{params:{role_id:role_id.toString()}})
  }

  AllRoleSup(){
    return this.http.get(`${this.apiUrl}/alluserroles`);
  }

  AllUserRole(){
    return this.http.get(`${this.apiUrl}/userroles`);
  }

navPage(){
  return this.http.get(`${this.apiUrl}/navigations`)
}

assignNav(data:any){
  console.log(typeof data);
  return this.http.post(`${this.apiUrl}/assignnavigations`,data);
}

deleteAssignNav(id:any){
  return this.http.delete(`${this.apiUrl}/assignnavigations/`+id)
}

deleteAll_nav(id:any) {
    return this.http.delete(`${this.apiUrl}/delete_all_navigations/` + id);
}


//add veichel service -------------

getOwners(){
  return this.http.get(`${this.apiUrl}/vehicleowners`)
}

getVehicleCategories(){
  return this.http.get(`${this.apiUrl}/vehiclecategories`)
}

addVehicle(formData: FormData) {
  return this.http.post(`${this.apiUrl}/vehicles`, formData);
}

getUpdateVehicledata(entry_by:any)
{
  return this.http.get(`${this.apiUrl}/vehicles`,{params:{user_id:entry_by.toString()}})
}

//---------Add Veichel for maintainace---------
fetchAllVeichel(orgaization_id:any){
  return this.http.get(`${this.apiUrl}/vehicles`,{params:{organization_id:orgaization_id.toString()}})
}

fetchAllDriver(orgaization_id:any){
  return this.http.get(`${this.apiUrl}/usersbasedonorg`,{params:{user_organization_id:orgaization_id.toString()}})
}

//same differ api
freeAllVeichel(orgaization_id:any){
  return this.http.get(`${this.apiUrl}/FreeVehicle`,{params:{organization_id:orgaization_id.toString()}})
}

freeAllDriver(orgaization_id:any){
  return this.http.get(`${this.apiUrl}/FreeDriver`,{params:{organization_id:orgaization_id.toString()}})
}
//same differ api end
fetchAuthority(orgaization_id:any){
  return this.http.get(`${this.apiUrl}/authoritybasedonorg`,{params:{user_organization_id:orgaization_id.toString()}})
}

fetchProblems(){
  return this.http.get(`${this.apiUrl}/vehicleproblems`)
}

fetchStations(){
  return this.http.get(`${this.apiUrl}/servicestation`)
}

addVeicleForMaintain(formData: FormData) {
  return this.http.post(`${this.apiUrl}/vehiclemaintences`, formData);
}

//------user registration
allOrganization(user_id:any){
  return this.http.get(`${this.apiUrl}/OrgbasedOnrole`,{params:{user_id:user_id.toString()}})
}

allRoles(){
  return this.http.get(`${this.apiUrl}/userroles`) 
}


submitUser(data:any){
  return this.http.post(`${this.apiUrl}/users`,data)
}

//--------add insurance

updateInsurance(insuranceIs:any,fromData:any){
  return this.http.post(`${this.apiUrl}/Insuranceupdate/`+insuranceIs, fromData);

}

getInsuranceReport(vehicleId:any){
  return this.http.get(`${this.apiUrl}/vehicleinsurances?vehicle_id=${vehicleId}`);
}

addInsurance(formData:any){
  return this.http.post(`${this.apiUrl}/vehicleinsurances`, formData);
}

//------add polution

getPollutionReport(vehicleId: any){
  return this.http.get(`${this.apiUrl}/vehiclepollutions?vehicle_id=${vehicleId}`)
}

updatePollution(vehicle_pollution_id:any,data:any){
  return this.http.post(`${this.apiUrl}/Pollutionupdate/`+vehicle_pollution_id, data);

}

addPollution(formData:any){
  return this.http.post(`${this.apiUrl}/vehiclepollutions`, formData);
}


//====add woner========
addOwner(formData:any){
  return this.http.post(`${this.apiUrl}/vehicleowners`, formData);
}

getOrganization(){
  return this.http.get(`${this.apiUrl}/organizations`);
}

getOwnerDetails(){
  return this.http.get(`${this.apiUrl}/vehicleowners`);
}

updateOwner(id: any, formData: any) {
  return this.http.put(`${this.apiUrl}/vehicleowners/${id}`, formData); 
}

//== authority maintainace====
getVehiclesByApproveStatus(data: any, authId: any = null) {
  return this.http.get(`${this.apiUrl}/ByApprovalStatus`, {
    params: { 
      maintenance_approve_status: data.toString(), 
      authority_id: authId ? authId.toString() : '' 
    }
  });
}

updateMaintenanceApprove(formData:any,id:any) {
  return this.http.patch(`${this.apiUrl}/vehicle-maintenance/`+id, formData)
}
//===========view user===========
viewUser(organization_id: any, role_id: any) {
  return this.http.get(`${this.apiUrl}/users`, {
    params: {
      user_organization_id: organization_id.toString(),
      role_id: role_id.toString()
    }
  });
}

viewUserdefault(organization_id: any) {
  return this.http.get(`${this.apiUrl}/users`, {
    params: {
      user_organization_id: organization_id.toString(),
    }
  });
}
updateUser(id:any,data:any){
  return this.http.post(`${this.apiUrl}/UserDetailsUpdate/${id}`, data);
}


//start veichel moment
startMomentadd(data:any){
  return this.http.post(`${this.apiUrl}/vehiclemovements`, data);
}

showupDatedmovement(entry_by:any)
{
  return this.http.get(`${this.apiUrl}/getSomedataofmovementbasedonentryby`,{params:{entry_by:entry_by.toString()}})
}
//=======VEHICLE MAINTENANCE APPROVE STATUS BY MANAGER
veicleDatamaintainace(status:any,user_id:any)
{ 
  return this.http.get(`${this.apiUrl}/vehiclemaintenancebyentryBy`, {
    params: {
      maintenance_approve_status: status.toString(),
      entry_by: user_id.toString()
    }
  });
}

veicleDatamaintain(status:any,user_id:any)
{ 
  return this.http.get(`${this.apiUrl}/getMaintenanceDataByManager`, {
    params: {
      maintenance_approve_status: status.toString(),
      entry_by: user_id.toString()
    }
  });
}

updateVeicleMantainManagerStart(data:any){
  return this.http.put(`${this.apiUrl}/VehicleMaintenanceupdatesoemdata`, data);
}


//veicle maintain status====

updateVehicleMaintenance(vehicleMaintenanceId: number, updateData: any) {
  return this.http.post(`${this.apiUrl}/VehicleMaintenanceupdate/${vehicleMaintenanceId}`, updateData);
}

//fuel station
addFillingStation(stationData: any) {
  return this.http.post(`${this.apiUrl}/fuelstations`, stationData);
}

getFillingStations(): Observable<any[]> {
  return this.http.get<any[]>(`${this.apiUrl}/fuelstations`);
}

updateFillingStation(id: string, stationData: any): Observable<any> {
  return this.http.put(`${this.apiUrl}/fuelstations/${id}`, stationData);
}

//=======end veicle moment manager
veicledataForEnd(user_id:any,st_date:any){
  return this.http.get(`${this.apiUrl}/vehicleAssignments`, {
    params: {
      start_date: st_date.toString(),
      user_id: user_id.toString()
    }
  });
}

submitEndMomentManager(data:any)
{
  return this.http.patch(`${this.apiUrl}/updateByManager`, data);
}

//====end veicle moment user===
veicledataEndDriver(user_id:any,st_date:any){
  return this.http.get(`${this.apiUrl}/vehiclesassignmentsCount`, {
    params: {
      start_date: st_date.toString(),
      user_id: user_id.toString()
    }
  });
}

  defaultVehicleMovement(user_id:any)
  {
    return this.http.get(`${this.apiUrl}/getVehicleswhichareinmovment`, {
      params: {
        user_id: user_id.toString()
      }
    });
  } 

veicledataEndDriverNodate(user_id:any){
  return this.http.get(`${this.apiUrl}/vehiclesassignmentsCount`, {
    params: {
      user_id: user_id.toString()
    }
  });
}

driverStKm(data:any){
  return this.http.patch(`${this.apiUrl}/updateKmReadingsByDriver`, data);
}
driverEndKm(data:any){
  return this.http.patch(`${this.apiUrl}/updateKmReadingsByDriver`, data);
}
//===filling fuel ===========
fuelStations(){
  return this.http.get(`${this.apiUrl}/fuelstations`) 
}

addFilling(data:any){
  return this.http.post(`${this.apiUrl}/fuelexpense`, data);
}

getFilldata(st_date:any,ed_date:any,entry_by:any)
{
  return this.http.get(`${this.apiUrl}/getFilteredFuelExpensesbyusridfromto`, {
    params: {
      user_id: entry_by.toString(),
      end_date: st_date.toString(),
      start_date: ed_date.toString()
    }
  });
}

getFilldataDefault(entry_by:any)
{
  return this.http.get(`${this.apiUrl}/getFilteredFuelExpensesbyusridfromto`, {
    params: {
      user_id: entry_by.toString()
    }
  });
}

updateFilling(data:any,id:any){
  return this.http.post(`${this.apiUrl}/fuelexpenseupdate/${id}`, data);
}

updateFillInMovement(mo_id:any,fill_id:any){
  const data = {fuel_expenses_id : fill_id}
  return this.http.put(`${this.apiUrl}/updateFuelExpenseIdInMovement/${mo_id}`, data);
}
//edit fuel expense
getFilldataForEdit(st_date:any,ed_date:any,organization_id:any)
{
  return this.http.get(`${this.apiUrl}/getFilteredFuelExpensesbasedonorg`, {
    params: {
      organization_id: organization_id.toString(),
      end_date: ed_date.toString(),
      start_date: st_date.toString()
    }
  });
}

updatefuelFunction(data:any,id:any){
  return this.http.post(`${this.apiUrl}/fuelexpenseupdate/${id}`, data);
}


//service station
addServiceStation(formData:any){
  return this.http.post(`${this.apiUrl}/servicestation`, formData);
}
getServiceStation(){
  return this.http.get(`${this.apiUrl}/servicestation`);
}

updateServiceStation(id: any, formData: any) {
  return this.http.put(`${this.apiUrl}/servicestation/${id}`, formData); 
}

//==add role

addRole(formData:any){
  return this.http.post(`${this.apiUrl}/userroles`, formData);
}
getRole(){
  return this.http.get(`${this.apiUrl}/userroles`);
}

updateRole(id: any, formData: any) {
  return this.http.put(`${this.apiUrl}/userroles/${id}`, formData); 
}



// API FOR REPORTS


getVehicle(){
  return this.http.get(`${this.apiUrl}/allvehicles`);
}
getVehicles(orgId:any){
  return this.http.get(`${this.apiUrl}/allvehicles?organization_id=${orgId}`);
}

getPolluReport(orgId:any){
  return this.http.get(`${this.apiUrl}/vehiclepollutions?organization_id=${orgId}`)
}

getInsurance(orgId:any){
  return this.http.get(`${this.apiUrl}/vehicleinsurances?organization_id=${orgId}`);
}
getUserRoles(){
  return this.http.get(`${this.apiUrl}/userroles`);
}

getVehiclegetUsers(orgId:any){
  return this.http.get(`${this.apiUrl}/users?user_organization_id=${orgId}`);
}
getUserDetailsById(roleId:any){
  return this.http.get(`${this.apiUrl}/users?role_id=${roleId}`);
}

fuelExpenseByDate(startDt:any,EndDt:any,orgId:any){
  return this.http.get(`${this.apiUrl}/daterangeFuelExpenseReport?start_date=${startDt}&end_date=${EndDt}&organization_id=${orgId}`);
}
fuelExpenseByVehicle(vehicleId:any){
  return this.http.get(`${this.apiUrl}/daterangeFuelExpenseReport?vehicle_id=${vehicleId}`);
}
fuelExpenseByBothVehicleAndDate(startDt: any, EndDt: any, vehicleId: any){
  return this.http.get(`${this.apiUrl}/daterangeFuelExpenseReport?start_date=${startDt}&end_date=${EndDt}&vehicle_id=${vehicleId}`);
}

fetchVehicleCategories(){
  return this.http.get(`${this.apiUrl}/vehiclecategories`);
}

getVehicleCategoryId(vehiclecategoryid:any){
  return this.http.get(`${this.apiUrl}/reportByVehicleCategory?vehicle_category_id=${vehiclecategoryid}`);
}
fetchVehicleCategoryId(vehiclecategoryid:any,vehicleId: any){
  return this.http.get(`${this.apiUrl}/reportByVehicleCategory?vehicle_category_id=${vehiclecategoryid}&vehicle_id=${vehicleId}`);
}

vehicleMaintainanceByDate(startDt:any,EndDt:string,orgId:any){
  return this.http.get(`${this.apiUrl}/reportByDateRangeVehicleMaintenance?start_date=${startDt}&end_date=${EndDt}&organization_id=${orgId}`);
}

vehicleMaintainanceByVehicle(vehicleId:any){
  return this.http.get(`${this.apiUrl}/reportByDateRangeVehicleMaintenance?vehicle_id=${vehicleId}`);
}
vehicleMaintainanceByBothVehicleAndDate(startDt: any, EndDt: any, vehicleId: any){
  return this.http.get(`${this.apiUrl}/reportByDateRangeVehicleMaintenance?start_date=${startDt}&end_date=${EndDt}&vehicle_id=${vehicleId}`);
}

fetchOwnerName(orgId:any){
  return this.http.get(`${this.apiUrl}/vehicleowners?organization_id=${orgId}`);
}
getFillingFuelByOwnerName(ownerId:any){
  return this.http.get(`${this.apiUrl}/FuelFillingReport?vehicle_owner_id=${ownerId}`);
}
getFillingFuelByDate(startDt:any,EndDt:any,orgId:any){
  return this.http.get(`${this.apiUrl}/FuelFillingReport?from_date=${startDt}&to_date=${EndDt}&organization_id=${orgId}`);
}
getFillingFuelByVehicleNo(vehicleId:any){
  return this.http.get(`${this.apiUrl}/FuelFillingReport?vehicle_id=${vehicleId}`);
}
getFillingFuelBothDateAndVehicleNo(startDt: any, EndDt: any, vehicleId: any){
  return this.http.get(`${this.apiUrl}/FuelFillingReport?from_date=${startDt}&to_date=${EndDt}&vehicle_id=${vehicleId}`);
}

getMovementsReportByDate(startDt:any,endDt:any,orgId:any){
  return this.http.get(`${this.apiUrl}/reportByDateRangeofMovement?from_date=${startDt}&to_date=${endDt}&organization_id=${orgId}`);
}
getMovementsReportByTime(startTm:any,endTm:any){
  return this.http.get(`${this.apiUrl}/reportByDateRangeofMovement?start_time=${startTm}&end_time=${endTm}`);
}

getMovementsReportByBothDateAndTime(startDt:any,endDt:any,startTm:any,endTm:any){
  return this.http.get(`${this.apiUrl}/reportByDateRangeofMovement?from_date=${startDt}&to_date=${endDt}&start_time=${startTm}&end_time=${endTm}`);
}
getMovementByVehicleNo(vehicleId:any){
  return this.http.get(`${this.apiUrl}/reportByDateRangeofMovement?vehicle_id=${vehicleId}`);
}
getMovementBothDateAndVehicleNo(startDt: any, EndDt: any, vehicleId: any){
  return this.http.get(`${this.apiUrl}/reportByDateRangeofMovement?from_date=${startDt}&to_date=${EndDt}&vehicle_id=${vehicleId}`);
}

vehicleMaintemceSomeDta(orgId:any){
  return this.http.get(`${this.apiUrl}/vehiclemaintences?organization_id=${orgId}`);
}
vehicleMovmentSomeDta(orgId:any){
  return this.http.get(`${this.apiUrl}/vehiclemovements?organization_id=${orgId}`);
}
FuelFillingSomeDta(orgId:any){
  return this.http.get(`${this.apiUrl}/fuelexpense?organization_id=${orgId}`);
}

// FOR UNOFFICIAL FUEL REPORT
getSomeDataUnofficialReport(orgId:any){
  return this.http.get(`${this.apiUrl}/UnofficialFuelAExpense?organization_id=${orgId}`);
}
getOtherOwner(orgId:any){
  return this.http.get(`${this.apiUrl}/OtherVehicleGet?organization_id=${orgId}`);
}
unOfficialReportByDate(orgId:any,startDt:any,endDt:any){
  return this.http.get(`${this.apiUrl}/UnofficialFuelAExpense?organization_id=${orgId}&from_date=${startDt}&to_date=${endDt}`);
}

unOfficialReportByOwner(orgId:any,othID:any){
  return this.http.get(`${this.apiUrl}/UnofficialFuelAExpense?organization_id=${orgId}&other_vehicle_id=${othID}`);
}

unOfficialReportByDateAndOwner(orgId:any,startDt:any,endDt:any,othID:any){
  return this.http.get(`${this.apiUrl}/UnofficialFuelAExpense?organization_id=${orgId}&from_date=${startDt}&to_date=${endDt}&other_vehicle_id=${othID}`);
}


//====show data for landing page=====
totalvehicle(org_id:any)
{
  return this.http.get(`${this.apiUrl}/getStatistics`,{params:{org_id:org_id.toString()}})
}

//my maintenace deiver 
driverMaintain(user_id:any){
  return this.http.get(`${this.apiUrl}/basedonDriverandDateMaintenancae`, {
    params: {
      driver_id: user_id.toString()
    }
  });
}

driverMaintaindate(user_id:any,st_date:any){
  return this.http.get(`${this.apiUrl}/basedonDriverandDateMaintenancae`, {
    params: {
      date: st_date.toString(),
      driver_id: user_id.toString()
    }
  });
}

//my maintenace fuel expense deiver 
driverFueldefault(user_id:any){
  return this.http.get(`${this.apiUrl}/basedOnDriverFuelFilling`, {
    params: {
      driver_id: user_id.toString()
    }
  });
}


driverFuel(user_id:any,st_date:any){
  return this.http.get(`${this.apiUrl}/basedOnDriverFuelFilling`, {
    params: {
      date: st_date.toString(),
      driver_id: user_id.toString()
    }
  });
}



//manage vehicle
getAllvehicleActiveInactive(org_id:any){
  return this.http.get(`${this.apiUrl}/allvehicles`, {
    params: {
      organization_id: org_id.toString(),
    }
  });
}

getVehicleOrgWise(org_id:any){
  return this.http.get(`${this.apiUrl}/vehicles`, {
    params: {
      organization_id: org_id.toString(),
    }
  });
}

getVehicleCategoryIdOrg(vehiclecategoryid:any,org_id:any){
  return this.http.get(`${this.apiUrl}/reportByVehicleCategory`, {
    params: {
      vehicle_category_id:vehiclecategoryid.toString(),
      organization_id: org_id.toString(),
    }
  });
}

updateVehicle(data:any,id:any){
  return this.http.post(`${this.apiUrl}/updateVehicle/${id}`, data);
}

//assign user role
assignRole(data:any)
{
  return this.http.post(`${this.apiUrl}/assignedRole`, data);
}

getAssignRoledata(user_id:any){
  return this.http.get(`${this.apiUrl}/assignedRole`, {
    params: {
      user_id: user_id.toString()
    }
  });
}

deleteRole(data:any)
{
   return this.http.delete(`${this.apiUrl}/removeRole`, {
    params: {
      user_id: data.user_id.toString(),
      assigned_role_id: data.assigned_role_id.toString(),
      role_id: data.role_id.toString()
    }
  });
}

viewUserdefaultInAssign(org_id:any){
  return this.http.get(`${this.apiUrl}/assinedroleuserData`, {
    params: {
      user_organization_id: org_id.toString()
    }
  });
}

viewUserIdWisenAssign(user_id:any){
  return this.http.get(`${this.apiUrl}/assinedroleuserData`, {
    params: {
      user_id : user_id.toString()
    }
  });
}

//========add vehicle other ==========
otherVehicleadd(data:any)
{
  return this.http.post(`${this.apiUrl}/OtherVehiclestore`, data);
}

  getOtherVehicleOrgWise(org_id:any){
  return this.http.get(`${this.apiUrl}/OtherVehicleGet`, {
    params: {
      organization_id : org_id.toString()
    }
  });
}

onOFFfuelSubmit(data:any)
{
  return this.http.post(`${this.apiUrl}/OtherVehiclesFuelStore`, data);
}

getUnofFilldataDefault(entry_by:any)
{
  return this.http.get(`${this.apiUrl}/OtherVehiclesFuelGetBasedONEntryBy`, {
    params: {
      entry_by: entry_by.toString()
    }
  });
}

getFilldataUnOff(st_date:any,ed_date:any,entry_by:any)
{
  return this.http.get(`${this.apiUrl}/OtherVehiclesFuelGetBasedONEntryBy`, {
    params: {
      user_id: entry_by.toString(),
      to_date:st_date.toString(),
      from_date: ed_date .toString()
    }
  });
}

updateOtherFilling(data:any,id:any){
  return this.http.post(`${this.apiUrl}/OtherVehiclesFuelUpdate/${id}`, data);
}
//=======current location========
getLocation(){
  return this.http.get('https://ipapi.co/json/');
}

checkIn(data:any){
  return this.http.post(`${this.apiUrl}/storeAttenDance`, data);
}

 atteData(user_id:any)
{
  return this.http.get(`${this.apiUrl}/getTodayAttendancebasedonuserid`,{params:{user_id:user_id.toString()}})
}

checkOut(attendance_id:any){
  const checkOutData = {
    attendance_id: attendance_id
  };
  return this.http.put(`${this.apiUrl}/checkOut/${attendance_id}`,checkOutData)
}

dateFilter(entry_by:any,st_date:any,ed_date:any){
  return this.http.get(`${this.apiUrl}/getAttendanceByDateRange`, {
    params: {
      user_id: entry_by.toString(),
      to_date:st_date.toString(),
      from_date: ed_date .toString()
    }
  });
}
//=======over time========
addOt(data:any){
  return this.http.post(`${this.apiUrl}/assignOvertimeDutytoDriver`, data);
}

getOtdata(user_id:any,from_date:any,to_date:any){
  return this.http.get(`${this.apiUrl}/getOvertimeDuty`, {
    params: {
      user_id: user_id.toString(),
      to_date:to_date.toString(),
      from_date: from_date .toString()
    }
  });
}

getdroverTime(user_id:any)
{
  return this.http.get(`${this.apiUrl}/getmyOverdutybasedondriver`, {
    params: {
      driver_id: user_id.toString(),
  }
});
}

getdroverTimeupdate(user_id:any)
{
  return this.http.get(`${this.apiUrl}/getRecentUpdatedData`, {
    params: {
      driver_id: user_id.toString(),
  }
});
}

overtimechekIn(formData: any) {
  return this.http.put(`${this.apiUrl}/handleOvertime`, formData); 
}

//add vehicle categories
addCategory(data:any){
  return this.http.post(`${this.apiUrl}/vehiclecategories`, data);
}

updateVehiclecatagories(id:any,data:any){
  return this.http.put(`${this.apiUrl}/updateVehicleCategory/${id}`, data);
}

//duty page of attendance
dutyData(user_id:any,from_date:any,to_date:any)
{
  return this.http.get(`${this.apiUrl}/getAllDriversReport`,{
    params: {
      user_id: user_id.toString(),
      date_to:to_date.toString(),
      date_from: from_date .toString()
    }
  });
}

//my attendance
allAttendance(data:any)
{
  console.log(data);
  return this.http.get(`${this.apiUrl}/filterAttendance`, {
    params: {
      user_id: data.user_id.toString(),
      from_date:data.from_date.toString(),
      to_date: data.to_date .toString(),
      role_id: data.role_id .toString()
    }
  });
}


}




