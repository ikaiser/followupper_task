<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DailyRecap extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $rooms;
    public $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $rooms, $files)
    {
        $this->user = $user;
        $this->rooms = $rooms;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.daily_recap');
    }
}
