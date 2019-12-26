import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { IonicModule } from '@ionic/angular';

import { DetailfacturePage } from './detailfacture.page';

describe('DetailfacturePage', () => {
  let component: DetailfacturePage;
  let fixture: ComponentFixture<DetailfacturePage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DetailfacturePage ],
      imports: [IonicModule.forRoot()]
    }).compileComponents();

    fixture = TestBed.createComponent(DetailfacturePage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
