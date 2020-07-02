<?php

namespace App\Mail;

use App\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewRoom extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $dc;
    public $project;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $dc)
    {
        $this->user = $user;
        $this->dc = $dc;
        $this->project = Project::find($dc->project_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.new_room');
    }
}
