import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { DetailfacturePageRoutingModule } from './detailfacture-routing.module';

import { DetailfacturePage } from './detailfacture.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    DetailfacturePageRoutingModule
  ],
  declarations: [DetailfacturePage]
})
export class DetailfacturePageModule {}
