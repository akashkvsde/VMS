import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-all-attendance',
  templateUrl: './all-attendance.component.html',
  styleUrls: ['./all-attendance.component.css']
})
export class AllAttendanceComponent {
  all_tatble_data:any
  filterForm!: FormGroup;
  all_role: any[] = [];
  message: any = "Please Select a Role!!";
  all_user: any[] = [];
  organization_id: any;
  selected_role: any = 0;
  data_message:string = '';
  constructor(private allService: VeichelserviceService,private formBuilder: FormBuilder) {}
  
  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.showRole();
      // Initialize the form
      this.filterForm = this.formBuilder.group({
        from_date: [''],
        to_date: [''],
        role_id: [''],
        user_id: ['']
      });
  }

  selectUserRole(role:any): void {
    if(role){
      this.allService.viewUser(this.organization_id, role).subscribe(
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
    }else{
      this.all_user = [];
    }
 
  }

  showRole(): void {
    this.allService.allRoles().subscribe(
      (data: any) => {
        // Filter the roles to only include 'manager', 'driver', and 'management driver'
        this.all_role = data.filter((role: any) => {
          const roleNameLower = role.role_name.toLowerCase();
          return roleNameLower === 'manager' || roleNameLower === 'driver' || roleNameLower === 'management driver';
        });
      },
      (err: any) => {
        console.error('Error fetching roles:', err);
      }
    );
  }
  
  onSubmit(): void {
    // Log the form data to the console
    console.log(this.filterForm.value);
    this.allService.allAttendance(this.filterForm.value).subscribe((res:any)=>{
      this.all_tatble_data = res.data;
      console.log(this.all_tatble_data);

      if(this.all_tatble_data != null){
       this.data_message = "";
      }else{
        this.data_message = "No Data Found";
      }
      
    },(err:any)=>{
      console.log(err); 
      this.data_message = "No Data Found";
      this.all_tatble_data = null;
    })
  }

  printDiv(){
    window.print()
  }
  
}
