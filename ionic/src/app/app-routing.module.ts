import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
const routes: Routes = [
  { path: '', redirectTo: 'home', pathMatch: 'full' },
  { path: 'home', loadChildren: () => import('./home/home.module').then( m => m.HomePageModule)},
  {
    path: 'dashboard/:id',
    loadChildren: () => import('./dashboard/dashboard.module').then( m => m.DashboardPageModule)
  },
  {
    path: 'choiceresto',
    loadChildren: () => import('./choiceresto/choiceresto.module').then( m => m.ChoicerestoPageModule)
  },
  {
    path: 'historyclient',
    loadChildren: () => import('./historyclient/historyclient.module').then( m => m.HistoryclientPageModule)
  },
  {
    path: 'menu',
    loadChildren: () => import('./menu/menu.module').then( m => m.MenuPageModule)
  },
  {
    path: 'detailfacture/:id',
    loadChildren: () => import('./detailfacture/detailfacture.module').then( m => m.DetailfacturePageModule)
  },
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
