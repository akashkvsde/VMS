import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FillingReportComponent } from './filling-report.component';

describe('FillingReportComponent', () => {
  let component: FillingReportComponent;
  let fixture: ComponentFixture<FillingReportComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [FillingReportComponent]
    });
    fixture = TestBed.createComponent(FillingReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
