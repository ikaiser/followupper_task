<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Methodology extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'methodology';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
