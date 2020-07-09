<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Typology extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'typology';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
