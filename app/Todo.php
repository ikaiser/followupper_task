<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Todo;

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

      return $query;
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
