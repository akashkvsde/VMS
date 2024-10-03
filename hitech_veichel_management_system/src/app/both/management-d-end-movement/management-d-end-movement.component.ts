import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-management-d-end-movement',
  templateUrl: './management-d-end-movement.component.html',
  styleUrls: ['./management-d-end-movement.component.css']
})
export class ManagementDEndMovementComponent {
  momentEndForm!: FormGroup;
  organization_id: any;
  entry_by: any;
  user_role_name: any;
  selected_date: any;
  vehicle_movement_id:any;
  veicle_moment_fetch_data: any;
  disable_btn:boolean = false;
  start_km_reading: any = 0; // st_km initialized
  distace_total: any = 0; // Initialize distance_total
  end_km_reading:any;
  currentDate = new Date().toISOString().split('T')[0];
  message:any = "";

  constructor(private allService: VeichelserviceService, private fb: FormBuilder) {}

  ngOnInit(): void {
    // Session area
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 
    const roleName = localStorage.getItem('roleName');
    this.user_role_name = roleName;
    // Session area end
    this.defaultData();
    this.initialiZatin(); // Form initialization;
  }

  //defaultdata
  defaultData(){
    this.allService.defaultVehicleMovement(this.entry_by).subscribe((data: any) => {
      this.veicle_moment_fetch_data = data;
      console.log(data);
      this.message = "";
      this.initialiZatin(); // Re-initialize form with fetched data
    }, (err: any) => {
      console.log(err);     
      this.veicle_moment_fetch_data = [];
      this.message = "No Data Avaialable";
    });
  }

  // Initialize form
  initialiZatin(): void {
    this.momentEndForm = this.fb.group({
      movement_end_km_reading_by_manager: ['', Validators.required],
      movement_end_km_reading_by_driver: ['', Validators.required],
  
      movement_distance_covered: [{ value: this.distace_total }], // Set initial value and disable control
      movement_end_date: ['', Validators.required],
  
      movement_end_time: ['', Validators.required],
      movement_end_time_by_driver: ['', Validators.required],
  
      vehicle_movement_id: [this.vehicle_movement_id],
      
      movement_status: [0]
    });
  
    // Automatically copy the values to corresponding fields
    this.momentEndForm.get('movement_end_km_reading_by_manager')?.valueChanges.subscribe(value => {
      this.momentEndForm.get('movement_end_km_reading_by_driver')?.setValue(value, { emitEvent: false });
    });
  
    this.momentEndForm.get('movement_end_time')?.valueChanges.subscribe(value => {
      this.momentEndForm.get('movement_end_time_by_driver')?.setValue(value, { emitEvent: false });
    });
  }
  

  // Start date
  onDateChange(dateValue: string): void {
    this.selected_date = dateValue;
    console.log(dateValue);
    
    this.allService.veicledataForEnd(this.entry_by, dateValue).subscribe((data: any) => {
      this.veicle_moment_fetch_data = data;
      console.log(data);
      this.message = "";
      this.initialiZatin(); // Re-initialize form with fetched data
    }, (err: any) => {
      console.log(err);
      this.veicle_moment_fetch_data = [];
      this.message = "No Data Avaialable";
    });
  }

  // Calculate distance
  distanceCalculate(end_km: any): void {
    this.end_km_reading = end_km;
    console.log(this.end_km_reading);
    
    this.distace_total = end_km - this.start_km_reading;
    this.momentEndForm.patchValue({ movement_distance_covered: this.distace_total });
  }

  //getsingle moment data
  getSinglemomentData(data:any){
   this.start_km_reading = data.movement_start_km_reading_by_manager;
   this.vehicle_movement_id = data.vehicle_movement_id;
   if(data.movement_end_km_reading_by_manager !=  null){
    this.disable_btn = true
   }else{
    this.disable_btn = false
   }
   this.initialiZatin();
  }
  // Submit
  onSubmit(): void {
    if(this.start_km_reading >= this.end_km_reading){
      alert("End km should be more than Start km reading");
    }else{
      
      if (this.momentEndForm.valid) {
        console.log(this.momentEndForm.value);
        this.allService.submitEndMomentManager(this.momentEndForm.value).subscribe(
          (data: any) => {
            alert(data.message);
           if(this.selected_date !=null){
              this.onDateChange(this.selected_date)
            }else{
              this.defaultData();
            }
            this.momentEndForm.reset(); // Reset the form after successful submission
          },
          (err: any) => {
            alert("Failed to update!!");
          }
        );
      } else {
        console.log('Form is invalid');
      }

    }
  }
}
