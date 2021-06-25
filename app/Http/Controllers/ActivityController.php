<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Activity;
use App\Todo;
use App\User;
use App\Quotation;

class ActivityController extends Controller
{
    /* Index page */
    public function index(){
      $activities = Activity::all();
      return view("activities.index", compact("activities"));
    }

    /* Create page */
    public function create(){
      return view("activities.create");
    }

    /* Store action */
    public function store( Request $request ){

      $rules = [
          'name' => 'required|max:255|unique:activities,name',
      ];

      $request->validate( $rules );

      $name        = $request->post("name");
      $description = $request->post("description");

      $activiy = new Activity();

      $activiy->name        = $name;
      $activiy->description = $description;

      $activiy->save();

      return redirect()->route('activities.index')->with('success', __('Activity added successfully!'));
    }

    /* Edit page */
    public function edit( $activityId ){
      $activiy = Activity::find($activityId);
      return view("activities.edit", compact("activiy"));
    }

    /* Update action */
    public function update( Request $request, $activityId ){

      $activiy = Activity::find($activityId);

      $rules = [
          'name' => 'required|max:255',
      ];

      $description = $request->post("description");
      $name        = $request->post("name");

      if ( $name != $activiy->name ){
        $rules["name"] = 'required|max:255|unique:activities,name';
      }

      $request->validate( $rules );

      $activiy->name        = $name;
      $activiy->description = $description;

      $activiy->save();

      return redirect()->route('activities.index')->with('success', __('Activity updated successfully!'));
    }

    /* Delete action */
    public function delete( $activityId ){
      Activity::find($activityId)->delete();
      return redirect()->route('activities.index')->with('success', __('Activity deleted successfully!'));
    }
}
