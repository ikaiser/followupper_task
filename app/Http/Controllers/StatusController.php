<?php

namespace App\Http\Controllers;

use App\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $status = Status::all();
        return view('quotations.status.index', ['status' => $status]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('quotations.status.create');
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
            'status' => 'required',
        ]);

        $status = new Status();

        $status->name = $request->get('status');
        $status->save();

        return redirect()->route('quotations_status.index')->with('message', __('Status Added Successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $status_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($status_id)
    {
        $status = Status::find($status_id);

        return view('quotations.status.edit', ['status' => $status]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $status_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $status_id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $status = Status::find($status_id);
        $status->name = $request->get('status');
        $status->save();

        return redirect()->route('quotations_status.index')->with('message', __('Status Updated Successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $status_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($status_id)
    {
        $status = Status::find($status_id);

        if(is_null($status))
        {
            return redirect()->route('quotations_status.index')->with('error', __('Status Already Deleted or not Existing'));
        }

        $status->delete();

        return redirect()->route('quotations_status.index')->with('message', __('Status Deleted Successfully!'));
    }
}
