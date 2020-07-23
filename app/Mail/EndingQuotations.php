<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EndingQuotations extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $quotations;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $quotation
     */
    public function __construct($user, $quotations)
    {
        $this->user = $user;
        $this->quotations = $quotations;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.ending_quotations');
    }
}
