import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AssignNavigationComponent } from './assign-navigation.component';

describe('AssignNavigationComponent', () => {
  let component: AssignNavigationComponent;
  let fixture: ComponentFixture<AssignNavigationComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AssignNavigationComponent]
    });
    fixture = TestBed.createComponent(AssignNavigationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
