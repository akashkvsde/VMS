import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddServiceStationComponent } from './add-service-station.component';

describe('AddServiceStationComponent', () => {
  let component: AddServiceStationComponent;
  let fixture: ComponentFixture<AddServiceStationComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AddServiceStationComponent]
    });
    fixture = TestBed.createComponent(AddServiceStationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
