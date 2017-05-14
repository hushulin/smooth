<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{
  
  protected $guarded = [];
  
  protected $dates = [];
  
  public static $rules = [
  
  ];
  
  public function hasManyOrder()
  {
    return $this->hasMany(Order::class, 'id_user', 'id_wechat');
  }
  public function getUser($id){
      $result = array();
      if ($id > 0){
          $result  = DB::table('users')
              ->select('users.*')
              ->where('id_introducer',$id)
              ->get();
      }
      return $result;
  }
  public function getUpdate($array = array()){

      return DB::table('users')->where('id_wechat',$array['id_wechat'])->increment('body_balance', $array['body_balance']);

  }
}
