import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';



import { AdminRoutingModule } from './admin-routing.module';
import { UserRegistrationComponent } from './user-registration/user-registration.component';
import { AddVeichelComponent } from './add-veichel/add-veichel.component';
import { ViewEmployeeComponent } from './view-employee/view-employee.component';
import { FormsModule } from '@angular/forms';
import { AssignNavigationComponent } from './assign-navigation/assign-navigation.component';
import { AddRoleComponent } from './add-role/add-role.component';
import { EditFuelExpenseComponent } from './edit-fuel-expense/edit-fuel-expense.component';
import { AddWonerComponent } from './add-woner/add-woner.component';
import { ManageVehicleComponent } from './manage-vehicle/manage-vehicle.component';
import { VehicleCategoriesComponent } from './vehicle-categories/vehicle-categories.component';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';



@NgModule({
  declarations: [
    UserRegistrationComponent,
    AddVeichelComponent,
    ViewEmployeeComponent,
    AssignNavigationComponent,
    AddRoleComponent,
    EditFuelExpenseComponent,
    AddWonerComponent,
    ManageVehicleComponent,
    VehicleCategoriesComponent,

  ],
  imports: [
    CommonModule,
    AdminRoutingModule,
    FormsModule,
    ReactiveFormsModule
  ],
 
})
export class AdminModule { }
