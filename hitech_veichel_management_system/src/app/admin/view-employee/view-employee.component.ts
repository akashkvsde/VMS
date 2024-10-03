import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { userDetails } from 'src/app/models/veichel-model.model';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-view-employee',
  templateUrl: './view-employee.component.html',
  styleUrls: ['./view-employee.component.css']
})
export class ViewEmployeeComponent implements OnInit {
  all_role: any[] = [];
  disable_driver: boolean = false;
  organization_id: any;
  entry_by: any;
  all_user: any[] = [];
  mymodel: userDetails | null = null;
  apiUrl = "http://yourapiurlputhere:5002/storage";
  userForm!: FormGroup;
  photo: File | null = null;
  dl_file: File | null = null;
  selected_role:any = 0;
  message:any = "Please Select a Role!!";
  constructor(private fb: FormBuilder, private allService: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 

    this.userForm = this.fb.group({
      user_name: ['', Validators.required],
      user_id: ['', Validators.required],
      user_1st_mobile_no: ['', Validators.required],
      user_wp_no: ['', Validators.required],
      photo: [null],// Initial value is null
      dl_file: [null], // Initial value is null
      dl_no: [null]  // Initial value is null
    });

    this.showRole();
    this.viewUserdefault();
  }


  //view user default
  viewUserdefault(): void {
    this.allService.viewUserdefault(this.organization_id).subscribe(
      (data: any) => {
        // Filter users to include only those with role name 'Driver'
        this.all_user = data.filter((user: any) => user.role && user.role.role_name === 'Driver');
        if(this.all_user){
          this.message = null;
        }else{
          this.message = "Please select a role";
        }
      },
      (err: any) => {
        console.log(err);
      }
    );
  }


  showRole(): void {
    this.allService.allRoles().subscribe(
      (data: any) => {
        this.all_role = data;
      },
      (err: any) => {
        console.error('Error fetching roles:', err);
      }
    );
  }

  selectUserRole(event: Event): void {
    const target = event.target as HTMLSelectElement;
    const selectedRoleString = target.value;
   
    if (selectedRoleString) {
      try {
        const selectedRole = JSON.parse(selectedRoleString);
        this.selected_role = selectedRole.role_id;
        this.disable_driver = !['driver', 'management driver'].includes(selectedRole.role_name.toLowerCase());
        // Call API
        this.allService.viewUser(this.organization_id, selectedRole.role_id).subscribe(
          (data: any) => {
            console.log(data);
            this.all_user = data;
            this.message = null;
          },
          (err: any) => {
            console.error('Something went wrong', err);
            this.all_user = [];
            this.message = "No Data avialable!!"
          }
        );
      } catch (error) {
        console.error('Error parsing selected role:', error);
      }
    } else {
      console.warn('No role data found for the selected option.');
    }
  }

  getPhotoUrl(photoPath: string): string {
    return `${this.apiUrl}/${photoPath}`;
  }

  getSingleUserData(user: any): void {
    this.mymodel = user;
  }

  editUser(user: any): void {
    console.warn(user);
    const driverDetails = user?.driver_details || {};
    this.userForm.patchValue({
      user_name: user?.user_name,
      user_id: user?.user_id,
      user_1st_mobile_no: user?.user_1st_mobile_no,
      user_wp_no: user?.user_wp_no,
      dl_no: driverDetails.dl_no || ''  // Use an empty string if dl_no is not available
    });
    this.resetFileInputs();
    this.resetFileInputsDl();
  }


  //selected role to fetch data again
  fetchDataAgain(){
    this.allService.viewUser(this.organization_id,this.selected_role).subscribe(
      (data: any) => {
        console.log(data);
        this.all_user = data;
      })
  }

  //update function
  handleSubmit(): void {
    if (this.userForm.valid) {
      const formData = new FormData();
      formData.append('user_name', this.userForm.get('user_name')?.value);
      // formData.append('user_id', this.userForm.get('user_id')?.value.toString() || '');
      formData.append('user_1st_mobile_no', this.userForm.get('user_1st_mobile_no')?.value);
      formData.append('user_wp_no', this.userForm.get('user_wp_no')?.value);
      const dlNo = this.userForm.get('dl_no')?.value;
      if (dlNo !== null && dlNo !== '') {
        formData.append('dl_no', dlNo);
      }

      if (this.photo) {
        formData.append('photo', this.photo);
      }
      if (this.dl_file) {
        formData.append('dl_file', this.dl_file);
      }
      this.allService.updateUser(this.userForm.get('user_id')?.value.toString(),formData).subscribe((response:any) => {
        console.log(this.selected_role);
        alert(response.message)
        if(this.selected_role != 0){
          this.fetchDataAgain();
        }else{
          this. viewUserdefault();
        }
       
      this.resetFileInputs();
      this.resetFileInputsDl();
      }, error => {
        console.error('Error updating user', error);
        alert("someting went wrong")
      });
    }
  }


  //img
  onFileChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.photo = input.files[0];
      this.userForm.patchValue({ photo: this.photo });
    }
  }

   //dl file
   onFileChangedl(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.dl_file = input.files[0];
      this.userForm.patchValue({ dl_file: this.dl_file });
    }
  }

  //reset img
  resetFileInputs(): void {
    // Get the file input element
    const photoInput = document.getElementById('photo') as HTMLInputElement;
  
    // Clear the file input value
    if (photoInput) {
      photoInput.value = '';
    }
  
    // Reset the FormControl value
    this.userForm.get('photo')?.setValue(null);
  
    // Clear the image preview if it exists
    const imgElement = document.querySelector('#photoPreview') as HTMLImageElement;
    if (imgElement) {
      imgElement.src = ''; // Clear the image preview
    }
  
    // Optionally reset the photo property if you use it to manage the selected file
    this.photo = null;
  }

  //reset dl
  resetFileInputsDl(): void {
    // Get the file input element
    const photoInput = document.getElementById('dl_file') as HTMLInputElement;
  
    // Clear the file input value
    if (photoInput) {
      photoInput.value = '';
    }
  
    // Reset the FormControl value
    this.userForm.get('dl_file')?.setValue(null);
  
    // Clear the image preview if it exists
    const imgElement = document.querySelector('#photoPreview') as HTMLImageElement;
    if (imgElement) {
      imgElement.src = ''; // Clear the image preview
    }
  
    // Optionally reset the photo property if you use it to manage the selected file
    this.dl_file = null;
  }


  //for print
  printDiv(divId: string): void {
    const printContents = document.getElementById(divId)?.innerHTML;
    if (printContents) {
      const printWindow = window.open('', '', 'height=600,width=800');
      if (printWindow) {
        printWindow.document.write('<html><head><title>User Report Hi_Tech Veichel Management</title>');
        printWindow.document.write('<style>@media print { .no-print { display: none; } }</style>');
        printWindow.document.write('</head><body >');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.addEventListener('afterprint', () => {
          printWindow.close();
        });
      }
    }
  }

  //===============toggle============
  toggleUserStatus(user: any) {
    // Toggle status: if true (1), set to false (0); if false (0), set to true (1)
    const newStatus = user.status === 1 ? 0 : 1;
    const formData = new FormData();
    formData.append('status', newStatus.toString()); // Send '1' or '0' directly
    
    // Ensure user_id is included in the FormData
    formData.append('user_id', user.user_id.toString());
  
    // Call the updateUser service method
    this.allService.updateUser(user.user_id.toString(), formData).subscribe(
      (response: any) => {
        console.log('User status updated successfully:', response);
        user.status = newStatus; // Update UI after a successful response
        alert(response.message)
      },
      (error: any) => {
        console.error('Error updating user status:', error);
      }
    );
  }
  
  
  
}
