import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VehicleMovementReportComponent } from './vehicle-movement-report.component';

describe('VehicleMovementReportComponent', () => {
  let component: VehicleMovementReportComponent;
  let fixture: ComponentFixture<VehicleMovementReportComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VehicleMovementReportComponent]
    });
    fixture = TestBed.createComponent(VehicleMovementReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
