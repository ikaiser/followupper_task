<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyContact;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $companies = Company::all();
        return view('companies.index', ['companies' => $companies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $contacts = $request->get('contact');
        foreach($contacts as $key => $contact)
        {
            if(empty($contact))
            {
                unset($contacts[$key]);
            }
        }
        $request->merge(['contact' => $contacts]);

        $rules = [
            'name'      => 'required|unique:company,name',
            'code'      => 'required|size:3|unique:company,code',
            'contact'   => 'required|array',
            'contact.*' => 'required'
        ];

        $customMessages = [
            'contact.*.required' => __('The company contact field is required.'),
        ];

        $request->validate($rules, $customMessages);

        $company = new Company();

        $company->name = $request->get('name');
        $company->code = $request->get('code');
        $company->save();

        foreach($request->get('contact') as $contact)
        {
            $company_contact = new CompanyContact();

            $company_contact->name = $contact;
            $company_contact->company_id = $company->id;

            $company_contact->save();
        }

        return redirect()->route('companies.index')->with('message', __('Company Added Successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $company_id
     * @return void
     */
    public function edit($company_id)
    {
        $company = Company::find($company_id);

        return view('companies.edit', ['company' => $company]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $company_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $company_id)
    {
        $contacts = $request->get('contact');
        foreach($contacts as $key => $contact)
        {
            if(empty($contact))
            {
                unset($contacts[$key]);
            }
        }
        $request->merge(['contact' => $contacts]);

        $rules = [
            'name'      => 'required|unique:company,name,' . $company_id,
            'code'      => 'required|size:3|unique:company,code,' . $company_id,
            'contact'   => 'required|array',
            'contact.*' => 'required'
        ];

        $customMessages = [
            'contact.*.required' => __('The company contact field is required.'),
        ];

        $request->validate($rules, $customMessages);

        $company = Company::find($company_id);

        $company->name = $request->get('name');
        $company->code = $request->get('code');
        $company->save();

        foreach($company->contacts as $contact)
        {
            $contact->delete();
        }

        foreach($request->get('contact') as $contact)
        {
            $company_contact = new CompanyContact();

            $company_contact->name = $contact;
            $company_contact->company_id = $company->id;

            $company_contact->save();
        }

        return redirect()->route('companies.index')->with('message', __('Company Updated Successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $company_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($company_id)
    {
        $company = Company::find($company_id);

        if(is_null($company))
        {
            return redirect()->route('companies.index')->with('error', __('Company Already Deleted or not Existing'));
        }

        $contacts = $company->contacts;

        foreach($contacts as $contact)
        {
            $contact->delete();
        }

        $company->delete();

        return redirect()->route('companies.index')->with('message', __('Company Deleted Successfully!'));
    }

    public function fetch(Request $request)
    {
        $query = $request->get('query');

        $companies = Company::where('name', 'LIKE', "%{$query}%")->get();

        $output = '<ul class="collection" style="display:block; position:relative">';
        foreach($companies as $company)
        {
            $output .= '<li class="collection-item" data-ref="company" data-value="' . $company->id . '"><a href="#">' . $company->name . '</a></li>';
        }
        $output .= '</ul>';
        echo $output;
    }

    public function get_contacts(Request $request)
    {
        $company_id = $request->get('company');

        $company = Company::find($company_id);

        return $company->contacts;
    }
}
