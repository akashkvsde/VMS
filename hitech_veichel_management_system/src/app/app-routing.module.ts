import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { DashboardComponent } from './dashboard/dashboard.component';
import { LoginComponent } from './login/login.component';
import { LandingPageComponent } from './both/landing-page/landing-page.component';
import { RouteGuard } from './guards/route.guard'; // Import the guard
import { NotfoundComponent } from './notfound/notfound.component';
import { AttendanceModule } from './attendance/attendance.module';
const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  

  {
    path: 'dashboard',
    component: DashboardComponent,
    canActivate: [RouteGuard], // Apply the guard here
    children: [
      { path: '', redirectTo: 'landing-page', pathMatch: 'full' },
      { path: 'landing-page', component: LandingPageComponent }, // Unrestricted access
      { path: 'admin', loadChildren: () => import('./admin/admin.module').then(mod => mod.AdminModule) },
      { path: 'manager', loadChildren: () => import('./manager/manager.module').then(mod => mod.ManagerModule) },
      { path: 'both', loadChildren: () => import('./both/both.module').then(mod => mod.BothModule) },
      { path: 'reports', loadChildren: () => import('./reports/reports.module').then(mod => mod.ReportsModule) },
      { path: 'attendance', loadChildren: () => import('./attendance/attendance.module').then(mod => mod.AttendanceModule) },

    ]
  },
  // Wildcard route to redirect to login page
  // { path: '**', redirectTo: '/notfound' }
  { path: '**', component: NotfoundComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
