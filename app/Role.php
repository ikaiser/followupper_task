<?php
namespace App;

    use Zizaco\Entrust\EntrustRole;

    class Role extends EntrustRole {
      protected $fillable = ['permissions','name']; //<---- Add this line

      public function permissions(){
        // return $this->belongsToMany('Permission', 'permission_role', 'permission_id', 'role_id');
        return $this->belongsToMany('\App\Permission', 'permission_role');
      }

    }

?>
