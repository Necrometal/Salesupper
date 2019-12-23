import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { IonicModule } from '@ionic/angular';

import { ChoicerestoPage } from './choiceresto.page';

describe('ChoicerestoPage', () => {
  let component: ChoicerestoPage;
  let fixture: ComponentFixture<ChoicerestoPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ChoicerestoPage ],
      imports: [IonicModule.forRoot()]
    }).compileComponents();

    fixture = TestBed.createComponent(ChoicerestoPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
