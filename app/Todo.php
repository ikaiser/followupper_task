<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Mail\NewTodo;

use App\Todo;
use App\User;

class Todo extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'todo_activities', 'todo_id', 'activity_id');
    }

    public function researcherMail(){
      $quotation     = $this->quotation;
      $mainUser      = $quotation->user;
      $collaborators = $quotation->collaborators->pluck("id")->toArray();
      $researchers   = array_merge([$mainUser->id],$collaborators);
      foreach($researchers as $userId )
      {
          $researcher = User::find($userId);
          Mail::to($researcher)->send(New NewTodo($researcher, $this));
      }
    }

    public static function searchFilter( $fields ){

      /* Simple fields */
      $query = Todo::where("title", 'LIKE' , '%'.$fields["search_title"].'%')
                   ->where("description", 'LIKE' , '%'.$fields["search_description"].'%');

      if ( $fields["user_search"] !== "" ) {
        $query = $query->whereIn("user_id", $fields["user_search"] );
      }

      if ( $fields["quotation_search"] !== "" ){
        $query = $query->whereIn("quotation_id", $fields["quotation_search"] );
      }

      if ( $fields["activities_search"] !== "" ){
        $query = $query->whereHas('activities', function ($q) use($fields) {
             $q->whereIn('id', $fields["activities_search"]);
        });
      }

      $query = $query->whereBetween('start_date', [reset($fields["todo_in_days"])["date"], end($fields["todo_in_days"])["date"]]);

      return $query;
    }

    public static function sevenDayArray( $date = "" ){

      /* First week */
      if( $date === "" ){
        $date = date();
      }

      $days = [];

      for($i=0;$i<7;$i++){
        $stringTime   = strtotime($date.' + '.$i.' day');
        $currDate     = date('Y-m-d', $stringTime);
        $dayName      = date('l', $stringTime);
        $dayNumber    = date('d', $stringTime);
        $dayMonthName = date('M', $stringTime);

        $days[$i]["label"] = __($dayName) . " " . $dayNumber . " " . __($dayMonthName);
        $days[$i]["date"]  = $currDate;
      }

      return $days;
    }

    public static function fourWeekArray( $week = "", $year = "" ){

      /* First week */
      if( $week === "" ) {
        $week = date("W");
      }

      if( $year === "" ) {
        $year = date("Y");
      }

      $lastWeekN = getIsoWeeksInYear($year);
      $weeks     = [];

      $weekTmp = $week - 1;

      for($i=0;$i<4;$i++){

        /* Check first and last week of year */
        $weekTmp = $weekTmp + 1;

        if ( $weekTmp > $lastWeekN ) {
          $weekTmp = 1;
          $year    = $year + 1;
        }

        if ( $weekTmp > 0 && $weekTmp < 10 ) {
          $strtotimeString = $year.'W0'.$weekTmp;
        }else{
          $strtotimeString = $year.'W'.$weekTmp;
        }

        $mondayLabel = date('d M',strtotime($strtotimeString ));
        $sundayLabel = date('d M',strtotime($strtotimeString.' + 6 day'));

        $weekDates["monday"]    = date('Y-m-d',strtotime($strtotimeString));
        $weekDates["tuesday"]   = date('Y-m-d',strtotime($strtotimeString.' + 1 day'));
        $weekDates["wednesday"] = date('Y-m-d',strtotime($strtotimeString.' + 2 day'));
        $weekDates["thursday"]  = date('Y-m-d',strtotime($strtotimeString.' + 3 day'));
        $weekDates["friday"]    = date('Y-m-d',strtotime($strtotimeString.' + 4 day'));
        $weekDates["saturday"]  = date('Y-m-d',strtotime($strtotimeString.' + 5 day'));
        $weekDates["sunday"]    = date('Y-m-d',strtotime($strtotimeString.' + 6 day'));

        $weeks[$weekTmp]["label"]      = $mondayLabel ." - ". $sundayLabel;
        $weeks[$weekTmp]["week_dates"] = $weekDates;

      }

      return $weeks;
    }

    public static function getTodoByUsers( $user, $todos ){
      $userTodoList = [];
      $authUser     = Auth::user();
      foreach ( $todos as $key => $todo ) {
        $mainUser      = $todo->quotation->user;
        $collaborators = $todo->quotation->collaborators->pluck("id")->toArray();
        $researchers   = array_merge([$mainUser->id],$collaborators);

        if ( ( $todo->user_id == $user->id && $authUser->hasRole("SuperAdmin")) || ( $todo->user_id == $user->id && in_array($authUser->id,$researchers) ) ){
          $startDate=date("Y-m-d",strtotime($todo->start_date));
          $userTodoList[$todo->quotation_id]["quotation"] = $todo->quotation;
          $userTodoList[$todo->quotation_id]["todos"][$startDate][$todo->id] = $todo;
        }
      }

      return $userTodoList;
    }

    public static function getUserTable( $user, $quotations, $todos ){

      $userTodoList = [];
      $authUser     = Auth::user();

      foreach ($quotations as $quotation) {

        $mainUser      = $quotation->user;
        $collaborators = $quotation->collaborators->pluck("id")->toArray();
        $researchers   = array_merge([$mainUser->id],$collaborators);

        if ( in_array( $user->id, $researchers ) || $user->hasRole("SuperAdmin") ){
          $userTodoList[$quotation->id]["quotation"] = $quotation;
          $userTodoList[$quotation->id]["todos"]     = [];

          foreach ( $todos as $key => $todo ) {
            if( $todo->quotation_id == $quotation->id && $todo->user_id == $user->id ){

              $startDate=date("Y-m-d",strtotime($todo->start_date));
              $userTodoList[$todo->quotation_id]["todos"][$startDate][$todo->id] = $todo;
            }
          }
        }

      }

      return $userTodoList;
    }

    /* Only if exist the TODO */
    public static function getTodoByQuotations( $quotation, $todos ){
      $quotationTodoList = [];

      foreach ( $todos as $key => $todo ) {
        if ( $todo->quotation_id == $quotation->id ){
          $startDate=date("Y-m-d",strtotime($todo->start_date));
          $quotationTodoList[$todo->user_id]["user"] = $todo->user;
          $quotationTodoList[$todo->user_id]["todos"][$startDate][$todo->id] = $todo;
        }
      }

      return $quotationTodoList;
    }

    /* Complete table of quotation with target characteristics */
    public static function getQuotationTable( $quotation, $users, $todos ){

      $mainUser      = $quotation->user;
      $collaborators = $quotation->collaborators->pluck("id")->toArray();
      $researchers   = array_merge([$mainUser->id],$collaborators);
      $researchers[] = 1; // Add also superadmin

      $quotationTodoList = [];

      foreach ($researchers as $researcherId ) {

        $user = User::find($researcherId);
        $usersIds = $users->pluck("id")->toArray();

        if ( in_array( $user->id, $usersIds ) || $user->hasRole("SuperAdmin") ){
          $quotationTodoList[$researcherId]["user"]  = $user;
          $quotationTodoList[$researcherId]["todos"] = [];

          foreach ($todos as $todo){
            if( $todo->quotation_id == $quotation->id && $todo->user_id == $researcherId ){
              $startDate=date("Y-m-d",strtotime($todo->start_date));
              $quotationTodoList[$todo->user_id]["todos"][$startDate][$todo->id] = $todo;
            }
          }
        }
      }

      return $quotationTodoList;
    }

    public static function getTodoOnFourWeek( $user, $todos, $week = "", $year = "" ){

      /* First week */
      if( $week === "" ) {
        $week = date("W");
      }

      if( $year === "" ) {
        $year = date("Y");
      }

      $userTodoList = [];
      $allEmpty     = true;

      foreach ( $todos as $k => $todo ) {
        if ( $todo->user_id == $user->id ){

          $longTodo = false;

          $lastWeekN = getIsoWeeksInYear($year);
          $weekTmp   = $week - 1;

          for($i=0;$i<4;$i++){

            /* Check first and last week of year */
            $weekTmp = $weekTmp + 1;

            if ( $weekTmp > $lastWeekN ) {
              $weekTmp = 1;
              $year    = $year + 1;
            }

            if ( $weekTmp > 0 && $weekTmp < 10 ) {
              $strtotimeString = $year.'W0'.$weekTmp;
            }else{
              $strtotimeString = $year.'W'.$weekTmp;
            }

            $monday = date('Y-m-d',strtotime($strtotimeString));
            $sunday = date('Y-m-d',strtotime($strtotimeString.' + 6 day'));

            $start_date = explode("T",$todo->start_date); /* [0] date - [1] houre:minutes */
            $end_date   = explode("T",$todo->end_date ); /* [0] date - [1] houre:minutes */

            /* In week case */
            if ( strtotime($start_date[0]) >= strtotime($monday) && strtotime($end_date[0]) <= strtotime($sunday) ){
              $userTodoList[$weekTmp]["todos"][$todo->id] = $todo;
              $longTodo = false;
              $allEmpty = false;
            }else
            /* more then this week case */
            if ( strtotime($start_date[0]) >= strtotime($monday) && !(strtotime($end_date[0]) <= strtotime($sunday) ) && !(strtotime($start_date[0]) > strtotime($sunday) ) && !$longTodo ){
              $userTodoList[$weekTmp]["todos"][$todo->id] = $todo;
              $longTodo = true;
              $allEmpty = false;
            }else
            /* more then this more week case */
            if ( $longTodo && !(strtotime($end_date[0]) <= strtotime($sunday)) ){
              $userTodoList[$weekTmp]["todos"][$todo->id] = $todo;
              $longTodo = true;
              $allEmpty = false;
            }else
            /* stop more week case */
            if ( $longTodo && strtotime($end_date[0]) <= strtotime($sunday) ){
              $userTodoList[$weekTmp]["todos"][$todo->id] = $todo;
              $longTodo = false;
              $allEmpty = false;
            }else{
              if ( empty($userTodoList[$weekTmp]["todos"]) ) {
                $userTodoList[$weekTmp]["todos"] = [];
              }
            }

          }

        }
      }

      if( $allEmpty == true ){
        $userTodoList = [];
      }

      return $userTodoList;

    }

}
