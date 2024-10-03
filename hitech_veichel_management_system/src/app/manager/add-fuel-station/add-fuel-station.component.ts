import { Component, OnInit, ElementRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-add-fuel-station',
  templateUrl: './add-fuel-station.component.html',
  styleUrls: ['./add-fuel-station.component.css']
})
export class AddFuelStationComponent {
  @ViewChild('exampleModal') exampleModal!: ElementRef;
  // organization_id:any
  addStation: any = {};
  // Organization: any[] = [];
  // org: any = {};
  entry_by:any;
  stations: any;
  // stationForm: any;
  stationsForm!:FormGroup
  editMode: boolean = false;
  selectedStationId: any | null = null;
  constructor(private fb:FormBuilder ,private vehicleService: VeichelserviceService) { }


  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    // this.organization_id = userInfo.organizationId; 
    this.entry_by = userInfo.userId;
    this.getFillingStation();
    this.stationsForm = this.fb.group({
      fuel_station_name: ['', Validators.required],
      location: ['', Validators.required]
    });



  }
  onSubmit(form: any): void {
    if (form.valid) {
      const formData = new FormData();
      formData.append('fuel_station_name', this.addStation.fuel_station_name || '');
      formData.append('location', this.addStation.location || '');
      formData.append('entry_by', this.entry_by);
    
        // Append the file name


      this.vehicleService.addFillingStation(formData).subscribe(
        (response: any) => {
          alert('FillingStation added successfully');
        form.reset();
        this.getFillingStation();
        },
        (error: any) => {
          alert('FillingStation not added');
          console.log(error);
        }
      );
    }
  }

getFillingStation(){
  this.vehicleService.getFillingStations().subscribe((res:any)=>{
    this.stations=res;
  })
}

editStation(st: any): void {
  console.log('Editing station:', st);
  this.editMode = true;
  this.selectedStationId = st.fuel_station_id;
  this.stationsForm.patchValue({
    fuel_station_name: st.fuel_station_name,
    location: st.location
  });
}

handleSubmit():void{
  console.log('Submitting form:', this.stationsForm.value); // Check the form data
  if (this.stationsForm.valid && this.selectedStationId !== null) {
    const updateData = {
      fuel_station_name: this.stationsForm.get('fuel_station_name')?.value,
      location: this.stationsForm.get('location')?.value,
      entry_by: this.entry_by
    };

    console.log('Update data:', updateData); // Verify the update data

    this.vehicleService.updateFillingStation(this.selectedStationId, updateData).subscribe(
      (data:any) => {
        alert('Filling Station updated successfully');
        this.stationsForm.reset();
        this.getFillingStation();
        this.editMode = false;
        this.selectedStationId = null;
   },
      (error: any) => {
        alert('Failed to update Filling Station');
        console.log(error);
      }
    );
  }
  this.getFillingStation();

}


}

