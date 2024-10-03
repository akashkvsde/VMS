import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';

import { BothRoutingModule } from './both-routing.module';
import { AuthorityMaintainaceComponent } from './authority-maintainace/authority-maintainace.component';
import { VeichelMomentEndComponent } from './veichel-moment-end/veichel-moment-end.component';
import { LandingPageComponent } from './landing-page/landing-page.component';
import { DriverMaintainaceComponent } from './driver-maintainace/driver-maintainace.component';
import { DriverFuelExpenseComponent } from './driver-fuel-expense/driver-fuel-expense.component';
import { ManagementDStMovementComponent } from './management-d-st-movement/management-d-st-movement.component';
import { ManagementDEndMovementComponent } from './management-d-end-movement/management-d-end-movement.component';
import { AssignRoleComponent } from './assign-role/assign-role.component';
import { NgSelectModule } from '@ng-select/ng-select';



@NgModule({
  declarations: [
    AuthorityMaintainaceComponent,
    VeichelMomentEndComponent,
    LandingPageComponent,
    DriverMaintainaceComponent,
    DriverFuelExpenseComponent,
    ManagementDStMovementComponent,
    ManagementDEndMovementComponent,
    AssignRoleComponent
  ],
  imports: [
    CommonModule,
    BothRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    NgSelectModule
  ]
})
export class BothModule { }
