import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-maintain-veichel-status',
  templateUrl: './maintain-veichel-status.component.html',
  styleUrls: ['./maintain-veichel-status.component.css']
})
export class MaintainVeichelStatusComponent {
  maintenanceForm!: FormGroup;
  organization_id:any
  entry_by:any
  veicle_status:any
  veicle_maintain_data:any 
  vehicle_maintenance_id:any
  disable_btn:boolean = true;
  itemsPerPage = 4;
  currentPage = 1;
  constructor(private allService:VeichelserviceService,private fb: FormBuilder){}
  
   
  ngOnInit()
  {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 

    this.initializeForm();
  }

   //form initialization
   initializeForm(){
    this.maintenanceForm = this.fb.group({
      maintenance_start_time: ['', Validators.required],
      maintenance_start_fuel_level: ['', Validators.required],
      maintenance_start_km_reading_by_manager: ['', Validators.required],
      vehicle_maintenance_id:[this.vehicle_maintenance_id]
    });
   }

  //get veicle data
  getMaintainVeicleData(status: any) {
    if (status != null) {
      this.veicle_status = status;
      this.allService.veicleDatamaintainace(status, this.entry_by).subscribe(
        (data: any) => {
          this.veicle_maintain_data = data;
          console.log(this.veicle_maintain_data);
        },
        (err: any) => {
          console.log(err);
          this.veicle_maintain_data = [];
        }
      );
    }
  
    this.disable_btn = status === 'Approved' ? false : true;
  }
  
  get paginatedData() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.veicle_maintain_data.slice(start, start + this.itemsPerPage);
  }
  
  nextPage() {
    if (this.currentPage * this.itemsPerPage < this.veicle_maintain_data.length) {
      this.currentPage++;
    }
  }
  
  prevPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
    }
  }
  
  goToPage(pageNumber: number) {
    this.currentPage = pageNumber;
  }



//get maintainace id
getVeicleMaintainId(vehicle_maintenance_id:any){
this.vehicle_maintenance_id = vehicle_maintenance_id;
this.initializeForm();
}


//submit form
onSubmit(): void {
  if (this.maintenanceForm.valid) {
    console.log(this.maintenanceForm.value);
    this.allService.updateVeicleMantainManagerStart(this.maintenanceForm.value).subscribe(
      (data: any) => {
        alert(data.message);
        this.getMaintainVeicleData(this.veicle_status);
        this.maintenanceForm.reset();  // Resetting the form fields
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
