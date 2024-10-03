import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VeichelDetailReportComponent } from './veichel-detail-report.component';

describe('VeichelDetailReportComponent', () => {
  let component: VeichelDetailReportComponent;
  let fixture: ComponentFixture<VeichelDetailReportComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VeichelDetailReportComponent]
    });
    fixture = TestBed.createComponent(VeichelDetailReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
