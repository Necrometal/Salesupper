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

            $success['token'] =  $user->createToken('MyApp')->accessToken;

            if($infoclient->type == "cliresto"){
                $inforesto = User::select("users.id_client","clients.name","fidelity_card_clients.nb_passage")
                    ->join("clients","clients.id","=","users.id_client")
                    ->join("fidelity_card_clients","fidelity_card_clients.mobile","=","users.login")
                    ->groupBy("users.id_client","users.id","clients.name","fidelity_card_clients.nb_passage")
                    ->where("users.login","=",$loginclient)
                    ->where("fidelity_card_clients.id_client", "users.id_client")
                    ->get();

                $fcard = DB::table('fidelity_card_clients')
                    ->select('fidelity_card_clients.id')
                    ->where('fidelity_card_clients.mobile', $loginclient)
                    ->first();

                $clientVisitHistories = DB::table('client_visit_histories')
                    ->select('client_visit_histories.*')
                    ->join('fidelity_card_clients', 'fidelity_card_clients.id_client', '=', 'client_visit_histories.id_client')
                    ->where('fidelity_card_clients.mobile', $loginclient)
                    ->where('client_visit_histories.id_fcard_client', 'fidelity_card_clients.id')
                    ->get();

                $infoclient->id_fcard = $fcard->id;

                return response()->json(
                    ['token' => $success['token'],
                    "infoClient"=>$infoclient,
                    "infoResto"=>$inforesto,
                    "clientHistory"=>$clientVisitHistories], 
                    "type" => "cliresto",
                    $this->successStatus
                ); 
            }elseif($infoclient->type == "salesman"){
                $inforesto = User::select("users.id_client","clients.name")
                    ->join("clients","clients.id","=","users.id_client")
                    ->join("demarcheurs","demarcheur.cin","=","users.login")
                    ->groupBy("users.id_client","users.id","clients.name")
                    ->where("users.login","=",$loginclient)
                    ->where("demarcheurs.id_client", "users.id_client")
                    ->get();

                return response()->json(
                    ['token' => $success['token'],
                    "infoClient"=>$infoclient,
                    "infoResto"=>$inforesto,
                    "type" => "salesman",
                    $this->successStatus
                );
            }else{
                return response()->json(['error'=>'Unauthorised'], 401);
            }
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
        $fidelityCard = FidelityCardClient::where("mobile",$id_client)
            ->where('id_client', $id_resto)
            ->first();
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

    public function get_demarcheur_salary(Request $request){
        $id_resto = $request->get('id_resto');
        $id_client = $request->get('id_client');
        $inforestone = Client::find($id_resto);

        $salary = DB::table('history_demarcheurs')
            ->select('history_demarcheurs.somme')
            // ->select(DB::raw("SUM(history_demarcheurs.somme) as somme"))
            ->join('demarcheurs', 'demarcheurs.id', '=', 'history_demarcheurs.id_demarcheur')
            ->where('history_demarcheurs.id_client', $id_resto)
            ->where('demarcheurs.cin', $id_client)
            ->sum('history_demarcheurs.somme');

        // $paiement = DB::table('history_demarcheurs')
        //     ->select(DB::raw('history_demarcheurs.somme, history_demarcheurs.created_at, history_demarcheurs.validation'))
        //     ->join('demarcheurs', 'demarcheurs.id', '=', 'history_demarcheurs.id_demarcheur')
        //     ->where('history_demarcheurs.id_client', $id_resto)
        //     ->where('demarcheurs.cin', $id_client)
        //     ->get();

        $data = [
            "inforestone"=>$inforestone,
            "salary" => $salary,
            "paiement" => $paiement
        ];

        return response()->json($data, $this->successStatus); 
    }

    public function get_salary_detail(Request $request){
        $id_resto = $request->get('id_resto');
        $id_client = $request->get('id_client');

        $salary = DB::table('history_demarcheurs')
            ->select('history_demarcheurs.*')
            ->join('demarcheurs', 'demarcheurs.id', '=', 'history_demarcheurs.id_demarcheur')
            ->where('history_demarcheurs.id_client', $id_resto)
            ->where('demarcheurs.cin', $id_client)
            ->get();

        $salary_somme = DB::table('history_demarcheurs')
            ->select('history_demarcheurs.somme')
            // ->select(DB::raw("SUM(history_demarcheurs.somme) as somme"))
            ->join('demarcheurs', 'demarcheurs.id', '=', 'history_demarcheurs.id_demarcheur')
            ->where('history_demarcheurs.id_client', $id_resto)
            ->where('demarcheurs.cin', $id_client)
            ->sum('history_demarcheurs.somme');

        $data = [
            "salary" => $salary,
            "salary_somme" => $salary_somme
        ];

        return response()->json($data, $this->successStatus); 
    }

    public function get_paiement_list(Request $request){
        $id_resto = $request->get('id_resto');
        $id_client = $request->get('id_client');

        $paiement = DB::table('history_demarcheurs')
            ->select('history_demarcheurs.*')
            ->join('demarcheurs', 'demarcheurs.id', '=', 'history_demarcheurs.id_demarcheur')
            ->where('history_demarcheurs.id_client', $id_resto)
            ->where('demarcheurs.cin', $id_client)
            ->where('history_demarcheurs.type_movement', 'retrait')
            ->orderBy('history_demarcheurs.created_at')
            ->get();

        $data = [
            "paiement" => $paiement,
        ];

        return response()->json($data, $this->successStatus); 
    }

    public function paiement_response(Request $request){
        $id = $request->get('id');
        $action = $request->get('action');

        $paiement = DB::table('history_demarcheurs')
            ->where('history_demarcheurs.id', $id)
            ->first();

        if($action == 'yes'){
            $paiement->validation = 1;
            $paiement->save();
            $data = [
                "paiement" => "accepted",
            ];
        }elseif($action == 'no'){
            $paiement->delete();
            $data = [
                "paiement" => "denied",
            ];
        }
        return response()->json($data, $this->successStatus); 
    }

// SELECT SUM(history_demarcheurs.somme) as somme FROM `history_demarcheurs` 
// inner join demarcheurs on demarcheurs.id = history_demarcheurs.id_demarcheur
// where history_demarcheurs.id_client = 16
// and demarcheurs.cin = '345 786 908 786'
}