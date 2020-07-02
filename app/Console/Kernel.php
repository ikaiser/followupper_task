<?php

namespace App\Console;

use App\Datacuration;
use App\DatacurationElement;
use App\Mail\DailyRecap;
use App\Task;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
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
            $users = User::whereHas('roles', function ($q) {$q->whereIn('id', [2, 3, 5]);})->get();

            foreach($users as $user)
            {
                $projects = $user->projects()->pluck('id');

                $rooms = Datacuration::whereIn('project_id', $projects)->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime('yesterday +18 hours')), date('Y-m-d H:i:s', strtotime('today +18 hours'))])->pluck('name');
                $files = DatacurationElement::whereIn('project_id', $projects)->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime('yesterday +18 hours')), date('Y-m-d H:i:s', strtotime('today +18 hours'))])->pluck('name');

                if($rooms->count() > 0 || $files->count() > 0)
                {
                    Mail::to($user)->send(New DailyRecap($user, $rooms, $files));
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
