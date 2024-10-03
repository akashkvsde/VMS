import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';

import { AttendanceRoutingModule } from './attendance-routing.module';
import { MyAttendanceComponent } from './my-attendance/my-attendance.component';
import { MyAttendanceDetailComponent } from './my-attendance-detail/my-attendance-detail.component';
import { OverdutyComponent } from './overduty/overduty.component';
import { NgSelectModule } from '@ng-select/ng-select';

@NgModule({
  declarations: [
    MyAttendanceComponent,
    MyAttendanceDetailComponent,
    OverdutyComponent
  ],
  imports: [
    CommonModule,
    AttendanceRoutingModule,
    NgSelectModule,
    FormsModule,
    ReactiveFormsModule
  ]
})
export class AttendanceModule { }
