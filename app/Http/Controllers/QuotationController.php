<?php

namespace App\Http\Controllers;

use App\Company;
use App\Quotation;
use App\QuotationHistory;
use App\Status;
use App\Typology;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if($role->id == 1)
        {
            $quotations = Quotation::all();
        }
        else
        {
            $quotations = $user->quotations()->get();
            $quotations = $quotations->merge($user->quotations_assigned);
        }

        return view('quotations.index', ['quotations' => $quotations]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $statuses = Status::all();
        $typologies = Typology::all();

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

        return view('quotations.create', ['statuses' => $statuses, 'typologies' => $typologies, 'researcher' => $researcher]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'researcher'        => 'required',
            'researchers'       => 'array',
            'name'              => 'required',
            'company'           => 'required',
            'company_contact'   => 'required',
            'sequential'        => 'numeric|nullable|required_if:manual_sequential,on|unique:quotation,sequential_number',
            'code'              => 'required',
            'description'       => 'required',
            'insertion_date'    => 'required|date',
            'end_date'          => 'required|date|after:insertion_date',
            'amount'            => 'numeric|nullable',
            'status'            => 'required',
            'amount_acquired'   => 'numeric|nullable',
            'probability'       => 'required|numeric',
            'feedback'          => 'string|nullable',
            'invoice_amount'    => 'numeric|nullable|required_if:project_closed,on',
            'test_typology'     => 'required',
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

        $researchers = array_unique($request->get('researchers'));

        $quotation = new Quotation();

        $quotation->name = $request->get('name');
        $quotation->user_id = $researcher->id;
        $quotation->company_id = $company->id;
        $quotation->company_contact_id = $request->get('company_contact');
        $quotation->sequential_number = $sequential;
        $quotation->code = $request->get('code');
        $quotation->description = $request->get('description');
        $quotation->insertion_date = date('Y-m-d', strtotime($request->get('insertion_date')));
        $quotation->deadline = date('Y-m-d', strtotime($request->get('end_date')));
        $quotation->amount = $request->get('amount');
        $quotation->status_id = $request->get('status');
        $quotation->amount_acquired = $request->get('amount_acquired');
        $quotation->chance = $request->get('probability');
        $quotation->feedback = $request->get('feedback');
        $quotation->closed = $request->get('project_closed') == 'on' ? 1 : 0;
        $quotation->invoice_amount = $request->get('invoice_amount');
        $quotation->typology_id = $request->get('test_typology');

        $quotation->save();

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
        $statuses = Status::all();
        $typologies = Typology::all();

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

        return view('quotations.edit', ['quotation' => $quotation, 'history' => $history, 'statuses' => $statuses, 'typologies' => $typologies, 'researcher' => $researcher]);
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
            'researcher'        => 'required',
            'researchers'       => 'array',
            'name'              => 'required',
            'company'           => 'required',
            'company_contact'   => 'required',
            'sequential'        => 'numeric|nullable|required_if:manual_sequential,on|unique:quotation,sequential_number,' . $quotation_id,
            'code'              => 'required',
            'description'       => 'required',
            'insertion_date'    => 'required|date',
            'end_date'          => 'required|date|after:insertion_date',
            'amount'            => 'numeric|nullable',
            'status'            => 'required',
            'amount_acquired'   => 'numeric|nullable',
            'probability'       => 'required|numeric',
            'feedback'          => 'string|nullable',
            'invoice_amount'    => 'numeric|nullable|required_if:project_closed,on',
            'test_typology'     => 'required',
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

        $quotation = Quotation::find($quotation_id);

        $this->find_differences($request, $quotation, $researcher, $company);

        $quotation->name = $request->get('name');
        $quotation->user_id = $researcher->id;
        $quotation->company_id = $company->id;
        $quotation->company_contact_id = $request->get('company_contact');
        $quotation->code = $request->get('code');
        $quotation->description = $request->get('description');
        $quotation->insertion_date = date('Y-m-d', strtotime($request->get('insertion_date')));
        $quotation->deadline = date('Y-m-d', strtotime($request->get('end_date')));
        $quotation->amount = $request->get('amount');
        $quotation->status_id = $request->get('status');
        $quotation->amount_acquired = $request->get('amount_acquired');
        $quotation->chance = $request->get('probability');
        $quotation->feedback = $request->get('feedback');
        $quotation->closed = $request->get('project_closed') == 'on' ? 1 : 0;
        $quotation->invoice_amount = $request->get('invoice_amount');
        $quotation->typology_id = $request->get('test_typology');
        $quotation->save();

        $quotation->collaborators()->detach();
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
            'company_contact_id'    => $request->get('company_contact'),
            'code'                  => $request->get('code'),
            'description'           => $request->get('description'),
            'insertion_date'        => date('Y-m-d', strtotime($request->get('insertion_date'))),
            'deadline'              => date('Y-m-d', strtotime($request->get('end_date'))),
            'amount'                => $request->get('amount'),
            'status_id'             => $request->get('status'),
            'amount_acquired'       => $request->get('amount_acquired'),
            'chance'                => $request->get('probability'),
            'feedback'              => $request->get('feedback'),
            'closed'                => $request->get('project_closed') == 'on' ? 1 : 0,
            'invoice_amount'        => $request->get('invoice_amount'),
            'typology_id'           => $request->get('test_typology'),

        );

        foreach($quotation_h as $key => $task_value)
        {
            $old_value = $quotation->$key;

            if($task_value != $old_value)
            {
                $field = '';

                switch($key)
                {
                    case 'user_id'              : $field = 'author';            break;
                    case 'company_id'           : $field = 'company';           break;
                    case 'company_contact_id'   : $field = 'company contact';   break;
                    case 'deadline'             : $field = 'end date';          break;
                    case 'status_id'            : $field = 'status';            break;
                    case 'chance'               : $field = 'probability';       break;
                    case 'closed'               : $field = 'project closed';    break;
                    case 'typology_id'          : $field = 'test typology';     break;
                    default                     : $field = $key;                break;
                }

                $quotation_history = new QuotationHistory();
                $quotation_history->quotation_id      = $quotation->id;
                $quotation_history->user_id      = $user->id;
                $quotation_history->field        = $field;
                $quotation_history->old_value    = is_null($old_value) ? '' : $old_value;
                $quotation_history->new_value    = is_null($task_value) ? '' : $task_value;

                $quotation_history->save();
            }
        }
    }
}
