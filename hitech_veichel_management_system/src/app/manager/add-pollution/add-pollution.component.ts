import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-add-pollution',
  templateUrl: './add-pollution.component.html',
  styleUrls: ['./add-pollution.component.css']
})
export class AddPollutionComponent implements OnInit {
  @ViewChild('pollutionFormRef') pollutionFormRef!: ElementRef;

  vehicles: any[] = [];
  addPollution: any = {};
  pollution_file: File | null = null;
  pollution_file_name: string = '';

  p: number = 1; // Current page number
  itemsPerPage: number = 10; // Number of items per page

  message: string = '';
  selectedVehicle: any;
  data: any[] = [];
  noDataAvailable: boolean = false;
  Math: any;
  currentPage: any;
  entry_by: string | undefined;

  constructor(private vehicleService: VeichelserviceService) { }

  organization_id: any;

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;
    this.entry_by = userInfo.userId;

    this.callVehicle();
  }

  callVehicle(): void {
    this.vehicleService.fetchAllVeichel(this.organization_id).subscribe((data: any) => {
      this.vehicles = data;
    });
  }

  onFileChange(event: any): void {
    this.pollution_file = event.target.files[0];
    if (this.pollution_file) {
      this.pollution_file_name = this.pollution_file.name;
    }
  }
  getPoll(selectedVehicleId: any) {

    if (selectedVehicleId) {
      this.vehicleService.getPollutionReport(selectedVehicleId).subscribe((res: any) => {
        this.data = res;
        this.message = ''
        this.noDataAvailable = this.data.length === 0;
      }, error => {
        this.noDataAvailable = true;
        this.data = []; // Clear data on error
        this.message = 'No data avialable!!'
      });
    } else {
      this.noDataAvailable = true;
      this.data = [];
    }
  }

  editPollution(pollution: any): void {
    this.addPollution = {
      vehicle_id: pollution.vehicle.vehicle_id,
      puc_no: pollution.vehicle_pollution_puc_no,
      puc_start_date: pollution.vehicle_pollution_start_date,
      puc_end_date: pollution.vehicle_pollution_end_date,
      pollution_file_name: pollution.pollution_file_name,
      vehicle_pollution_id: pollution.vehicle_pollution_id
    };

    // Focus the form when editing
    setTimeout(() => {
      const formElement = this.pollutionFormRef.nativeElement as HTMLFormElement;
      formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      formElement.focus();
    }, 0);
  }

  onSubmit(form: any): void {
    if (form.valid && this.pollution_file) {
      const formData = new FormData();
      formData.append('vehicle_id', this.addPollution.vehicle_id || '');
      formData.append('vehicle_pollution_puc_no', this.addPollution.puc_no || '');
      formData.append('vehicle_pollution_start_date', this.addPollution.puc_start_date || '');
      formData.append('vehicle_pollution_end_date', this.addPollution.puc_end_date || '');
      formData.append('entry_by', this.entry_by || '');
      if (this.pollution_file) {
        formData.append('vehicle_pollution_puc_file', this.pollution_file);
      }
  
      if (this.addPollution.vehicle_pollution_id) {
        // Update specific record
        this.vehicleService.updatePollution(this.addPollution.vehicle_pollution_id, formData).subscribe((response: any) => {
            alert('Vehicle pollution data updated successfully');
            form.reset();
            this.addPollution = {}; // Clear form data
          },
          (error: any) => {
            alert('Error updating vehicle pollution data');
            console.log(error);
          }
        );
      } else {
        // Add new record
        this.vehicleService.addPollution(formData).subscribe(
          (response: any) => {
            alert('Vehicle pollution data added successfully');
            form.reset();
            this.addPollution = {}; // Clear form data
          },
          (error: any) => {
            alert('Error adding vehicle pollution data');
            console.log(error);
          }
        );
      }
    }
    
  }
}
