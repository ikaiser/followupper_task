<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Todo;

class Todo extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public static function searchFilter( $fields ){

      /* Simple fields */
      $query = Todo::where("title", 'LIKE' , '%'.$fields["search_title"].'%')
                   ->where("title", 'LIKE' , '%'.$fields["search_description"].'%');

      if ( $fields["user_search"] !== "" ) {
        $query = $query->whereIn("user_id", $fields["user_search"] );
      }

      if ( $fields["quotation_search"] !== "" ){
        $query = $query->whereIn("quotation_id", $fields["quotation_search"] );
      }

      return $query;
    }
}
