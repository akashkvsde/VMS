import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManagementDStMovementComponent } from './management-d-st-movement.component';

describe('ManagementDStMovementComponent', () => {
  let component: ManagementDStMovementComponent;
  let fixture: ComponentFixture<ManagementDStMovementComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ManagementDStMovementComponent]
    });
    fixture = TestBed.createComponent(ManagementDStMovementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
