import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DriverMaintainaceComponent } from './driver-maintainace.component';

describe('DriverMaintainaceComponent', () => {
  let component: DriverMaintainaceComponent;
  let fixture: ComponentFixture<DriverMaintainaceComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DriverMaintainaceComponent]
    });
    fixture = TestBed.createComponent(DriverMaintainaceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
