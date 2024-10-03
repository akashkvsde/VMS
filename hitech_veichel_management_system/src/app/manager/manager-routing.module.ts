import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

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

const routes: Routes = [
  {path:'add-maintainace',component:AddVeichelMaintainComponent},
  {path:'fuel-filling',component:FuelFillingComponent},
  {path:'add-pollution',component:AddPollutionComponent},
  {path:'add-insurance',component:AddInsuranceComponent},
  {path:'start-veichel-moment',component:StartVeichelMomentComponent},
  {path:'veichel-maintainace-status',component:VeichelMaintainStatusComponent},
  {path:'maintained-veichel-status',component:MaintainVeichelStatusComponent},
  {path:'add-fuel-station',component:AddFuelStationComponent},
  {path:'add-service-station',component:AddServiceStationComponent},
  {path:'end_movement_manager',component:EndMovementManagerComponent},
  {path:'fuel_filling_other',component:FuelFillingOtherComponent},
  
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ManagerRoutingModule { }
