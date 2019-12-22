import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { IonicModule } from '@ionic/angular';

import { HistoryclientPage } from './historyclient.page';

describe('HistoryclientPage', () => {
  let component: HistoryclientPage;
  let fixture: ComponentFixture<HistoryclientPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ HistoryclientPage ],
      imports: [IonicModule.forRoot()]
    }).compileComponents();

    fixture = TestBed.createComponent(HistoryclientPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
