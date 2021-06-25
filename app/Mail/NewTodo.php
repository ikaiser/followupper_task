<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewTodo extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $todo;

    public function __construct($user, $todo)
    {
        $this->user = $user;
        $this->todo = $todo;
    }

    public function build()
    {
        return $this->subject(__("New TODO created"))->markdown('emails.todos.new_todo');
    }
}
