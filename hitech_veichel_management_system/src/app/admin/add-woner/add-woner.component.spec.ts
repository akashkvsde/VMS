import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddWonerComponent } from './add-woner.component';

describe('AddWonerComponent', () => {
  let component: AddWonerComponent;
  let fixture: ComponentFixture<AddWonerComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddWonerComponent]
    });
    fixture = TestBed.createComponent(AddWonerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
