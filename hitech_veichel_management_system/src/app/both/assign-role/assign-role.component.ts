import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
@Component({
  selector: 'app-assign-role',
  templateUrl: './assign-role.component.html',
  styleUrls: ['./assign-role.component.css']
})
export class AssignRoleComponent {
  all_role: any[] = [];
  organization_id: any;
  entry_by: any;
  all_user: any[] = [];
  assign_role_data: any[] = [];
  apiUrl = "http://yourapiurlputhere:5002";
  userForm!: FormGroup;
  photo: File | null = null;
  dl_file: File | null = null;
  selected_role: any = 0;
  message: any = "Please Select a Role!!";
  all_user_in_table: any[] = [];
  
  itemsPerPage: number = 4; // Number of items per page
  currentPage: number = 1;  // Current page
  totalItems: number = 0;   // Total number of items
  
  constructor(private fb: FormBuilder, private allService: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 
    this.showRole();
    this.viewUserdefault();
    this.initialization();
  }

  initialization() {
    this.userForm = this.fb.group({
      user_name: ['', Validators.required],
      user_id: [null],
      role_id: ['', Validators.required],
      entry_by: [this.entry_by]
    });
  }

  viewUserdefault(): void {   
    this.allService.viewUserdefaultInAssign(this.organization_id).subscribe(
      (data: any) => {
        this.all_user_in_table = data.data;
        this.totalItems = this.all_user_in_table.length; // Calculate total items

        if (this.all_user_in_table) {
          this.message = null;
        } else {
          this.message = "Please select a role";
        }
      },
      (err: any) => {
        console.log(err);
      }
    );
  }

 

  onPageChange(page: number): void {
    if (page >= 1 && page <= this.getTotalPages()) {
      this.currentPage = page;
    }
  }

  getTotalPages(): number {
    return Math.ceil(this.totalItems / this.itemsPerPage);
  }

  getPaginatedData(): any[] {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    return this.all_user_in_table.slice(startIndex, startIndex + this.itemsPerPage);
  }

//get data as per user id in user main table
user_id_fetch:any
getDataPerUserId(u_id:any){
  this.user_id_fetch = u_id;
  console.log(u_id);
  
  this.allService.viewUserIdWisenAssign(u_id).subscribe(
    (data: any) => {
      // Filter users to include only those with role name 'Driver'
      this.all_user_in_table = data.data;
    },
    (err: any) => {
      console.log(err);
      this.viewUserdefault();
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
    return `${this.apiUrl}${photoPath}`;
  }



  editUser(user: any): void {
    this.userForm.patchValue({
      user_name: user?.user_name,
      user_id: user?.user_id,
      role_id:user?.role_id,
      entry_by:this.entry_by
    });

  }


  //selected role to fetch data again
  fetchDataAgain(){
    this.allService.viewUser(this.organization_id,this.selected_role).subscribe(
      (data: any) => {
        console.log(data);
        this.all_user = data;
      })
  }

//assign user submit data
handleSubmit(){
  console.log(this.userForm.value);
  
  if (this.userForm.valid) {
    this.allService.assignRole(this.userForm.value).subscribe(
      (response:any) => {
        console.log('Data submitted successfully:', response);
       alert(response.message)
       if(this.user_id_fetch  != 0){
        this.getDataPerUserId(this.user_id_fetch)
       }else{
        this.viewUserdefault(); 
       }
       
      },
      (error:any) => {
        console.error('Error submitting data:', error);
        alert(error.error.errors[0]);
      }
    );
  } else {
    console.log('Form is invalid');
  }
 }

//remove part
getUserIdForRemove(user_id:any)
{
 this.allService.getAssignRoledata(user_id).subscribe((data:any)=>{
  this.assign_role_data = data.roles
  console.log(this.assign_role_data );
  
 },(err:any)=>{
  console.log(err);
 })
}


deleteRole(user: any) {
  const isConfirmed = confirm('Are you sure you want to remove this role?');

  if (isConfirmed) {
    this.allService.deleteRole(user).subscribe(
      (data: any) => {
        alert(data.message);
        this.getUserIdForRemove(user.user_id);
        if(this.user_id_fetch  != 0){
          this.getDataPerUserId(this.user_id_fetch)
         }else{
          this.viewUserdefault();
         }
      },
      (err: any) => {
        console.error(err);
      }
    );
  }
}


}
