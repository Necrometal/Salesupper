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
    a : any;
    formtype = {
        login:'',
        password:"",
    }
    httpOptions = {
        headers: new HttpHeaders({
            'Content-Type': 'application/json',
        })
    }
    constructor(
        public actionSheetController: ActionSheetController,public http: HttpClient,private route :Router,public dataservice:DataService
    ) {}

    register(){
        
    }
    login(){
        let postData =  this.formtype;
        this.http.post("http://mobile.api.salesupper.com/api/login", postData, this.httpOptions)
        .subscribe(data => {
            if(data.token){
                this.dataservice.setToken(data.token);
                this.dataservice.setclientHistory(data.clientHistory);
                this.dataservice.setinfoClient(data.infoClient);
                this.dataservice.setinfoResto(data.infoResto);
                this.route.navigate(['/choiceresto']);
                console.log(data)
            }
        }, error => {
            console.log(error);
        });
    }
}
