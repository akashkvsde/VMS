import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EndMovementManagerComponent } from './end-movement-manager.component';

describe('EndMovementManagerComponent', () => {
  let component: EndMovementManagerComponent;
  let fixture: ComponentFixture<EndMovementManagerComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [EndMovementManagerComponent]
    });
    fixture = TestBed.createComponent(EndMovementManagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
