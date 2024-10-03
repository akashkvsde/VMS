import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-landing-page',
  templateUrl: './landing-page.component.html',
  styleUrls: ['./landing-page.component.css']
})
export class LandingPageComponent implements OnInit {
  organization_id: any;
  entry_by: any;
  running_vehicle: number = 0;
  total_vehicle: number = 0;
  total_driver: number = 0;
  maintain_vehicle: number = 0;
  allnavigations: any;
  targetVehicles: number = 0;
  targetDrivers: number = 0;
  targetRunningVehicles: number = 0;
  targetMaintainVehicles: number = 0;

  constructor(private allService: VeichelserviceService) {}

  ngOnInit() {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId; 
    this.entry_by = userInfo?.userId; 
    this.allnavigations = userInfo?.navigations; 
    this.totalVehicle();
  } 

  // Fetch total vehicle data
  totalVehicle() {
    this.allService.totalvehicle(this.organization_id).subscribe((data: any) => {
      this.targetVehicles = data.total_vehicles;
      this.targetRunningVehicles = data.active_vehicles;
      this.targetDrivers = data.total_active_drivers;
      this.targetMaintainVehicles = data.vehicles_under_maintenance;

      this.animateCountUp();
    }, (err: any) => {
      console.error(err);
    });
  }

  // Animate count up effect
  animateCountUp() {
    const duration = 2000;
    const stepTime = 50;

    // Calculate increment values for each field
    const vehicleIncrement = this.targetVehicles / (duration / stepTime);
    const driverIncrement = this.targetDrivers / (duration / stepTime);
    const runningVehicleIncrement = this.targetRunningVehicles / (duration / stepTime);
    const maintainVehicleIncrement = this.targetMaintainVehicles / (duration / stepTime);

    // Animate the total_vehicle count
    const vehicleInterval = setInterval(() => {
      this.total_vehicle += vehicleIncrement;
      if (this.total_vehicle >= this.targetVehicles) {
        this.total_vehicle = this.targetVehicles;
        clearInterval(vehicleInterval);
      }
    }, stepTime);

    // Animate the total_driver count
    const driverInterval = setInterval(() => {
      this.total_driver += driverIncrement;
      if (this.total_driver >= this.targetDrivers) {
        this.total_driver = this.targetDrivers;
        clearInterval(driverInterval);
      }
    }, stepTime);

    // Animate the running_vehicle count
    const runningVehicleInterval = setInterval(() => {
      this.running_vehicle += runningVehicleIncrement;
      if (this.running_vehicle >= this.targetRunningVehicles) {
        this.running_vehicle = this.targetRunningVehicles;
        clearInterval(runningVehicleInterval);
      }
    }, stepTime);

    // Animate the maintain_vehicle count
    const maintainVehicleInterval = setInterval(() => {
      this.maintain_vehicle += maintainVehicleIncrement;
      if (this.maintain_vehicle >= this.targetMaintainVehicles) {
        this.maintain_vehicle = this.targetMaintainVehicles;
        clearInterval(maintainVehicleInterval);
      }
    }, stepTime);
  }
  
  getNavUrlSegments(navUrl: string): string[] {
    return ['/dashboard', ...navUrl.split('/')];
  }
}
