<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Client;
use App\ClientVisitHistory;
use Illuminate\Support\Facades\Auth; 
use Validator;
class UserController extends Controller 
{
public $successStatus = 200;
/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        header("Access-Control-Allow-Origin: *");
        if(Auth::attempt(['login' => request('login'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $infoclient = $user->first();
            $inforesto = Client::find($infoclient->id_client)->first();
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $clientVisitHistories = ClientVisitHistory::where('id_fcard_client',$infoclient->id_client)->get();
            return response()->json(['token' => $success['token'],"infoClient"=>$infoclient,"infoResto"=>$inforesto,"clientvisit"=>$clientVisitHistories], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
$input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['name'] =  $user->name;
return response()->json(['success'=>$success], $this-> successStatus); 
    }
/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 

    public function get_history($id){
        $fidelityCard = FidelityCardClient::find($id);
        //Client visit histories
        $clientVisitHistories = ClientVisitHistory::where('id_fcard_client',$id)->get();
        $products = FinalProduct::where('id_client',$this->idCurrentClient)->orderBy('title')->get();
        // $final_product = DB::table('final_products')
        //     ->join('class_products', 'class_products.id', '=', 'final_products.id_classProduct')
        //     ->select(DB::raw("final_products.*, class_products.name"))
        //     ->where('final_products.id_client',$this->idCurrentClient)
        //     ->orderBy('final_products.title')
        //     ->get();
        $gratuite = DB::table('gratuite')
                ->select('gratuite.*')
                ->where('gratuite.id_fcard_client', $fidelityCard->id)
                ->where('gratuite.actif', 0)
                ->get();
        if(count($gratuite) > 0){
            $fidelityCard->gratuiteCount = count($gratuite);
        }else{
            $fidelityCard->gratuiteCount = 0;
        }
        $index = 0;
        $data = [
            'fidelityCard'=>$fidelityCard,
            'clientVisitHistories'=>$clientVisitHistories,
            'products'=>$products,
            'index'=>$index,
            // 'final_product' => $final_product
        ];
    }
}