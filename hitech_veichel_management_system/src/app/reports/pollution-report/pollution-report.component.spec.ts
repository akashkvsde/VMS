import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PollutionReportComponent } from './pollution-report.component';

describe('PollutionReportComponent', () => {
  let component: PollutionReportComponent;
  let fixture: ComponentFixture<PollutionReportComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [PollutionReportComponent]
    });
    fixture = TestBed.createComponent(PollutionReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
