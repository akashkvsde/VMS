import { TestBed } from '@angular/core/testing';

import { VeichelserviceService } from './veichelservice.service';

describe('VeichelserviceService', () => {
  let service: VeichelserviceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(VeichelserviceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
