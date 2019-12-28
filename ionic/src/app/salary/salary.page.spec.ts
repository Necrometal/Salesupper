import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { IonicModule } from '@ionic/angular';

import { SalaryPage } from './salary.page';

describe('SalaryPage', () => {
  let component: SalaryPage;
  let fixture: ComponentFixture<SalaryPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SalaryPage ],
      imports: [IonicModule.forRoot()]
    }).compileComponents();

    fixture = TestBed.createComponent(SalaryPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
