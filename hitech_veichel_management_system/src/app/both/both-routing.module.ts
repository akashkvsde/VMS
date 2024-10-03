import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthorityMaintainaceComponent } from './authority-maintainace/authority-maintainace.component';
import { VeichelMomentEndComponent } from './veichel-moment-end/veichel-moment-end.component';
import { DriverMaintainaceComponent } from './driver-maintainace/driver-maintainace.component';
import { DriverFuelExpenseComponent } from './driver-fuel-expense/driver-fuel-expense.component';
import { ManagementDStMovementComponent } from './management-d-st-movement/management-d-st-movement.component';
import { ManagementDEndMovementComponent } from './management-d-end-movement/management-d-end-movement.component';
import { AssignRoleComponent } from './assign-role/assign-role.component';

const routes: Routes = [
  {path:'authority-maintainace',component:AuthorityMaintainaceComponent},
  {path:'veichel-moment-end',component:VeichelMomentEndComponent},
  {path:'driver_maintainace',component:DriverMaintainaceComponent},
  {path:'driver_fuel_expense',component:DriverFuelExpenseComponent},
  {path:'st_movement_management_driver',component:ManagementDStMovementComponent}, //both/st_movement_management_driver
  {path:'end_movement_management_driver',component:ManagementDEndMovementComponent},//both/end_movement_management_driver
  {path:'assign_role',component:AssignRoleComponent}//both/assign_role
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BothRoutingModule { }
