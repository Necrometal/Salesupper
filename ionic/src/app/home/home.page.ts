import { Component } from '@angular/core';
import { ActionSheetController } from '@ionic/angular';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
})
export class HomePage {
  formtype = {
    tel:'',
    password:"",
  }
  constructor(public actionSheetController: ActionSheetController) {}

  register(){
    console.log(this.formtype.tel)
    console.log(this.formtype.password)
  }
}
