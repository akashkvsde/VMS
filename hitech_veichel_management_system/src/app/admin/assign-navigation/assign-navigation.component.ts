import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
@Component({
  selector: 'app-assign-navigation',
  templateUrl: './assign-navigation.component.html',
  styleUrls: ['./assign-navigation.component.css']
})
export class AssignNavigationComponent {
  constructor(private allService: VeichelserviceService) {}
 
  enter_by:any //after session it will
  college_id:any 
  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.enter_by = userInfo.userId; 
   
    
    this.fetchUserrole();    //USE TO FETCH USER ROLE
    this.fetchnavPage();     //USE TO FETCH NAV BAR
  }
  checks:boolean = false

  bulk(e: any) {
    if (e.target.checked) {
      this.selectedRows = this.geNavPage.map((row: any) => ({
        nav_id: row.nav_id,
        role_id: this.selectedRole,
        entry_by: this.enter_by
      }));
      console.log('Master checkbox checked. All rows selected:', this.selectedRows);
    } else {
      this.selectedRows = [];
      console.log('Master checkbox unchecked. All rows deselected.');
    }
  }
  
  
//session

//session ended


  selectedRole: number = 0;
  selectedRows: { nav_id: number; role_id: number; entry_by:any }[] = [];

  fetchAssignrole:any;

  onRoleChange(roleId: number) {
    console.log(roleId);
    this.selectedRole = roleId;
    this.clearPreviousRoleSelections();
    this.allService.searchAssignNav(roleId).subscribe((result:any)=>{
      console.log(result);
      
      this.fetchAssignrole = result.navigations;
      // console.log(this.fetchAssignrole);

    },(error:any)=>{
       console.log(error.error.message);
      // alert(error.error.message);
      
      this.fetchAssignrole = null
    })
  }           


 
  toggleRowSelection(rowId: number) {
    const index = this.selectedRows.findIndex(
      item => item.nav_id === rowId && item.role_id === this.selectedRole
    );
  
    if (index > -1) {
      this.selectedRows.splice(index, 1); // Deselect
    } else {
      if (this.selectedRole !== 0) {
        this.selectedRows.push({ nav_id: rowId, role_id: this.selectedRole, entry_by: this.enter_by }); // Include 'enter_by'
      } else {
        alert('Please select role!');
        location.reload();
      }
    }
  }
  

  isSelected(rowId: number): boolean {
    return this.selectedRows.some(
      item => item.nav_id === rowId && item.role_id === this.selectedRole
    );
  }

  clearPreviousRoleSelections() {
    this.selectedRows = this.selectedRows.filter(
      item => item.role_id === this.selectedRole
    );
  }

  //fetch role
  getuserRole: any;
    fetchUserrole() {
    this.allService.AllRoleSup().subscribe(data => {
      this.getuserRole = data;  
    });
  }

 //fetch nav page
 geNavPage_data: any;
 geNavPage:any
 fetchnavPage() {
   this.allService.navPage().subscribe(data => {
     this.geNavPage_data = data;
     this.geNavPage= this.geNavPage_data
    //  console.log(this.geNavPage_data);
   });
 }


  takeArray(role: number) {
    const selectedRowsWithCurrentRole = this.selectedRows.filter(
      item => item.role_id === role
    );
     console.log(selectedRowsWithCurrentRole);
   if(role != 0){
    //insert section
    this.allService.assignNav(selectedRowsWithCurrentRole).subscribe((result:any)=>{
        alert(result.message);
       console.log(result.message);
       
    },(err:any)=>{
      // console.log(err.error.errors[0]);
      alert(err.error.errors[0]);
      
    })
    //insert section ended

   }else{
    alert("please select Role!!")
    // location.reload();
   }
   
  }

  // /=====delete=/
  deletRole(data:any){
    if(confirm("Are you sure to delete? ")) {
      this.allService. deleteAssignNav(data).subscribe((result:any)=>{  //for delete
        console.log(result);
        alert(result.message)
     
        this.allService.searchAssignNav(this.selectedRole).subscribe((result:any)=>{
         this.fetchAssignrole = result.navigations;
       })
     
        })
    }
   
  }


  //remove all role
  removeAll(selectedRole:any){
  if(selectedRole == 0){
    alert("Please select role first!!")
  }else{
    const result = window.confirm("Are you sure you want to delete?");
  if (result) {
    this.allService. deleteAll_nav(selectedRole).subscribe((result)=>{  //for delete
      alert(result)
  
      this.allService.searchAssignNav(this.selectedRole).subscribe((getres)=>{ //for dynamic reload
        this.fetchAssignrole = getres;
        console.log(this.fetchAssignrole);
      },(error:any)=>{
        // console.log(error.error.message);
        alert(error.error.message);
      })
  
     })
  } else {
    // If canceled, do something else or don't delete
    console.log('Deletion canceled');
  }
  }
  }


  //hide and sick nav
  delnav:boolean = true
  alllnav:boolean = false

  delpages(){
    this.delnav = false
    this.alllnav = true
  }
  allPages(){
    this.delnav = true
    this.alllnav = false
  }



}
