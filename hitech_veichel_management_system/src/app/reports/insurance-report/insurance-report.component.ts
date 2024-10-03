

import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-insurance-report',
  templateUrl: './insurance-report.component.html',
  styleUrls: ['./insurance-report.component.css']
})
export class InsuranceReportComponent implements OnInit {
  organization_id:any;
 data: any[] = []; // Initialize as an empty array
  vehicles: any[] = [];
  noDataAvailable: boolean = false;
  noDataMessage: string = ''; // Variable for no data message
  fileurl = 'http://yourapiurlputhere:5002/storage';
  itemsPerPage = 15;
  currentPage = 1;

  constructor(private service: VeichelserviceService) {}

  ngOnInit(): void {

    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;


// console.log(this.organization_id)
    this.fetchInsuranceReport();
    this.selectVehicle();

  }

  fetchInsuranceReport() {
    // alert(this.organization_id)
    this.service.getInsurance(this.organization_id).subscribe((res: any) => {
      this.data = res;
      this.noDataAvailable = this.data.length === 0;
      this.currentPage = 1; // Reset to the first page when new data is fetched
    });
  }
  selectVehicle() {
    // alert(this.organization_id)
    // Pass the organization_id when calling the service method
    this.service.getVehicles(this.organization_id).subscribe((res: any) => {
      this.vehicles = res;
    });
  }

  getInsurance(vehicleId: any) {
    this.service.getInsuranceReport(vehicleId).subscribe(
      (data: any) => {
        if (data.length > 0) {
          this.data = data;
          this.noDataMessage = ''; // Clear the no-data message if data is present
        } else {
          this.data = [];
          this.noDataMessage = 'No insurance data available for the selected vehicle.';
        }
        this.currentPage = 1; // Reset to the first page when new data is fetched
      },
      (error: any) => {
        this.data = [];
        this.noDataMessage = 'Error fetching insurance data.';
      }
    );
  }



  get paginatedData() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.data.slice(start, start + this.itemsPerPage);
  }

  nextPage() {
    if (this.currentPage * this.itemsPerPage < this.data.length) {
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

