import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { DetailfacturePage } from './detailfacture.page';

const routes: Routes = [
  {
    path: '',
    component: DetailfacturePage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class DetailfacturePageRoutingModule {}
