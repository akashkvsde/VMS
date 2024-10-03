import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
@Component({
  selector: 'app-driver-maintainace',
  templateUrl: './driver-maintainace.component.html',
  styleUrls: ['./driver-maintainace.component.css']
})
export class DriverMaintainaceComponent {
  message:any = "No data available";
  st_btn:boolean = false
  ed_btn:boolean = false
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
  this.allService.driverMaintaindate(this.entry_by,dateValue).subscribe((data:any)=>{
    this.veicle_moment_fetch_data = data.data;
    if(this.veicle_moment_fetch_data != 0){
      this.message = null;
    }else{
      this.message = "No data availabele!";
    }
    console.log(data);
  },(err:any)=>{
    alert("Failed To show data !!");
    this.message = "No data availabele!";
  })
}

defaultDate(): void {
  this.allService.driverMaintain(this.entry_by).subscribe((data:any)=>{
    console.log(data);
    this.veicle_moment_fetch_data = data.data;
    if(this.veicle_moment_fetch_data != 0){
      this.message = null;
    }else{
      this.message = "No data availabele!";
    }
  },(err:any)=>{
    console.log("Failed To show data !!");
    console.log(err);
    this.message = "No data availabele!";
  })
}
 //get single moment data
 getSinglemomentData(data:any){
  this.vehicle_movement_id = data.vehicle_movement_id ;
  console.log(this.vehicle_movement_id,data.movement_start_km_reading_by_driver,data.movement_end_km_reading_by_driver);
  if((data.movement_start_km_reading_by_driver) != null){
    this.st_btn = true;
  }else{
    this.st_btn = false;
  }

  if((data.movement_end_km_reading_by_driver) != null){
    this.ed_btn = true;
  }else{
    this.ed_btn = false;
  }

 }

//update start km reading
stKmreadingUpdata(stkm: any) {
  const starray = {
    vehicle_movement_id: this.vehicle_movement_id,
    movement_start_km_reading_by_driver: stkm
  };

  this.allService.driverStKm(starray).subscribe(
    (data: any) => {
      alert(data.message);
      this.onDateChange(this.selected_date)
    },
    (err: any) => {
      alert("Failed to update");
      this.onDateChange(this.selected_date)
      console.log(this.selected_date);
    }
  );
}



//update end km
endKmReading(endKm: any) {
  const starray = {
    vehicle_movement_id: this.vehicle_movement_id,
    movement_end_km_reading_by_driver: endKm
  };

  this.allService.driverStKm(starray).subscribe(
    (data: any) => {
      alert(data.message);
      this.onDateChange(this.selected_date)
    },
    (err: any) => {
      alert("Failed to update");
    }
  );
}


}
