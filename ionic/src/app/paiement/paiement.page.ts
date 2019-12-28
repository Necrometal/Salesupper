import { Component, OnInit } from '@angular/core';
import {MenuController,LoadingController } from '@ionic/angular';
import { HTTP } from '@ionic-native/http/ngx';
import { Router,ActivatedRoute} from '@angular/router';
import {DataService} from '../service/data.service';

@Component({
  selector: 'app-paiement',
  templateUrl: './paiement.page.html',
  styleUrls: ['./paiement.page.scss'],
})
export class PaiementPage implements OnInit {
	token:"";
	infoClient:any;
	infoResto:any;
	id_resto:any;
	paiement: any;
	formtype = {
	    id_resto:'',
	    id_client:'',
	}

	formtype_action = {
	    id: '',
	    action: '',
	}

	infovisit:any;
	restoName:any;
	restoId: any;

	headers: {
	    'Content-Type': 'application/json'
	}
	data:any;

	constructor(public http: HTTP,private route :Router,public dataservice:DataService,private getid:ActivatedRoute,public menu: MenuController,public load : LoadingController) {
		this.token=this.dataservice.getToken();
	    this.infoClient=this.dataservice.getinfoClient();
	    this.infoResto=this.dataservice.getinfoResto();
	   this.formtype.id_resto = this.getid.snapshot.paramMap.get('id');
	   this.formtype.id_client = this.infoClient.login;
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
	    this.http.post("http://mobile.api.salesupper.com/api/paiment_list", postData,this.headers)
      	.then(datas => {
      		if(datas){
      			this.data = datas;
      			console.log(this.data);
      			this.data = JSON.parse(this.data.data);
      			this.paiement = this.data.paiement
			}
		}).catch(async err => {
	        console.log(err);
	        loading.dismiss();
	    });
	}

	paiement_response(data, id){
	   	this.formtype_action.id = id;
	   	this.formtype_action.action = data;

	   	const loading = await this.load.create({
	        message: 'chargement. . .',
	        duration: 5000
	    });
	    await loading.present();
	    let postData =  this.formtype_action;
	    this.http.post("http://mobile.api.salesupper.com/api/paiement_response", postData,this.headers)
      	.then(datas => {
      		if(datas){
      			this.data = datas;
      			console.log(this.data);
      			this.data = JSON.parse(this.data.data);
      			loading.dismiss();
      			if(this.data.paiement == "accepted"){
      				const alert = await this.alertCtrl.create({
			              message: 'La transaction a été acceptée',
			              buttons: ['OK']
			        });'OK']
			        let id_rest = this.getid.snapshot.paramMap.get('id');
			        this.route.navigate(['/paiement/', +id_rest]);
      			}else if(this.data.paiement == "denied"){
					const alert = await this.alertCtrl.create({
			            message: 'La transaction a été annulée',
			            buttons: ['OK']
			        });
			        let id_rest = this.getid.snapshot.paramMap.get('id');
			        this.route.navigate(['/paiement/', +id_rest]);
      			}
      			
			}
		}).catch(async err => {
	        console.log(err);
	        loading.dismiss();
	    });
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
