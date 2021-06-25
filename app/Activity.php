<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public function todos()
    {
        return $this->belongsToMany(Todo::class, 'todo_activities', 'activity_id', 'todo_id');
    }
}
