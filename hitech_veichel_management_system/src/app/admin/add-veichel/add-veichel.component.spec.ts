import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddVeichelComponent } from './add-veichel.component';

describe('AddVeichelComponent', () => {
  let component: AddVeichelComponent;
  let fixture: ComponentFixture<AddVeichelComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddVeichelComponent]
    });
    fixture = TestBed.createComponent(AddVeichelComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
