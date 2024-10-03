

import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-pollution-report',
  templateUrl: './pollution-report.component.html',
  styleUrls: ['./pollution-report.component.css']
})
export class PollutionReportComponent implements OnInit {
  organization_id:any;
  data: any[] = [];
  vehicles: any[] = [];
  imgurl: any;
  fileurl = 'http://yourapiurlputhere:5002/storage';
  noDataAvailable: boolean = false;
  noDataMessage: string = ''; // Variable for no data message
  itemsPerPage = 15;
  currentPage = 1;

  constructor(private service: VeichelserviceService, private http: HttpClient) {}

  ngOnInit(): void {

    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;

    this.selectVehicle();
    this.fetchPollutionReport();
  }

  selectVehicle() {
    // alert(this.organization_id)
    this.service.getVehicles(this.organization_id).subscribe((res: any) => {
      this.vehicles = res;
    });
  }

  getPoll(vehicleId: any) {
    this.service.getPollutionReport(vehicleId).subscribe(
      (data: any) => {
        if (data.length > 0) {
          this.data = data;
          this.noDataMessage = '';
        } else {
          this.data = [];
          this.noDataMessage = 'No pollution data available for the selected vehicle.';
        }
        this.currentPage = 1; // Reset to the first page when new data is fetched
      },
      (error: any) => {
        this.data = [];
        this.noDataMessage = 'No pollution data available for the selected vehicle.';
      }
    );
  }

  fetchPollutionReport() {
    // alert(this.organization_id)
    this.service.getPolluReport(this.organization_id).subscribe((res: any) => {
      this.data = res;
      this.noDataAvailable = this.data.length === 0;
      this.currentPage = 1; // Reset to the first page when new data is fetched
    });
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

