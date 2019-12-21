import { Component } from '@angular/core';
import { ActionSheetController } from '@ionic/angular';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
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
  httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'application/json',
    })
  }
  constructor(public actionSheetController: ActionSheetController,public http: HttpClient,private route :Router) {}

  register(){
    
  }
  login(){
    let postData =  this.formtype;
    this.http.post("http://mobile.api.salesupper.com/api/login", postData, this.httpOptions)
    .subscribe(data => {
      if(data.success.token){

      }
      this.route.navigate(['/dashboard'])
     }, error => {
      console.log(error);
    });
  }
}
