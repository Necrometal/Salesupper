<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FidelityCardClient extends Model
{
    protected $guarded=[''];
	protected $fillable = ['name_client', 'mobile','id_demarcheur', 'birthday_at'];

    public function demarcheur()
    {
    	return $this->belongsTo('App\Demarcheur','id_demarcheur');
    }

    public function cliResto(){
        //on recupere l'utilisateur associé à la carte de fidélité
        $user = User::where('type','=','cliresto')->where('id_salesman','=',$this->id)->first();
        if(!empty($user)){
            return $user;
        }
        return new User();
    }

    public function client(){
        return $this->belongsTo('App\Client','id_client');
    }

    public static function findByUserId($user_id, $order='desc'){
        return self::orderBy('id', $order)->where('user_id', '=', $user_id)->get();
    }
}
