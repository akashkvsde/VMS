import { ComponentFixture, TestBed } from '@angular/core/testing';

import { StartVeichelMomentComponent } from './start-veichel-moment.component';

describe('StartVeichelMomentComponent', () => {
  let component: StartVeichelMomentComponent;
  let fixture: ComponentFixture<StartVeichelMomentComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [StartVeichelMomentComponent]
    });
    fixture = TestBed.createComponent(StartVeichelMomentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
