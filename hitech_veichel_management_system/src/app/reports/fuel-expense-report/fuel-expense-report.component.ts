import { Component, OnInit } from '@angular/core';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-fuel-expense-report',
  templateUrl: './fuel-expense-report.component.html',
  styleUrls: ['./fuel-expense-report.component.css']
})
export class FuelExpenseReportComponent implements OnInit {
  selectedOwner:any;
  selectedReportType: string = '';
  selectedFuelType: string = '';
  selectedFromDate: string = '';
  selectedToDate: string = '';
  organization_id: any;
  expense: any[] = [];
  fuel: any;
  selectedVehicleNo: string = '';
  fdate: string = '';
  tdate: string = '';
  itemsPerPage = 15;
  currentPage = 1;
  noDataMessage: boolean = false;

  totalCalculation: any = {
    total_filling_quantity: 0,
    total_filling_amount: 0
  };

  paginatedData: any[] = [];
  paginatedTotal: any = {
    total_filling_quantity: 0,
    total_filling_amount: 0
  };

  constructor(private service: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo.organizationId;

    this.getSomeFuelExpense();
    this.fetchVehicleNo();
    this.selectOwnerName();
  }

  getSomeFuelExpense() {
    this.service.FuelFillingSomeDta(this.organization_id).subscribe((res: any) => {
      this.expense = res.data;
      this.totalCalculation = res.totals;
      this.filterFuelType(); // Apply filtering and pagination
    });
  }

  fetchVehicleNo() {
    this.service.getVehicles(this.organization_id).subscribe((res: any) => {
      this.fuel = res;
    });
  }
  owners:any;
  selectOwnerName() {
    this.service.fetchOwnerName(this.organization_id).subscribe((res: any) => {
      this.owners = res;
    });
  }

  onOwnerChange(ownerId: string) {
    this.selectedOwner = ownerId;
    this.getDataByOwnerName(); // Fetch data when owner changes
  }

  getDataByOwner(ownerId: string) {
    this.service.getFillingFuelByOwnerName(ownerId).subscribe((res: any) => {
        this.expense = res.data || [];

        // Ensure all quantities and amounts are numbers
        this.expense.forEach(exp => {
            exp.filling_quantity = Number(exp.filling_quantity);
            exp.filling_amount = Number(exp.filling_amount);
        });

        this.totalCalculation = res.totals || { total_filling_quantity: 0, total_filling_amount: 0 };

        this.noDataMessage = this.expense.length === 0;

        this.filterFuelType();
    });
}


  getDataByOwnerName() {
    if (this.selectedOwner) {
      this.getDataByOwner(this.selectedOwner);
    } else {
      this.expense = [];
      this.paginatedData = [];
      this.paginatedTotal = { total_filling_quantity: 0, total_filling_amount: 0 };
      this.noDataMessage = true; // Set no data message when no owner is selected
    }
  }

  filterFuelType() {
    let filteredData = this.expense;

    // Apply fuel type filter if selected
    if (this.selectedFuelType && this.selectedReportType === 'fuelType') {
        filteredData = filteredData.filter(exp => exp.vehicle.vehicle_fuel_type === this.selectedFuelType);
    }

    console.log('Filtered Data:', filteredData); // Log filtered data

    this.paginatedData = this.paginate(filteredData);

    // Calculate totals from filtered data
    this.paginatedTotal.total_filling_quantity = filteredData.reduce(
        (sum, exp) => sum + (Number(exp.filling_quantity) || 0),
        0
    );

    this.paginatedTotal.total_filling_amount = filteredData.reduce(
        (sum, exp) => sum + (Number(exp.filling_amount) || 0),
        0
    );

    // Format the totals to two decimal places
    this.paginatedTotal.total_filling_quantity = parseFloat(this.paginatedTotal.total_filling_quantity.toFixed(2));
    this.paginatedTotal.total_filling_amount = parseFloat(this.paginatedTotal.total_filling_amount.toFixed(2));

    console.log('Calculated Totals:', this.paginatedTotal); // Log calculated totals
}



fetchExpenses() {
  // Reset totals
  this.paginatedTotal = { total_filling_quantity: 0, total_filling_amount: 0 };

  // Fetch data based on selected criteria
  if (this.fdate && this.tdate && this.selectedVehicleNo) {
      this.fuelExpenseByBothVehicleAndDate(this.fdate, this.tdate, this.selectedVehicleNo);
  } else if (this.fdate && this.tdate) {
      this.fetchExpenseByDate(this.fdate, this.tdate);
  } else if (this.selectedVehicleNo) {
      this.fetchExpenseByVehicle(this.selectedVehicleNo);
  } else {
      // If no criteria selected, clear the table
      this.expense = [];
      this.paginatedData = [];
  }
}


  fetchExpenseByDate(fdate: string, tdate: string) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;

    this.service.fuelExpenseByDate(fdate, tdate,this.organization_id).subscribe(
      (res: any) => {
        this.expense = res.data || [];
        this.totalCalculation = res.totals || { total_filling_quantity: 0, total_filling_amount: 0 };
        this.filterFuelType(); // Apply filtering and pagination
      },
      (error) => {
        console.error('Error fetching expenses by date', error);
        this.expense = [];
        this.totalCalculation = { total_filling_quantity: 0, total_filling_amount: 0 };
        this.paginatedData = [];
        this.paginatedTotal = { total_filling_quantity: 0, total_filling_amount: 0 };
      }
    );
  }

  fetchExpenseByVehicle(vehicleId: string) {
    this.service.fuelExpenseByVehicle(vehicleId).subscribe(
        (res: any) => {
            this.expense = res.data || [];
            console.log('Fetched vehicle expense:', this.expense); // Log raw data
            this.totalCalculation = res.totals || { total_filling_quantity: 0, total_filling_amount: 0 };

            this.expense.forEach(exp => {
                exp.filling_quantity = Number(exp.filling_quantity);
                exp.filling_amount = Number(exp.filling_amount);
            });

            this.filterFuelType();
        },
        (error) => {
            console.error('Error fetching expenses by vehicle', error);
            this.expense = [];
            this.totalCalculation = { total_filling_quantity: 0, total_filling_amount: 0 };
            this.paginatedData = [];
            this.paginatedTotal = { total_filling_quantity: 0, total_filling_amount: 0 };
        }
    );
}



  fuelExpenseByBothVehicleAndDate(fdate: string, tdate: string, vehicleNo: string) {
    this.selectedFromDate = fdate;
    this.selectedToDate = tdate;

    this.service.fuelExpenseByBothVehicleAndDate(fdate, tdate, vehicleNo).subscribe(
      (res: any) => {
        this.expense = res.data || [];
        this.totalCalculation = res.totals || { total_filling_quantity: 0, total_filling_amount: 0 };
        this.filterFuelType(); // Apply filtering and pagination
      },
      (error) => {
        console.error('Error fetching expenses by date and vehicle', error);
        this.expense = [];
        this.totalCalculation = { total_filling_quantity: 0, total_filling_amount: 0 };
        this.paginatedData = [];
        this.paginatedTotal = { total_filling_quantity: 0, total_filling_amount: 0 };
      }
    );
  }

  printDiv() {
    window.print();
  }

  paginate(data: any[]) {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    const end = start + this.itemsPerPage;
    return data.slice(start, end);
  }

  nextPage() {
    if (this.currentPage * this.itemsPerPage < this.expense.length) {
      this.currentPage++;
      this.filterFuelType(); // Reapply filters after page change
    }
  }

  prevPage() {
    if (this.currentPage > 1) {
      this.currentPage--;
      this.filterFuelType(); // Reapply filters after page change
    }
  }

  goToPage(pageNumber: number) {
    this.currentPage = pageNumber;
    this.filterFuelType(); // Reapply filters after page change
  }

  calculatePaginatedTotals() {
    const paginated = this.paginatedData;

    // Calculate the total filling quantity and amount
    const totalFillingQuantity = paginated.reduce(
        (sum, item) => sum + parseFloat(item.filling_quantity || '0'),
        0
    );

    const totalFillingAmount = paginated.reduce(
        (sum, item) => sum + parseFloat(item.filling_amount || '0'),
        0
    );

    // Format the totals to two decimal places
    this.paginatedTotal.total_filling_quantity = parseFloat(totalFillingQuantity.toFixed(2));
    this.paginatedTotal.total_filling_amount = parseFloat(totalFillingAmount.toFixed(2));
}
}
