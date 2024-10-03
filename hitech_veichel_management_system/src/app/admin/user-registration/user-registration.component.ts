import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-user-registration',
  templateUrl: './user-registration.component.html',
  styleUrls: ['./user-registration.component.css']
})
export class UserRegistrationComponent implements OnInit {
  form_disable:boolean = true;
  disable_driver: boolean = true;
  role_name: string | null = null;
  organization_id: any;
  entry_by: any;
  all_organization: any;
  selected_organization: any;
  all_user_role: any;
  selected_user_role: any;
  myForm!: FormGroup;

  constructor(private allService: VeichelserviceService, private fb: FormBuilder) {}

  ngOnInit() {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 
    
    this.initializeForm();
    this.getOrganization();
    
  }

  // Initialize form
  initializeForm() {
    const normalizedRoleName = this.role_name ? this.role_name.toLowerCase() : '';
    const isDriverOrManagementDriver = normalizedRoleName === 'driver' || normalizedRoleName === 'management driver';
    const dlNoValidators = isDriverOrManagementDriver ? [Validators.required] : [];
    const dlFileValidators = isDriverOrManagementDriver ? [Validators.required] : [];

    this.myForm = this.fb.group({
      user_name: ['', [Validators.required, Validators.pattern('^[A-Za-z ]+$')]],
      user_1st_mobile_no: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      user_2nd_mobile_no: ['', Validators.pattern('^[0-9]{10}$')],
      user_wp_no: ['', Validators.pattern('^[0-9]{10}$')],
      doj: ['', Validators.required],
      dob: ['', Validators.required],
      aadhar_no: ['', [Validators.required, Validators.pattern('^[0-9]{12}$')]],
      address: ['', Validators.required],
      gender: ['', [Validators.required]],
      photo: [null, [Validators.required]], // File input should be handled separately
      dl_no: ['', dlNoValidators],
      dl_file: [null, dlFileValidators],
      entry_by: [this.entry_by],
      user_organization_id: [this.selected_organization],
      role_id: [this.selected_user_role],

       // File input should be handled separately
    });
  }

  

  // Handle file input change
onFileChange(event: any, controlName: string) {
  const file = event.target.files[0];
  if (file) {
    this.myForm.patchValue({
      [controlName]: file
    });
  }
}

  // Fetch all organizations
  getOrganization() {
    this.allService.allOrganization(this.entry_by).subscribe(
      (data: any) => {
        console.log(data);  
        this.all_organization = data;
      },
      (err: any) => {
        console.log(err);
      }
    );
  }

  // Select organization this.form_disable = false;
  selectOrganization(data: any) {
    console.log(data);
    this.selected_organization = data;
   if(this.selected_organization){
    this.getUserRole();
    this.form_disable = true;
   }else{
    this.all_user_role = null
    this.form_disable = true;
   }
    this.initializeForm(); // Reinitialize form based on selected role
  }

  // Fetch user roles
  getUserRole() {
    this.allService.allRoles().subscribe(
      (data: any) => {
        this.all_user_role = data;
        
        console.log(this.all_user_role);     
      },
      (err: any) => {
        console.log(err);
      }
    );
  }

  // Select user role and update form
  selectUserRole(event: Event) {
    
    const target = event.target as HTMLSelectElement;
    const selectedRoleString = target.value; // Get the JSON string from the value attribute
  
    if (selectedRoleString) {
      this.form_disable = false;
      this.resetFileInputs();
      try {
        const selectedRole = JSON.parse(selectedRoleString);
        this.selected_user_role = selectedRole.role_id;
        this.role_name = selectedRole.role_name;
        
        if (selectedRole.role_name.toLowerCase() === 'driver' || selectedRole.role_name.toLowerCase() === 'management driver') {
          this.disable_driver = false;
        } else {
          this.disable_driver = true;
        }
        
        
         this.initializeForm(); // Reinitialize form based on selected role
      } catch (error) {
        console.error('Error parsing selected role:', error);
      }
    } else {
      this.form_disable = true;
      console.warn('No role data found for the selected option.');
    }
  }
  
  
  
  
  

  // Handle form submission
  onSubmit() {
    if (this.myForm.valid) {
      // Prepare form data for submission
      const formData = new FormData();
      Object.keys(this.myForm.controls).forEach(key => {
        const value = this.myForm.get(key)?.value;
        if (value) {
          formData.append(key, value);
        }
      });
   
     //Call the service method to submit the form data
      this.allService.submitUser(formData).subscribe(
        (response:any) => {
          console.log(response.message);    
            alert(response.message);
            this.getOrganization();
            this.getUserRole();
            this.initializeForm();
            this.myForm.reset();
            this.resetFileInputs();
        },
        (error:any) => {
          // alert("Error Occurs/ plese check image Field..")
          // alert("Someting went Wrong!!")
          alert(error.error.errors[0]);
        }
      );

      
    } else {
      this.myForm.markAllAsTouched();
    }
  }

//clear img
resetFileInputs() {
  const photoInput = document.getElementById('photo') as HTMLInputElement;
  const dlFileInput = document.getElementById('dl_file') as HTMLInputElement;
  
  // Clear the file input fields if they exist
  if (photoInput) {
    photoInput.value = '';
  }
  if (dlFileInput) {
    dlFileInput.value = '';
  }

  // Clear the values of the corresponding form controls
  this.myForm.get('dl_no')?.setValue('');
  this.myForm.get('dl_file')?.setValue('');
}
}
