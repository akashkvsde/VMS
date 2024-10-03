import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VeichelMomentEndComponent } from './veichel-moment-end.component';

describe('VeichelMomentEndComponent', () => {
  let component: VeichelMomentEndComponent;
  let fixture: ComponentFixture<VeichelMomentEndComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VeichelMomentEndComponent]
    });
    fixture = TestBed.createComponent(VeichelMomentEndComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
