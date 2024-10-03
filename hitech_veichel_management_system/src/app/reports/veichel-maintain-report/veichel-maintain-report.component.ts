

import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-veichel-maintain-report',
  templateUrl: './veichel-maintain-report.component.html',
  styleUrls: ['./veichel-maintain-report.component.css'],
})
export class VeichelMaintainReportComponent implements OnInit {
  selectedFromDate: string = '';
  selectedToDate: string = '';
  noDataAvailable: boolean = false;
  noDataMessage: string = 'No data available.';

  organization_id:any;
  maintain: any[] = [];
  fuel: any;
  showNoDataMessage: boolean = false;
  selectedVehicleNo: string = '';
  fdate: string = '';
  tdate: string = '';
  fileurl = 'http://yourapiurlputhere:5002/storage';
  itemsPerPage = 4;
  currentPage = 1;

  constructor(private service: VeichelserviceService) {}

  ngOnInit(): void {

    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;

    this.getSomeData();
    this.fetchVehicleNo();
  }

  fetchVehicleNo() {
    this.service.getVehicles(this.organization_id).subscribe((res: any) => {
      this.fuel = res;
    });
  }

  getSomeData() {
    this.service.vehicleMaintemceSomeDta(this.organization_id).subscribe(
      (res: any) => {
        console.log(res);
        if (Array.isArray(res)) {
          this.maintain = res.slice(-5);
          this.noDataAvailable = this.maintain.length === 0;
          this.currentPage = 1; // Reset to the first page when new data is fetched
        } else {
          console.error('Unexpected response format', res);
          this.maintain = [];
          this.noDataAvailable = true; // Set no data available if response is not an array
        }
      },
      (error) => {
        console.error('Error fetching data', error);
        this.maintain = [];
        this.noDataAvailable = true; // Set no data available in case of error
      }
    );
  }


  vehicleMaintainance() {
    if (this.fdate && this.tdate && this.selectedVehicleNo) {
      this.vehicleMaintainanceByDateAndVehicleNo(this.fdate, this.tdate, this.selectedVehicleNo);
    } else if (this.fdate && this.tdate) {
      this.vehicleMaintainByDate(this.fdate, this.tdate);
    } else if (this.selectedVehicleNo) {
      this.vehicleMaintainByVehicleNo(this.selectedVehicleNo);
    } else {
      this.maintain = [];
      this.showNoDataMessage = true;
    }
    this.currentPage = 1; // Reset to the first page after fetching data based on criteria
  }

  vehicleMaintainanceByDateAndVehicleNo(fdate: any, tdate: any, vehicleNo: any) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;

    this.service.vehicleMaintainanceByBothVehicleAndDate(fdate, tdate, vehicleNo).subscribe((res: any) => {
        this.maintain = res.data;
        this.showNoDataMessage = this.maintain.length === 0;
      this.currentPage = 1; // Reset to the first page when new data is fetched
});
  }

  vehicleMaintainByDate(fdate: any, tdate: any) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;

    this.service.vehicleMaintainanceByDate(fdate, tdate,this.organization_id).subscribe((res: any) => {
      this.maintain = res.data;
      this.showNoDataMessage = this.maintain.length === 0;
      this.currentPage = 1; // Reset to the first page when new data is fetched
    });
  }

  vehicleMaintainByVehicleNo(vehicleId: any) {
    this.service.vehicleMaintainanceByVehicle(vehicleId).subscribe((res: any) => {
      this.maintain = res.data;
      this.showNoDataMessage = this.maintain.length === 0;
      this.currentPage = 1; // Reset to the first page when new data is fetched
    });
  }

  get paginatedData() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.maintain.slice(start, start + this.itemsPerPage);
  }

  nextPage() {
    if (this.currentPage * this.itemsPerPage < this.maintain.length) {
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

  printDiv() {
    window.print();
  }
}

