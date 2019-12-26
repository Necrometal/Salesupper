import { Component } from '@angular/core';
import { ActionSheetController,LoadingController,AlertController  } from '@ionic/angular';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import {DataService} from '../service/data.service';
import { from } from 'rxjs';
@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
})
export class HomePage { 
  formtype = {
    login:'',
    password:"",
  }
  httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'application/json',
    })
  }
  constructor(public actionSheetController: ActionSheetController,public http: HttpClient,private route :Router,public dataservice:DataService,public load : LoadingController,private alertCtrl: AlertController) {}

  register(){
    
  }
  async login(){
    const loading = await this.load.create({
      message: 'chargement. . .',
      duration: 5000
    });
    await loading.present();
    let postData =  this.formtype;
    this.http.post("http://mobile.api.salesupper.com/api/login", postData, this.httpOptions)
    .subscribe(data => {
      if(data.token){
        this.dataservice.setToken(data.token);
        this.dataservice.setclientHistory(data.clientHistory);
        this.dataservice.setinfoClient(data.infoClient);
        this.dataservice.setinfoResto(data.infoResto);
        loading.dismiss();
        this.route.navigate(['/menu']);
        console.log(data)
      }
     }, async error => {
      loading.dismiss();
     const alert = await this.alertCtrl.create({
         message: 'Login ou mot de passe invalide ou verifier votre connection internet',
         buttons: ['OK']
      });
      await alert.present();
      console.log(error);
    });
  }
}
