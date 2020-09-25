<?php

namespace App;

use Illuminate\Support\Facades\Auth;
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
        'company_contact_id',
        'sequential_number',
        'code',
        'description',
        'insertion_date',
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
        return $this->belongsToMany(User::class, 'quotation_user', 'quotation_id', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function company_contact()
    {
        return $this->belongsTo(CompanyContact::class, 'company_contact_id', 'id');
    }

    public function company_contacts()
    {
        return $this->belongsToMany(CompanyContact::class, 'quotation_company_contacts', 'quotation_id', 'ccontact_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function typologies()
    {
        return $this->belongsToMany(Typology::class, 'quotation_typology', 'quotation_id', 'typology_id');
    }

    public function methodology()
    {
        return $this->belongsTo(Methodology::class, 'methodology_id', 'id');
    }

    public function methodologies()
    {
        return $this->belongsToMany(Methodology::class, 'quotation_methodology', 'quotation_id', 'methodology_id');
    }

    public function history()
    {
        return $this->hasMany(QuotationHistory::class, 'quotation_id', 'id');
    }

    static function filter_quotation( $filters ){

      $quotations = Quotation::orderBy("name","asc");

      /* Methodologies */
      if( isset( $filters["methodologies"] ) && $filters["methodologies"] !== "" ) {
        $quotations = $quotations->whereHas( 'methodologies', function ($methodologies) use ($filters){
                        $methodologies->whereIn('id', $filters["methodologies"]);
                      });
      }

      /* Typologies */
      if( isset( $filters["typologies"] ) && $filters["typologies"] !== "" ) {
        $quotations = $quotations->whereHas( 'typologies', function ($typologies) use ($filters){
                        $typologies->whereIn('id', $filters["typologies"]);
                      });
      }

      /* Statuses */
      if( isset( $filters["statuses"] ) && $filters["statuses"] !== "" ) {
        $quotations = $quotations->whereHas( 'status', function ($statuses) use ($filters){
                        $statuses->whereIn('id', $filters["statuses"]);
                      });
      }

      /* Researchers */
      if( isset( $filters["researchers"] ) && $filters["researchers"] !== "" ) {
        $quotations = $quotations->where( function ($researchers) use ($filters) {
                        $researchers->whereIn('user_id', $filters["researchers"])
                                    ->whereHas( 'collaborators', function ($collaborators) use ($filters){
                                        $collaborators->whereIn('id', $filters["researchers"]);
                                    });
                      });
      }

      /* Companies */
      if( isset( $filters["companies"] ) && $filters["companies"] !== "" ) {
        $quotations = $quotations->whereHas( 'company', function ($companies) use ($filters){
                        $companies->whereIn('id', $filters["companies"]);
                      });
      }

      /* Probability */
      if( isset( $filters["probability"] ) && $filters["probability"] !== "" ) {
        $quotations = $quotations->where('chance', '=', $filters["probability"] );
      }

      /* Project delivery date */
      if( isset( $filters["project_delivery_date_from"] ) && $filters["project_delivery_date_from"] !== "" ) {
        $quotations = $quotations->whereDate('deadline', '>=', date('Y-m-d', strtotime($filters["project_delivery_date_from"]) ));
      }
      if( isset( $filters["project_delivery_date_to"] ) && $filters["project_delivery_date_to"] !== "" ) {
        $quotations = $quotations->whereDate('deadline', '<=', date('Y-m-d', strtotime($filters["project_delivery_date_to"]) ));
      }

      /* Project insertion date */
      if( isset( $filters["insertion_date_from"] ) && $filters["insertion_date_from"] !== "" ) {
        $quotations = $quotations->whereDate('insertion_date', '<=', date('Y-m-d', strtotime($filters["insertion_date_from"]) ));
      }
      if( isset( $filters["insertion_date_to"] ) && $filters["insertion_date_to"] !== "" ) {
        $quotations = $quotations->whereDate('insertion_date', '<=', date('Y-m-d', strtotime($filters["insertion_date_to"]) ));
      }

      /* Open projects */
      if( isset( $filters["open_projects"] ) && $filters["open_projects"] == "on" ) {
        $quotations = $quotations->where('closed', '=', 0 );
      }

      return $quotations->get();

    }

    static function filter_quotation_for_user( $filters ){

      $user = Auth::user();
      $userQuotations = $user->quotations()->get();
      $userQuotations = $userQuotations->merge($user->quotations_assigned);
      $userIds = [];
      /* Get ids */
      foreach ( $userQuotations as $userQuotation ) {
        $userIds[] = $userQuotation->id;
      }

      $quotations = Quotation::whereIn( "id", $userIds );

      /* Methodologies */
      if( isset( $filters["methodologies"] ) && $filters["methodologies"] !== "" ) {
        $quotations = $quotations->whereHas( 'methodologies', function ($methodologies) use ($filters){
                        $methodologies->whereIn('id', $filters["methodologies"]);
                      });
      }

      /* Typologies */
      if( isset( $filters["typologies"] ) && $filters["typologies"] !== "" ) {
        $quotations = $quotations->whereHas( 'typologies', function ($typologies) use ($filters){
                        $typologies->whereIn('id', $filters["typologies"]);
                      });
      }

      /* Statuses */
      if( isset( $filters["statuses"] ) && $filters["statuses"] !== "" ) {
        $quotations = $quotations->whereHas( 'status', function ($statuses) use ($filters){
                        $statuses->whereIn('id', $filters["statuses"]);
                      });
      }

      /* Researchers */
      // if( isset( $filters["researchers"] ) && $filters["researchers"] !== "" ) {
      //   $quotations = $quotations->where( function ($researchers) use ($filters) {
      //                   $researchers->whereIn('user_id', $filters["researchers"])
      //                               ->whereHas( 'collaborators', function ($collaborators) use ($filters){
      //                                   $collaborators->whereIn('id', $filters["researchers"]);
      //                               });
      //                 });
      // }

      /* Companies */
      if( isset( $filters["companies"] ) && $filters["companies"] !== "" ) {
        $quotations = $quotations->whereHas( 'company', function ($companies) use ($filters){
                        $companies->whereIn('id', $filters["companies"]);
                      });
      }

      /* Probability */
      if( isset( $filters["probability"] ) && $filters["probability"] !== "" ) {
        $quotations = $quotations->where('chance', '=', $filters["probability"] );
      }

      /* Project delivery date */
      if( isset( $filters["project_delivery_date_from"] ) && $filters["project_delivery_date_from"] !== "" ) {
        $quotations = $quotations->whereDate('deadline', '>=', date('Y-m-d', strtotime($filters["project_delivery_date_from"]) ));
      }
      if( isset( $filters["project_delivery_date_to"] ) && $filters["project_delivery_date_to"] !== "" ) {
        $quotations = $quotations->whereDate('deadline', '<=', date('Y-m-d', strtotime($filters["project_delivery_date_to"]) ));
      }

      /* Project insertion date */
      if( isset( $filters["insertion_date_from"] ) && $filters["insertion_date_from"] !== "" ) {
        $quotations = $quotations->whereDate('insertion_date', '<=', date('Y-m-d', strtotime($filters["insertion_date_from"]) ));
      }
      if( isset( $filters["insertion_date_to"] ) && $filters["insertion_date_to"] !== "" ) {
        $quotations = $quotations->whereDate('insertion_date', '<=', date('Y-m-d', strtotime($filters["insertion_date_to"]) ));
      }

      /* Open projects */
      if( isset( $filters["open_projects"] ) && $filters["open_projects"] == "on" ) {
        $quotations = $quotations->where('closed', '=', 0 );
      }

      return $quotations->get();

    }
}
