import { Component, OnInit } from '@angular/core';
import {MenuController,LoadingController } from '@ionic/angular';
import { HTTP } from '@ionic-native/http/ngx';
import { Router,ActivatedRoute} from '@angular/router';
import {DataService} from '../service/data.service';
@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.page.html',
  styleUrls: ['./dashboard.page.scss'],
})
export class DashboardPage implements OnInit {
  token:"";
  infoClient:any;
  infoResto:any;
  clientHistory:any;
  id_resto:any;
  formtype = {
    id_resto:'',
    id_client:'',
  }
  infovisit:any;
  restoName:any;
  nb_passage:any;
  visithistories:any;
  headers: {
    'Content-Type': 'application/json'
  }
  data:any;
  constructor(public http: HTTP,private route :Router,public dataservice:DataService,private getid:ActivatedRoute,public menu: MenuController,public load : LoadingController) {
    this.token=this.dataservice.getToken();
    this.infoClient=this.dataservice.getinfoClient();
    this.infoResto=this.dataservice.getinfoResto();
    this.clientHistory=this.dataservice.getclientHistory();
    // this.dataservice.setToken(this.token);
    // this.dataservice.setclientHistory(this.clientHistory);
    // this.dataservice.setinfoClient(this.infoClient);
    // this.dataservice.setinfoResto(this.infoResto);
   this.formtype.id_resto = this.getid.snapshot.paramMap.get('id');
   this.formtype.id_client = this.infoClient.id;
   console.log("dfdf")
   console.log(this.infoResto);
   console.log("dfdf")
   }
  async ngOnInit() {
    const loading = await this.load.create({
      message: 'chargement. . .',
      duration: 5000
    });
    await loading.present();
    let postData =  this.formtype;
    this.http.post("http://mobile.api.salesupper.com/api/get_history_client", postData,this.headers)
    .then(datas => {
      if(datas){
        this.data = datas;
        console.log(this.data);
        this.data = JSON.parse(this.data.data);
        this.infovisit = this.data;
        this.restoName = this.infovisit.inforestone.name;
        this.nb_passage = this.infovisit.fidelityCard.nb_passage;
        this.visithistories = this.infovisit.clientVisitHistories;
        loading.dismiss();
      }
    }).catch(async err => {
      console.log(err);
      loading.dismiss();
  });
}
  async view_details(data) {
    this.dataservice.setToken(this.token);
    this.dataservice.setclientHistory(this.clientHistory);
    this.dataservice.setinfoClient(this.infoClient);
    this.dataservice.setinfoResto(this.infoResto);
    this.route.navigate(['/detailfacture/', +data]);
  }
  openFirst() {
    this.menu.enable(true, 'firstdash');
    this.menu.open('firstdash');
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
