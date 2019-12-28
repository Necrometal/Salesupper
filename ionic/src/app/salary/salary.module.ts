import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { SalaryPageRoutingModule } from './salary-routing.module';

import { SalaryPage } from './salary.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    SalaryPageRoutingModule
  ],
  declarations: [SalaryPage]
})
export class SalaryPageModule {}
