<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyContact;
use App\Mail\AdminReport;
use App\Mail\EndingQuotations;
use App\Mail\MissingAmountQuotations;
use App\Mail\CollaboratorsA1Report;
use App\Mail\CollaboratorsB1Report;
use App\Mail\CollaboratorsAmountReport;
use App\Mail\CollaboratorsDeliveredReport;
use App\Methodology;
use App\Quotation;
use App\QuotationHistory;
use App\Status;
use App\Typology;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $role = Auth::user()->roles->first();

        $statuses      = Status::orderBy('name')->get();
        $typologies    = Typology::orderBy('name')->get();
        $methodologies = Methodology::orderBy('name')->get();
        $companies     = Company::orderBy('name')->get();

        $researchers = '';
        if(Auth::user()->roles->first()->id == 3)
        {
            $researchers = Auth::user()->name;
        }
        elseif(Auth::user()->roles->first()->id == 2)
        {
            $researchers = Auth::user()->users()->whereHas('roles', function($q) {$q->where('id', 3);})->get();
        }
        else
        {
            $researchers = User::whereHas('roles', function($q) {$q->where('id', 3);})->get();
        }

        if($role->id == 1)
        {
            $quotations = Quotation::all();
        }
        else
        {
            $quotations = $user->quotations()->get();
            $quotations = $quotations->merge($user->quotations_assigned);
        }


        /* Manage filters */
        if ( isset( $_GET['filters_active'] ) && $_GET['filters_active'] == 1 ) {
          if($role->id == 1)
          {
              $quotations = Quotation::filter_quotation($_GET);
          }
          else
          {
              $quotations = Quotation::filter_quotation_for_user($_GET);
          }
        }
        return view('quotations.index', ['quotations' => $quotations, 'statuses' => $statuses, 'typologies' => $typologies, 'researchers' => $researchers, 'methodologies' => $methodologies, 'companies' => $companies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $statuses = Status::orderBy('name')->get();
        $typologies = Typology::orderBy('name')->get();
        $methodologies = Methodology::orderBy('name')->get();

        $researcher = '';
        if(Auth::user()->roles->first()->id == 3)
        {
            $researcher = Auth::user()->name;
        }
        elseif(Auth::user()->roles->first()->id == 2)
        {
            $researcher = Auth::user()->users()->whereHas('roles', function($q) {$q->where('id', 3);})->get();
        }
        else
        {
            $researcher = User::whereHas('roles', function($q) {$q->where('id', 3);})->get();
        }

        return view('quotations.create', ['statuses' => $statuses, 'typologies' => $typologies, 'researcher' => $researcher, 'methodologies' => $methodologies]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function  store(Request $request)
    {
        $rules = [
            'researcher'            => 'required',
            'researchers'           => 'array',
            'name'                  => 'required',
            'company'               => 'required',
            'company_contact'       => 'required',
            'sequential'            => 'numeric|nullable|required_if:manual_sequential,on',
            'code'                  => 'required',
            'description'           => 'required',
            'insertion_date'        => 'required|date',
            'project_delivery_date' => 'required|date|after_or_equal:insertion_date',
            'amount'                => 'numeric|nullable',
            'status'                => 'required',
            'amount_acquired'       => 'numeric|nullable',
            'probability'           => 'required|numeric',
            'feedback'              => 'string|nullable',
            'invoice_amount'        => 'numeric|nullable|required_if:project_closed,on',
            'test_typology'         => 'required',
            'methodology'           => 'required'
        ];

        $customMessages = [
            'sequential.required_if' => __('The Sequential Number is required if you want to insert it manually'),
            'invoice_amount.required_if' => __('The Invoice Amount is required if the project is closed'),
        ];

        $request->validate($rules, $customMessages);

        if(!is_numeric($request->get('researcher')))
        {
            $researcher = User::where('name', $request->get('researcher'))->first();
        }
        else
        {
            $researcher = User::find($request->get('researcher'));
        }

        $company = Company::where('name', $request->get('company'))->first();

        if(is_null($request->get('sequential')))
        {
            $sequential = generate_sequential();
        }
        else
        {
            $sequential = $request->get('sequential');
        }

        $researchers   = array_unique($request->get('researchers'));
        $typologies    = $request->get('test_typology');
        $methodologies = $request->get('methodology');
        $company_contacts = $request->get('company_contact');

        $quotation = new Quotation();

        $quotation->name = $request->get('name');
        $quotation->user_id = $researcher->id;
        $quotation->company_id = $company->id;
        $quotation->sequential_number = $sequential;
        $quotation->code = $request->get('code');
        $quotation->description = $request->get('description');
        $quotation->insertion_date = date('Y-m-d', strtotime($request->get('insertion_date')));
        $quotation->deadline = date('Y-m-d', strtotime($request->get('project_delivery_date')));
        $quotation->amount = $request->get('amount');
        $quotation->status_id = $request->get('status');
        $quotation->amount_acquired = $request->get('amount_acquired');
        $quotation->chance = $request->get('probability');
        $quotation->feedback = $request->get('feedback');
        $quotation->closed = $request->get('project_closed') == 'on' ? 1 : 0;
        $quotation->invoice_amount = $request->get('invoice_amount');

        /* Integration with old */
        $quotation->methodology_id = null;
        $quotation->company_contact_id = null;

        $quotation->save();

        foreach($typologies as $typology)
        {
            $quotation->typologies()->attach($typology);
        }

        foreach($methodologies as $methodology)
        {
            $quotation->methodologies()->attach($methodology);
        }

        foreach($company_contacts as $company_contact)
        {
            $quotation->company_contacts()->attach($company_contact);
        }

        foreach($researchers as $other_researcher)
        {
            if(is_null($other_researcher))
            {
                continue;
            }
            if($other_researcher == $researcher->name)
            {
                continue;
            }

            $other_researcher = User::where('name', $other_researcher)->first();

            $quotation->collaborators()->attach($other_researcher->id);
        }

        return redirect()->route('quotations.index')->with('message', __('Quotation Added Successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $quotation_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($quotation_id)
    {
        $quotation = Quotation::find($quotation_id);
        $history = $quotation->history;
        $statuses = Status::orderBy('name')->get();
        $typologies = Typology::orderBy('name')->get();
        $methodologies = Methodology::orderBy('name')->get();

        $researcher = '';
        if(Auth::user()->roles->first()->id == 3)
        {
            $researcher = Auth::user()->name;
        }
        elseif(Auth::user()->roles->first()->id == 2)
        {
            $researcher = Auth::user()->users()->whereHas('roles', function($q) {$q->where('id', 3);})->get();
        }
        else
        {
            $researcher = User::whereHas('roles', function($q) {$q->where('id', 3);})->get();
        }

        return view('quotations.edit', ['quotation' => $quotation, 'history' => $history, 'statuses' => $statuses, 'typologies' => $typologies, 'methodologies' => $methodologies, 'researcher' => $researcher]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $quotation_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $quotation_id)
    {
        $rules = [
            'researcher'            => 'required',
            'researchers'           => 'array',
            'name'                  => 'required',
            'company'               => 'required',
            'company_contact'       => 'required',
            'sequential'            => 'numeric|nullable|required_if:manual_sequential,on',
            'code'                  => 'required',
            'description'           => 'required',
            'insertion_date'        => 'required|date',
            'project_delivery_date' => 'required|date|after_or_equal:insertion_date',
            'amount'                => 'numeric|nullable',
            'status'                => 'required',
            'amount_acquired'       => 'numeric|nullable',
            'probability'           => 'required|numeric',
            'feedback'              => 'string|nullable',
            'invoice_amount'        => 'numeric|nullable|required_if:project_closed,on',
            'test_typology'         => 'required',
            'methodology'           => 'required',
        ];

        $customMessages = [
            'invoice_amount.required_if' => __('The Invoice Amount is required if the project is closed'),
        ];

        $request->validate($rules, $customMessages);

        if(!is_numeric($request->get('researcher')))
        {
            $researcher = User::where('name', $request->get('researcher'))->first();
        }
        else
        {
            $researcher = User::find($request->get('researcher'));
        }

        $company = Company::where('name', $request->get('company'))->first();

        $researchers = array_unique($request->get('researchers'));
        $typologies = $request->get('test_typology');
        $methodologies = $request->get('methodology');
        $company_contacts = $request->get('company_contact');

        $quotation = Quotation::find($quotation_id);

        $this->find_differences($request, $quotation, $researcher, $company);

        $quotation->name = $request->get('name');
        $quotation->user_id = $researcher->id;
        $quotation->company_id = $company->id;
        $quotation->sequential_number = $request->get('sequential');
        $quotation->code = $request->get('code');
        $quotation->description = $request->get('description');
        $quotation->insertion_date = date('Y-m-d', strtotime($request->get('insertion_date')));
        $quotation->deadline = date('Y-m-d', strtotime($request->get('project_delivery_date')));
        $quotation->amount = $request->get('amount');
        $quotation->status_id = $request->get('status');
        $quotation->amount_acquired = $request->get('amount_acquired');
        $quotation->chance = $request->get('probability');
        $quotation->feedback = $request->get('feedback');
        $quotation->closed = $request->get('project_closed') == 'on' ? 1 : 0;
        $quotation->invoice_amount = $request->get('invoice_amount');

        $quotation->methodology_id = null;
        $quotation->company_contact_id = null;

        $quotation->save();

        $quotation->typologies()->detach();
        $quotation->methodologies()->detach();
        $quotation->collaborators()->detach();
        $quotation->company_contacts()->detach();

        foreach($researchers as $other_researcher)
        {
            if(is_null($other_researcher))
            {
                continue;
            }
            if($other_researcher == $researcher->name)
            {
                continue;
            }

            $other_researcher = User::where('name', $other_researcher)->first();

            $quotation->collaborators()->attach($other_researcher->id);
        }

        foreach($typologies as $typology)
        {
            $quotation->typologies()->attach($typology);
        }

        foreach($methodologies as $methodology)
        {
            $quotation->methodologies()->attach($methodology);
        }

        foreach($company_contacts as $company_contact)
        {
            $quotation->company_contacts()->attach($company_contact);
        }

        return redirect()->route('quotations.index')->with('message', __('Quotation Updated Successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $quotation_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($quotation_id)
    {
        $user = Auth::user();
        $role = $user->roles->first();
        $quotation = Quotation::find($quotation_id);

        if(is_null($quotation))
        {
            return redirect()->route('quotations.index')->with('error', __('Quotation Already Deleted or not Existing'));
        }

        $creator = $quotation->user->id;
        if($role->id > 1 && $creator != $user->id && !$quotation->collaborators->contains('id', $user->id))
        {
            return redirect()->route('quotations.index')->with('error', __('You do not have the permission to delete this quotation'));
        }

        $quotation->delete();

        return redirect()->route('quotations.index')->with('message', __('Quotation Deleted Successfully!'));
    }

    public function import()
    {
        $file_handle = fopen(public_path() . '/import.csv', 'r');
        while (!feof($file_handle)) {
            $line = fgetcsv($file_handle, 0, ',');

            if(is_bool($line))
            {
                break;
            }

            $line_quotation = array(
                'sequential'        => $line[2],
                'insertion_date'    => $line[3],
                'deadline'          => $line[4],
                'company'           => $line[5],
                'company_contact'   => $line[8],
                'description'       => $line[9],
                'user'              => $line[10],
                'amount'            => $line[11],
                'status'            => $line[12],
            );

            $quotation = new Quotation();
            $quotation->name = $line_quotation['description'];
            $quotation->sequential_number = $line_quotation['sequential'];
            $quotation->code = 'IMPORT';
            $quotation->description = $line_quotation['description'];
            $quotation->insertion_date = date('Y-m-d', strtotime($line_quotation['insertion_date']));
            $quotation->deadline = date('Y-m-d', strtotime($line_quotation['deadline']));
            $quotation->amount = intval(str_replace(',', '', $line_quotation['amount']));
            $quotation->amount_acquired = intval(str_replace(',', '', $line_quotation['amount']));
            $quotation->chance = '100';
            $quotation->closed = 0;

            $quotation->setCreatedAt(date('Y-m-d H:i:s'));
            $quotation->setUpdatedAt(date('Y-m-d H:i:s'));

            $user = User::where('name', $line_quotation['user'])->get();
            if($user->count() > 0)
            {
                $quotation->user_id = $user[0]->id;
            }
            else
            {
                $quotation->user_id = 1;
            }

            $company = Company::where('name', $line_quotation['company'])->get();
            if($company->count() > 0)
            {
                $quotation->company_id = $company[0]->id;
            }
            else
            {
                $quotation->company_id = 2;
            }

            $company_contact = CompanyContact::where('name', $line_quotation['company_contact'])->get();
            if($company_contact->count() > 0)
            {
                $quotation->company_contact_id = $company_contact[0]->id;
            }
            else
            {
                $quotation->company_contact_id = 2;
            }

            $status = Status::where('name', $line_quotation['status'])->get();
            if($status->count() > 0)
            {
                $quotation->status_id = $status[0]->id;
            }
            else
            {
                $quotation->status_id = 1;
            }
            $quotation->save();
        }
        fclose($file_handle);
        die(var_dump(public_path()));
    }

    public function export()
    {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="quotations_export.csv";');

        $role = Auth::user()->roles->first();

        $f = fopen( 'php://memory', 'w' );

        $quotations = Quotation::all();

        /* Manage filters */
        if ( isset( $_GET['filters_active'] ) && $_GET['filters_active'] == 1 ) {
          if($role->id == 1)
          {
              $quotations = Quotation::filter_quotation($_GET);
          }
          else
          {
              $quotations = Quotation::filter_quotation_for_user($_GET);
          }
        }

        echo "Id;Nome,Utente;Collaboratori;Azienda;Contatto Azienda;Numero Sequenziale;Descrizione;Data Inserimento;Data Consegna;Importo;Stato;Metodologia;Tipologie Test;Importo Acquisito;Probabilità;Feedback;Preventivo Chiuso;Importo Fatturato;\n";

        foreach($quotations as $quotation)
        {
            $quotationCode = get_code($quotation);
            $line = "{$quotationCode};{$quotation->user->name};";
            $insertion_date = date('d/m/Y', strtotime($quotation->insertion_date));
            $deadline = date('d/m/Y', strtotime($quotation->deadline));

            $collaborators = $quotation->collaborators;

            if($collaborators->count() > 0)
            {
                foreach($collaborators as $collaborator)
                {
                    $line .= "{$collaborator->name} - ";
                }
                $line = substr($line, 0, -3) . ';';

            }
            else
            {
                $line .= ";";
            }

            $line .= "{$quotation->company->name};";

            $company_contacts = $quotation->company_contacts;
            if($company_contacts->count() > 0)
            {
                foreach($company_contacts as $company_contact)
                {
                    $line .= "{$company_contact->name} - ";
                }

                $line = substr($line, 0, -3) . ';';
            }
            else
            {
                $line .= ";";
            }

            $statusName = "";
            if ( !is_null($quotation->status) && $quotation->status !== "" && $quotation->status->name !== "" ) {
              $statusName = $quotation->status->name;
            }

            $line .= "{$quotation->sequential_number};{$quotation->description};{$insertion_date};{$deadline};{$quotation->amount};{$statusName};";

            $methodologies = $quotation->methodologies;
            if($methodologies->count() > 0)
            {
                foreach($methodologies as $methodology)
                {
                    $line .= "{$methodology->name} | {$methodology->type} - ";
                }

                $line = substr($line, 0, -3) . ';';
            }
            else
            {
                $line .= ";";
            }

            $typologies = $quotation->typologies;
            if($typologies->count() > 0)
            {
                foreach($typologies as $typology)
                {
                    $line .= "{$typology->name} - ";
                }

                $line = substr($line, 0, -3) . ';';
            }
            else
            {
                $line .= ";";
            }
            $line .= "{$quotation->amount_acquired};{$quotation->chance};{$quotation->feedback};";

            $line .= $quotation->closed == 1 ? "chiuso;" : "non chiuso;";
            $line .= !is_null($quotation->invoice_amount) ? "{$quotation->invoice_amount};" : ";";

            $line .= ";\n";

            echo $line;
        }

        fclose($f);

        exit();
    }

    public function report()
    {
        $type = $_POST['report'];

        if($type == 'admin')
        {
            $users = User::whereHas('roles', function($q) {$q->whereIn('id', ['1', '2']);})->get();

            $total_quotations = Quotation::all();
            $total_quotations = $total_quotations->count();
            $open_quotations = Quotation::where('closed', 0)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->get()->count();
            $open_not_invoiced_quotations = Quotation::where('closed', 0)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->where(function ($q) { $q->whereNull('invoice_amount')->orWhere('invoice_amount', 0);})->get()->count();
            $closed_not_invoiced_quotations = Quotation::where('closed', 1)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->where(function ($q) { $q->whereNull('invoice_amount')->orWhere('invoice_amount', 0);})->get()->count();

            $quotation_stats = array($total_quotations, $open_quotations, $open_not_invoiced_quotations, $closed_not_invoiced_quotations);

            foreach($users as $user)
            {
                Mail::to($user)->send(New AdminReport($user, $quotation_stats));
            }
        }
        elseif($type == 'deadlines')
        {
            $next_working_day = date('w');

            if($next_working_day == 5)
            {
                $next_working_day = date('Y-m-d', strtotime('+3 day'));
            }
            else
            {
                $next_working_day = date('Y-m-d', strtotime('+1 day'));
            }

            $users = DB::table('users')
                ->join('quotation', 'users.id', '=', 'quotation.user_id')
                ->where('deadline', $next_working_day)
                ->select('users.*')
                ->get();

            foreach($users as $user)
            {
                $user = User::find($user->id);

                $quotations = $user->quotations()->where('deadline', $next_working_day)->get();

                if($quotations->count() > 0)
                {
                    Mail::to($user)->send(New EndingQuotations($user, $quotations));
                }
            }
        }
        elseif( $type == 'amounts' )
        {
            $today = date('Y-m-d');
            $users = DB::table('users')
                ->join('quotation', 'users.id', '=', 'quotation.user_id')
                ->whereRaw("MOD(DATEDIFF({$today}, quotation.insertion_date), 3) = 0")
                ->whereDate('quotation.insertion_date', '<', $today)
                ->whereNull('quotation.amount')
                ->orWhere('quotation.amount', '=', 0)
                ->select('users.id', 'quotation.id as quotation_id')
                ->get();

            $quotation_users = array();
            foreach($users as $user)
            {
                $quotation_users[$user->id][] = $user->quotation_id;
            }

            foreach($quotation_users as $key => $quotations)
            {
                $user = User::find($key);
                Mail::to($user)->send(New MissingAmountQuotations($user, $quotations));
            }
        }
        elseif( $type == 'status_a1' )
        {

          $quotations = Quotation::whereHas('status', function ($query) {
              $query->where('name', 'like', '%A1%');
          })->get();

          $user_list = [];

          foreach( $quotations as $key => $quotation ) {

            /* User */
            if ( !is_null( $quotation->user ) && !is_null( $quotation->user->email ) ) {
              $user_list[$quotation->user->email][] = $quotation;
            }

            /* Collaborators */
            if ( !empty($quotation->collaborators) ) {
              foreach( $quotation->collaborators as $k => $collaborators ) {
                  $user_list[$collaborators->email][] = $quotation;
              }
            }

          }

          foreach ( $user_list as $userEmail => $quotationList ) {
            $user = User::where("email", "caparonejacopo99@gmail.com")->get()->first();

            Mail::to($user)
                ->cc("caparonejacopo99@gmail.com")
                ->send(New CollaboratorsA1Report( $user, $quotationList ));
          }

        }
        elseif( $type == 'status_b1' )
        {

          $quotations = Quotation::whereHas('status', function ($query) {
              $query->where('name', 'like', '%B1%');
          })->get();

          $user_list = [];

          foreach( $quotations as $key => $quotation ) {

            /* User */
            if ( !is_null( $quotation->user ) && !is_null( $quotation->user->email ) ) {
              $user_list[$quotation->user->email][] = $quotation;
            }

            /* Collaborators */
            if ( !empty($quotation->collaborators) ) {
              foreach( $quotation->collaborators as $k => $collaborators ) {
                  $user_list[$collaborators->email][] = $quotation;
              }
            }

          }

          foreach ( $user_list as $userEmail => $quotationList ) {
            $user = User::where("email", "caparonejacopo99@gmail.com")->get()->first();

            Mail::to($user)
                ->cc("caparonejacopo99@gmail.com")
                ->send(New CollaboratorsB1Report( $user, $quotationList ));
          }

        }
        elseif( $type == 'operators_amount' )
        {

          $quotations = Quotation::whereHas('status', function ($query) {
                            $query->where('name', 'not like', '%A1%');
                        })->where(function ($amountQuery) {
                            $amountQuery->where('amount', '=', 0)
                                        ->orWhereNull('amount');
                        })->get();

          $user_list = [];

          foreach( $quotations as $key => $quotation ) {

            /* User */
            if ( !is_null( $quotation->user ) && !is_null( $quotation->user->email ) ) {
              $user_list[$quotation->user->email][] = $quotation;
            }

            /* Collaborators */
            if ( !empty($quotation->collaborators) ){
              foreach( $quotation->collaborators as $k => $collaborators ){
                  $user_list[$collaborators->email][] = $quotation;
              }
            }

          }

          foreach ( $user_list as $userEmail => $quotationList ) {
            $user = User::where("email", "caparonejacopo99@gmail.com")->get()->first();

            Mail::to($user)
                ->cc("caparonejacopo99@gmail.com")
                ->send(New CollaboratorsAmountReport( $user, $quotationList ));
          }

        }
        elseif( $type == 'operators_not_delivered' )
        {

          $quotations = Quotation::where( "closed", "=", 0 )
                                 ->whereHas('status', function ($query) {
                                      $query->where('name', 'like', '%C1%');
                                 })->get();

          $user_list = [];

          foreach( $quotations as $key => $quotation ) {

            /* User */
            if ( !is_null( $quotation->user ) && !is_null( $quotation->user->email ) ) {
              $user_list[$quotation->user->email][] = $quotation;
            }

            /* Collaborators */
            if ( !empty($quotation->collaborators) ){
              foreach( $quotation->collaborators as $k => $collaborators ){
                  $user_list[$collaborators->email][] = $quotation;
              }
            }

          }

          foreach ( $user_list as $userEmail => $quotationList ) {
            $user = User::where("email", "caparonejacopo99@gmail.com")->get()->first();

            Mail::to($user)
                ->cc("caparonejacopo99@gmail.com")
                ->send(New CollaboratorsDeliveredReport( $user, $quotationList ));
          }

        }
    }

    /**
     * @param $request
     * @param $quotation
     */
    private function find_differences($request, $quotation, $researcher, $company)
    {
        $user = Auth::user();

        $quotation_h = array(
            'name'                  => $request->get('name'),
            'user_id'               => $researcher->id,
            'company_id'            => $company->id,
            // 'company_contact_id'    => $request->get('company_contact'),
            'code'                  => $request->get('code'),
            'description'           => $request->get('description'),
            'insertion_date'        => date('Y-m-d', strtotime($request->get('insertion_date'))),
            'deadline'              => date('Y-m-d', strtotime($request->get('project_delivery_date'))),
            'amount'                => $request->get('amount'),
            'status_id'             => $request->get('status'),
            'amount_acquired'       => $request->get('amount_acquired'),
            'chance'                => $request->get('probability'),
            'feedback'              => $request->get('feedback'),
            'closed'                => $request->get('project_closed') == 'on' ? 1 : 0,
            'invoice_amount'        => $request->get('invoice_amount'),
            // 'methodology_id'        => null,
        );

        foreach($quotation_h as $key => $task_value)
        {
            $old_value = $quotation->$key;

            if($task_value != $old_value)
            {
                $field = '';

                switch($key)
                {
                    case 'user_id'              : $field = 'author';                break;
                    case 'company_id'           : $field = 'company';               break;
                    // case 'company_contact_id'   : $field = 'company contact';       break;
                    case 'deadline'             : $field = 'project_delivery_date'; break;
                    case 'status_id'            : $field = 'status';                break;
                    case 'chance'               : $field = 'probability';           break;
                    case 'closed'               : $field = 'project closed';        break;
                    // case 'methodology_id'       : $field = 'methodology';           break;
                    default                     : $field = $key;                    break;
                }

                $quotation_history = new QuotationHistory();
                $quotation_history->quotation_id = $quotation->id;
                $quotation_history->user_id      = $user->id;
                $quotation_history->field        = $field;
                $quotation_history->old_value    = is_null($old_value) ? '' : $old_value;
                $quotation_history->new_value    = is_null($task_value) ? '' : $task_value;

                $quotation_history->save();
            }
        }
    }
}
