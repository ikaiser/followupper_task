<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Todo;
use App\User;
use App\Quotation;

class TodoController extends Controller
{

    // public function show_all_old( Request $request ){
    //   $user  = Auth::user();
    //
    //   /* Fields */
    //   $search["search_title"]       = ( isset( $_GET["search_title"] ) ) ? $_GET["search_title"] : "";
    //   $search["search_description"] = ( isset( $_GET["search_description"] ) ) ? $_GET["search_description"] : "";
    //
    //   $search["search_start_date"] = ( isset( $_GET["search_start_date"] ) ) ? $_GET["search_start_date"] : "";
    //   $search["search_end_date"]   = ( isset( $_GET["search_end_date"] ) ) ? $_GET["search_end_date"] : "";
    //
    //   $search["user_search"]      = ( isset( $_GET["user_search"] ) ) ? $_GET["user_search"] : "";
    //   $search["quotation_search"] = ( isset( $_GET["quotation_search"] ) ) ? $_GET["quotation_search"] : "";
    //
    //   $query = Todo::searchFilter( $search );
    //
    //   $todos = $query->get();
    //
    //   $quotationAll = Quotation::all();
    //   $userAll      = User::all();
    //
    //   return view( 'quotations.todos.superadmin_all', compact( 'todos', 'user', 'quotationAll', 'userAll' ) );
    // }

    public function show_all( Request $request ){
      $user  = Auth::user();

      /* Fields */
      $search["search_title"]       = ( isset( $_GET["search_title"] ) ) ? $_GET["search_title"] : "";
      $search["search_description"] = ( isset( $_GET["search_description"] ) ) ? $_GET["search_description"] : "";

      $search["search_start_date"] = ( isset( $_GET["search_start_date"] ) ) ? $_GET["search_start_date"] : "";
      $search["search_end_date"]   = ( isset( $_GET["search_end_date"] ) ) ? $_GET["search_end_date"] : "";

      $search["user_search"]      = ( isset( $_GET["user_search"] ) ) ? $_GET["user_search"] : "";
      $search["quotation_search"] = ( isset( $_GET["quotation_search"] ) ) ? $_GET["quotation_search"] : "";

      if ( ( isset( $_GET["search_start_date"] ) ) && $_GET["search_start_date"] !== "" ) {
          $search["search_start_date"] = $_GET["search_start_date"];
          $year = date("Y", strtotime( $_GET["search_start_date"] ) );
          $week = date("W", strtotime( $_GET["search_start_date"] ));
      }else{
          $search["search_start_date"] = date("d-m-Y");
          $year = date("Y");
          $week = date("W");
      }

      $search["search_start_year"] = $year;
      $search["search_start_week"] = $week;

      $query = Todo::searchFilter( $search );

      $todos = $query->get();

      $quotationAll = Quotation::all();
      $userAll      = User::all();

      /* Create 4 weeks array */
      $weeksArray = Todo::fourWeekArray( $week, $year );

      /* Create usere to list for this 4 weeks */
      $usersTodoArray = [];

      foreach ( $userAll as $key => $userSingle ){
        $userToDoArray = Todo::getTodoOnFourWeek( $userSingle, $todos, $week, $year );
        if( count($userToDoArray) > 0){
          $usersTodoArray[$userSingle->id]["user"]  = $userSingle;
          $usersTodoArray[$userSingle->id]["todos"] = $userToDoArray;
        }
      }

      return view( 'quotations.todos.superadmin_all', compact( 'usersTodoArray', 'weeksArray', 'user', 'quotationAll', 'userAll', 'search' ) );
    }

    public function create( Request $request ){

      $user = Auth::user();

      $title       = $request->post("title");
      $description = $request->post("description");
      $startDate   = $request->post("start");
      $endDate     = $request->post("end");
      $allDay      = $request->post("all_day");
      $quotationId = $request->post("quotation");

      $todo = new Todo();

      $todo->quotation_id = $quotationId;
      $todo->title        = $title;
      $todo->description  = $description;

      $todo->start_date   = $startDate;
      $todo->end_date     = $endDate;
      $todo->all_day      = $allDay;

      $todo->user_id      = $user->id;

      $todo->save();

      return response()->json([
        'event'         => $todo->id,
        'title'         => $title,
        'description'   => $description,
        'start'         => $startDate,
        'end'           => $endDate,
        'all_day'       => $allDay
      ]);

    }

    public function edit( Request $request, $todoId ){

      $user = Auth::user();

      $title       = $request->post("title");
      $description = $request->post("description");
      $startDate   = $request->post("start");
      $endDate     = $request->post("end");
      $allDay      = $request->post("all_day");
      $quotationId = $request->post("quotation");

      $todo = Todo::find($todoId);

      $todo->quotation_id = $quotationId;
      $todo->title        = $title;
      $todo->description  = $description;

      $todo->start_date   = $startDate;
      $todo->end_date     = $endDate;
      $todo->all_day      = $allDay;

      /* change the todo creator */
      // $todo->user_id      = $user->id;

      $todo->save();

      return response()->json([
        'event'         => $todo->id,
        'title'         => $title,
        'description'   => $description,
        'start'         => $startDate,
        'end'           => $endDate,
        'all_day'       => $allDay
      ]);

    }

    public function delete( Request $request, $todoId ){
      $remove = Todo::find($todoId)->delete();
      $status = "error";

      if ( $remove ){
        $status = "success";
      }

      return response()->json([
        'status' => $status
      ]);
    }
}
