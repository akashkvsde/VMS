import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import {editFuelModel} from 'src/app/models/veichel-model.model'


@Component({
  selector: 'app-edit-fuel-expense',
  templateUrl: './edit-fuel-expense.component.html',
  styleUrls: ['./edit-fuel-expense.component.css']
})
export class EditFuelExpenseComponent {
  message:any="please Select Date !!";
  t_date:any
  f_date:any
  all_filling_data:any
  organization_id:any
  entry_by:any
  mymodel: editFuelModel = new editFuelModel();
  constructor(private allService: VeichelserviceService) {}
  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId;
    this.entry_by = userInfo?.userId;
  }

  //get fuel data
  getFuelData(tDate:any,fDate:any){
    this.t_date = tDate;
    this.f_date = fDate;
    this.allService.getFilldataForEdit(tDate,fDate,this.organization_id).subscribe((data:any)=>{
      this.all_filling_data = data.data;
      if(this.all_filling_data != 0)
      {
        this.message = "";
      }else{
        this.message = "No data Found";
      }
    },(err:any)=>{
      alert ("failed to fetch")
    })
  }

  
//get fuel data
getFueldata(data: any) {
  console.log(data.filling_date, data.filling_amount, data.filling_quantity, data.last_km_reading);
  this.mymodel.filling_date = data.filling_date;
  this.mymodel.filling_amount = data.filling_amount;
  this.mymodel.filling_quantity = data.filling_quantity;
  this.mymodel.last_km_reading = data.last_km_reading;
  this.mymodel.fuel_expenses_id = data.fuel_expenses_id;
  this.mymodel.updated_by = this.entry_by;
}
  

//submit to update
onSubmit(): void {
  if (this.mymodel && this.mymodel.filling_date && this.mymodel.filling_amount && 
      this.mymodel.filling_quantity && this.mymodel.last_km_reading) {
    
    // Call the service method to update the fuel data
    this.allService.updatefuelFunction(this.mymodel,this.mymodel.fuel_expenses_id).subscribe(
      (response: any) => {
        console.log('Fuel data updated successfully:', response);
        alert(response.message);
        this.getFuelData(this.t_date,this.f_date);
        // Handle success, like closing the modal or resetting the form
      },
      error => {
        console.error('Error updating fuel data:', error);
        // Handle error, like showing an error message
      }
    );
  } else {
    // Handle the case where any field is missing
    alert('Please fill in all the required fields.');
    console.log('Missing fields:', this.mymodel);
  }
}

}
