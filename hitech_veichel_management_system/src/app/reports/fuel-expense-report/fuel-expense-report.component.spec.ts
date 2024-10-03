import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FuelExpenseReportComponent } from './fuel-expense-report.component';

describe('FuelExpenseReportComponent', () => {
  let component: FuelExpenseReportComponent;
  let fixture: ComponentFixture<FuelExpenseReportComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [FuelExpenseReportComponent]
    });
    fixture = TestBed.createComponent(FuelExpenseReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
