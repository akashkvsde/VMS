import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManagementDEndMovementComponent } from './management-d-end-movement.component';

describe('ManagementDEndMovementComponent', () => {
  let component: ManagementDEndMovementComponent;
  let fixture: ComponentFixture<ManagementDEndMovementComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ManagementDEndMovementComponent]
    });
    fixture = TestBed.createComponent(ManagementDEndMovementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
