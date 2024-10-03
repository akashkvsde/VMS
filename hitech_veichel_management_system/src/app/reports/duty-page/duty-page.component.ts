import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-duty-page',
  templateUrl: './duty-page.component.html',
  styleUrls: ['./duty-page.component.css']
})
export class DutyPageComponent {
constructor(private fb: FormBuilder, private allService: VeichelserviceService) {}
all_role: any[] = [];
selected_role: any = 0;
all_user: any[] = [];
organization_id: any;
entry_by: any;
dyty_data:any
todayDate: string = ''; 

ngOnInit(): void {
  const userInfoString = localStorage.getItem('userInfo');
  const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
  this.organization_id = userInfo?.organizationId; 
  this.entry_by = userInfo?.userId; 
  this.showRole();
  const today = new Date();
    const year = today.getFullYear();
    const month = ('0' + (today.getMonth() + 1)).slice(-2); // Months are 0-based, so add 1
    const day = ('0' + today.getDate()).slice(-2); // Add leading zero if needed

    // Format the date as 'yyyy-MM-dd'
    // this.todayDate = `${year}-${month}-${day}`;
}



//get data as per user id in user main table
user_id_fetch:any
getDataPerUserId(u_id:any,f_date:any,t_date:any){
  this.user_id_fetch = u_id;
  console.log(u_id);
  
  this.allService.dutyData(u_id,f_date,t_date).subscribe(
    (data: any) => {
      // Filter users to include only those with role name 'Driver'
      this.dyty_data = data;
    },
    (err: any) => {
      console.log(err);
    }
  );
}


showRole(): void {
  this.allService.fetchAllDriver(this.organization_id).subscribe(
    (data: any) => {
      this.all_user = data;
    },
    (err: any) => {
      console.error('Error fetching roles:', err);
    }
  );
}

printDiv(){
  window.print()
}


}
