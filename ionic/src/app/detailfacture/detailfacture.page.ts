import { Component, OnInit,Input } from '@angular/core';
import { ActionSheetController,NavController,MenuController,LoadingController } from '@ionic/angular';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import { Router,ActivatedRoute} from '@angular/router';
import {DataService} from '../service/data.service';
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
  constructor(public actionSheetController: ActionSheetController,public http: HttpClient,private route :Router,public dataservice:DataService,private getid:ActivatedRoute,public menu:MenuController,public load : LoadingController) { 
    this.token=this.dataservice.getToken();
    this.infoClient=this.dataservice.getinfoClient();
    this.infoResto=this.dataservice.getinfoResto();
    // this.dataservice.setToken(this.token);
    // this.dataservice.setclientHistory(this.clientHistory);
    // this.dataservice.setinfoClient(this.infoClient);
    // this.dataservice.setinfoResto(this.infoResto);
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
    let httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'Authorization': 'Bearer '+this.token,
      })
    }
    let tosend = {
      id_histo:this.id_histo
    }
    let postData = tosend;
    this.http.post("http://mobile.api.salesupper.com/api/get_history_details", postData,httpOptions)
    .subscribe(data => {
      if(data){
        loading.dismiss();
        this.detail_to_show =data.historical;
        console.log(data);
      }
     }, error => {
      loading.dismiss();
      console.log(error);
    });
  }
  openFirst() {
    this.menu.enable(true, 'firstdetfact');
    this.menu.open('firstdetfact');
  }
  close_menu(){
    this.menu.close();
  }
}
