
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-add-woner',
  templateUrl: './add-woner.component.html',
  styleUrls: ['./add-woner.component.css']
})
export class AddWonerComponent implements OnInit {
  ownerForm!: FormGroup;
  organizations: any[] = [];
  owners: any[] = [];
  editMode: boolean = false;
  selectedOwnerId: any | null = null;
  entry_by: any;

  constructor(private fb: FormBuilder, private vehicleService: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.entry_by = userInfo.userId;
    this.initializeForm();
    this.getOrganizations();
    this.getOwners();
  }

  initializeForm(): void {
    this.ownerForm = this.fb.group({
      vehicle_owner_id: [null],
      vehicle_owner_name: ['', Validators.required],
      organization_id: ['', Validators.required],
      vehicle_owner_mobile_no_1: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      vehicle_owner_mobile_no_2: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]]
    });
  }

  getOrganizations(): void {
    this.vehicleService.getOrganization().subscribe((data: any) => {
      this.organizations = data;
    });
  }

  getOwners() {
    this.vehicleService.getOwnerDetails().subscribe((res: any) => {
      this.owners = res;
    });
  }

  onSubmit(): void {
    if (this.ownerForm.valid) {
      const ownerData = {
        vehicle_owner_name: this.ownerForm.get('vehicle_owner_name')?.value,
        organization_id: this.ownerForm.get('organization_id')?.value,
        vehicle_owner_mobile_no_1: this.ownerForm.get('vehicle_owner_mobile_no_1')?.value,
        vehicle_owner_mobile_no_2: this.ownerForm.get('vehicle_owner_mobile_no_2')?.value,
        entry_by: this.entry_by
      };

      if (this.editMode && this.selectedOwnerId !== null) {
        this.updateOwner(ownerData);
      } else {
        this.addOwner(ownerData);
      }
    } else {
      console.warn('Form is invalid:', this.ownerForm);
    }
  }

  addOwner(ownerData: any): void {
    this.vehicleService.addOwner(ownerData).subscribe(
      (response: any) => {
        console.log('Owner added successfully:', response);
        alert('Owner added successfully');
        this.resetForm();
        this.getOwners();
      },
      (error: any) => {
        console.error('Error adding owner:', error);
        alert('Failed to add owner. Please try again.');
      }
    );
    
  }

  updateOwner(ownerData: any): void {
    this.vehicleService.updateOwner(this.selectedOwnerId, ownerData).subscribe(
      (response: any) => {
        console.log('Owner updated successfully:', response);
        alert('Owner updated successfully');
        this.resetForm();
        this.getOwners();
      },
      (error: any) => {
        console.error('Error updating owner:', error);
        alert('Failed to update owner. Please try again.');
      }
    );
  }

  editOwner(owner: any): void {
    this.editMode = true;
    this.selectedOwnerId = owner.vehicle_owner_id;
    this.ownerForm.patchValue({
      vehicle_owner_name: owner.vehicle_owner_name,
      organization_id: owner.organization.organization_id,
      vehicle_owner_mobile_no_1: owner.vehicle_owner_mobile_no_1,
      vehicle_owner_mobile_no_2: owner.vehicle_owner_mobile_no_2
    });

    // Scroll to the form section
  const formSection = document.getElementById('formSection');
  if (formSection) {
    formSection.scrollIntoView({ behavior: 'smooth' });
  }
  }

  resetForm(): void {
    this.ownerForm.reset();
    this.editMode = false;
    this.selectedOwnerId = null;
  }
}
