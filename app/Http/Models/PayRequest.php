<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PayRequest extends Model
{
  
  protected $guarded = [];
  
  protected $dates = [];
  
  public static $rules = [
  
  ];
  
  public function user()
  {
    return $this->belongsTo('App\Http\Models\User', 'id_user', 'id_wechat');
  }
  
}
