import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UnofficialFuelExpenseComponent } from './unofficial-fuel-expense.component';

describe('UnofficialFuelExpenseComponent', () => {
  let component: UnofficialFuelExpenseComponent;
  let fixture: ComponentFixture<UnofficialFuelExpenseComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [UnofficialFuelExpenseComponent]
    });
    fixture = TestBed.createComponent(UnofficialFuelExpenseComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
