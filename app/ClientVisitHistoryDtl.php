<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientVisitHistoryDtl extends Model
{
    protected $guarded=[''];

    public function clientVisitHistory()
    {
    	return $this->belongsTo('App\ClientVisitHistory','id_cvisit_history');
    }

    public function finalProduct()
    {
    	return $this->belongsTo('App\FinalProduct','id_finalProduct');
    }
}
