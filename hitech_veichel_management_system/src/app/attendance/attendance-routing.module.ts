import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MyAttendanceComponent } from './my-attendance/my-attendance.component';
import { MyAttendanceDetailComponent } from './my-attendance-detail/my-attendance-detail.component';
import { OverdutyComponent } from './overduty/overduty.component';

const routes: Routes = [
  {path:'my-attendance',component:MyAttendanceComponent},
  {path:'my-attendance-detail',component:MyAttendanceDetailComponent},
  {path:'overtime',component:OverdutyComponent}

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AttendanceRoutingModule { }
