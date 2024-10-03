import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NgxPaginationModule } from 'ngx-pagination';
import { ReactiveFormsModule} from '@angular/forms';
import { ReportsRoutingModule } from './reports-routing.module';
import { PollutionReportComponent } from './pollution-report/pollution-report.component';
import { InsuranceReportComponent } from './insurance-report/insurance-report.component';
import { UserDetailReportComponent } from './user-detail-report/user-detail-report.component';
import { VeichelMaintainReportComponent } from './veichel-maintain-report/veichel-maintain-report.component';
import { FillingReportComponent } from './filling-report/filling-report.component';
import { VeichelDetailReportComponent } from './veichel-detail-report/veichel-detail-report.component';
import { FuelExpenseReportComponent } from './fuel-expense-report/fuel-expense-report.component';
import { VehicleMovementReportComponent } from './vehicle-movement-report/vehicle-movement-report.component';
import { FormsModule } from '@angular/forms';
import { UnofficialFuelExpenseComponent } from './unofficial-fuel-expense/unofficial-fuel-expense.component';
import { AllAttendanceComponent } from './all-attendance/all-attendance.component';
import { DutyPageComponent } from './duty-page/duty-page.component';


@NgModule({
  declarations: [
    PollutionReportComponent,
    InsuranceReportComponent,
    UserDetailReportComponent,
    VeichelMaintainReportComponent,
    FillingReportComponent,
    VeichelDetailReportComponent,
    FuelExpenseReportComponent,
    VehicleMovementReportComponent,
    UnofficialFuelExpenseComponent,
    AllAttendanceComponent,
    DutyPageComponent
  ],
  imports: [
    CommonModule,
    ReportsRoutingModule,
    NgxPaginationModule,
    FormsModule,
    ReactiveFormsModule
  ]
})
export class ReportsModule { }
