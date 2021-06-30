<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Todo;
use App\User;
use App\Quotation;
use App\Activity;

class TodoController extends Controller
{

    public function show_all( Request $request ){
      $user  = Auth::user();

      /* Fields */
      $search["search_title"]       = ( isset( $_GET["search_title"] ) ) ? $_GET["search_title"] : "";
      $search["search_description"] = ( isset( $_GET["search_description"] ) ) ? $_GET["search_description"] : "";

      $search["search_start_date"]  = ( isset( $_GET["search_start_date"] ) ) ? $_GET["search_start_date"] : "";
      $search["search_end_date"]    = ( isset( $_GET["search_end_date"] ) ) ? $_GET["search_end_date"] : "";

      $search["user_search"]        = ( isset( $_GET["user_search"] ) ) ? $_GET["user_search"] : "";
      $search["quotation_search"]   = ( isset( $_GET["quotation_search"] ) ) ? $_GET["quotation_search"] : "";
      $search["activities_search"]  = ( isset( $_GET["activities_search"] ) ) ? $_GET["activities_search"] : "";

      $search["order_by"] = ( isset( $_GET["order_by"] ) ) ? $_GET["order_by"] : "user";

      if ( ( isset( $_GET["search_start_date"] ) ) && $_GET["search_start_date"] !== "" ) {
          $search["search_start_date"] = $_GET["search_start_date"];
          if(date('D', strtotime($_GET["search_start_date"])) === 'Mon') {
            $date = date("Y-m-d", strtotime($_GET["search_start_date"]) );
          }else{
            $currDate = strtotime( 'last monday', strtotime($_GET["search_start_date"]) );
            $date = date("Y-m-d", $currDate );
          }
      }else{
          $search["search_start_date"] = date("Y-m-d");
          if(date('D', strtotime("today")) === 'Mon') {
            $date     = date("Y-m-d");
          }else{
            $currDate = strtotime( 'last monday' );
            $date     = date("Y-m-d", $currDate );
          }
      }

      /* Create 7 days array */
      $daysArray = Todo::sevenDayArray( $date );
      $search["todo_in_days"] = $daysArray;

      $query = Todo::searchFilter( $search );
      $todos = $query->get();

      $quotationAll = Quotation::where( "closed", "=", 0 )
                             ->whereHas('status', function ($statusQuery){
                                  $statusQuery->where('name', 'like', '%C1%');
                                  $statusQuery->orWhere('name', 'like', '%A1%');
                             });

     $quotationFiltered = Quotation::where( "closed", "=", 0 )
                            ->whereHas('status', function ($statusQuery){
                                 $statusQuery->where('name', 'like', '%C1%');
                                 $statusQuery->orWhere('name', 'like', '%A1%');
                            });

      /* Filter also tables */
      if ( $search["quotation_search"] !== "" && count($search["quotation_search"]) > 0 ) {
        $quotationFiltered = $quotationFiltered->whereIn("id",$search["quotation_search"]);
      }

      $userAll = User::all();

      /* Filter also tables */
      if ( $search["user_search"] !== "" ){
        $userFiltered = User::whereIn("id",$search["user_search"])->get();
      }else{
        $userFiltered = User::all();
      }

      if ( !$user->hasRole("SuperAdmin") ){
        $quotationAll = $quotationAll->where(function($query) use ($user){
                                        $query->where('user_id', '=', $user->id);
                                        $query->orWhereHas('collaborators', function($collaborators) use ($user) {
                                            $collaborators->whereIn('id', [$user->id]);
                                        });
                                      })->get();

        $quotationFiltered = $quotationFiltered->where(function($query) use ($user){
                                        $query->where('user_id', '=', $user->id);
                                        $query->orWhereHas('collaborators', function($collaborators) use ($user) {
                                            $collaborators->whereIn('id', [$user->id]);
                                        });
                                      })->get();
      }else{
        $quotationAll      = $quotationAll->get();
        $quotationFiltered = $quotationFiltered->get();
      }

      /* Create users todo list */
      $usersTodoArray = [];

      /* Create users todo list */
      $quotationsTodoArray = [];

      // if ( $search["order_by"] == "user") {
      //   foreach ( $userAll as $key => $userSingle ){
      //     $userToDoArray = Todo::getTodoByUsers( $userSingle, $todos );
      //     if( count($userToDoArray) > 0 ){
      //       $usersTodoArray[$userSingle->id]["user"]            = $userSingle;
      //       $usersTodoArray[$userSingle->id]["quotation_todos"] = $userToDoArray;
      //     }
      //   }
      // }else{
      //   foreach ( $quotationAll as $key => $quotationSingle ){
      //     $quotationsTodoGroup = Todo::getTodoByQuotations( $quotationSingle, $todos );
      //     if( count($quotationsTodoGroup) > 0 ){
      //       $quotationsTodoArray[$quotationSingle->id]["quotation"]  = $quotationSingle;
      //       $quotationsTodoArray[$quotationSingle->id]["user_todos"] = $quotationsTodoGroup;
      //     }
      //   }
      // }

      if ( $search["order_by"] == "user") {
        foreach ( $userFiltered as $key => $userSingle ){
          $userToDoArray = Todo::getUserTable( $userSingle, $quotationFiltered, $todos );
          if( count($userToDoArray) > 0 ){
            $usersTodoArray[$userSingle->id]["user"]            = $userSingle;
            $usersTodoArray[$userSingle->id]["quotation_todos"] = $userToDoArray;
          }
        }
      }else{
        foreach ( $quotationFiltered as $key => $quotationSingle ){
          $quotationsTodoGroup = Todo::getQuotationTable( $quotationSingle, $userFiltered, $todos );
          if( count($quotationsTodoGroup) > 0 ){
            $quotationsTodoArray[$quotationSingle->id]["quotation"]  = $quotationSingle;
            $quotationsTodoArray[$quotationSingle->id]["user_todos"] = $quotationsTodoGroup;
          }
        }
      }

      $activities = Activity::all();

      return view( 'quotations.todos.superadmin_all', compact( 'usersTodoArray', 'quotationsTodoArray', 'daysArray', 'user', 'quotationAll', 'userAll', 'search', 'activities' ) );
    }

    public function create( Request $request ){

      $user = Auth::user();

      // $title       = $request->post("todo_title");
      $description = (!is_null($request->post("todo_description"))) ? $request->post("todo_description") : "";
      $startDate   = $request->post("start_date");
      $endDate     = $request->post("end_date");
      $quotationId = $request->post("todo_quotation");
      $todoUser    = $request->post("todo_user");
      $activity    = $request->post("todo_activity");

      $todo = new Todo();

      $todo->quotation_id = $quotationId;
      $todo->title        = "";
      $todo->description  = $description;

      $todo->start_date   = date("Y-m-d", strtotime($startDate));
      $todo->end_date     = date("Y-m-d", strtotime($endDate));

      $todo->user_id      = $todoUser;

      $todo->save();

      $todo->activities()->sync([$activity]);

      $todo->researcherMail();

      return redirect()->route("todos.superadmin-all")->with(["success" => __("Todo added successfully")]);

    }

    public function edit( Request $request, $todoId ){

      $user = Auth::user();

      // $title       = $request->post("todo_title");
      $description = (!is_null($request->post("todo_description"))) ? $request->post("todo_description") : "";
      $startDate   = $request->post("start_date");
      $endDate     = $request->post("end_date");
      $quotationId = $request->post("todo_quotation");
      $todoUser    = $request->post("todo_user");
      $activity    = $request->post("todo_activity");

      $todoCompleted = $request->post("todo_completed");

      $todo = Todo::find($todoId);

      $todo->quotation_id = $quotationId;
      $todo->title        = "";
      $todo->description  = $description;

      $todo->start_date   = date("Y-m-d", strtotime($startDate));
      $todo->end_date     = date("Y-m-d", strtotime($endDate));

      $todo->user_id      = $todoUser;

      $todo->completed    = $todoCompleted;

      $todo->save();

      $todo->activities()->sync([$activity]);

      return response()->json([
        'status'  => 'success',
        'message' => __('Todo edited successfully')
      ]);

    }

    public function delete( Request $request, $todoId ){
      $remove = Todo::find($todoId)->delete();
      return redirect()->route("todos.superadmin-all")->with(["success" => __("Todo deleted successfully")]);
    }
}
