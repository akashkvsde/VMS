import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OverdutyComponent } from './overduty.component';

describe('OverdutyComponent', () => {
  let component: OverdutyComponent;
  let fixture: ComponentFixture<OverdutyComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [OverdutyComponent]
    });
    fixture = TestBed.createComponent(OverdutyComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
