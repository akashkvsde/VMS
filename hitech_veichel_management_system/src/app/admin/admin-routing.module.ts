import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { UserRegistrationComponent } from './user-registration/user-registration.component';
import { AddVeichelComponent } from './add-veichel/add-veichel.component';
import { ViewEmployeeComponent } from './view-employee/view-employee.component'; 
import { AssignNavigationComponent } from './assign-navigation/assign-navigation.component';
import { AddRoleComponent } from './add-role/add-role.component';
import { EditFuelExpenseComponent } from './edit-fuel-expense/edit-fuel-expense.component';
import { AddWonerComponent } from './add-woner/add-woner.component';
import { ManageVehicleComponent } from './manage-vehicle/manage-vehicle.component';
import { VehicleCategoriesComponent } from './vehicle-categories/vehicle-categories.component';

const routes: Routes = [
  {path:'user-registration',component:UserRegistrationComponent},
  {path:'add-veichel',component:AddVeichelComponent},
  {path:'view-employee',component:ViewEmployeeComponent},
  {path:'assign-navigation',component:AssignNavigationComponent},
  {path:'add-role',component:AddRoleComponent},
  {path:'edit-fule-expense',component:EditFuelExpenseComponent},
  {path:'add-woner',component:AddWonerComponent},
  {path:'manage-vehicle',component:ManageVehicleComponent},
  {path:'vehicle-categories',component:VehicleCategoriesComponent},

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdminRoutingModule { }
