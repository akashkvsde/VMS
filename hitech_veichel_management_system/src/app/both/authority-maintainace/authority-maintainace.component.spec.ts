import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AuthorityMaintainaceComponent } from './authority-maintainace.component';

describe('AuthorityMaintainaceComponent', () => {
  let component: AuthorityMaintainaceComponent;
  let fixture: ComponentFixture<AuthorityMaintainaceComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AuthorityMaintainaceComponent]
    });
    fixture = TestBed.createComponent(AuthorityMaintainaceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
