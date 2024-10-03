import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MaintainVeichelStatusComponent } from './maintain-veichel-status.component';

describe('MaintainVeichelStatusComponent', () => {
  let component: MaintainVeichelStatusComponent;
  let fixture: ComponentFixture<MaintainVeichelStatusComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [MaintainVeichelStatusComponent]
    });
    fixture = TestBed.createComponent(MaintainVeichelStatusComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
