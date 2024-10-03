

import { Component,OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';


@Component({
  selector: 'app-unofficial-fuel-expense',
  templateUrl: './unofficial-fuel-expense.component.html',
  styleUrls: ['./unofficial-fuel-expense.component.css'],

})
export class UnofficialFuelExpenseComponent implements OnInit {
  organization_id:any;
  owner: any;
  other: any;
  fdate: string = '';
  tdate: string = '';
  filteredData: any[] = []; // To store filtered data
  selectedOwnerId: string = '';
  selectedFromDate: string = '';
  selectedToDate: string = '';
  selectedOwnerName: string = '';
  fileurl = 'http://yourapiurlputhere:5002/storage';
  noDataMessage: string = ''; // Property to hold no data message

  constructor(private service: VeichelserviceService) {}

  ngOnInit(): void {

    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;

    this.selectOwnerName();
    this.fetchOtherVehicleData();
  }

  printsDiv() {
    window.print();
  }

  selectOwnerName() {
    this.service.getOtherOwner(this.organization_id).subscribe((res: any) => {
      if (res && res.data) {
        this.owner = res.data; // Access data field
      }
    });
  }

  fetchOtherVehicleData() {
    // Replace with the actual organization ID
    this.service.getSomeDataUnofficialReport(this.organization_id).subscribe((res: any) => {
      if (res && res.data && res.data.length) {
        this.filteredData = res.data; // Initially, show all data
        this.noDataMessage = ''; // Clear no data message
      } else {
        this.filteredData = [];
        this.noDataMessage = 'No data available for the initial fetch.';
      }
    }, (err: any) => {
      this.filteredData = [];
      this.noDataMessage = 'Error fetching data.';
    });
  }


  ownerWiseData(owner: any) {
    // Replace with the actual organization ID
    this.service.unOfficialReportByOwner(this.organization_id, owner).subscribe((res: any) => {
      if (res && res.data && res.data.length) {
        this.filteredData = res.data; // Show data for the selected owner
        this.noDataMessage = ''; // Clear no data message
      } else {
        this.filteredData = [];
        this.noDataMessage = 'No data available for the selected owner.';
      }
    }, (err: any) => {
      this.filteredData = [];
      this.noDataMessage = 'Error fetching data.';
    });
  }

  unOfficialData() {
    if (this.fdate && this.tdate) {
      if (this.selectedOwnerName) {
        this.fetchDataByDateAndOwnerName(this.fdate, this.tdate, this.selectedOwnerName);
      } else {
        this.fetchDataByDate(this.fdate, this.tdate);
      }
    } else {
      this.filteredData = [];
      this.noDataMessage = 'Please provide both from and to dates.';
    }
  }

  fetchDataByDate(fdate: string, tdate: string) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;
    const orgId = '1'; // Replace with the actual organization ID
    this.service.unOfficialReportByDate(orgId, fdate, tdate).subscribe((res: any) => {
      if (res && res.data && res.data.length) {
        this.filteredData = res.data; // Show data for the selected date range
        this.noDataMessage = ''; // Clear no data message
      } else {
        this.filteredData = [];
        this.noDataMessage = 'No data available for the selected date range.';
      }
    }, (err: any) => {
      this.filteredData = [];
      this.noDataMessage = 'No data available for the selected date range.';
    });
  }


  fetchDataByDateAndOwnerName(fdate: string, tdate: string, owner: any) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;
     // Replace with the actual organization ID
    this.service.unOfficialReportByDateAndOwner(this.organization_id, fdate, tdate, owner).subscribe((res: any) => {
      if (res && res.data && res.data.length) {
        this.filteredData = res.data; // Show data for the selected date range and owner
        this.noDataMessage = ''; // Clear no data message
      } else {
        this.filteredData = [];
        this.noDataMessage = 'No data available for the selected date range and owner.';
      }
    }, (err: any) => {
      this.filteredData = [];
      this.noDataMessage = 'No data available for the selected date range and owner.';
    });
  }
}


