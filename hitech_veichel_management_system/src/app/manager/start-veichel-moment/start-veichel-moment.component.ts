import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-start-veichel-moment',
  templateUrl: './start-veichel-moment.component.html',
  styleUrls: ['./start-veichel-moment.component.css']
})
export class StartVeichelMomentComponent {
  movement: any[] = [];
  organization_id: any;
  entry_by: any;
  all_veichels: any[]=[];
  all_drivers: any[] = [];
  movementForm!: FormGroup;
  purpose_of_visit: any;

  constructor(private allService: VeichelserviceService, private fb: FormBuilder,private router: Router) { }

  ngOnInit() {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId;
    this.entry_by = userInfo?.userId;

    this.allDrivers();
    this.allVeichel();
    this.initialization();
    this.fetchstMovedata();
    setInterval(() => {
      this.allDrivers();
      this.allVeichel();
    }, 300000);
  }

  initialization() {
    // Initialize the form without applying any validators initially to the `purpose_of_visit` field
    const today = new Date().toISOString().split('T')[0]; // Get today's date in 'yyyy-MM-dd' format
    this.movementForm = this.fb.group({
      vehicle_id: [null, Validators.required],
      driver_id: [null, Validators.required],
      movement_start_date: [today, Validators.required],
      movement_start_from: ['', Validators.required],
      movement_destination: ['', Validators.required],
      taken_by: ['', Validators.required],
      purpose: ['', Validators.required],
      purpose_of_visit: ['', null], // No validator initially
      movement_start_km_reading_by_manager: ['', Validators.required],
      movement_start_time: ['', Validators.required],
      entry_by: [this.entry_by],
      organization_id: [this.organization_id],
      manager_id: [this.entry_by],
      movement_status: [1]
    });
  }

  purposeVisit(pur: any) {
    this.purpose_of_visit = pur;
    
    // Dynamically apply or clear Validators based on the selected purpose
    if (this.purpose_of_visit === 'other') {
      this.movementForm.get('purpose_of_visit')?.setValidators(Validators.required);
    } else {
      this.movementForm.get('purpose_of_visit')?.clearValidators();
    }

    // Update the validity status after changing validators
    this.movementForm.get('purpose_of_visit')?.updateValueAndValidity();
  }

  // Get all vehicle data
  allVeichel() {
    this.allService.freeAllVeichel(this.organization_id).subscribe(
      (data: any) => {
        this.all_veichels = data;
      },
      (err: any) => {
        console.log(err);
      }
    );
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

  // Submit function
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
          this.initialization()
          // Optionally, reapply conditional validation after resetting
          this.movementForm.get('purpose_of_visit')?.clearValidators();
          if (this.purpose_of_visit === 'other') {
            this.movementForm.get('purpose_of_visit')?.setValidators(Validators.required);
          }
          this.movementForm.get('purpose_of_visit')?.updateValueAndValidity();

          // Optionally mark the form as untouched
          this.movementForm.markAsUntouched();
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

  // Fetch start movement updated data
  fetchstMovedata() {
    this.allService.showupDatedmovement(this.entry_by).subscribe((data: any) => {
      this.movement = data.data;
      console.log(data.data);

    }, (err: any) => {
      console.log(err);
    });
  }

  //redirect to fuel
  navigateToFuelFilling(move: any): void {
    this.router.navigate(['/dashboard/manager/fuel-filling'], {
      queryParams: {
        vehicle_movement_id:move?.vehicle_movement_id,
        vehicle_id: move?.vehicle?.vehicle_id,
        driver_id: move?.driver?.user_id,
        movement_start_date: move?.movement_start_date,
      },
    });
  }
}
