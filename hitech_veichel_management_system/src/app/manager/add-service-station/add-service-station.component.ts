
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-add-service-station',
  templateUrl: './add-service-station.component.html',
  styleUrls: ['./add-service-station.component.css']
})
export class AddServiceStationComponent implements OnInit {
  addStation: any = {};
  services: any[] = [];
  stationsForm!: FormGroup;
  isModalOpen = false;
  selectedStationId: any | null = null;
  entry_by: any;
  submissionError: string | null = null;

  constructor(private fb: FormBuilder, private vehicleService: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.entry_by = userInfo.userId;

    this.stationsForm = this.fb.group({
      garage_name: ['', Validators.required],
      garage_owner: ['', Validators.required],
      location: ['', Validators.required],
      contact_person: ['', Validators.required],
      contact_no: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],  // Validation for exactly 10 digits
    });

    this.getServiceStation();
  }

  onSubmit(form: any): void {
    if (form.valid) {
      const formData = new FormData();
      formData.append('garage_name', this.addStation.garage_name || '');
      formData.append('garage_owner', this.addStation.garage_owner || '');
      formData.append('location', this.addStation.location || '');
      formData.append('contact_person', this.addStation.contact_person || '');
      formData.append('contact_no', this.addStation.contact_no || '');
      formData.append('entry_by', this.entry_by);

      this.vehicleService.addServiceStation(formData).subscribe(
        (response: any) => {
          alert('Service Station added successfully');
          form.reset();
          this.getServiceStation();
          this.submissionError = null;  // Clear error message after successful submission
        },
        (error: any) => {
          this.submissionError = 'Failed to add Service Station. Please try again.';  // Set the error message
          console.error(error);
        }
      );
    } else {
      this.markFormGroupTouched(form);  // Mark fields as touched to show validation errors
    }
  }

  getServiceStation() {
    this.vehicleService.getServiceStation().subscribe((res: any) => {
      this.services = res;
    });
  }

  openModal(service: any) {
    this.selectedStationId = service.garage_id;
    this.stationsForm.patchValue(service);
    this.isModalOpen = true;
  }

  closeModal() {
    this.isModalOpen = false;
    this.stationsForm.reset();
    this.selectedStationId = null;
  }

  handleUpdate() {
    if (this.stationsForm.valid) {
      const updatedStation = {
        ...this.stationsForm.value
      };

      this.vehicleService.updateServiceStation(this.selectedStationId, updatedStation).subscribe(
        (response: any) => {
          alert('Service Station updated successfully');
          this.getServiceStation();  // Refresh the list of services
          this.closeModal();
        },
        (error: any) => {
          alert('Failed to update Service Station');
          console.error(error);
        }
      );
    }
  }

  // Mark all fields as touched to show validation errors
  markFormGroupTouched(formGroup: any) {
    Object.keys(formGroup.controls).forEach((key) => {
      const control = formGroup.controls[key];
      control.markAsTouched();
    });
  }
}
