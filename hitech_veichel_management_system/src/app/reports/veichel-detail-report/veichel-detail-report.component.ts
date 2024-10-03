

import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-veichel-detail-report',
  templateUrl: './veichel-detail-report.component.html',
  styleUrls: ['./veichel-detail-report.component.css']
})
export class VeichelDetailReportComponent implements OnInit {
organization_id:any;
cat:any;
vehicle:any;
noDataAvailable: boolean = false;
fileurl='http://yourapiurlputhere:5002/storage';


  constructor(private service: VeichelserviceService) {}

  ngOnInit(): void {

    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;

      this.selectategories();
      this.getSomeVehicleDAta();
     }


  getSomeVehicleDAta() {
    // alert(this.organization_id)
    this.service.getVehicles(this.organization_id).subscribe((res:any) => {
      console.log(res);
      // Check if res is an array and has more than 10 items
      if (Array.isArray(res)) {
        // Get the last 10 items
        this.vehicle = res.slice(-5);
      } else {
        // Handle the case where res is not an array
        console.error('Unexpected response format', res);
        this.vehicle = [];
      }
    }, (error) => {
      console.error('Error fetching data', error);
      this.vehicle = [];
    });
  }

  selectategories(){
    this.service.fetchVehicleCategories().subscribe((res:any) => {
        this.cat = res;
    });
  }


  getVehicle(catId: any) {
    this.service.getVehicleCategoryId(catId).subscribe((res: any) => {
    this.vehicle = res.data;
 });
  }

printDiv(){
    window.print();
  }



}


