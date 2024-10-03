import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddVeichelMaintainComponent } from './add-veichel-maintain.component';

describe('AddVeichelMaintainComponent', () => {
  let component: AddVeichelMaintainComponent;
  let fixture: ComponentFixture<AddVeichelMaintainComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddVeichelMaintainComponent]
    });
    fixture = TestBed.createComponent(AddVeichelMaintainComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
