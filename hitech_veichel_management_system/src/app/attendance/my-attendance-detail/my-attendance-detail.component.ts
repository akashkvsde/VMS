import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-my-attendance-detail',
  templateUrl: './my-attendance-detail.component.html',
  styleUrls: ['./my-attendance-detail.component.css']
})
export class MyAttendanceDetailComponent {
  constructor(private allService: VeichelserviceService) {}
  entry_by:any;
  organization_id:any;
  currentDate:any;
  ngOnInit() {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 
    this.currentDate = new Date().toISOString().split('T')[0];;
    console.log(this.currentDate);
    
    this.defaultData();
  }

  attendanceData: any[] = [];
  defaultData() {
    this.allService.dateFilter(this.entry_by, this.currentDate, this.currentDate).subscribe(
      (response: any) => {
        console.log(response);
        this.attendanceData = response.data; // Store the data in the attendanceData property
      },
      (err: any) => {
        console.log(err);
      }
    );
  }

  getDate(from_date:any,to_date:any){
  console.log(from_date,to_date);
  this.allService.dateFilter(this.entry_by, to_date, from_date).subscribe(
    (response: any) => {
      console.log(response);
      this.attendanceData = response.data; // Store the data in the attendanceData property
    },
    (err: any) => {
      console.log(err);
    }
  );
  }
}
