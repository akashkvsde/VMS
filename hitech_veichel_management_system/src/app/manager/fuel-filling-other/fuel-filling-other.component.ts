import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';


@Component({
  selector: 'app-fuel-filling-other',
  templateUrl: './fuel-filling-other.component.html',
  styleUrls: ['./fuel-filling-other.component.css']
})
export class FuelFillingOtherComponent {

  vehicleAddform!:FormGroup;
  fillingFormImg!: FormGroup;
  fileError: boolean = false;
  fillingForm!: FormGroup;
  fillingStationForm!: FormGroup;
  organization_id: any;
  entry_by: any;
  all_veichels: any[] = [];
  all_drivers: any[] = [];
  all_filling_stations: any[] = [];
  all_filling_data: any[] = [];
  apiUrl = "http://yourapiurlputhere:5002/storage/filling_bills/";
  fuel_expenses_id:any
  message:any = "please select a date range!!"
 
  // my_display:boolean = true;
  vehicleId: string | null = null;
  driverId: string | null = null;
  movementStartDate: string | null = null;

  constructor(private allService: VeichelserviceService, private fb: FormBuilder,private gb: FormBuilder,private ib: FormBuilder, private vf: FormBuilder) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId;
    this.entry_by = userInfo?.userId;

    this.defaultFillingData();
    this.allVeichel();
    this.fillingstation();
    this.initialization();
    this.initialiseServiceStation();
    this.initializationImg();
    this.initializeVehicle();

  }
  

  //all veichel
  allVeichel(): void {
    console.log('Fetching all vehicles');
    this.allService.getOtherVehicleOrgWise(this.organization_id).subscribe(
      (data: any) => {
        this.all_veichels = data.data;
      }
    
    );


  }
  
  

 
  
  
  //all filling station fetch
  fillingstation(): void {
    this.allService.fuelStations().subscribe(
      (data: any) => {
        this.all_filling_stations = data;
      },
      (err: any) => {
        console.error(err);
      }
    );
  }

  initialization(): void {
    this.fillingForm = this.ib.group({
      other_vehicle_id: ['', Validators.required],
      quantity: ['', Validators.required],
      filling_date: ['', Validators.required],
      filling_station: ['', Validators.required],
      approved_by: ['', Validators.required],
      last_km_reading: ['', Validators.required],
      entry_by: [this.entry_by],
      organization_id:[this.organization_id]
    });
  }
  


//for submit initialization 
  onSubmit(): void {
    if (this.fillingForm.valid) {
      const formData = new FormData();
  
      // Append form values to formData
      formData.append('other_vehicle_id', this.fillingForm.get('other_vehicle_id')?.value);
      formData.append('quantity', this.fillingForm.get('quantity')?.value);
      formData.append('filling_date', this.fillingForm.get('filling_date')?.value);
      formData.append('filling_station', this.fillingForm.get('filling_station')?.value);
      formData.append('approved_by', this.fillingForm.get('approved_by')?.value);
      formData.append('organization_id', this.fillingForm.get('organization_id')?.value);
       formData.append('last_km_reading', this.fillingForm.get('last_km_reading')?.value);
      formData.append('entry_by', this.fillingForm.get('entry_by')?.value); // Make sure this is not undefined
     
      this.allService.onOFFfuelSubmit(formData).subscribe(
        (data: any) => {
          alert(data.message);
          this.defaultFillingData();
          this.fillingForm.reset();
          this.initialization(); // Reset the form after successful submission
        },
        (err: any) => {
          alert(err.error.errors[0]);
        }
      );
    } else {
      // Handle form errors
      console.log('Form is invalid');
      console.log(this.fillingForm);
    }
  }
  


  //get fuel data
  getFuelData(tDate:any,fDate:any){
    this.allService.getFilldataUnOff(tDate,fDate,this.entry_by).subscribe((data:any)=>{
      this.all_filling_data = data.data
      console.log(data);
      
      if(this.all_filling_data != null){
       this.message = ""
      }else{
        this.message = "No data Found!!"
      }
    },(err:any)=>{
      console.log(err);;
      this.message = "No data Found!!"
      this.all_filling_data = [];
    })

   
  }

  getPhotoUrl(photoPath: string): string {
    return `${this.apiUrl}/${photoPath}`;
  }


  //initialize add service station
  initialiseServiceStation()
  {
    this.fillingStationForm = this.gb.group({
      fuel_station_name: ['', Validators.required],
      location: ['', Validators.required],
      entry_by: [this.entry_by]
    });
  }

  //submit fuelstation
  onSubmitStation(): void {
    if (this.fillingStationForm.valid) {
        // Handle form submission
        console.log(this.fillingStationForm.value);
        this.allService.addFillingStation(this.fillingStationForm.value).subscribe(
            (data: any) => {
                alert(data.message);
                // Reset the form after successful submission
                this.fillingStationForm.reset();
                this.fillingstation();
            },
            (err: any) => {
                alert("Facing Problem adding station!!");
            }
        );
    } else {
        // Handle form errors
        console.log('Form is invalid');
    }
}


//edit section=====
getFillId(fuel_expenses_id:any){
  this.fuel_expenses_id = fuel_expenses_id;
  this.initializationImg()
}

initializationImg(){
  this.fillingFormImg = this.fb.group({
    amount: ['', Validators.required],
    filling_bill: [null, Validators.required],
    other_fuel_id: [this.fuel_expenses_id]
  });
}

onFileChange(event: any): void {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 10 * 1024 * 1024) {  // Check if file exceeds 10MB
      this.fileError = true;
    } else {
      this.fileError = false;
      this.compressImage(file, 2 * 1024 * 1024).then((compressedFile) => {
        this.fillingFormImg.patchValue({ filling_bill: compressedFile });
      });
    }
  }
}

compressImage(file: File, maxSize: number): Promise<File> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file); // Read the file as data URL

    reader.onload = (event: any) => {
      const img = new Image();
      img.src = event.target.result;

      img.onload = () => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Set desired width/height, preserving aspect ratio
        const MAX_WIDTH = 800;  // Adjust the maximum width if necessary
        const MAX_HEIGHT = 800;
        let width = img.width;
        let height = img.height;

        if (width > height) {
          if (width > MAX_WIDTH) {
            height = Math.round((height *= MAX_WIDTH / width));
            width = MAX_WIDTH;
          }
        } else {
          if (height > MAX_HEIGHT) {
            width = Math.round((width *= MAX_HEIGHT / height));
            height = MAX_HEIGHT;
          }
        }

        canvas.width = width;
        canvas.height = height;

        // Draw the image on canvas
        ctx?.drawImage(img, 0, 0, width, height);

        // Compress the image iteratively until the size is <= 2MB
        let quality = 0.9;  // Start with high quality
        canvas.toBlob((blob) => {
          if (blob) {
            // Check if the compressed image is already below maxSize
            if (blob.size <= maxSize) {
              const compressedFile = new File([blob], file.name, {
                type: file.type,
                lastModified: Date.now(),
              });
              resolve(compressedFile);
            } else {
              // If not, reduce the quality and try again
              this.reduceImageSize(canvas, file, maxSize, resolve, reject, quality);
            }
          } else {
            reject(new Error('Image compression failed'));
          }
        }, file.type, quality); // Start with 90% quality
      };
    };

    reader.onerror = (error) => {
      reject(error);
    };
  });
}

reduceImageSize(canvas: HTMLCanvasElement, file: File, maxSize: number, resolve: Function, reject: Function, quality: number): void {
  if (quality < 0.1) {
    reject(new Error('Unable to compress the image below the desired size'));
    return;
  }

  canvas.toBlob((blob) => {
    if (blob) {
      if (blob.size <= maxSize) {
        const compressedFile = new File([blob], file.name, {
          type: file.type,
          lastModified: Date.now(),
        });
        resolve(compressedFile);
      } else {
        // Recursively reduce quality and try again
        quality -= 0.1;
        this.reduceImageSize(canvas, file, maxSize, resolve, reject, quality);
      }
    }
  }, file.type, quality);
}

onUpdate(): void {
  if (this.fillingFormImg.valid && !this.fileError) {
    const formData = new FormData();
    formData.append('amount', this.fillingFormImg.get('amount')?.value);

    const fillingBillFile = this.fillingFormImg.get('filling_bill')?.value;
    if (fillingBillFile) {
      formData.append('filling_bill', fillingBillFile);
    }

    formData.append('other_fuel_id', this.fillingFormImg.get('other_fuel_id')?.value);

    this.allService.updateOtherFilling(formData,this.fuel_expenses_id).subscribe(
      (data: any) => {
        alert(data.message);
        this.resetForm();
        this.defaultFillingData();
        this.initializationImg();
        this.fillingFormImg.reset(); // Reset the form after successful submission
        this.fileError = false;  // Reset file error status
      },
      (err: any) => {
        alert('Failed to update!!');
        this.resetForm();
        this.initializationImg();
        this.fillingFormImg.reset();
      }
    );
  } else {
    console.log('Form is invalid or file error exists');
    console.log(this.fillingFormImg);
  }
}


resetForm(): void {
  this.fillingFormImg.reset(); // Reset form controls
  
  // Clear the file input manually
  const fileInput = document.getElementById('fillingBill') as HTMLInputElement;
  if (fileInput) {
    fileInput.value = ''; // Clear the file input value
  }

  this.fileError = false; // Reset any file error flag
}

//default 10 data get 
defaultFillingData()
{
  this.allService.getUnofFilldataDefault(this.entry_by).subscribe((data:any)=>{
    this.all_filling_data = data.data
    console.log(data);
    
    if(this.all_filling_data != null){
      this.message = ""
     }else{
       this.message = "No data Found!!"
     }
  },(err:any)=>{
    console.log(err);;
    this.message = "No data Found!!"
  })
}


//======add vehicle and its woner==============
initializeVehicle()
{
    this.vehicleAddform = this.vf.group({
      other_vehicle_number: ['', Validators.required],
      other_owner_name: ['', Validators.required],
      entry_by: [this.entry_by]  ,
      organization_id:[this.organization_id ]
    });
}

onAddother(){
  if(this.vehicleAddform.valid){
    console.log(this.vehicleAddform.value);
    this.allService.otherVehicleadd(this.vehicleAddform.value).subscribe((data:any)=>{
      alert(data.message);
      this.vehicleAddform.reset();
      this.allVeichel();
      this.initializeVehicle();
    },(err:any)=>{
      alert(err.error.errors[0])
      console.log(err);     
    });
  }else{
    console.log('Form is invalid');
  } 
}

}
