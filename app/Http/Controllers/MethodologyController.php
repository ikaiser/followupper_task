<?php

namespace App\Http\Controllers;

use App\Methodology;
use Illuminate\Http\Request;

class MethodologyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $methodologies = Methodology::all();
        return view('quotations.methodology.index', ['methodologies' => $methodologies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('quotations.methodology.create');
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
            'name' => 'required|unique:methodology,name',
            'type' => 'required',
        ]);

        $methodology = new Methodology();

        $methodology->name = $request->get('name');
        $methodology->type = $request->get('type');

        $methodology->save();

        return redirect()->route('quotations_methodology.index')->with('message', __('Methodology Added Successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $methodology_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($methodology_id)
    {
        $methodology = Methodology::find($methodology_id);

        return view('quotations.methodology.edit', ['methodology' => $methodology]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $methodology_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $methodology_id)
    {
        $request->validate([
            'name' => 'required|unique:methodology,name,' . $methodology_id,
            'type' => 'required',
        ]);

        $methodology = Methodology::find($methodology_id);

        $methodology->name = $request->get('name');
        $methodology->type = $request->get('type');

        $methodology->save();

        return redirect()->route('quotations_methodology.index')->with('message', __('Methodology Updated Successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $methodology_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($methodology_id)
    {
        $methodology = Methodology::find($methodology_id);

        if(is_null($methodology))
        {
            return redirect()->route('quotations_methodology.index')->with('error', __('Methodology Already Deleted or not Existing'));
        }

        $methodology->delete();

        return redirect()->route('quotations_methodology.index')->with('message', __('Methodology Deleted Successfully!'));
    }
}
