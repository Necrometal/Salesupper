<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use Validator;
use DB;
use Illuminate\Support\Facades\Auth; 
use App\User; 
use App\Client;
use App\FinalProduct;
use App\ClientVisitHistory;
use App\FidelityCardClient;
use App\ClientVisitHistoryDtl;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller; 
class UserController extends Controller 
{
public $successStatus = 200;

    public function login(){ 
        header("Access-Control-Allow-Origin: *");
        if(Auth::attempt(['login' => request('login'), 'password' => request('password')])){ 
            
            $infoclient = DB::table('users')
                            ->where('login', '=', request('login'))
                            ->first();
            $user = Auth::user();
            $loginclient = $infoclient->login;
            $id_login = $infoclient->id;
            $inforesto = User::select("users.id_client","clients.name","fidelity_card_clients.nb_passage")
                    ->join("clients","clients.id","=","users.id_client")
                    ->join("fidelity_card_clients","fidelity_card_clients.user_id","=","users.id")
                    ->groupBy("users.id_client","users.id","clients.name","fidelity_card_clients.nb_passage")
                    ->where("users.id","=",$id_login)
                    ->get();
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $fcard = DB::table('fidelity_card_clients')
                ->select('fidelity_card_clients.id')
                ->where('fidelity_card_clients.mobile', $loginclient)
                ->first();
            $clientVisitHistories = ClientVisitHistory::where('id_fcard_client',$fcard->id)->get();
            $infoclient->id_fcard = $fcard->id;
            return response()->json(['token' => $success['token'],"infoClient"=>$infoclient,"infoResto"=>$inforesto,"clientHistory"=>$clientVisitHistories], $this->successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
function register(Request $request) 
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

    public function get_history(Request $request){
        $id_resto = $request->get('id_resto');
        $id_client = $request->get('id_client');
        $inforestone = Client::find($id_resto);
        $fidelityCard = FidelityCardClient::where("user_id",$id_client)->first();
        $clientVisitHistories = ClientVisitHistory::where('id_fcard_client',$fidelityCard->id)->get();
        $products = FinalProduct::where('id_client',$id_resto)->orderBy('title')->get();
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
        $data = [
            'fidelityCard'=>$fidelityCard,
            'clientVisitHistories'=>$clientVisitHistories,
            'products'=>$products,
            "inforestone"=>$inforestone,
        ];
        return response()->json($data, $this->successStatus); 
    }
    public function get_history_details(Request $request){
        $idhistorique = $request->get("id_histo");
        $allhisto = DB::table('client_visit_history_dtls')
                                        ->select("client_visit_history_dtls.*","final_products.*")
                                        ->join("final_products","final_products.id","=","client_visit_history_dtls.id_finalProduct")
                                        ->where("client_visit_history_dtls.id_cvisit_history",$idhistorique)
                                        ->get();
        $data = [
            "historical" => $allhisto,
        ];
        return response()->json($data, $this->successStatus);
    }
    public function change_mdp(Request $request){
        $pass = $request->get('actual_pass');
        $new = $request->get('new_pass');
        $iduser = $request->get('user');
        $infouser = User::where('id',$iduser)->first();
        if (Hash::check($pass, $infouser->password)) {
           $new_password = Hash::make($new);
           $data = array("password" =>$new_password );
           DB::table('users')
           ->where('id', $iduser)
           ->update($data);
           return response()->json(['success'=>'1'], $this->successStatus); 
        }else{
            return response()->json(['success'=>'0'], $this->successStatus); 
        }
    }
}