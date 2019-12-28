import { Component } from '@angular/core';
import { ActionSheetController,LoadingController,AlertController  } from '@ionic/angular';
import { Router } from '@angular/router';
import {DataService} from '../service/data.service';
import { HTTP } from '@ionic-native/http/ngx';
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

  headers: {
    'Content-Type': 'application/json'
  }
  data:any
  constructor(public actionSheetController: ActionSheetController,public http: HTTP,private route :Router,public dataservice:DataService,public load : LoadingController,private alertCtrl: AlertController) {}

  register(){
    
  }
  async login(){
    const loading = await this.load.create({
      message: 'chargement. . .',
      duration: 5000
    });
    await loading.present();
    let postData =  this.formtype;
    this.http.post("http://mobile.api.salesupper.com/api/login", postData,this.headers)
    .then(datas => {
      if(datas){
        this.data = datas;
        console.log(this.data.data);
        this.data = JSON.parse(this.data.data);
        this.dataservice.setToken(this.data.token);
        this.dataservice.setclientHistory(this.data.clientHistory);
        this.dataservice.setinfoClient(this.data.infoClient);
        this.dataservice.setinfoResto(this.data.infoResto);
        loading.dismiss();
        this.route.navigate(['/menu']);
      }
     }).catch(async err => {
      console.log(err);
      loading.dismiss();
     const alert = await this.alertCtrl.create({
         message: 'Login ou mot de passe invalide ou verifier votre connection internet',
         buttons: ['OK']
   });
   alert.present();
  });
}
}
