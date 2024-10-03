import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { VeichelserviceService } from '../services/veichelservice.service';
@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent {
  allnavigations: any[] = [];
  user_name: any;
  user_role: any;
  selectedNav: any;
  user_roles:any;
  location:any;

  constructor(private router: Router,private allService:VeichelserviceService) {}

  ngOnInit() {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.allnavigations = userInfo?.navigations; 
    this.user_name = userInfo?.user_name; 
    this.user_role = localStorage.getItem('roleName');
    this.user_roles = localStorage.getItem('roleNames');
   
    console.log(this.allnavigations);
    console.log(this.router.url);
    this. getLocation();
  }

  getLocation()
  {
    this.allService.getLocation().subscribe((data:any)=>{
     this.location = [data.city,data.region,data.postal];
     localStorage.setItem('location',this.location);
    },(err:any)=>{
      console.log(err);
    })
  }

  logout() {
    const confirmation = confirm('Are you sure you want to logout?');
    if (confirmation) {
      // Clear localStorage
      localStorage.removeItem('authToken');
      localStorage.removeItem('roleId');
      localStorage.removeItem('userInfo');
      // Navigate to the login page
      this.router.navigate(['/login']);
      console.clear();
    }
  }

  forward() {
    window.history.forward();
  }

  backward() {
    window.history.back();
  }

  onNavClick(nav: any) {
    this.selectedNav = nav;
  }
}
