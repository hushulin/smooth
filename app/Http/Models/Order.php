<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{

  protected $guarded = [];

  protected $dates = [];

  public static $rules = [

  ];

  public function user()
  {
    return $this->belongsTo('App\Http\Models\User', 'id_user', 'id_wechat');
  }

  public function object()
  {
    return $this->belongsTo('App\Http\Models\Object', 'id_object');
  }
  
  public function content()
  {
    return $this->belongsTo('App\Http\Models\Object', 'id_object');
  }

  public function getOrder()
  {

    $dateStr = date('Y-m-d', time());
    //$timestamp0     = strtotime($dateStr);
    $execute = DB::table('day_executes')->where('id', 3)->first();
    //if ($timestamp0 < $execute->end){
    $timestamp0 = $execute->end;
    //}
    $timestamp24 = strtotime($dateStr) + 86400;
    $result = DB::table('orders')
      ->select('orders.id', 'orders.id_user', 'orders.body_stake', 'orders.times', DB::raw('SUM(body_stake) as sum_body_stake'), 'users.id_wechat', 'users.id_introducer',
        'users.body_balance', 'users.is_disabled', 'users.grade')
      ->join('users', 'users.id_wechat', '=', 'orders.id_user')
      ->where('times', '>=', $timestamp0)
      ->where('times', '<=', $timestamp24)
      ->groupBy('id_user')
      ->get();

    return $result;

  }

  public function getOrderMonth()
  {
    $dateStr = date('Y-m-d', time());
    //$timestamp0     = strtotime($dateStr);
    $execute = DB::table('month_executes')->where('id', 1)->first();
    //if ($timestamp0 < $execute->end){
    $timestamp0 = $execute->end;
    //}
    $timestamp24 = strtotime($dateStr) + 86400;
    $result = DB::table('orders')
      ->select('orders.id', 'orders.id_user', 'orders.body_stake', 'orders.times', DB::raw('SUM(body_stake) as sum_body_stake'), 'users.id_wechat', 'users.id_introducer',
        'users.body_balance', 'users.is_disabled', 'users.grade')
      ->join('users', 'users.id_wechat', '=', 'orders.id_user')
      ->where('times', '>=', $timestamp0)
      ->where('times', '<=', $timestamp24)
      ->groupBy('id_user')
      ->get();

    return $result;
  }
}
