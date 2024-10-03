import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DriverFuelExpenseComponent } from './driver-fuel-expense.component';

describe('DriverFuelExpenseComponent', () => {
  let component: DriverFuelExpenseComponent;
  let fixture: ComponentFixture<DriverFuelExpenseComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DriverFuelExpenseComponent]
    });
    fixture = TestBed.createComponent(DriverFuelExpenseComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
