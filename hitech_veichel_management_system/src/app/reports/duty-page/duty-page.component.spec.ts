import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DutyPageComponent } from './duty-page.component';

describe('DutyPageComponent', () => {
  let component: DutyPageComponent;
  let fixture: ComponentFixture<DutyPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DutyPageComponent]
    });
    fixture = TestBed.createComponent(DutyPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
