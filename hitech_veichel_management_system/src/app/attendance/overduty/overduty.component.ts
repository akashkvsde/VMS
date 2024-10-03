import { Component } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-overduty',
  templateUrl: './overduty.component.html',
  styleUrls: ['./overduty.component.css']
})
export class OverdutyComponent {
  organization_id: any;
  entry_by: any;
  all_drivers: any[] = [];
  movementForm!: FormGroup;
  get_over_time_data: any[] = [];
  currentPage = 1; // Current page number for pagination
  itemsPerPage = 5; // Items per page for pagination
  totalPages: number = 0;
  today: string = '';
  sevenDaysBeforeFormatted: string = '';
  message: string = '';

  constructor(
    private allService: VeichelserviceService, 
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit() {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId;
    this.entry_by = userInfo?.userId;
    const currentDate = new Date();
    this.today = currentDate.toISOString().split('T')[0]; // Format today's date as 'YYYY-MM-DD'

    // Get the date 7 days before today
    const sevenDaysBefore = new Date(currentDate);
    sevenDaysBefore.setDate(currentDate.getDate() - 7);
    this.sevenDaysBeforeFormatted = sevenDaysBefore.toISOString().split('T')[0]; // Format it as 'YYYY-MM-DD'

    // Fetch OT data for the date range
    this.getOtdata(this.sevenDaysBeforeFormatted, this.today);
    this.allDrivers();
    this.initialization();

    setInterval(() => {
      this.allDrivers();
    }, 50000);
  }

  initialization() {
    const today = new Date().toISOString().split('T')[0]; // Get today's date in 'YYYY-MM-dd' format
    this.movementForm = this.fb.group({
      driver_id: [null, Validators.required],
      start_date: [today, Validators.required], // Set default value to today's date
      entry_by: [this.entry_by],
    });
  }

  // Fetch all driver data
  allDrivers() {
    this.allService.fetchAllDriver(this.organization_id).subscribe(
      (data: any) => {
        this.all_drivers = data;
      },
      (err: any) => {
        console.log(err);
      }
    );
  }

  // Submit function for form submission
  onSubmit(): void {
    if (this.movementForm.valid) {
      console.log(this.movementForm.value);

      // Send form data to the service
      this.allService.addOt(this.movementForm.value).subscribe(
        (response: any) => {
          console.log('Form submitted successfully:', response);
          alert(response.message);

          // Re-fetch driver data and OT data
          this.allDrivers();
          this.getOtdata(
            this.from_date || this.sevenDaysBeforeFormatted, 
            this.to_date || this.today
          );

          // Reset the form after successful submission
          this.movementForm.reset();
          this.initialization();
        },
        error => {
          console.error('Error submitting form:', error);
        }
      );
    } else {
      this.movementForm.markAllAsTouched(); // Trigger validation messages
    }
  }

  // Fetch OT data
  from_date: any;
  to_date: any;
  getOtdata(from: any, to: any) {
    this.from_date = from;
    this.to_date = to;
    this.allService.getOtdata(this.entry_by, from, to).subscribe(
      (data: any) => {
        this.get_over_time_data = data.data;
        this.totalPages = Math.ceil(this.get_over_time_data.length / this.itemsPerPage); // Update total pages
        this.message = this.get_over_time_data.length ? '' : 'No data available';
      },
      (err: any) => {
        console.log(err);
        this.message = 'No data available';
      }
    );
  }

  // Pagination: Get paginated OT data
  getPaginatedData() {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    return this.get_over_time_data.slice(startIndex, startIndex + this.itemsPerPage);
  }

  // Pagination: Go to next page
  goToNextPage() {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
    }
  }

  // Pagination: Go to previous page
  goToPreviousPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
    }
  }
}
