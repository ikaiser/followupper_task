<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CollaboratorsB1Report extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $quotation_list;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $quotationList )
    {
        $this->user           = $user;
        $this->quotation_list = $quotationList;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.report_b1_collaborators');
    }
}
