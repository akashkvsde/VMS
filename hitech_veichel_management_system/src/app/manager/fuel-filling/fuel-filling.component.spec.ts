import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FuelFillingComponent } from './fuel-filling.component';

describe('FuelFillingComponent', () => {
  let component: FuelFillingComponent;
  let fixture: ComponentFixture<FuelFillingComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [FuelFillingComponent]
    });
    fixture = TestBed.createComponent(FuelFillingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
