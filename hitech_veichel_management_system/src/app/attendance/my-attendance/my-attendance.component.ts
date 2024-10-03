import { Component, OnInit, OnDestroy } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-my-attendance',
  templateUrl: './my-attendance.component.html',
  styleUrls: ['./my-attendance.component.css']
})
export class MyAttendanceComponent implements OnInit, OnDestroy {
  selectedButton: string = 'daily';  // Set default button if necessary
  con1:boolean = false;
  con2:boolean = true;
  setButton(button: string) {
    this.selectedButton = button;
  }
  check_out_time:any
  attendance_id:any
  currentDate: any;
  currentTime: any;
  private timer: any;
  location:any [] = [];
  entry_by:any;
  organization_id:any;
  messageColor: string = 'black';
  message:string = 'You Have Not Given Your Attendance Yet!!';
  disble_check_out:boolean = false;
  my_location:any [] = [];
  over_duty_data:any;
  constructor(private allService: VeichelserviceService) {}

  ngOnInit() {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 
    this.my_location = localStorage.getItem('location')
    ? localStorage.getItem('location')!.split(',')
    : [];
    this.defaultatteData();
    // Initialize the current date and time
    this.updateDateTime();
    this.getLocation();
    this.getOverdutydata()
    // Update the time every second
    this.timer = setInterval(() => {
      this.updateDateTime();
    }, 1000);

  }

  //hide section 
  hideDaily(){
    this.con1 = false;
    this.con2 = true;
    this.getOverdutydata();
  }

  hideOt(){
    this.con1 = true;
    this.con2 = false;
  }
  //hide section
  getLocation()
  {
    this.allService.getLocation().subscribe((data:any)=>{
     this.location = [data.city,data.region,data.postal];
    },(err:any)=>{
      console.log(err);
      
    })
  }
  
  updateDateTime() {
    const now = new Date();
    this.currentDate = now.toLocaleDateString(); // Gets the current date in the local format
    this.currentTime = now.toLocaleTimeString(); // Gets the current time in the local format
  }

  ngOnDestroy() {
    // Clear the timer when the component is destroyed to prevent memory leaks
    if (this.timer) {
      clearInterval(this.timer);
    }
  }

//checkIn
confirm_status:any
disable:boolean = false;

checkIn() {
  const checkInData = {
    user_id: this.entry_by,
    location: this.location.toString(),
    status: '1'
  };

  console.log(checkInData);

  // Call the checkIn service
  this.allService.checkIn(checkInData).subscribe((data: any) => {
    this.message = data.message;
    console.log(data.success);
    this.confirm_status = data.success;

    if (this.confirm_status == true) {
      this.disable = true;
      this.message = data.message;
      this.messageColor = 'green';

      // Set a timeout to call this.defaultatteData() after 5 seconds (5000 milliseconds)
      setTimeout(() => {
        this.defaultatteData();
      }, 5000); // 5 seconds in milliseconds

    } else {
      this.disable = false;
      this.messageColor = 'black';
    }
  }, (err: any) => {
    console.log(err);
  });
}


//default attendance data
defaultatteData()
{
  this.allService.atteData(this.entry_by).subscribe((data:any)=>{
    console.warn(data);
    this.attendance_id = data.data.attendance_id;
    this.check_out_time = data.data.check_out_time;
    console.log(this.attendance_id,this.check_out_time);
    //===for check out 
    if(this.check_out_time != null){
      this.disble_check_out = true;
    }else{
      this.disble_check_out = false;
    }
    
    //===for check in
    if((data.success) == true){
      this.disable = true
      this.message = "Your Attendance Already Been Taken For Today!!"
      this.messageColor = 'green';
    }else{
      this.disable = false
       this.message = "You Have Not Given Your Attendance Yet!!"
       this.messageColor = 'red';
    }
  },(err:any)=>{
    console.log(err);
     this.message = "You Have Not Given Your Attendance Yet!!"
     this.messageColor = 'red';
  })
}

//==for checkOut
checkOut()
{
  this.allService.checkOut(this.attendance_id).subscribe((data:any)=>{
    this.message = data.message;
    this.defaultatteData();
      if(this.check_out_time != null){
        this.disble_check_out = true;
      }else{
        this.disble_check_out = false;
      }
  },(err:any)=>{
    alert("Error !!facing Some issue");
  })
}


//============overduty========
getOverdutydata()
{
  this.allService.getdroverTime(this.entry_by).subscribe((data:any)=>{
   this.over_duty_data = data.data;
  },(err:any)=>{
    console.log(err);
  })
}

getOverdutydataupdate()
{
  this.allService.getdroverTimeupdate(this.entry_by).subscribe((data:any)=>{
   this.over_duty_data = data.data;
   console.log(this.over_duty_data);
   
  },(err:any)=>{
    console.log(err);
  })
}
overtimecheckIn(overtime_id: any) {
  const payload = { overtime_id: overtime_id }; // Send overtime_id as an array

  this.allService.overtimechekIn(payload).subscribe(
    (data: any) => {
      alert(data.message);
      this.getOverdutydata();
    },
    (err: any) => {
      console.log(err);
    }
  );
}

overtimecheckOut(overtime_id: any)
{
  const payload = {check_out_time:'', overtime_id: overtime_id ,end_date:''}; // Send overtime_id as an array
  this.allService.overtimechekIn(payload).subscribe(
    (data: any) => {
      alert(data.message);
      // this.getOverdutydataupdate();
      this.getOverdutydata();
    },
    (err: any) => {
      console.log(err);
    }
  );
}


}
