import { Component, OnInit } from '@angular/core';
import { MenuController } from '@ionic/angular';
import { Router } from '@angular/router';
import {DataService} from '../service/data.service';
import { from } from 'rxjs';
import { HTTP } from '@ionic-native/http/ngx';

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
  constructor(private menu: MenuController,public http: HTTP,private route :Router,public dataservice:DataService) {
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
  openFirst() {
    this.menu.enable(true, 'firstch');
    this.menu.open('firstch');
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
