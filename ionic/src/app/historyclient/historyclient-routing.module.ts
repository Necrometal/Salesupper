import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { HistoryclientPage } from './historyclient.page';

const routes: Routes = [
  {
    path: '',
    component: HistoryclientPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class HistoryclientPageRoutingModule {}
