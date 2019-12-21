<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Gratuite extends Model
{
	protected $table = 'gratuite';
	protected $guarded=[''];

	public function product()
    {
    	return $this->belongsTo('App\FinalProduct','id_product');
    }

    public function setUpdatedAt($value){
       
   	}
}