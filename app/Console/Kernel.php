<?php

namespace App\Console;

use App\Datacuration;
use App\DatacurationElement;
use App\Mail\admin_report;
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
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            // Preventivi in Scadenza
            $next_working_day = date('w');

            if($next_working_day == 5)
            {
                $next_working_day = date('Y-m-d', strtotime('+3 day'));
            }
            else
            {
                $next_working_day = date('Y-m-d', strtotime('+1 day'));
            }

            $users = DB::table('users')
                ->join('quotation', 'users.id', '=', 'quotation.user_id')
                ->where('deadline', $next_working_day)
                ->select('users.*')
                ->get();

            foreach($users as $user)
            {
                $user = User::find($user->id);

                $quotations = $user->quotations()->where('deadline', $next_working_day)->get();

                if($quotations->count() > 0)
                {
                    Mail::to($user)->send(New EndingQuotations($user, $quotations));
                }
            }

            //Preventivi senza Importo
            $today = date('Y-m-d');
            $users = DB::table('users')
                ->join('quotation', 'users.id', '=', 'quotation.user_id')
                ->whereRaw("MOD(DATEDIFF({$today}, quotation.insertion_date), 3) = 0")
                ->whereDate('quotation.insertion_date', '<', $today)
                ->whereNull('quotation.amount')
                ->orWhere('quotation.amount', '=', 0)
                ->select('users.id', 'quotation.id as quotation_id')
                ->get();

            $quotation_users = array();
            foreach($users as $user)
            {
                $quotation_users[$user->id][] = $user->quotation_id;
            }

            foreach($quotation_users as $key => $quotations)
            {
                $user = User::find($key);

                Mail::to($user)->send(New MissingAmountQuotations($user, $quotations));
            }

        })->cron('0 18 * * *');


        $schedule->call(function () {

            $users = User::whereHas('roles', function($q) {$q->whereIn('id', ['1', '2']);})->get();

            $total_quotations = Quotation::all();
            $total_quotations = $total_quotations->count();
            $open_quotations = Quotation::where('closed', 0)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->get()->count();
            $open_not_invoiced_quotations = Quotation::where('closed', 0)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->where(function ($q) { $q->whereNull('invoice_amount')->orWhere('invoice_amount', 0);})->get()->count();
            $closed_not_invoiced_quotations = Quotation::where('closed', 1)->where('chance', '>', 0)->where('insertion_date', '<=', date('Y-m-d'))->where(function ($q) { $q->whereNull('invoice_amount')->orWhere('invoice_amount', 0);})->get()->count();

            $quotation_stats = array($total_quotations, $open_quotations, $open_not_invoiced_quotations, $closed_not_invoiced_quotations);

            foreach($users as $user)
            {
                Mail::to($user)->send(New AdminReport($user, $quotation_stats));
            }
        //})->cron('0 18 * * 4');
        })->cron('* * * * *');

            // $schedule->command('inspire')
        //          ->hourly();
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
