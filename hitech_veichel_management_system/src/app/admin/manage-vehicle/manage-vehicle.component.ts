import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-manage-vehicle',
  templateUrl: './manage-vehicle.component.html',
  styleUrls: ['./manage-vehicle.component.css']
})
export class ManageVehicleComponent implements OnInit {
  cat: any;
  vehicle: any;
  message: string | null = null;
  fileurl = 'http://yourapiurlputhere:5002/storage';
  organization_id: any;
  entry_by: any;
  editVehicleForm!: FormGroup;
  selectedVehicle: any;

  constructor(private service: VeichelserviceService, private fb: FormBuilder) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId;
    this.entry_by = userInfo?.userId;

    this.selectategories();
    this.getSomeVehicleDAta();
    
    this.editVehicleForm = this.fb.group({
      vehicle_name: ['', Validators.required],
      vehicle_rto_no: ['', Validators.required],
      vehicle_model: ['', Validators.required],
      vehicle_fuel_type: ['', Validators.required],
      vehicle_purchase_date: ['', Validators.required],
      vehicle_rc_no: [null],
      vehicle_fastag_no: [null],
      // vehicle_fitness_end: ['', Validators.required],
      vehicle_chassis_no: [null],
      vehicle_engine_no: [null],
      vehicle_rc_file: [null]
    });
  }

  getSomeVehicleDAta() {
    this.service.getAllvehicleActiveInactive(this.organization_id).subscribe((res: any) => {
      if (Array.isArray(res)) {
        this.vehicle = res.slice(-5);
        this.category = null;
      } else {
        console.error('Unexpected response format', res);
        this.vehicle = [];
      }
    }, (error) => {
      console.error('Error fetching data', error);
      alert('Error fetching data');
      this.vehicle = [];
    });
  }

  selectategories() {
    this.service.fetchVehicleCategories().subscribe((res: any) => {
      this.cat = res;
    }, (error) => {
      console.error('Error fetching categories', error);
      alert('Error fetching categories');
    });
  }

  category:any
  getVehicle(catId: any) {
    this.service.getVehicleCategoryIdOrg(catId, this.organization_id).subscribe((res: any) => {
      this.vehicle = res.data;
      this.message = this.vehicle.length === 0 ? "No data Available!!" : null;
      this.category = catId;
    }, (error) => {
      console.error('Error fetching vehicles by category', error);
      alert('Error fetching vehicles for the selected category');
    });
  }

  printDiv() {
    window.print();
  }

  vehicle_id:any
  editUser(vehicle: any) {
    this.vehicle_id = vehicle.vehicle_id;
    this.selectedVehicle = vehicle;
    this.editVehicleForm.patchValue({
      vehicle_name: vehicle.vehicle_name || '',
      vehicle_rto_no: vehicle.vehicle_rto_no || '',
      vehicle_model: vehicle.vehicle_model || '',
      vehicle_fuel_type: vehicle.vehicle_fuel_type || '',
      vehicle_purchase_date: vehicle.vehicle_purchase_date || '',
      vehicle_rc_no: vehicle.vehicle_rc_no || '',
      vehicle_fastag_no: vehicle.vehicle_fastag_no || '',
      vehicle_fitness_end: vehicle.vehicle_fitness_end || '',
      vehicle_chassis_no: vehicle.vehicle_chassis_no || '',
      vehicle_engine_no: vehicle.vehicle_engine_no || '',
    });
  }

  onSubmit() {
    if (this.editVehicleForm.invalid) {
      alert('Please fill all required fields');
      return;
    }
  
    const formData = new FormData();
    const formValues = this.editVehicleForm.value;
  
    // Append each form value to FormData, excluding `vehicle_rc_file` if it is null or not selected
    Object.keys(formValues).forEach(key => {
      if (key === 'vehicle_rc_file' && formValues[key]) {
        formData.append(key, formValues[key]);
      } else if (key !== 'vehicle_rc_file') {
        formData.append(key, formValues[key]);
      }
    });
  
    this.service.updateVehicle(formData,this.vehicle_id ).subscribe((response:any) => {
      alert(response.message);
      // this.resetForm();
      if(this.category != null ){
        this.getVehicle(this.category);  
       }else{
        this.getSomeVehicleDAta();
       }
      this.closeModal();
    },( error:any )=> {
      console.error('Error updating vehicle', error);
      alert('Failed to update vehicle');
    });
  }
  

  onFileChange(event: any) {
    const file = event.target.files[0];
    if (file) {
      this.editVehicleForm.patchValue({
        vehicle_rc_file: file
      });
    }
  }

  resetForm() {
    this.editVehicleForm.reset();
  }

  closeModal() {
    const modalElement = document.getElementById('exampleModal');
    if (modalElement) {
      const bootstrapModal = (window as any).bootstrap.Modal.getInstance(modalElement) || new (window as any).bootstrap.Modal(modalElement);
      bootstrapModal.hide();
    }
  }

  //=====active inactive ======
  toggleActiveStatus(vehicle: any) {
    const newStatus = vehicle.is_active === 1 ? 0 : 1; // Toggle the status    
    // Create FormData and append the new status
    const formData = new FormData();
    formData.append('is_active', newStatus.toString());
  
    // Call the service with formData and vehicle_id
    this.service.updateVehicle(formData, vehicle.vehicle_id).subscribe((response: any) => {
      console.log('Status updated successfully:', response);
      alert(response.message)
      if(this.category != null ){
        this.getVehicle(this.category);  
       }else{
        this.getSomeVehicleDAta();
       }
    }, (error: any) => {
      console.error('Error updating status:', error);
    });
  }
  
  
}
