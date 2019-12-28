import { Component, OnInit } from '@angular/core';
import { MenuController,AlertController,LoadingController } from '@ionic/angular';
import { Router } from '@angular/router';
import {DataService} from '../service/data.service';
import { HTTP } from '@ionic-native/http/ngx';
@Component({
  selector: 'app-menu',
  templateUrl: './menu.page.html',
  styleUrls: ['./menu.page.scss'],
})
export class MenuPage implements OnInit {
  token:"";
  infoClient:any;
  infoResto:any;
  clientHistory:any;
  formtype = {
    actual_pass:"",
    new_pass:"",
    conf_new_pass:"",
    user:""
  }
  data:any;
  headers: {
    'Content-Type': 'application/json'
  }
  constructor(private menu: MenuController,private route :Router,public dataservice:DataService,public http:HTTP,public alertCtrl:AlertController,public load :LoadingController) {

    this.token=this.dataservice.getToken();
    this.infoClient=this.dataservice.getinfoClient();
    this.formtype.user = this.infoClient.id;
    this.infoResto=this.dataservice.getinfoResto();
    this.clientHistory=this.dataservice.getclientHistory();
    console.log(this.infoClient);
   }

  ngOnInit() {
  }
  openFirst() {
    this.menu.enable(true, 'first');
    this.menu.open('first');
  }
  close_menu(){
    this.menu.close();
  }
  moncompte(){
    this.dataservice.setToken(this.token);
    this.dataservice.setclientHistory(this.clientHistory);
    this.dataservice.setinfoClient(this.infoClient);
    this.dataservice.setinfoResto(this.infoResto);
    this.route.navigate(['/menu']);
  }
  mesresto(){
    this.dataservice.setToken(this.token);
    this.dataservice.setclientHistory(this.clientHistory);
    this.dataservice.setinfoClient(this.infoClient);
    this.dataservice.setinfoResto(this.infoResto);
    this.route.navigate(['/choiceresto']);
  }
  logout(){
    this.route.navigateByUrl('/home', { skipLocationChange: true });
  }
  async change_password(){
    if(this.formtype.actual_pass !="" && this.formtype.new_pass !="" && this.formtype.conf_new_pass !=""){
      if(this.formtype.new_pass != this.formtype.conf_new_pass){
        const alert = await this.alertCtrl.create({
          message: 'Veuillez retaper correctement le nouveau mots de passe',
          buttons: ['OK']
    });
        alert.present();
      }else{
      const loading = await this.load.create({
        message: 'chargement. . .',
        duration: 5000
      });
      await loading.present();
      let postData =  this.formtype;
      this.http.post("http://mobile.api.salesupper.com/api/change_mdp", postData,this.headers)
      .then(async datas => {
        if(datas){
          console.log(datas);
          this.data = datas;
          if(JSON.parse(this.data.data).success == '1' ){
            const alert = await this.alertCtrl.create({
              message: 'Le mots de passe est changé avec succès',
              buttons: ['OK']
        });
            loading.dismiss();
            alert.present();
          }else{
            const alert = await this.alertCtrl.create({
              message: 'Le mots de passe est incorrecte',
              buttons: ['OK']
        });
            loading.dismiss();
            alert.present();
          }
          
        }
       }).catch(async err => {
        console.log(err);
        loading.dismiss();
       const alert = await this.alertCtrl.create({
           message: 'Le mots de passe actuel est incorrecte',
           buttons: ['OK']
     });
     alert.present();
    });
    }
  }else{
    const alert = await this.alertCtrl.create({
      message: 'Veuillez remplir le formulaire correctement',
      buttons: ['OK']
});
alert.present();
  }
}
}
