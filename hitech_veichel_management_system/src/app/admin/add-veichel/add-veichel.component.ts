
import { Component, OnInit } from '@angular/core';
import { error } from 'jquery';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-add-veichel',
  templateUrl: './add-veichel.component.html',
  styleUrls: ['./add-veichel.component.css']
})
export class AddVeichelComponent implements OnInit {
  organization_id:any
  fileurl = 'http://yourapiurlputhere:5002/storage';
  entry_by:any
  vehicleOwners: any[] = [];
  vehicleCategories: any[] = [];
  vehicle: any = {};
  rcFile: File | null = null;
  errorMessages: { [key: string]: string } = {};
  vehicle_fetch_data:any[]=[];

  constructor(private vehicleService: VeichelserviceService) { }

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId; 
    this.entry_by = userInfo.userId; 

    this.callVehicleCat(); 
    this.callVehicleOwner();
    this.fetchVehiclUpdatedata();
  }

  callVehicleCat(): void {
    this.vehicleService.getVehicleCategories().subscribe((data: any) => {
      this.vehicleCategories = data;
    });
  }

  callVehicleOwner(): void {
    this.vehicleService.getOwners().subscribe((data: any) => {
      this.vehicleOwners = data;
    });
  }

  onFileChange(event: any): void {
    this.rcFile = event.target.files[0];
  }

  validateForm(): boolean {
    this.errorMessages = {};

    if (!this.vehicle.vehicle_category_id) this.errorMessages['vehicle_category_id'] = 'Category is required.';
    if (!this.vehicle.vehicle_owner_id) this.errorMessages['vehicle_owner_id'] = 'Owner is required.';
    if (!this.vehicle.vehicle_name) this.errorMessages['vehicle_name'] = 'Vehicle Name is required.';
    if (!this.vehicle.vehicle_model) this.errorMessages['vehicle_model'] = 'Vehicle Model is required.';
    // if (!this.vehicle.rc_no) this.errorMessages['rc_no'] = 'RC No. is required.';
    // if (!this.rcFile) this.errorMessages['rc_file'] = 'RC File is required.';
    if (!this.vehicle.purchase_date) this.errorMessages['purchase_date'] = 'Purchase Date is required.';
    if (!this.vehicle.rto_no) this.errorMessages['rto_no'] = 'RTO No. is required.';
    // if (!this.vehicle.chesis_no) this.errorMessages['chesis_no'] = 'Chesis No. is required.';
    // if (!this.vehicle.engine_no) this.errorMessages['engine_no'] = 'Engine No. is required.';
    if (!this.vehicle.fuel_type) this.errorMessages['fuel_type'] = 'Fuel Type is required.';

    return Object.keys(this.errorMessages).length === 0;
  }

  onSubmit(formvalid:any): void {
    if (!this.validateForm()) {
      return;
    }

    const formData = new FormData();
    formData.append('vehicle_category_id', this.vehicle.vehicle_category_id || '');
    formData.append('vehicle_owner_id', this.vehicle.vehicle_owner_id || '');
    formData.append('vehicle_name', this.vehicle.vehicle_name || '');
    formData.append('vehicle_model', this.vehicle.vehicle_model || '');
    formData.append('vehicle_rc_no', this.vehicle.rc_no || '');

    if (this.rcFile) {
      formData.append('vehicle_rc_file', this.rcFile, this.rcFile.name);
    }
    
    formData.append('vehicle_purchase_date', this.vehicle.purchase_date || '');
    formData.append('vehicle_fastag_no', this.vehicle.fasttag_no || '');
    formData.append('vehicle_rto_no', this.vehicle.rto_no || '');
    formData.append('vehicle_chassis_no', this.vehicle.chesis_no || '');
    formData.append('vehicle_engine_no', this.vehicle.engine_no || '');
    formData.append('vehicle_fuel_type', this.vehicle.fuel_type || '');
    formData.append('entry_by', this.entry_by); 
    formData.append('organization_id', this.organization_id);

 

    this.vehicleService.addVehicle(formData).subscribe(
      (response: any) => {
        console.log(response.message);
        
        alert(response.message);
        formvalid.reset(); 
        this.fetchVehiclUpdatedata()
      },
      (error: any) => {
        alert(error.error.errors[0]);
      }
    );
  }
  

  //fetchvehicle updated data
  fetchVehiclUpdatedata() {
    this.vehicleService.getUpdateVehicledata(this.entry_by).subscribe(
      (data: any) => {
        if (data) {
          this.vehicle_fetch_data = data.slice(-4).reverse(); // Get the last 4 items and reverse the order
          console.log(this.vehicle_fetch_data);
        } else {
          console.log("No data available");
        }
      },
      (err: any) => {
        console.log(err);
      }
    );
  }
  
  

}
