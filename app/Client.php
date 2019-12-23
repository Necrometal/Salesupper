<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
	protected $guarded = [''];

	public function users()
    {
        return $this->hasMany('App\User','id_client');
    }

    public function articleClients()
    {
    	return $this->hasMany('App\ArticleClient','id_client');
    }

    public function products()
    {
    	return $this->hasMany('App\FinalProduct','id_client');
    }

    public function storages()
    {
    	return $this->hasMany('App\Storage','id_client');
    }

    public function providers()
    {
    	return $this->hasMany('App\Provider','id_client');
    }

    public function demarcheurs()
    {
        return $this->belongsToMany('App\Demarcheur')->withPivot('start_period','end_period')
                ->withTimestamps();
    }
}


