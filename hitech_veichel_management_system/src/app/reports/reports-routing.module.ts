import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { PollutionReportComponent } from './pollution-report/pollution-report.component';
import { InsuranceReportComponent } from './insurance-report/insurance-report.component';
import { UserDetailReportComponent } from './user-detail-report/user-detail-report.component';
import { VeichelMaintainReportComponent } from './veichel-maintain-report/veichel-maintain-report.component';
import { FillingReportComponent } from './filling-report/filling-report.component';
import { VeichelDetailReportComponent } from './veichel-detail-report/veichel-detail-report.component';
import { FuelExpenseReportComponent } from './fuel-expense-report/fuel-expense-report.component';
import { VehicleMovementReportComponent } from './vehicle-movement-report/vehicle-movement-report.component';
import { UnofficialFuelExpenseComponent } from './unofficial-fuel-expense/unofficial-fuel-expense.component';
import { AllAttendanceComponent } from './all-attendance/all-attendance.component';
import { DutyPageComponent } from './duty-page/duty-page.component';
const routes: Routes = [
  {path:'pollution-report',component:PollutionReportComponent},
  {path:'insurance-report',component:InsuranceReportComponent},
  {path:'user-detail-report',component:UserDetailReportComponent},
  {path:'veichel-maintainace-report',component:VeichelMaintainReportComponent},
  {path:'filling-report',component:FillingReportComponent},
  {path:'veichel-detail-report',component:VeichelDetailReportComponent},
  {path:'fuel-expense-report',component:FuelExpenseReportComponent},
  {path:'vehicle-movement-report',component:VehicleMovementReportComponent},
  {path:'unofficial_fuel_expense',component:UnofficialFuelExpenseComponent},
  {path:'all_attendance',component:AllAttendanceComponent},
  {path:'duty',component:DutyPageComponent}

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ReportsRoutingModule { }
