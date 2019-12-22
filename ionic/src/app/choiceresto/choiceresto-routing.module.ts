import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ChoicerestoPage } from './choiceresto.page';

const routes: Routes = [
  {
    path: '',
    component: ChoicerestoPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ChoicerestoPageRoutingModule {}
