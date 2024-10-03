import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VeichelMaintainReportComponent } from './veichel-maintain-report.component';

describe('VeichelMaintainReportComponent', () => {
  let component: VeichelMaintainReportComponent;
  let fixture: ComponentFixture<VeichelMaintainReportComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VeichelMaintainReportComponent]
    });
    fixture = TestBed.createComponent(VeichelMaintainReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
