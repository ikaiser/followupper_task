<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'name',
        'logo'
    ];

    public function dc()
    {
        return $this->hasMany(Datacuration::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }

    public function tasks_by_user($user_id)
    {
        $user = User::find($user_id);
        $role = $user->roles->first();

        if($role->id == 1 || $role->id == 2)
        {
            return $this->tasks;
        }

        $tasks = $this->tasks()->where('user_id', $user_id)->get();
        $tasks_assigned = $user->tasks_assigned()->where('project_id', $this->id)->get();

        $total_tasks = $tasks->merge($tasks_assigned);

        return $total_tasks;
    }

    public function files()
    {
        return $this->hasMany(DatacurationElement::class, 'project_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')->with('roles');
    }
}
