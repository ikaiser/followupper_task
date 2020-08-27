<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminReport extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $quotation_stats;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $quotation_stats)
    {
        $this->user = $user;
        $this->quotation_stats = $quotation_stats;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.admin_report');
    }
}
