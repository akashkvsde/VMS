import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { LoginComponent } from './login/login.component';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { ReactiveFormsModule } from '@angular/forms';
import { NgxPaginationModule } from 'ngx-pagination';
import { NotfoundComponent } from './notfound/notfound.component';
import { CustomHeaderInterceptor } from './custom-header.interceptor';







@NgModule({
  declarations: [
    AppComponent,
    DashboardComponent,
    LoginComponent,
    NotfoundComponent

  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    ReactiveFormsModule,NgxPaginationModule,
  
    
  ],
  providers: [
   {
    provide:HTTP_INTERCEPTORS,
    useClass:CustomHeaderInterceptor,
    multi:true,
   }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
