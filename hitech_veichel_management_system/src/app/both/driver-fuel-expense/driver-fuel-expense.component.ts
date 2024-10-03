import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-driver-fuel-expense',
  templateUrl: './driver-fuel-expense.component.html',
  styleUrls: ['./driver-fuel-expense.component.css']
})
export class DriverFuelExpenseComponent {
 
  message:any = "No data avaialable";
  vehicle_movement_id:any
  organization_id:any;
  entry_by:any;
  user_role_name:any;
  selected_date:any
  veicle_moment_fetch_data:any
constructor(private allService:VeichelserviceService){}

ngOnInit() {

  //session area
  const userInfoString = localStorage.getItem('userInfo');
  const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
  this.organization_id = userInfo?.organizationId; 
  this.entry_by = userInfo?.userId; 
  const roleName = localStorage.getItem('roleName');
  this.user_role_name = roleName;
 // session area end
  
this.defaultDate();
}


//start date

onDateChange(dateValue: string): void {
  this.selected_date = dateValue;
  this.allService.driverFuel(this.entry_by,dateValue).subscribe((data:any)=>{
    this.veicle_moment_fetch_data = data.data;
    console.log(data.driver);
    if(this.veicle_moment_fetch_data != 0){
      
      this.message = "";
      console.log("hi",this.veicle_moment_fetch_data );
      
    }else{
      this.message = "No data availabele!";
      this.veicle_moment_fetch_data = null;
    }
  },(err:any)=>{
    // alert("Failed To show data !!");
    this.message = "No data availabele!";
    this.veicle_moment_fetch_data = null;
  })
}

defaultDate(): void {
  this.allService.driverFueldefault(this.entry_by).subscribe((data:any)=>{
    this.veicle_moment_fetch_data = data.data;
    console.log(this.veicle_moment_fetch_data);
    if(this.veicle_moment_fetch_data != 0){
      this.message = "";
    }else{
      this.message = "No data availabele!";
    }
  },(err:any)=>{
    console.log("Failed To show data !!");
    console.log(err);
    this.veicle_moment_fetch_data = null;
  })
}
 
}
