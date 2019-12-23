<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FinalProduct extends Model
{
    protected $guarded = [];

    public function recipes()
    {
    	return $this->hasMany('App\Recipe','id_finalProduct');
    }

    public function forecastProducts()
    {
    	return $this->hasMany('App\ForecastProduct','id_product');
    }

    public function classProduct()
    {
    	return $this->belongsTo('App\ClassProduct','id_classProduct');
    }

    public function stockManagementDtls()
    {
        return $this->hasMany('App\StockManagementDtl','id_product');
    }

    public function stocks()
    {
        return $this->hasMany('App\Stock','id_product');
    }

    public function client()
    {
        return $this->belongsTo('App\Client','id_client');
    }

    public function getSalesPriceCurrencyAttribute()
    {
        return number_format($this->sales_price,0,' ',' ');
    }

    public function getDateCreationAttribute()
    {
        Carbon::setLocale('fr');
        return Carbon::parse($this->created_at)->format('d-m-Y h:i:s');
    }
}
