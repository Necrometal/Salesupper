import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class DataService {
  token:"";
  infoClient:any;
  infoResto:any;
  clientHistory:any;
  constructor() { }

 setToken(data) {
   this.token = data;
  }
  getToken(){
    return this.token;
  }



  setinfoClient(data){
    this.infoClient =data;
  }
  getinfoClient(){
  return this.infoClient;
  }

  setinfoResto(data){
    this.infoResto = data;
  }
  getinfoResto(){
    return this.infoResto;
  }



  setclientHistory(data){
    this.clientHistory = data;
  }
  getclientHistory(){
    return this.clientHistory;
  }

}
