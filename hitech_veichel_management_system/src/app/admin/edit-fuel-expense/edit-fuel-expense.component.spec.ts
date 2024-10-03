import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EditFuelExpenseComponent } from './edit-fuel-expense.component';

describe('EditFuelExpenseComponent', () => {
  let component: EditFuelExpenseComponent;
  let fixture: ComponentFixture<EditFuelExpenseComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [EditFuelExpenseComponent]
    });
    fixture = TestBed.createComponent(EditFuelExpenseComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
