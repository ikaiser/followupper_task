<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Datacuration extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'datacuration';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'thumbnail',
        'parent_dc',
        'project_id',
        'tags',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function getLevel($level = 1)
    {
        if(!is_null($this->parent_dc))
        {
            $parent = Datacuration::find($this->parent_dc);
            $level = $parent->getLevel(++$level);
        }
        return $level;
    }

    public function parent()
    {
        return $this->belongsTo(Datacuration::class, 'parent_dc');
    }

    public function childrens()
    {
        return $this->hasMany(Datacuration::class, 'parent_dc');
    }


    public function files()
    {
        return $this->belongsToMany(DatacurationElement::class, 'datacuration_element_datacuration', 'datacuration_id', 'datacuration_element_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'dc_tags', 'datacuration_id', 'tag_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'dc_user', 'dc_id', 'user_id');
    }
}
