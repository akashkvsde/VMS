
import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-authority-maintainace',
  templateUrl: './authority-maintainace.component.html',
  styleUrls: ['./authority-maintainace.component.css']
})
export class AuthorityMaintainaceComponent implements OnInit {
  vehicles: any[] = [];
  organization_id: any;
  entry_by: any;
  vehicle_maintenance_id: any;

  constructor(private vehicleService: VeichelserviceService) { }

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId; 
    this.entry_by = userInfo.userId; 
    
    this.onStatusChange('Pending'); // Initialize with 'pending' status
    setInterval(() => {
      this.onStatusChange('pending');
    }, 50000);
  }

  onStatusChange(statusOrEvent?: string | Event) {
    let selectedStatus: string;
  
    if (typeof statusOrEvent === 'string') {
      selectedStatus = statusOrEvent;
    } else if (statusOrEvent instanceof Event) {
      const selectElement = statusOrEvent.target as HTMLSelectElement;
      selectedStatus = selectElement?.value || 'pending';
    } else {
      selectedStatus = 'pending';
    }
  
    // Clear previous data before making a new request
    this.vehicles = [];
    this.vehicleService.getVehiclesByApproveStatus(selectedStatus, this.entry_by).subscribe(
      (response: any) => {
        if (response.length > 0) {
          this.vehicles = response;
          this.vehicle_maintenance_id = this.vehicles[0].vehicle_maintenance_id;
          console.log(response);
        } else {
          this.vehicles = []; // No data found
        }
        console.log(selectedStatus);
      },
      error => {
        console.error('Error fetching vehicles:', error.error.message);
        this.vehicles = []; // Clear data on error as well
      }
    );
  }    

  changeStatus(vehicle: any, status: string) {
    if (status !== vehicle.maintenance_approve_status) {
      const data = {
        maintenance_approve_status: status
      };
  
      this.vehicleService.updateMaintenanceApprove(data, this.vehicle_maintenance_id).subscribe(
        (response: any) => {
          vehicle.maintenance_approve_status = status;
          console.log('Status updated successfully:', response);
        },
        error => {
          console.error('Error updating status:', error);
        }
      );
    }
  }
}

