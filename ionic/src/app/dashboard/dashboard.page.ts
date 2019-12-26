import { Component, OnInit } from '@angular/core';
import { ActionSheetController,NavController,MenuController,LoadingController } from '@ionic/angular';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import { Router,ActivatedRoute} from '@angular/router';
import {DataService} from '../service/data.service';
import { from } from 'rxjs';
import { DetailfacturePage } from '../detailfacture/detailfacture.page';
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
  constructor(public actionSheetController: ActionSheetController,public http: HttpClient,private route :Router,public dataservice:DataService,private getid:ActivatedRoute,public menu: MenuController,public load : LoadingController) {
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
   let httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'Authorization': 'Bearer '+this.token,
      })
    }
    let postData =  this.formtype;
    this.http.post("http://mobile.api.salesupper.com/api/get_history_client", postData,httpOptions)
    .subscribe(data => {
      if(data){
        console.log(data);
        this.infovisit = data;
        this.restoName = this.infovisit.inforestone.name;
        this.nb_passage = this.infovisit.fidelityCard.nb_passage;
        this.visithistories = this.infovisit.clientVisitHistories;
        loading.dismiss();
      }
     }, error => {
      loading.dismiss();
      console.log(error);
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
}
