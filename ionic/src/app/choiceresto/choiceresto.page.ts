import { Component, OnInit } from '@angular/core';
import { ActionSheetController } from '@ionic/angular';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import {DataService} from '../service/data.service';
import { from } from 'rxjs';
@Component({
  selector: 'app-choiceresto',
  templateUrl: './choiceresto.page.html',
  styleUrls: ['./choiceresto.page.scss'],
})
export class ChoicerestoPage implements OnInit {

  token:"";
  infoClient:any;
  infoResto:any;
  clientHistory:any;
  constructor(public actionSheetController: ActionSheetController,public http: HttpClient,private route :Router,public dataservice:DataService) {
    this.token=this.dataservice.getToken();
    this.infoClient=this.dataservice.getinfoClient();
    this.infoResto=this.dataservice.getinfoResto();
    this.clientHistory=this.dataservice.getclientHistory();
    console.log(this.infoClient);
   }

  ngOnInit() {
  }
  viewhistory(id_resto){
    this.dataservice.setToken(this.token);
    this.dataservice.setclientHistory(this.clientHistory);
    this.dataservice.setinfoClient(this.infoClient);
    this.dataservice.setinfoResto(this.infoResto);
    this.route.navigate(['/dashboard/', +id_resto]);
  }
}
