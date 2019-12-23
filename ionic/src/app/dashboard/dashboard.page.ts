import { Component, OnInit } from '@angular/core';
import { ActionSheetController,NavController } from '@ionic/angular';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import { Router,ActivatedRoute} from '@angular/router';
import {DataService} from '../service/data.service';
import { from } from 'rxjs';
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
 
  constructor(public actionSheetController: ActionSheetController,public http: HttpClient,private route :Router,public dataservice:DataService,private getid:ActivatedRoute) {
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
   console.log(this.infoClient.id);
   console.log("dfdf")
   }
  ngOnInit() {
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
      }
     }, error => {
      console.log(error);
    });
  }

}
