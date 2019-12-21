<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClientVisitHistory extends Model
{
    protected $guarded=[''];

    public function fidelityCardClient()
    {
    	return $this->belongsTo('App\FidelityCardClient','id_fcard_client');
    }

    public function demarcheur()
    {
    	return $this->belongsTo('App\Demarcheur','id_demarcheur');
    }

    public function clientVisitHistoryDtls()
    {
    	return $this->hasMany('App\ClientVisitHistoryDtl','id_cvisit_history');
    }

    public function historyDemarcheurs()
    {
        return $this->hasMany('App\HistoryDemarcheur','id_client_visit_history');
    }

    public function getPriceBillTextAttribute( )
    {
        $priceBill = number_format($this->price_bill,0,' ',' ');
        return $priceBill;
    }

    public function getDatePassageTextAttribute()
    {
        return Carbon::parse($this->date_passage)->format('d-m-Y h:i:s');
    }
}
