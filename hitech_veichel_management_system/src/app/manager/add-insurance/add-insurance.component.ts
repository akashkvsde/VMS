
import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { NgForm } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-add-insurance',
  templateUrl: './add-insurance.component.html',
  styleUrls: ['./add-insurance.component.css']
})
export class AddInsuranceComponent implements OnInit {
  @ViewChild('insuranceFormRef') insuranceFormRef!: ElementRef;
  vehicles: any[] = [];
  addInsurance: any = {};
  insurance_file: File | null = null;
  insurance_file_name: string = '';
  data: any[] = [];
  selectedVehicle: any;
  isEditing: boolean = false;
  organization_id: any;
  entry_by: string | undefined;
  noDataMessage: string = 'No insurance data available for the selected vehicle.';

  constructor(private vehicleService: VeichelserviceService) { }

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
      console.log(this.vehicles);
    });
  }

  onFileChange(event: any): void {
    this.insurance_file = event.target.files[0];
    if (this.insurance_file) {
      this.insurance_file_name = this.insurance_file.name;
    }
  }
  any_veicle_id:any

  onSubmit(form: NgForm): void {
    if (form.valid && this.insurance_file) {
      const formData = new FormData();
      formData.append('vehicle_id', this.addInsurance.vehicle_id || '');
      formData.append('insurance_company_name', this.addInsurance.insurance_company_name || '');
      formData.append('vehicle_insurance_agent_name', this.addInsurance.agent_name || '');
      formData.append('vehicle_insurance_agent_mobile_no', this.addInsurance.agent_mobile || '');
      formData.append('vehicle_insurance_no', this.addInsurance.insurance_no || '');
      formData.append('vehicle_insurance_start_date', this.addInsurance.insurance_start_date || '');
      formData.append('vehicle_insurance_end_date', this.addInsurance.insurance_end_date || '');
      formData.append('entry_by', this.entry_by || '');
      formData.append('vehicle_insurance_file', this.insurance_file);

      if (this.isEditing) {
        this.vehicleService.updateInsurance(this.addInsurance.insurance_id, formData).subscribe(
          (response: any) => {
            alert('Vehicle insurance data updated successfully');
            form.reset();
            this.isEditing = false;
          },
          (error: any) => {
            alert('Error updating vehicle insurance data');
          }
        );
      } else {
        this.vehicleService.addInsurance(formData).subscribe(
          (response: any) => {
            alert('Vehicle insurance data added successfully');
            form.reset();
          },
          (error: any) => {
            alert('Error adding vehicle insurance data');
          }
        );
      }
    }
    this.getInsurance(this.any_veicle_id);
  }
  message:any;
  
  getInsurance(vehicleId: any): void {
    this.any_veicle_id = vehicleId;
    this.vehicleService.getInsuranceReport(vehicleId).subscribe((data: any) => {
      this.data = data;
      this.message = data.message;
      console.log(this.data);
    });
  }

  editInsurance(insurance: any): void {
    this.addInsurance = {
      vehicle_id: insurance.vehicle.vehicle_id,
      insurance_company_name: insurance.insurance_company_name,
      agent_name: insurance.vehicle_insurance_agent_name,
      agent_mobile: insurance.vehicle_insurance_agent_mobile_no,
      insurance_no: insurance.vehicle_insurance_no,
      insurance_start_date: insurance.vehicle_insurance_start_date,
      insurance_end_date: insurance.vehicle_insurance_end_date,
      insurance_id: insurance.vehicle_insurance_id,
      
    };
    this.isEditing = true;

    // Focus on the form when editing
    setTimeout(() => {
      const formElement = this.insuranceFormRef.nativeElement as HTMLFormElement;
      formElement.scrollIntoView({ behavior: 'smooth' });
      formElement.focus();
    }, 0);
  }
}