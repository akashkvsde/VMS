


import { Component,Input, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
@Component({
  selector: 'app-user-detail-report',
  templateUrl: './user-detail-report.component.html',
  styleUrls: ['./user-detail-report.component.css']
})
export class UserDetailReportComponent  implements OnInit{
  organization_id:any;
  users:any;
  user:any;
  use:any;
  selectedRole:any;
  data: any[] = []; // Initialize as an empty array
  vehicles: any[] = [];
 noDataAvailable: boolean = false;
 noDataMessage: string = ''; // Variable for no data message
 selectedRoleName: string = '';


  itemsPerPage = 15;
  currentPage = 1;
  roles: any;



 constructor(private service: VeichelserviceService) {}

    ngOnInit(): void {

      const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;

    this.selectUserRoles();
    this.selectAllUsers();

    }
    selectUserRoles(){
      this.service.getUserRoles().subscribe((res:any) => {
          this.users = res;
      });
    }

    selectAllUsers(){
      this.service.getVehiclegetUsers(this.organization_id).subscribe((res:any) => {
          this.data = res;
      });
    }

    // fetchUsersById(roleId:any){
    //    this.service.getUserDetailsById(roleId).subscribe((res:any) => {
    //       this.use = res;

    //   });

    // }
    role_name:any;
    fetchUsersById(selectedValue: string) {
      if (selectedValue) {
        const [roleId, roleName] = selectedValue.split('-');
        this.role_name=roleName;

        this.service.getUserDetailsById(roleId).subscribe(
          (data: any) => {
            if (data.length > 0) {
              this.data = data;
              this.noDataMessage = ''; // Clear the no-data message if data is present
            } else {
              this.data = [];
              this.noDataMessage = 'No users data available for the selected Category.';
            }
            this.currentPage = 1; // Reset to the first page when new data is fetched
            console.log(this.data);
          },
          (error: any) => {
            this.data = [];
            this.noDataMessage = 'No users data available for the selected Category.';
            console.error('Error fetching data:', error);
          }
        );
      }
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


