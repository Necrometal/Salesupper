import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { HistoryclientPageRoutingModule } from './historyclient-routing.module';

import { HistoryclientPage } from './historyclient.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    HistoryclientPageRoutingModule
  ],
  declarations: [HistoryclientPage]
})
export class HistoryclientPageModule {}
