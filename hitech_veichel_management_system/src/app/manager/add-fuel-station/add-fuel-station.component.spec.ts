import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddFuelStationComponent } from './add-fuel-station.component';

describe('AddFuelStationComponent', () => {
  let component: AddFuelStationComponent;
  let fixture: ComponentFixture<AddFuelStationComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddFuelStationComponent]
    });
    fixture = TestBed.createComponent(AddFuelStationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
