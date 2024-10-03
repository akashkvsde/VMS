import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VeichelMaintainStatusComponent } from './veichel-maintain-status.component';

describe('VeichelMaintainStatusComponent', () => {
  let component: VeichelMaintainStatusComponent;
  let fixture: ComponentFixture<VeichelMaintainStatusComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VeichelMaintainStatusComponent]
    });
    fixture = TestBed.createComponent(VeichelMaintainStatusComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
