<?php

namespace App\Http\Controllers;

use App\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
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
}
