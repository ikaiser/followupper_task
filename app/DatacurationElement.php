<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatacurationElement extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'datacuration_element';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'name',
        'thumbnail',
        'description',
        'topic',
        'project_id',
        'tags',
        'extension',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'file_id', 'id');
    }

    public function dc()
    {
        return $this->belongsToMany(Datacuration::class, 'datacuration_element_datacuration', 'datacuration_element_id', 'datacuration_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'dce_tags', 'datacuration_element_id', 'tag_id');
    }
}
