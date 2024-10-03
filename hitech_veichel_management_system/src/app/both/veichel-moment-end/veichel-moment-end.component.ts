import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-veichel-moment-end',
  templateUrl: './veichel-moment-end.component.html',
  styleUrls: ['./veichel-moment-end.component.css']
})
export class VeichelMomentEndComponent {
  kmReadingForm!: FormGroup;
  endKmreadingForm!: FormGroup;
   start_km_reading:any
   end_km_reading:any
  vehicle_movement_id: any;
  organization_id: any;
  entry_by: any;
  user_role_name: any;
  selected_date: any;
  veicle_moment_fetch_data: any;
  message:any = ""

  constructor(private allService: VeichelserviceService, private fb: FormBuilder) {}

  ngOnInit() {
    // Session area
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 
    const roleName = localStorage.getItem('roleName');
    this.user_role_name = roleName;
    // Session area end
  
    this.onDefaultChange();
    setInterval(() => {
      this.onDefaultChange();
    }, 500000);

    this.startKminiTializer();
    this.endKmInitializer();
  }

  // Start KM reading initialization
  startKminiTializer() {
    this.kmReadingForm = this.fb.group({
      movement_start_km_reading_by_driver: ['', Validators.required],
      movement_start_time_by_driver: ['', Validators.required],
      vehicle_movement_id: [this.vehicle_movement_id],
    });
  }

  // End KM reading initialization
  endKmInitializer() {
    this.endKmreadingForm = this.fb.group({
      movement_end_km_reading_by_driver: ['', Validators.required],
      movement_end_time_by_driver: ['', Validators.required],
    });
  }

  // Start date
  onDateChange(dateValue: string): void {
    this.selected_date = dateValue;
    console.log(this.selected_date);
    this.allService.veicledataEndDriver(this.entry_by, this.selected_date).subscribe(
      (data: any) => {
        // Fetch data and filter out rows where manager.user_id matches entry_by
        this.veicle_moment_fetch_data = data.movements.filter((movement: any) => movement.manager.user_id !== this.entry_by);
        console.log(this.veicle_moment_fetch_data);
        
        if (this.veicle_moment_fetch_data.length > 0) {
          this.message = "";
        } else {
          this.message = "No data found!!";
        }
      },
      (err: any) => {
        alert("Failed To show data !!");
        this.message = "No data found!!";
      }
    );
  }
  
  onDefaultChange(): void {
    this.allService.veicledataEndDriverNodate(this.entry_by).subscribe(
      (data: any) => {
        // Fetch data and filter out rows where manager.user_id matches entry_by
        this.veicle_moment_fetch_data = data.movements.filter((movement: any) => movement.manager.user_id !== this.entry_by);
        console.log(this.veicle_moment_fetch_data);
  
        if (this.veicle_moment_fetch_data.length > 0) {
          this.message = "";
        } else {
          this.message = "No data found!!";
        }
      },
      (err: any) => {
        alert("Failed To show data !!");
      }
    );
  }

  // Get single moment data
  getSinglemomentData(data: any) {
    this.vehicle_movement_id = data.vehicle_movement_id;
    this.startKminiTializer(); // Initialization of start KM reading
    this.endKmInitializer(); // Initialization of end KM reading
    console.log(this.vehicle_movement_id, data.movement_start_km_reading_by_driver, data.movement_end_km_reading_by_driver);
    this.start_km_reading =  data.movement_start_km_reading_by_driver
  }

  // Update start KM reading
  stKmreadingUpdata() {
    if (this.kmReadingForm.valid) {
      console.log('Form Submitted', this.kmReadingForm.value);
      this.allService.driverStKm(this.kmReadingForm.value).subscribe(
        (data: any) => {
          alert(data.message);
          if(this.selected_date != null){
            this.onDateChange(this.selected_date);
          }else{
            this.onDefaultChange();
          }
         
        },
        (err: any) => {
          alert("Failed to update");
          this.onDateChange(this.selected_date);
        }
      );
    } else {
      console.log('Form is invalid');
    }
  }

  //get end km reading
  getEndkm(ed_km:any){
   this.end_km_reading = ed_km;
   console.log(this.end_km_reading);
   
  }

  // Update end KM reading and time
  endUpdate() {
    if(this.start_km_reading < this.end_km_reading){
      if (this.endKmreadingForm.valid) {
        const updateData = {
          ...this.endKmreadingForm.value,
          vehicle_movement_id: this.vehicle_movement_id
        };
        console.log('Form Submitted', updateData);
        this.allService.driverEndKm(updateData).subscribe(
          (data: any) => {
            alert(data.message);
            if(this.selected_date != null){
              this.onDateChange(this.selected_date);
            }else{
              this.onDefaultChange();
            }
           
          },
          (err: any) => {
            alert("Failed to update");
          }
        );
      } else {
        console.log('Form is invalid');
      }
    }else{
      alert("Start km reading cant be more than end km reading!!")
    }
  }


}
