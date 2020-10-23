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
        // $schedule->call(function () {

        //     // Preventivi in Scadenza
        //     $next_working_day = date('w');

        //     if($next_working_day == 5)
        //     {
        //         $next_working_day = date('Y-m-d', strtotime('+3 day'));
        //     }
        //     else
        //     {
        //         $next_working_day = date('Y-m-d', strtotime('+1 day'));
        //     }

        //     $users = DB::table('users')
        //         ->join('quotation', 'users.id', '=', 'quotation.user_id')
        //         ->where('deadline', $next_working_day)
        //         ->select('users.*')
        //         ->get();

        //     foreach($users as $user)
        //     {
        //         $user = User::find($user->id);

        //         $quotations = $user->quotations()->where('deadline', $next_working_day)->get();

        //         if($quotations->count() > 0)
        //         {
        //             Mail::to($user)
        //             ->cc("tommaso.pronunzio@alesresearch.com")
        //             ->send(New EndingQuotations($user, $quotations));
        //         }
        //     }

        //     //Preventivi senza Importo
        //     $today = date('Y-m-d');
        //     $users = DB::table('users')
        //         ->join('quotation', 'users.id', '=', 'quotation.user_id')
        //         ->whereRaw("MOD(DATEDIFF({$today}, quotation.insertion_date), 3) = 0")
        //         ->whereDate('quotation.insertion_date', '<', $today)
        //         ->whereNull('quotation.amount')
        //         ->orWhere('quotation.amount', '=', 0)
        //         ->select('users.id', 'quotation.id as quotation_id')
        //         ->get();

        //     $quotation_users = array();
        //     foreach($users as $user)
        //     {
        //         $quotation_users[$user->id][] = $user->quotation_id;
        //     }

        //     foreach($quotation_users as $key => $quotations)
        //     {
        //         $user = User::find($key);

        //         Mail::to($user)
        //         ->cc("tommaso.pronunzio@alesresearch.com")
        //         ->send(New MissingAmountQuotations($user, $quotations));
        //     }

        // })->cron('0 18 * * *');


        // $schedule->call(function () {

        //     $users = User::whereHas('roles', function($q) {$q->whereIn('id', ['1', '2']);})->get();

        //     $total_quotations = Quotation::all();
        //     $total_quotations = $total_quotations->count();
        //     $open_quotations = Quotation::where('closed', 0)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->get()->count();
        //     $open_not_invoiced_quotations = Quotation::where('closed', 0)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->where(function ($q) { $q->whereNull('invoice_amount')->orWhere('invoice_amount', 0);})->get()->count();
        //     $closed_not_invoiced_quotations = Quotation::where('closed', 1)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->where(function ($q) { $q->whereNull('invoice_amount')->orWhere('invoice_amount', 0);})->get()->count();

        //     $quotation_stats = array($total_quotations, $open_quotations, $open_not_invoiced_quotations, $closed_not_invoiced_quotations);

        //     foreach($users as $user)
        //     {
        //         Mail::to($user)
        //         ->cc("tommaso.pronunzio@alesresearch.com")
        //         ->send(New AdminReport($user, $quotation_stats));
        //     }
        // })->cron('0 10 * * 4'); /* Tuesday at 10 */

        /* Collaborators daily STATUS A1 */
        $schedule->call( function () {

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

      } )->cron('0 12 * * 1,4'); /* Weekly at monday and thursday */

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
