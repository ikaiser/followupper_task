<?php

namespace App\Mail;

use App\Project;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewDocument extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $dce;
    public $project;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $dce)
    {
        $this->user = $user;
        $this->dce = $dce;
        $this->project = Project::find($dce->project_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.new_document');
    }
}
