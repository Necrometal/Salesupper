<?php

namespace App\Http\Controllers;

use App\FidelityCardClientUser;
use App\User;
use Dompdf\Dompdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Controllers\BaseController;
use App\FidelityCardClient;
use App\Demarcheur;
use App\Http\Requests\FidelityCardRequest;
use App\ClientVisitHistory;
use App\FinalProduct;
use App\Client;
use App\Gratuite;
use App\GratuiteDetail;
use App\Stock_detal;

class FidelityCardController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fidelityCards = DB::table('fidelity_card_clients')
            ->select('fidelity_card_clients.*')
            ->where('id_client',$this->idCurrentClient)
            ->get();
        foreach($fidelityCards as $f){
            $gratuite = DB::table('gratuite')
                ->select('gratuite.*')
                ->where('gratuite.id_fcard_client', $f->id)
                ->where('gratuite.actif', 0)
                ->get();
            if(count($gratuite) > 0){
                $f->gratuiteCount = count($gratuite);
            }else{
                $f->gratuiteCount = 0;
            }
        }
        // dd($fidelityCards);
        // $fidelityCards = FidelityCardClient::where('id_client',$this->idCurrentClient)->get();
        return view('sales.fcard.fcard-clients')->with('fidelityCards',$fidelityCards);
    }
	
	public function clientDuMois()
    {
       
        /*$fidelityCards = FidelityCardClient::selectRaw("*")
				->where('id_client',$this->idCurrentClient)
				->whereMonth('created_at','=',Carbon::now()->month)
                ->get(); */
		$ClientByMonths = DB::table('client_visit_histories')
			->select(DB::raw('count(*) as eff,fidelity_card_clients.name_client')) 
			->join('fidelity_card_clients','fidelity_card_clients.id','=','client_visit_histories.id_fcard_client')
			->where('fidelity_card_clients.id_client',$this->idCurrentClient)
			->whereMonth('client_visit_histories.date_passage', '=', date('m'))
			->groupBy('fidelity_card_clients.name_client')
			->get();
			//dd($ClientByMonths);
        return view('sales.fcard.clientdumois')->with('ClientByMonths',$ClientByMonths);
    }
	public function clientDuJour()
    {
       // $fidelityCards = FidelityCardClient::where('id_client',$this->idCurrentClient)->whereMonth('created_at',date('m'))->get();
        //$fidelityCards = FidelityCardClient::selectRaw("*")
		$ClientByDays = DB::table('client_visit_histories')
			//->select('fidelity_card_clients.id','fidelity_card_clients.code_fcard','fidelity_card_clients.name_client','fidelity_card_clients.nb_passage')
			->select(DB::raw('count(*) as eff,fidelity_card_clients.name_client')) 
			->join('fidelity_card_clients','fidelity_card_clients.id','=','client_visit_histories.id_fcard_client')
			->where('fidelity_card_clients.id_client',$this->idCurrentClient)
			//->whereDay('client_visit_histories.date_passage', '=', date('d'))
			->whereDate('client_visit_histories.date_passage', '=', date('Y-m-d'))
			->groupBy('fidelity_card_clients.name_client')
			
			//->whereMonth('created_at', '11')
			//->whereDate("created_at", "=", date("Y-m-d"))
			//->whereDate('created_at', '=', Carbon::today()->toDateString())
			//->where(DB::raw("DATE(created_at) = '".date('Y-m-d')."'"))
			->get();
			//dd($ClientByDays);
        return view('sales.fcard.clientdujour')->with('clientDuJours',$ClientByDays);
    }
	public function clientByYears()
    {
       // $fidelityCards = FidelityCardClient::where('id_client',$this->idCurrentClient)->whereMonth('created_at',date('m'))->get();
        $fidelityCards = FidelityCardClient::selectRaw("*")
				->where('id_client',$this->idCurrentClient)
				->whereYear('created_at','=',Carbon::now()->year)
                ->get(); 
        return view('sales.fcard.fcard-clients')->with('fidelityCards',$fidelityCards);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fidelityCard = new FidelityCardClient;
        $client = Client::findOrFail($this->idCurrentClient);
        $demarcheurs = Demarcheur::where('id_client','=', $client->id)->get();
        $data = [
            'fidelityCard'=>$fidelityCard,
            'demarcheurs'=>$demarcheurs
        ];

        return view('sales.fcard.create-fcard-client')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //get last fidelitycard de ce demarcheur
        $demarcheur = Demarcheur::find($request->get('id_demarcheur'));
        $lastFcard = FidelityCardClient::where('id_demarcheur',$demarcheur->id)
            ->latest()->first();

        $user = Auth::user();

        //1 clientresto pour 1 resto

        $fidelityCard = new FidelityCardClient;
        $data = $request->all();
        $fidelityCard->id_client = $user->id_client;
        $fidelityCard->fill($data)->save();
        if($lastFcard)
        {
            //000101
            $lastCode = substr($lastFcard->code_fcard,5); //retourne 1
            //next doit être 000102
            $nextCode = $lastCode+1;
            $fidelityCard->code_fcard = $demarcheur->code_card.$nextCode;
            // return $fidelityCard->code_fcard;
        }
        else
        {
            $fidelityCard->code_fcard = $demarcheur->code_card.'1';
        }

        $fidelityCard->save();

        //auth
        //on regarde si le numéro de téléphone existe deja
        $mobile = $request->get('mobile');
        $clienresto = User::findByPhone($mobile);
        if(empty($clienresto)){
            //si ca n'existe pas encore on le crée
            $clienresto = User::create([
                'name' => $request->get('name_client'),
                'login' => $mobile,
                'email' => str_random(10).'@gmail.com',
                'password' => hashed_pass($mobile, true, true),
                'id_salesman' => $fidelityCard->id,
                'type' => 'cliresto'
            ]);

            $clienresto->id_client = $fidelityCard->id_client;
            $clienresto->default_password = str_replace(' ','', str_replace('+261', '0', $mobile));;
            $clienresto->save();
        }

        $fidelityCard->user_id = $clienresto->id;
        $fidelityCard->save();

//        //insertion de la relation entre l'utilisateur, le fcard et le resto
//        FidelityCardClientUser::insert($fidelityCard->id, $user->id_client, $clienresto->id);

        flash('fidelity card number '.$fidelityCard->code_fcard.' successfully created!','success');
        return redirect('sales/fcard');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
        return view('sales.fcard.show-fcard-client')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fidelityCard = FidelityCardClient::find($id);
        $client = Client::find($this->idCurrentClient);
        $demarcheurs = Demarcheur::where('id_client','=', $client->id)->get();
        $data = [
            'fidelityCard'=>$fidelityCard,
            'demarcheurs'=>$demarcheurs
        ];
        return view('sales.fcard.edit-fcard-client')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FidelityCardRequest $request, $id)
    {

        $fidelityCard = FidelityCardClient::find($id);
        $data = $request->except(['email', 'password']);

        $user = User::findByPhone($fidelityCard->mobile);

        $fidelityCard->fill($data)->save();


        //auth
        if(empty($user)){
            $user = new User();

            $user->email = str_random(10).'@gmail.com';
            $user->default_password = str_replace(' ','', str_replace('+261', '0', $request->get('mobile')));
            $user->password = hashed_pass($request->get('mobile'), true, true);
        }

        $user->name = $request->get('name_client');
        $user->login = $request->get('mobile');
        $user->type = "cliresto";
        $user->id_salesman = $fidelityCard->id;
        $user->id_client = $fidelityCard->id_client;
        $user->save();

        $fidelityCard->user_id = $user->id;
        $fidelityCard->save();

//        //relation entre fcard, resto et user
//        $fcard_client_user = FidelityCardClientUser::find($fidelityCard->id, $fidelityCard->id_client, $user->id);
//        if(empty($fcard_client_user)){
//            //si vide on ajoute une rélation
//            FidelityCardClientUser::insert($fidelityCard->id, $fidelityCard->id_client, $user->id);
//        }

        flash('fidelity card number '.$fidelityCard->code_fcard.' successfully updated!','success');
        return redirect('sales/fcard');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fidelityCard = FidelityCardClient::find($id);

        //effacer la relation entre fcard, resto et user
        $user = User::findByPhone($fidelityCard->mobile);
        if(!empty($user)){
            FidelityCardClientUser::remove($fidelityCard->id, $fidelityCard->id_client, $user->id);
        }

        $fidelityCard->delete();

        flash('fidelity card number '.$fidelityCard->code_fcard.' successfully deleted!','success');
        return redirect('sales/fcard');
    }

    public function invoice($id, $hist_id){
        $fidelityCard = FidelityCardClient::find($id);
        //Client visit histories
        $clientVisitHistory = ClientVisitHistory::find($hist_id);

        if(empty($fidelityCard) || empty($clientVisitHistory)){
            abort(404);
        }

        $products = FinalProduct::where('id_client',$this->idCurrentClient)->orderBy('title')->get();
        $index = 0;
        $company = Client::find(Auth::User()->id_client);
        if(empty($company)){
            $company = new Client();
        }
        $data = [
            'fidelityCard'=>$fidelityCard,
            'clientVisitHistory'=>$clientVisitHistory,
            'products'=>$products,
            'index'=>$index,
            'company' => $company,
            'pdf' => false,
        ];

        return view('sales.fcard.invoice')->with($data);
    }

    public function invoicePDF($id, $hist_id){

        $fidelityCard = FidelityCardClient::find($id);
        //Client visit histories
        $clientVisitHistory = ClientVisitHistory::find($hist_id);

        if(empty($fidelityCard) || empty($clientVisitHistory)){
            abort(404);
        }

        $products = FinalProduct::where('id_client',$this->idCurrentClient)->orderBy('title')->get();
        $index = 0;
        $company = Client::find(Auth::User()->id_client);
        if(empty($company)){
            $company = new Client();
        }
        $data = [
            'fidelityCard'=>$fidelityCard,
            'clientVisitHistory'=>$clientVisitHistory,
            'products'=>$products,
            'index'=>$index,
            'company' => $company,
        ];

        $html = view('sales.fcard.invoice')->with($data)->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->render();
        $name = 'Facture_'.date('Y').sprintf("%05s", $clientVisitHistory->id);
        $pdf->stream($name);
    }

    //profil demarcheur (salesman only)
    public function demarcheurMy()
    {
        $user = Auth::User();
        $fidelityCards = FidelityCardClient::findByUserId($user->id);
        //Client visit histories
        if(notNull($fidelityCards)){
            foreach ($fidelityCards as $fidelityCard){
                //recupértion du montant total et du date du dernier visite
                $total_montant = 0;
                $dernier_visite = null;
                $clihist =  ClientVisitHistory::orderBy('id','desc')->where('id_fcard_client',$fidelityCard->id)->where('id_client', $fidelityCard->id_client)->get();
                if(notNull($clihist)){
                    $dernier_visite = date('d-m-Y', strtotime($clihist[0]->date_passage));
                    foreach ($clihist as $clih){
                        $total_montant += $clih->price_bill;
                    }
                }
                $fidelityCard->last_visit = $dernier_visite;
                $fidelityCard->total_montant = $total_montant;
            }
        }

        $index = 0;
        $data = [
            'fidelityCards'=>$fidelityCards,
            'index'=>$index,
            'demarcheur_account' => true,
        ];
        return view('sales.fcard.history-fcard-clients')->with($data);
    }

    public function demarcheurMyShow(Request $request, $id)
    {
        $salesman = $request->get("salesman", null);

        if(notNull($salesman)){
            $fidelityCard = FidelityCardClient::find($id);
            $demarcheur = verify_salesman_account($salesman);
            if(!notNull($fidelityCard)){
                abort(404);
            }
        }
        else{
            $fidelityCard = verify_clientresto_account($id);
            $demarcheur = $fidelityCard->demarcheur;
        }

        //Client visit histories
        $clientVisitHistories = ClientVisitHistory::where('id_fcard_client',$id)->get();
        $products = FinalProduct::where('id_client',$demarcheur->id_client)->orderBy('title')->get();
        $index = 0;
        $data = [
            'fidelityCard'=>$fidelityCard,
            'clientVisitHistories'=>$clientVisitHistories,
            'products'=>$products,
            'index'=>$index,
            'demarcheur_account' => true,
        ];
        return view('sales.fcard.show-fcard-client')->with($data);
    }


    public function demarcheurMyInvoice(Request $request, $id, $hist_id){

        $salesman = $request->get("salesman", null);

        if(!empty($salesman)){
            $fidelityCard = FidelityCardClient::find($id);
            $demarcheur = verify_salesman_account($salesman);
            if(empty($fidelityCard)){
                abort(404);
            }
        }
        else{
            $fidelityCard = verify_clientresto_account($id);
            $demarcheur = $fidelityCard->demarcheur;
        }

        //Client visit histories
        $clientVisitHistory = ClientVisitHistory::find($hist_id);

        if(empty($fidelityCard) || empty($clientVisitHistory)){
            abort(404);
        }

        $products = FinalProduct::where('id_client',$demarcheur->id_client)->orderBy('title')->get();
        $index = 0;
        $company = Client::find($demarcheur->id_client);
        if(empty($company)){
            $company = new Client();
        }
        $data = [
            'fidelityCard'=>$fidelityCard,
            'clientVisitHistory'=>$clientVisitHistory,
            'products'=>$products,
            'index'=>$index,
            'company' => $company,
            'pdf' => false,
            'demarcheur_account' => true,
        ];

        return view('sales.fcard.invoice')->with($data);
    }

    public function demarcheurMyInvoicePDF(Request $request,$id, $hist_id){

        $salesman = $request->get("salesman", null);

        if(!empty($salesman)){
            $fidelityCard = FidelityCardClient::find($id);
            $demarcheur = verify_salesman_account($salesman);
            if(empty($fidelityCard)){
                abort(404);
            }
        }
        else{
            $fidelityCard = verify_clientresto_account($id);
            $demarcheur = $fidelityCard->demarcheur;
        }

        //Client visit histories
        $clientVisitHistory = ClientVisitHistory::find($hist_id);

        if(empty($fidelityCard) || empty($clientVisitHistory)){
            abort(404);
        }

        $products = FinalProduct::where('id_client',$demarcheur->id_client)->orderBy('title')->get();
        $index = 0;
        $company = Client::find($demarcheur->id_client);
        if(empty($company)){
            $company = new Client();
        }
        $data = [
            'fidelityCard'=>$fidelityCard,
            'clientVisitHistory'=>$clientVisitHistory,
            'products'=>$products,
            'index'=>$index,
            'company' => $company,
            'demarcheur_account' => true,
        ];

        $html = view('sales.fcard.invoice')->with($data)->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->render();
        $name = 'Facture_'.date('Y').sprintf("%05s", $clientVisitHistory->id);
        $pdf->stream($name);
    }

    public function gratuite($idfcard = 0){
        $user = Auth::User();
        if($idfcard == 0){
            $gratuite = DB::table('gratuite')
                ->join('fidelity_card_clients', 'fidelity_card_clients.id', '=', 'gratuite.id_fcard_client')
                ->select(DB::raw("gratuite.id as idgratuite, gratuite.*, fidelity_card_clients.*"))
                ->where('fidelity_card_clients.id_client', $user->id_client)
                ->get();

            $data['fidelityCards'] = $gratuite;
            return view('sales.fcard.gratuite-all-fcard')->with($data);
        }else{
            $data['name_client'] = FidelityCardClient::find($idfcard);

            $gratuite = DB::table('gratuite')
                ->join('fidelity_card_clients', 'fidelity_card_clients.id', '=', 'gratuite.id_fcard_client')
                ->select(DB::raw("gratuite.id as idgratuite, gratuite.*, fidelity_card_clients.*"))
                ->where('fidelity_card_clients.id_client', $user->id_client)
                ->where('gratuite.id_fcard_client', $idfcard)
                ->get();
            $data['fidelityCards'] = $gratuite;
            return view('sales.fcard.gratuite-fcard')->with($data);
        }

    }

    public function achatgratuite($idfcard, $idgratuite){
        $user = Auth::User();
        $fidelityCard = FidelityCardClient::find($idfcard); 
        $products = FinalProduct::where('id_client',$user->id_client)->orderBy('title')->get();
        $index = 0;
        $data['idgratuite'] = $idgratuite;
        $data['fidelityCard'] = $fidelityCard;
        $data['products'] = $products;
        $data['index'] = $index;
        return view('sales.fcard.gratuite-achat')->with($data);
    }

    public function saveachatgratuite(Request $request){
        for ($i=0; $i < $request->get('nbInput'); $i++)
        {
            $idProd = $request->get('product_'.$i, 0);
            $quantity = $request->get('quantity_'.$i, 0);

            if($idProd && $quantity){
                $detail = new GratuiteDetail;
                $detail->id_gratuite = $request->get('id_gratuite'); 
                $detail->id_product = $idProd;
                $detail->quantity = $quantity;
                $detail->created_at = date('Y-m-d H:i:s');
                $detail->save();

                $prod = FinalProduct::find($idProd);
                if((int)($prod->amout) < $quantity){
                    $prod->amout = 0;
                }else{
                    $prod->amout = (int)($prod->amout) - (int)($quantity);
                }
                $prod->save();

                $stock = new Stock_detal;
                $stock->product_name = $prod->title;
                $stock->id_product = $idProd;
                $stock->amount_stock =  $quantity;
                $stock->unit_price = $prod->sales_price;
                $stock->movement_type = $request->get('movement_type');
                $stock->save();


            }
        }
        $gratuite = Gratuite::find($request->get('id_gratuite'));
        $gratuite->actif = 1;
        $gratuite->save();
        return redirect('sales/fcard/gratuite/list/'.$request->get('id_fcard_client'));
        
    }

    public function gratuitedetail($idfcard){
        $detail = DB::table('gratuite')
            ->join('gratuite_detail', 'gratuite_detail.id_gratuite', '=', 'gratuite.id')
            ->select(DB::raw("gratuite_detail.*, gratuite.id_fcard_client"))
            ->where('gratuite.id', $idfcard)
            ->get();
        foreach($detail as $d){
            $product = FinalProduct::find($d->id_product);
            $d->name_product = $product->title;
        }
        $data['client'] = DB::table('fidelity_card_clients')
            ->select('fidelity_card_clients.name_client')
            ->where('fidelity_card_clients.id', $detail[0]->id_fcard_client)
            ->first();
        $data['detail'] = $detail;
        $data['id_gratuite'] = $idfcard;
        return view('sales.fcard.gratuite-detail')->with($data);
    }
}
