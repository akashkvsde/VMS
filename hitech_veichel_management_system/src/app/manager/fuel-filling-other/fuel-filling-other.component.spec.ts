import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FuelFillingOtherComponent } from './fuel-filling-other.component';

describe('FuelFillingOtherComponent', () => {
  let component: FuelFillingOtherComponent;
  let fixture: ComponentFixture<FuelFillingOtherComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [FuelFillingOtherComponent]
    });
    fixture = TestBed.createComponent(FuelFillingOtherComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
