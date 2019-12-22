import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ChoicerestoPageRoutingModule } from './choiceresto-routing.module';

import { ChoicerestoPage } from './choiceresto.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ChoicerestoPageRoutingModule
  ],
  declarations: [ChoicerestoPage]
})
export class ChoicerestoPageModule {}
