<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Client;

class Demarcheur extends Model
{
    protected $guarded=[''];

    protected $fillable = ['cin', 'cin_fait_a', 'cin_fait_le', 'name', 'address', 'mobile', 'point_demarcheur',];

    public function fidelityCardClients()
    {
    	return $this->hasMany('App\FidelityCardClient','id_demarcheur');
    }

    public function clients()
    {
    	return $this->belongsToMany('App\Client')->withPivot('start_period','end_period')
    			->withTimestamps();
    }

    public function historyDemarcheurs()
    {
        return $this->hasMany('App\HistoryDemarcheur','id_client_visit_history');
    }

    public function getcodeAndNameAttribute()
    {
        return '['.$this->code_card.'] '.$this->name;
    }
    
    public function getDateDebutAssignationAttribute($idClient)
    {
        // Carbon::setLocale('fr');
        $client = Client::find($idClient);

        foreach ($client->demarcheurs as $demarcheur) 
        {
            if($demarcheur->id==$this->id)
            {
                return Carbon::parse($demarcheur->pivot->start_period)->format('d-m-Y h:i:s');
            }
        }
    }

    public function salesManUser(){
        //on recupere l'utilisateur associé au demarcheur
        $user = User::where('type','=','salesman')->where('id_salesman','=',$this->id)->first();
        if(!empty($user)){
            return $user;
        }
        return new User();
    }

}
