<?php

namespace App\Console;

use App\Datacuration;
use App\DatacurationElement;
use App\Mail\admin_report;
use App\Mail\CollaboratorsA1Report;
use App\Mail\CollaboratorsB1Report;
use App\Mail\CollaboratorsAmountReport;
use App\Mail\CollaboratorsDeliveredReport;
use App\Mail\AdminReport;
use App\Mail\DailyRecap;
use App\Mail\EndingQuotations;
use App\Mail\MissingAmountQuotations;
use App\Mail\DailyTodo;
use App\Quotation;
use App\Task;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

      $schedule->command('view:clear')->cron('0 4 * * *');
      $schedule->command('cache:clear')->cron('10 4 * * *');
      $schedule->command('route:cache')->cron('20 4 * * *');
      $schedule->command('config:cache')->cron('30 4 * * *');

      /* TODOS all day at 9 */
      $schedule->call( function (){
        $users = User::all();
        foreach( $users as $user ) {
          $quotations = Quotation::all();
          $todos      = [];
          foreach( $quotations as $key => $quotation ) {
            $mainUser      = $quotation->user;
            $collaborators = $quotation->collaborators->pluck("id")->toArray();
            $researchers   = array_merge([$mainUser->id],$collaborators);

            if( in_array( $user->id(), $researchers ) && $quotation->todosNotDone > 0 ){
              $todos[$key]["quotation"] = $quotation;
              $todos[$key]["todos"]     = $quotation->todosNotDone;
            }
          }
          if (count($todos) > 0){
            Mail::to($user)->send(New DailyTodo($user, $todos));
          }
        }
      } )->cron('0 9 * * mon-fri'); /* Daily at 9 not saturday and sunday */

      /* Collaborators daily STATUS A1 */
      $schedule->call( function () {

        $quotations = [];
        $quotations = Quotation::whereHas('status', function ($query) {
            $query->where('name', 'like', '%A1%');
        })->get();

        $user_list = [];

        foreach( $quotations as $key => $quotation ) {

          /* User */
          if ( !is_null( $quotation->user ) && !is_null( $quotation->user->email ) ) {
            $user_list[$quotation->user->email][] = $quotation;
          }

          /* Collaborators */
          if ( !empty($quotation->collaborators) ) {
            foreach( $quotation->collaborators as $k => $collaborators ) {
                $user_list[$collaborators->email][] = $quotation;
            }
          }

        }

        foreach ( $user_list as $userEmail => $quotationList ){
          $user = User::where("email", $userEmail)->get()->first();
          Mail::to($user)
          ->cc("tommaso.pronunzio@alesresearch.com")
          ->send(New CollaboratorsA1Report( $user, $quotationList ));
        }

      } )->cron('0 12 * * *'); /* Daily at 12 */

      /* Collaborators weekly STATUS B1 */
      $schedule->call( function () {

        $quotations = [];
        $quotations = Quotation::whereHas('status', function ($query) {
            $query->where('name', 'like', '%B1%');
        })->get();

        $user_list = [];

        foreach( $quotations as $key => $quotation ) {

          /* User */
          if ( !is_null( $quotation->user ) && !is_null( $quotation->user->email ) ) {
            $user_list[$quotation->user->email][] = $quotation;
          }

          /* Collaborators */
          if ( !empty($quotation->collaborators) ) {
            foreach( $quotation->collaborators as $k => $collaborators ) {
                $user_list[$collaborators->email][] = $quotation;
            }
          }

        }

        foreach ( $user_list as $userEmail => $quotationList ) {
          $user = User::where("email", $userEmail)->get()->first();
          Mail::to($user)
          ->cc("tommaso.pronunzio@alesresearch.com")
          ->send(New CollaboratorsB1Report( $user, $quotationList ));
        }

      } )->cron('30 9 * * 4'); /* Weekly at 9:30 of thursday */

      /* Collaborators weekly amount not like A1 and 0 or NULL */
      $schedule->call( function () {

        $quotations = [];
        $quotations = Quotation::whereHas('status', function ($query) {
                          $query->where('name', 'not like', '%A1%');
                      })->where(function ($amountQuery) {
                          $amountQuery->where('amount', '=', 0)
                                      ->orWhereNull('amount');
                      })->get();

        $user_list = [];

        foreach( $quotations as $key => $quotation ) {

          /* User */
          if ( !is_null( $quotation->user ) && !is_null( $quotation->user->email ) ) {
            $user_list[$quotation->user->email][] = $quotation;
          }

          /* Collaborators */
          if ( !empty($quotation->collaborators) ) {
            foreach( $quotation->collaborators as $k => $collaborators ) {
                $user_list[$collaborators->email][] = $quotation;
            }
          }

        }

        foreach ( $user_list as $userEmail => $quotationList ) {
          $user = User::where("email", $userEmail)->get()->first();
          Mail::to($user)
          ->cc("tommaso.pronunzio@alesresearch.com")
          ->send(New CollaboratorsAmountReport( $user, $quotationList ));
        }

      } )->cron('30 9 * * 2'); /* Weekly at 9:30 of tuesday */

      /* Collaborators daily not delivered */
      $schedule->call( function () {

        $quotations = [];
        $quotations = Quotation::where( "closed", "=", 0 )
                               ->whereHas('status', function ($query) {
                                    $query->where('name', 'like', '%C1%');
                               })->get();

        $user_list = [];

        foreach( $quotations as $key => $quotation ) {

          /* User */
          if ( !is_null( $quotation->user ) && !is_null( $quotation->user->email ) ) {
            $user_list[$quotation->user->email][] = $quotation;
          }

          /* Collaborators */
          if ( !empty($quotation->collaborators) ) {
            foreach( $quotation->collaborators as $k => $collaborators ) {
                $user_list[$collaborators->email][] = $quotation;
            }
          }

        }

        foreach ( $user_list as $userEmail => $quotationList ) {
          $user = User::where("email", $userEmail)->get()->first();
          Mail::to($user)
          ->cc("tommaso.pronunzio@alesresearch.com")
          ->send(New CollaboratorsDeliveredReport( $user, $quotationList ));
        }

      } )->cron('10 12 * * 1,4'); /* Weekly at monday and thursday */

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
