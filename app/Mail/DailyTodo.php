<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DailyTodo extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $todos;

    public function __construct($user,$todos)
    {
        $this->user  = $user;
        $this->todos = $todos;
    }

    public function build()
    {
        return $this->subject(__("TODO list to complete"))->markdown('emails.todos.daily_todo');
    }
}
