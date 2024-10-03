import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddPollutionComponent } from './add-pollution.component';

describe('AddPollutionComponent', () => {
  let component: AddPollutionComponent;
  let fixture: ComponentFixture<AddPollutionComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddPollutionComponent]
    });
    fixture = TestBed.createComponent(AddPollutionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
