<?php

namespace App\Http\Controllers;

use App\Typology;
use Illuminate\Http\Request;

class TypologyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $typology = Typology::all();
        return view('quotations.typology.index', ['typology' => $typology]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('quotations.typology.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'typology' => 'required|unique:typology,name',
        ]);

        $typology = new Typology();

        $typology->name = $request->get('typology');
        $typology->save();

        return redirect()->route('quotations_typology.index')->with('message', __('Typology Added Successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $typology_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($typology_id)
    {
        $typology = Typology::find($typology_id);

        return view('quotations.typology.edit', ['typology' => $typology]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $typology_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $typology_id)
    {
        $request->validate([
            'typology' => 'required|unique:typology,name,' . $typology_id,
        ]);

        $typology = Typology::find($typology_id);
        $typology->name = $request->get('typology');
        $typology->save();

        return redirect()->route('quotations_typology.index')->with('message', __('Typology Updated Successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $typology_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($typology_id)
    {
        $typology = Typology::find($typology_id);

        if(is_null($typology))
        {
            return redirect()->route('quotations_typology.index')->with('error', __('Typology Already Deleted or not Existing'));
        }

        $typology->delete();

        return redirect()->route('quotations_typology.index')->with('message', __('Typology Deleted Successfully!'));
    }
}
