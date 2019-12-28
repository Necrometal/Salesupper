import { Component, OnInit } from '@angular/core';
import {MenuController,LoadingController } from '@ionic/angular';
import { Router,ActivatedRoute} from '@angular/router';
import {DataService} from '../service/data.service';
import { HTTP } from '@ionic-native/http/ngx';

@Component({
  selector: 'app-detailfacture',
  templateUrl: './detailfacture.page.html',
  styleUrls: ['./detailfacture.page.scss'],
})
export class DetailfacturePage implements OnInit {
  token:"";
  infoClient:any;
  infoResto:any;
  id_resto:any;
  infovisit:any;
  restoName:any;
  visithistories:any;
  id_histo:any;
  detail_to_show:any;
  data:any;
  clientHistory:any;
  headers: {
    'Content-Type': 'application/json'
  }
  constructor(public http: HTTP,private route :Router,public dataservice:DataService,private getid:ActivatedRoute,public menu:MenuController,public load : LoadingController) { 
    this.token=this.dataservice.getToken();
    this.infoClient=this.dataservice.getinfoClient();
    this.infoResto=this.dataservice.getinfoResto();
    this.clientHistory=this.dataservice.getclientHistory();
    this.dataservice.setToken(this.token);
    this.dataservice.setclientHistory(this.clientHistory);
    this.dataservice.setinfoClient(this.infoClient);
    this.dataservice.setinfoResto(this.infoResto);
   this.id_histo = this.getid.snapshot.paramMap.get('id');
   console.log("dfdf")
   console.log(this.id_histo);
   console.log("dfdf")
  }

  async ngOnInit() {
    const loading = await this.load.create({
      message: 'chargement. . .',
      duration: 5000
    });
    await loading.present();
    let tosend = {
      id_histo:this.id_histo
    }
    let postData = tosend;
    this.http.post("http://mobile.api.salesupper.com/api/get_history_details", postData,this.headers)
    .then(datas => {
      if(datas){
        this.data =datas;
        this.data = JSON.parse(this.data.data);
        loading.dismiss();
        this.detail_to_show =this.data.historical;
      }
    }).catch(async err => {
      console.log(err);
      loading.dismiss();
    });
  }

  openFirst() {
    this.menu.enable(true, 'firstdetfact');
    this.menu.open('firstdetfact');
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
}
