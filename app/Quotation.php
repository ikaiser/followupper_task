<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
        'company_id',
        'sequential_number',
        'code',
        'description',
        'deadline',
        'amount',
        'status_id',
        'amount_acquired',
        'chance',
        'feedback',
        'closed',
        'invoice_amount',
        'typology_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'invoice_user', 'invoice_id', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function typology()
    {
        return $this->belongsTo(Typology::class, 'typology_id', 'id');
    }
}
