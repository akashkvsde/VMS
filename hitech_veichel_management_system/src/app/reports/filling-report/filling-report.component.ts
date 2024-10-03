

import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-filling-report',
  templateUrl: './filling-report.component.html',
  styleUrls: ['./filling-report.component.css']
})
export class FillingReportComponent implements OnInit {
  fileurl = 'http://yourapiurlputhere:5002/storage';
  selectedFromDate: string = '';
  selectedToDate: string = '';
  vehicles: any;
  owners:any;
  filling: any[] = [];
  selectedVehicleNo: string = '';
  fdate: string = '';
  tdate: string = '';
  selectedOwner: string = '';
  showNoDataMessage: boolean = false;
  organization_id:any;
  itemsPerPage = 12;
  currentPage = 1;

  constructor(private service: VeichelserviceService) {}

  ngOnInit(): void {


    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;

    this.selectVehicleNo();
    this.getSomeFillingFuelDAta();
    this.selectOwnerName();
  }

  getSomeFillingFuelDAta() {
    this.service.FuelFillingSomeDta(this.organization_id).subscribe((res: any) => {
      console.log(res);
      if (Array.isArray(res.data)) {
        this.filling = res.data; // Get the last 5 items
      } else {
        console.error('Unexpected response format', res);
        this.filling = [];
      }
    }, (error) => {
      console.error('Error fetching data', error);
      this.filling = [];
    });
  }

  selectVehicleNo() {
    this.service.getVehicles(this.organization_id).subscribe((res: any) => {
      this.vehicles = res;
    });
  }

  selectOwnerName() {
    this.service.fetchOwnerName(this.organization_id).subscribe((res: any) => {
      this.owners = res;
    });
  }
  getDataByOwnerName(){
    if (this.selectedOwner) {
      this.getDataByOwner(this.selectedOwner);
    } else {
      this.filling = [];
      this.showNoDataMessage = true;
    }
  }

  getDataByOwner(ownerId: string) {
    this.service.getFillingFuelByOwnerName(ownerId).subscribe((res: any) => {
      this.filling = res.data;
      this.showNoDataMessage = this.filling.length === 0;
    });
  }

  getFillingData() {
    if (this.fdate && this.tdate && this.selectedVehicleNo) {
      this.getDataByDateAndVehicleNo(this.fdate, this.tdate, this.selectedVehicleNo);
    } else if (this.fdate && this.tdate) {
      this.getFillingDataByDate(this.fdate, this.tdate);
    } else if (this.selectedVehicleNo) {
      this.getDataByVehicleNo(this.selectedVehicleNo);
    } else {
      this.filling = [];
      this.showNoDataMessage = true;
    }
  }

  getDataByDateAndVehicleNo(fdate: any, tdate: any, vehicleNo: any) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;

    this.service.getFillingFuelBothDateAndVehicleNo(fdate, tdate, vehicleNo).subscribe((res: any) => {
      this.filling = res.data;
      this.showNoDataMessage = this.filling.length === 0;
    });
  }

  getFillingDataByDate(fdate: any, tdate: any) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;

    this.service.getFillingFuelByDate(fdate, tdate,this.organization_id).subscribe((res: any) => {
      this.filling = res.data;
      this.showNoDataMessage = this.filling.length === 0;
    });
  }

  getDataByVehicleNo(vehicleId: any) {
    this.service.getFillingFuelByVehicleNo(vehicleId).subscribe((res: any) => {
      this.filling = res.data;
      this.showNoDataMessage = this.filling.length === 0;
    });
  }

  printsDiv() {
    window.print();
  }

  // Pagination Methods
  get paginatedData() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.filling.slice(start, start + this.itemsPerPage);
  }

  nextPage() {
    if (this.currentPage * this.itemsPerPage < this.filling.length) {
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
}
