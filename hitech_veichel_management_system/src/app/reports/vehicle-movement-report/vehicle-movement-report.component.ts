

import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-vehicle-movement-report',
  templateUrl: './vehicle-movement-report.component.html',
  styleUrls: ['./vehicle-movement-report.component.css']
})
export class VehicleMovementReportComponent implements OnInit {
  selectedFromDate: string = '';
  selectedToDate: string = '';
  organization_id:any;
  movement: any[] = [];
  vehicles: any;
  selectedVehicleNo: any='';
  fdate: string = '';
  tdate: string = '';
  stime: string = '';
  etime: string = '';

  itemsPerPage = 15;
  currentPage = 1;

  constructor(private service: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;

    this.getSomeMovementDAta();
    this.selectVehicleNo();
  }

  getSomeMovementDAta() {
    this.service.vehicleMovmentSomeDta(this.organization_id).subscribe(
      (res: any) => {
        console.log(res);
        this.movement=res;
        // Check if res is an array and has more than 10 items
        // if (Array.isArray(res)) {
        //   // Get the last 5 items
        //   this.movement = res.slice(-5);
        // } else {
        //   // Handle the case where res is not an array
        //   console.error('Unexpected response format', res);
        //   this.movement = [];
        // }
      },
      (error) => {
        console.error('Error fetching data', error);
        this.movement = [];
      }
    );
  }

  selectVehicleNo() {
    this.service.getVehicles(this.organization_id).subscribe((res: any) => {
      this.vehicles = res;
    });
  }

  getMovementData() {
    // Fetch data based on selected criteria
    if (this.fdate && this.tdate && this.selectedVehicleNo) {
      // Get data by both date range and vehicle number
      this.movementReportByVehicleNoAndDate(this.fdate, this.tdate, this.selectedVehicleNo);
    } else if (this.fdate && this.tdate) {
      // Get data by date range only
      this.movementReportByDate(this.fdate, this.tdate);
    } else if (this.selectedVehicleNo) {
      // Get data by vehicle number only
      this.movementReportByVehicleNo(this.selectedVehicleNo);
    } else {
      // If no criteria selected, you can clear the table or handle it as needed
      this.movement = [];

    }
  }

  movementReportByDate(fdate: string, tdate: string) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;

    this.service.getMovementsReportByDate(fdate, tdate,this.organization_id).subscribe((res: any) => {
      this.movement = res.data || []; // Ensure array is assigned
    }, error => {
      console.error('Error fetching data by date', error);
      this.movement = [];
    });
  }

  movementReportByVehicleNo(vehicleId: any) {
    this.service.getMovementByVehicleNo(vehicleId).subscribe((res: any) => {
      this.movement = res.data || []; // Ensure array is assigned
    }, error => {
      console.error('Error fetching data by vehicle number', error);
      this.movement = [];
    });
  }

  movementReportByVehicleNoAndDate(fdate: string, tdate: string, vehicleId: any) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;

    this.service.getMovementBothDateAndVehicleNo(fdate, tdate, vehicleId).subscribe((res: any) => {
      this.movement = res.data || []; // Ensure array is assigned
    }, error => {
      console.error('Error fetching data by date and vehicle number', error);
      this.movement = [];
    });
  }

  get paginatedData() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.movement.slice(start, start + this.itemsPerPage);
  }

  nextPage() {
    if (this.currentPage * this.itemsPerPage < this.movement.length) {
      this.currentPage++;
    }
  }

  prevPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
    }
  }

  goToPage(pageNumber: number) {
    this.currentPage = pageNumber;
  }

  printsDiv() {
    window.print();
  }
}



