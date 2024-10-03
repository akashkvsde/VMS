import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';

import { ManagerRoutingModule } from './manager-routing.module';
import { AddVeichelMaintainComponent } from './add-veichel-maintain/add-veichel-maintain.component';
import { FuelFillingComponent } from './fuel-filling/fuel-filling.component';
import { AddPollutionComponent } from './add-pollution/add-pollution.component';
import { AddInsuranceComponent } from './add-insurance/add-insurance.component';
import { StartVeichelMomentComponent } from './start-veichel-moment/start-veichel-moment.component';
import { VeichelMaintainStatusComponent } from './veichel-maintain-status/veichel-maintain-status.component';
import { MaintainVeichelStatusComponent } from './maintain-veichel-status/maintain-veichel-status.component';
import { AddFuelStationComponent } from './add-fuel-station/add-fuel-station.component';
import { AddServiceStationComponent } from './add-service-station/add-service-station.component';
import { EndMovementManagerComponent } from './end-movement-manager/end-movement-manager.component';
import { FuelFillingOtherComponent } from './fuel-filling-other/fuel-filling-other.component';
import { NgSelectModule } from '@ng-select/ng-select';




@NgModule({
  declarations: [
    AddVeichelMaintainComponent,
    FuelFillingComponent,
    AddPollutionComponent,
    AddInsuranceComponent,
    StartVeichelMomentComponent,
    VeichelMaintainStatusComponent,
    MaintainVeichelStatusComponent,
    AddFuelStationComponent,
    AddServiceStationComponent,
    EndMovementManagerComponent,
    FuelFillingOtherComponent

  ],
  imports: [
    CommonModule,
    ManagerRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    NgSelectModule
  
  ]
})
export class ManagerModule { }
