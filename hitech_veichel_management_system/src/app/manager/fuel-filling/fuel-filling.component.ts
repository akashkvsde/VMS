import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-fuel-filling',
  templateUrl: './fuel-filling.component.html',
  styleUrls: ['./fuel-filling.component.css']
})
export class FuelFillingComponent implements OnInit {
[x: string]: any;
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
  apiUrl = "http://yourapiurlputhere:5002/storage/";
  fuel_expenses_id:any
  message:any = "please select a date range!!"
 
  // my_display:boolean = true;
  vehicleId: string | null = null;
  driverId: string | null = null;
  movementStartDate: string | null = null;
  vehicle_movement_id:any;

  constructor(private allService: VeichelserviceService, private fb: FormBuilder,private gb: FormBuilder,private ib: FormBuilder,private route: ActivatedRoute,private router:Router) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId;
    this.entry_by = userInfo?.userId;
  
    this.routeMatch();  // Initialize route params first
    this.defaultFillingData();
    this.allVeichel();
    this.allDrivers();
    this.fillingstation();
    this.initialization();
    this.initialiseServiceStation();
    this.initializationImg();

    this.route.queryParams.subscribe(params => {
      this.vehicleId = params['vehicle_id'];
  
      if (this.vehicleId) {
        this.fillingForm.patchValue({ vehicle_id: this.vehicleId });
  
        // Simulate the event for vehicle selection
        this.getVehicleName({
          target: { value: this.vehicleId }
        } as unknown as Event);
      }
    });
  }
  
//get route data to match
routeMatch()
{
    this.route.queryParams.subscribe((params) => {
      this.vehicle_movement_id = params['vehicle_movement_id'];
      this.vehicleId = params['vehicle_id'];
      this.driverId = params['driver_id'];
      this.movementStartDate = params['movement_start_date'];
    });
}
  //all veichel
  allVeichel(): void {
    console.log('Fetching all vehicles');
    
    this.allService.fetchAllVeichel(this.organization_id).subscribe(
      (data: any) => {
        console.log('Fetched data:', data);
        
        // Check if a vehicleId is present from route params
        if (this.vehicleId) {
          console.log('Vehicle ID from route:', this.vehicleId);
          
          // Ensure vehicleId is not null and is a valid number
          const vehicleIdNumber = parseInt(this.vehicleId, 10);
          
          if (!isNaN(vehicleIdNumber)) {
            // Try to find the vehicle matching the vehicleId
            const matchedVehicle = data.find((vehicle: any) => vehicle.vehicle_id === vehicleIdNumber);
            console.log('Matched vehicle:', matchedVehicle);
            
            if (matchedVehicle) {
              // If a matching vehicle is found, set it as the selected value in the form
              this.fillingForm.patchValue({ vehicle_id: vehicleIdNumber });
  
              // Display all vehicles in the dropdown
              this.all_veichels = data;
            } else {
              // If no matching vehicle is found, display all vehicles
              this.all_veichels = data;
            }
          } else {
            console.error('Invalid vehicle ID:', this.vehicleId);
            this.all_veichels = data; // Handle invalid vehicle ID case by displaying all vehicles
          }
        } else {
          // If no vehicleId is present in the route, display all vehicles
          this.all_veichels = data;
        }
      },
      (err: any) => {
        console.error('Error fetching vehicles:', err);
      }
    );
  }
  
  
  

  //all drivers
  allDrivers(): void {
    console.log('Fetching all drivers');
    
    this.allService.fetchAllDriver(this.organization_id).subscribe(
      (data: any) => {
        console.log('Fetched drivers:', data);
        
        // Check if a driverId is present from route params
        if (this.driverId) {
          console.log('Driver ID from route:', this.driverId);
          
          // Ensure driverId is not null and is a valid number
          const driverIdNumber = parseInt(this.driverId, 10);
          
          if (!isNaN(driverIdNumber)) {
            // Try to find the driver matching the driverId
            const matchedDriver = data.find((driver: any) => driver.user_id === driverIdNumber);
            console.log('Matched driver:', matchedDriver);
            
            if (matchedDriver) {
              // If a matching driver is found, display only that driver
              this.all_drivers = [matchedDriver];
              
              // Set the form control value to the matched driver
              this.fillingForm.get('driver_id')?.setValue(matchedDriver.user_id);
            } else {
              // If no matching driver is found, display all drivers
              this.all_drivers = data;
              console.warn('No matching driver found for driverId:', this.driverId);
            }
          } else {
            console.error('Invalid driver ID:', this.driverId);
            this.all_drivers = data; // Handle invalid driver ID case by displaying all drivers
          }
        } else {
          // If no driverId is present in the route, display all drivers
          this.all_drivers = data;
        }
        
        // Set the filling date if movementStartDate is present
        if (this.movementStartDate) {
          // Convert movementStartDate to the format required by input type="date"
          const formattedDate = new Date(this.movementStartDate).toISOString().split('T')[0];
          this.fillingForm.get('filling_date')?.setValue(formattedDate);
        }
      },
      (err: any) => {
        console.error('Error fetching drivers:', err);
      }
    );
  }
  
  
  //all filling station
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
      vehicle_id: [null, Validators.required],
      driver_id: [null, Validators.required],
      filling_date: ['', Validators.required],
      fuel_station_id: ['', Validators.required],
      filling_quantity: ['', Validators.required],
      last_km_reading: ['', Validators.required],
      entry_by: [this.entry_by],
     
    });
  }
  
  updateFillInMovement(move_id:any,fuel_id:any)
  {
    const fill_id = 1;
    this.allService.updateFillInMovement(move_id,fuel_id).subscribe((res:any)=>{
      console.log(res.message);
    })
  }

//for submit initialization
  onSubmit(): void {
    if (this.fillingForm.valid) {
      const formData = new FormData();
  
      // Append form values to formData
      formData.append('vehicle_id', this.fillingForm.get('vehicle_id')?.value);
      formData.append('driver_id', this.fillingForm.get('driver_id')?.value);
      formData.append('filling_date', this.fillingForm.get('filling_date')?.value);
      formData.append('fuel_station_id', this.fillingForm.get('fuel_station_id')?.value);
      formData.append('filling_quantity', this.fillingForm.get('filling_quantity')?.value);
      formData.append('last_km_reading', this.fillingForm.get('last_km_reading')?.value);
      formData.append('entry_by', this.fillingForm.get('entry_by')?.value); // Make sure this is not undefined
     
     
      this.allService.addFilling(formData).subscribe(
        (data: any) => {
          alert(data.message);
           this.updateFillInMovement(this.vehicle_movement_id,data.data.fuel_expenses_id)
          this.defaultFillingData();
          this.fillingForm.reset();
          this.initialization(); // Reset the form after successful submission
          // this.router.navigateByUrl('dashboard/manager/start-veichel-moment');
        },
        (err: any) => {
          alert('Failed to add!!');
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
    this.allService.getFilldata(tDate,fDate,this.entry_by).subscribe((data:any)=>{
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
    filling_amount: ['', Validators.required],
    filling_bill: [null, Validators.required],
    fuel_expenses_id: [this.fuel_expenses_id]
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
    formData.append('filling_amount', this.fillingFormImg.get('filling_amount')?.value);

    const fillingBillFile = this.fillingFormImg.get('filling_bill')?.value;
    if (fillingBillFile) {
      formData.append('filling_bill', fillingBillFile);
    }

    formData.append('fuel_expenses_id', this.fillingFormImg.get('fuel_expenses_id')?.value);

    this.allService.updateFilling(formData,this.fuel_expenses_id).subscribe(
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
  this.allService.getFilldataDefault(this.entry_by).subscribe((data:any)=>{
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

//get vehiclename
getVehicleName(event: Event | { target: { value: string } }): void {
  let vehicleId: number ;

  if (event instanceof Event) {
    vehicleId = +((event.target as HTMLSelectElement).value);
  } else {
    vehicleId = +event.target.value;
  }

  console.log('Selected Vehicle ID:', vehicleId);

  // Find the selected vehicle from the list
  const selectedVehicle = this.all_veichels.find(vehicle => vehicle.vehicle_id === vehicleId);
}






}
