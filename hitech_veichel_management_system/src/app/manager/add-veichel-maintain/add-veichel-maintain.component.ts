import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-add-veichel-maintain',
  templateUrl: './add-veichel-maintain.component.html',
  styleUrls: ['./add-veichel-maintain.component.css']
})
export class AddVeichelMaintainComponent implements OnInit {
  serviceForm!: FormGroup;
  myForm!: FormGroup;
  manager_id: any;
  organization_id: any;
  all_veichels: any; 
  all_drivers: any; 
  all_authority: any;
  all_problems: any;
  entry_by: any;
  service_station: any;

  constructor(private fb: FormBuilder, private allServices: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 
    this.manager_id = userInfo?.userId; 

    this.initializeForms();
    this.fetchData();
    this.fetchServiceStation();
  }

  // Initialize forms
  initializeForms(): void {
    this.myForm = this.fb.group({
      vehicle_id: ['', Validators.required],
      driver_id: ['', Validators.required],
      authority_id: ['', Validators.required],
      maintenance_problems_id: ['', Validators.required],
      maintenance_problems_other_details: ['', Validators.required],
      maintenance_service_center_name: ['', Validators.required],
      maintenance_start_date: ['', Validators.required],
      exp_amt: ['', Validators.required],
      manager_id: [this.manager_id],
      maintenance_status: ['active'],
      maintenance_approve_status: ['Pending'],
      entry_by: [this.entry_by]
    });

    this.serviceForm = this.fb.group({
      garage_name: ['', Validators.required],
      location: ['', Validators.required],
      garage_owner: ['', [Validators.required, Validators.pattern('^[a-zA-Z ]*$')]],
      contact_person: ['', [Validators.required, Validators.pattern('^[a-zA-Z ]*$')]],
      contact_no: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      entry_by: [this.entry_by] // Hidden field to include in form submission
    });
  }

  // Fetch data for dropdowns and service stations
  fetchData(): void {
    this.allServices.fetchAllVeichel(this.organization_id).subscribe(
      (data: any) => this.all_veichels = data,
      (err: any) => console.log(err)
    );

    this.allServices.fetchAllDriver(this.organization_id).subscribe(
      (data: any) => this.all_drivers = data,
      (err: any) => console.log(err)
    );

    this.allServices.fetchAuthority(this.organization_id).subscribe(
      (data: any) => this.all_authority = data,
      (err: any) => console.log(err)
    );

    this.allServices.fetchProblems().subscribe(
      (data: any) => this.all_problems = data,
      (err: any) => console.log(err)
    );

   
  }

  fetchServiceStation()
  {
    this.allServices.fetchStations().subscribe(
      (data: any) => this.service_station = data,
      (err: any) => console.log(err)
    );
  }

  // Submit maintenance form
  onSubmit(): void {
    if (this.myForm.valid) {
      console.log('Form Submitted:', this.myForm.value);

      // Convert the FormGroup data to FormData
      const formData = new FormData();
      Object.keys(this.myForm.controls).forEach(key => {
        formData.append(key, this.myForm.get(key)?.value);
      });

      // Submit the form data using the service
      this.allServices.addVeicleForMaintain(formData).subscribe(
        (data: any) => {
          console.log('Form submission successful:', data);
          alert(JSON.stringify(data.message));
          // Reset the form after submission
          this.myForm.reset();
          this.initializeForms(); // Reinitialize forms
        },
        (err: any) => alert('Failed to submit form:')
      );
    } else {
      console.log('Form is invalid');
    }
  }

  // Submit service form
  onSubmitService(): void {
    if (this.serviceForm.valid) {
      console.log('Service Form Submitted:', this.serviceForm.value);
      this.allServices.addServiceStation(this.serviceForm.value).subscribe((res:any)=>{
        alert(res.message)
        this.fetchServiceStation();
        this.serviceForm.reset();
          this.initializeForms()
      },(err:any)=>{
        alert("Failed to add data")
      })
      // Add your form submission logic here
    }
  }


}
