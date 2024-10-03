import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MyAttendanceDetailComponent } from './my-attendance-detail.component';

describe('MyAttendanceDetailComponent', () => {
  let component: MyAttendanceDetailComponent;
  let fixture: ComponentFixture<MyAttendanceDetailComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [MyAttendanceDetailComponent]
    });
    fixture = TestBed.createComponent(MyAttendanceDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
