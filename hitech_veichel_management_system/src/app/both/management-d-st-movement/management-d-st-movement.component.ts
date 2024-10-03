import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms'
@Component({
  selector: 'app-management-d-st-movement',
  templateUrl: './management-d-st-movement.component.html',
  styleUrls: ['./management-d-st-movement.component.css']
})
export class ManagementDStMovementComponent {
  movement:any[]=[];
  organization_id:any
  entry_by:any
  all_veichels:any
  all_drivers:any
  movementForm!: FormGroup;
  constructor (private allService : VeichelserviceService,private fb: FormBuilder){}
  
ngOnInit()
{
  const userInfoString = localStorage.getItem('userInfo');
  const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
  this.organization_id = userInfo?.organizationId; 
  this.entry_by = userInfo?.userId; 

  this.allDrivers();
  this.allVeichel();
  this.initialization();
  this.fetchstMovedata();
  setInterval(() => {
    this.allVeichel();
  }, 50000);
}

// Get all vehicle data
allVeichel() {
  this.allService.freeAllVeichel(this.organization_id).subscribe(
    (data: any) => {
      // Filter vehicles with category_id equal to 14
      this.all_veichels = data.filter((vehicle: any) => vehicle.vehicle_category_id === 13);
    },
    (err: any) => {
      console.log(err);
    }
  );
}


initialization() {
  this.movementForm = this.fb.group({
    vehicle_id: [null, Validators.required],
    driver_id: [this.entry_by],
    movement_start_date: ['', Validators.required],
    movement_start_from: ['', Validators.required],
    movement_destination: ['', Validators.required],
    taken_by: ['', Validators.required],
    purpose_of_visit: ['', Validators.required],
    purpose: ['other'],
    movement_start_km_reading_by_manager: ['', Validators.required],
    movement_start_km_reading_by_driver: ['', Validators.required],
    movement_start_time: ['', Validators.required],
    movement_start_time_by_driver: ['', Validators.required],
    entry_by: [this.entry_by],
    organization_id: [this.organization_id],
    manager_id: [this.entry_by],
    movement_status: [1]
  });

  // Automatically copy the values to corresponding fields
  this.movementForm.get('movement_start_km_reading_by_manager')?.valueChanges.subscribe(value => {
    this.movementForm.get('movement_start_km_reading_by_driver')?.setValue(value, { emitEvent: false });
  });

  this.movementForm.get('movement_start_time')?.valueChanges.subscribe(value => {
    this.movementForm.get('movement_start_time_by_driver')?.setValue(value, { emitEvent: false });
  });
}
// Get all driver data
allDrivers() {
  this.allService.freeAllDriver(this.organization_id).subscribe(
    (data: any) => {
      this.all_drivers = data;
    },
    (err: any) => {
      console.log(err);
    }
  );
}


//submit function
onSubmit(): void {
  if (this.movementForm.valid) {
    // Submit form data
    console.log(this.movementForm.value);
    
    // Send form data to the service
    this.allService.startMomentadd(this.movementForm.value).subscribe(
      (response: any) => {
        console.log('Form submitted successfully:', response);
        alert(response.message);
        this.allDrivers();
        this.allVeichel();
        // Reset the form after successful submission
        this.movementForm.reset();
        
        // Optionally mark the form as untouched
        this.movementForm.markAsUntouched();
        this.initialization();
        this.fetchstMovedata();
      },
      error => {
        console.error('Error submitting form:', error);
        // Handle error (e.g., show an error message)
      }
    );
  } else {
    // Mark all fields as touched to trigger validation messages
    this.movementForm.markAllAsTouched();
  }
}


//fetch start movement updated data
fetchstMovedata()
{
  this.allService.showupDatedmovement(this.entry_by).subscribe((data:any)=>{
   this.movement =data.data;
   console.log(data.data);
   
  },(err:any)=>{
    console.log(err);   
  })
}


}
