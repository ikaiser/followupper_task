<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function dc()
    {
        return $this->belongsToMany(Datacuration::class, 'dc_tags', 'tag_id', 'datacuration_id');
    }

    public function dce()
    {
        return $this->belongsToMany(DatacurationElement::class, 'dce_tags', 'tag_id', 'datacuration_element_id');
    }
}
