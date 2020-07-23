<?php

namespace App\Console;

use App\Datacuration;
use App\DatacurationElement;
use App\Mail\DailyRecap;
use App\Mail\EndingQuotations;
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
            switch($next_working_day)
            {
                case '0' : $next_working_day = date('Y-m-d', strtotime('+1 day')); break;
                case '7' : $next_working_day = date('Y-m-d', strtotime('+2 day')); break;
                case '6' : $next_working_day = date('Y-m-d', strtotime('+3 day')); break;
                default  : $next_working_day = date('Y-m-d'); break;
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
                    //Mail::to($user)->send(New EndingQuotations($user, $quotations));
                }
            }

            //Preventivi senza Importo
            $today = date('Y-m-d');
            $users = DB::table('users')
                ->join('quotation', 'users.id', '=', 'quotation.user_id')
                ->whereRaw("MOD(DATEDIFF({$today}, quotation.creation_date), 3) = 0")
                ->whereDate('quotation.creation_date', '<', $today)
                ->whereNull('quotation.amount')
                ->orWhere('quotation.amount', '=', 0)
                ->select('users.*')
                ->get();

            foreach($users as $user)
            {
                $user = User::find($user->id);

                $quotations = $user->quotations()->where('deadline', $next_working_day)->get();

                if($quotations->count() > 0)
                {
                    //Mail::to($user)->send(New MissingAmountQuotations($user, $quotations));
                }
            }

        })->cron('0 18 * * *');

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
