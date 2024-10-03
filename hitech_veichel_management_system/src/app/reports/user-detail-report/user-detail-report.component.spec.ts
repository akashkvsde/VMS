import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UserDetailReportComponent } from './user-detail-report.component';

describe('UserDetailReportComponent', () => {
  let component: UserDetailReportComponent;
  let fixture: ComponentFixture<UserDetailReportComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [UserDetailReportComponent]
    });
    fixture = TestBed.createComponent(UserDetailReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
