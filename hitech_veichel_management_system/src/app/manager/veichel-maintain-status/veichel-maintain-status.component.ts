
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-veichel-maintain-status',
  templateUrl: './veichel-maintain-status.component.html',
  styleUrls: ['./veichel-maintain-status.component.css']
})
export class VeichelMaintainStatusComponent implements OnInit {

  vehicles: any[] = [];
  selectedVehicle: any = {};
  organization_id: number | undefined;
  entry_by: number | undefined;
  vehicle_maintenance_id: number | undefined;
  maintenanceForm: FormGroup;

  constructor(private vehicleService: VeichelserviceService, private fb: FormBuilder) {
    this.maintenanceForm = this.fb.group({
      maintenanceEndDate: ['', Validators.required],
      maintenanceEndTime: ['', Validators.required],
      endKmReading: ['', [Validators.required, Validators.pattern('^[0-9]+$')]],
      amount: ['', [Validators.required, Validators.pattern('^[0-9]*\\.?[0-9]+$')]],
      fuelEndReading: ['', [Validators.required, Validators.pattern('^[0-9]*\\.?[0-9]+$')]],
      receiptFile: [null]
    });
  }

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId; 
    this.entry_by = userInfo.userId; 
    
    this.onStatusChange('Approved'); 
  }

  onStatusChange(selectedStatus: any) {
    console.log(selectedStatus,this.entry_by);
    
    this.vehicles = [];
    this.vehicleService.veicleDatamaintain(selectedStatus,this.entry_by).subscribe(
        (response: any) => {
          console.log(response.data);
          
            if (response.data.length > 0) {
                this.vehicles = response.data;
                console.log(this.vehicles);
            } else {
                this.vehicles = []; // No data found
            }
            // console.log(selectedStatus);
        },
        error => {
            console.error('Error fetching vehicles:', error.error.message);
            this.vehicles = []; // Clear data on error as well
        }
    );
  }

  openEditModal(vehicle_maintenance_id: any) {
    this.vehicle_maintenance_id = vehicle_maintenance_id;
    this.maintenanceForm.patchValue({
      maintenanceEndDate: vehicle_maintenance_id.maintenance_end_date,
      maintenanceEndTime: vehicle_maintenance_id.maintenance_end_time,
      endKmReading: vehicle_maintenance_id.maintenance_end_km_reading_by_manager,
      amount: vehicle_maintenance_id.maintenance_amount,
      fuelEndReading: vehicle_maintenance_id.maintenance_end_fuel_level
    });
  }

  onSave() {
    if (this.maintenanceForm.invalid) {
      console.error('Form is invalid.');
      return;
    }

    if (this.vehicle_maintenance_id === undefined) {
        console.error('Vehicle maintenance ID is not defined.');
        return;
    }

    const formData = new FormData();
    formData.append('maintenance_end_date', this.maintenanceForm.get('maintenanceEndDate')?.value);
    formData.append('maintenance_end_time', this.maintenanceForm.get('maintenanceEndTime')?.value);
    formData.append('maintenance_end_km_reading_by_manager', this.maintenanceForm.get('endKmReading')?.value);
    formData.append('maintenance_amount', this.maintenanceForm.get('amount')?.value);
    formData.append('maintenance_end_fuel_level', this.maintenanceForm.get('fuelEndReading')?.value);
    formData.append('maintenance_status', 'Inactive');
    formData.append('maintenance_approve_status', 'Approved');
    formData.append('vehicle_maintenance_id', this.vehicle_maintenance_id.toString());

    // Append the file if it's selected
    const file = this.maintenanceForm.get('receiptFile')?.value;
    if (file) {
      formData.append('maintenance_service_center_recept_file', file, file.name);
    }

    this.vehicleService.updateVehicleMaintenance(this.vehicle_maintenance_id, formData).subscribe(
        (response) => {
            console.log('Update successful', response);
            alert('Maintenance details updated successfully!');
            this.onStatusChange('Approved'); // Refresh the list
        },
        (error) => {
            console.error('Error updating maintenance details', error);
        }
    );
  }

  onFileChange(event: any) {
    const file = event.target.files[0];
    this.maintenanceForm.patchValue({ receiptFile: file });
  }
}
