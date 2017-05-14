<?php

namespace App\Http\Controllers\Api;

use App\Http\Models\Order;
use App\Http\Models\Object;
use App\Http\Models\Record;
use App\Http\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ApiController extends \App\Http\Controllers\ApiController
{
  
  /**
   * 登录接口
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request)
  {
    if (empty($request->get('tel'))) {
      return response()->json(['status' => '500', 'msg' => '登录帐号不能为空!']);
    }
    if (empty($request->get('password'))) {
      return response()->json(['status' => '500', 'msg' => '验证码不能为空!']);
    }
    if ($info = User::where('id_wechat', $request->get('tel'))->first()) {
      if (md5($request->get('password')) == $info->password) {
        unset($info->password);
        return response()->json(['status' => '200', 'msg' => '登录成功', 'data' => $info]);
      } else {
        return response()->json(['status' => '500', 'msg' => '密码错误']);
      }
    } else {
      return response()->json(['status' => '500', 'msg' => '用户不存在']);
    }
  }
  
  public function register(Request $request)
  {
    if (empty($request->get('tel'))) {
      return response()->json(['status' => '500', 'msg' => '注册帐号不能为空!']);
    }
    if (empty($request->get('code'))) {
      return response()->json(['status' => '500', 'msg' => '验证码不能为空!']);
    }
    if (empty($request->get('type'))) {
      return response()->json(['status' => '500', 'msg' => '参数错误!']);
    }
    if (User::where('id_wechat', $request->get('tel'))->first()) {
      return response()->json(['status' => '500', 'msg' => '该手机号已存在']);
    } else {
      $data = [
        'id_wechat' => $request->get('tel'),
        'body_phone' => $request->get('tel')
      ];
      if ($request->get('type') == 1) {
        if (User::create($data)) {
          $info = User::where('id_wechat', $request->get('tel'))->first();
          return response()->json(['status' => '200', 'msg' => '注册成功', 'data' => $info]);
        } else {
          return response()->json(['status' => '500', 'msg' => '注册失败']);
        }
      } else {
        if ($request->get('code') == session('code')) {
          if ($id = User::create($data)) {
            $info = User::findOrFail($id);
            return response()->json(['status' => '200', 'msg' => '注册成功', 'data' => $info]);
          } else {
            return response()->json(['status' => '500', 'msg' => '注册失败']);
          }
        } else {
          return response()->json(['status' => '500', 'msg' => '验证码错误']);
        }
      }
    }
  }
  
  public function userInfo(Request $request)
  {
    $tel = $request->get('tel');
    if ($info = User::where('id_wechat', $tel)->first()) {
      unset($info->password);
      $dayTime = strtotime(date('Y-m-d 00:00:00'));
      $win = Order::where('id_user', $tel)->where('body_is_win', 1)->where('times', '>=', $dayTime)->sum('body_bonus');
      $draw = Order::where('id_user', $tel)->where('body_is_win', 0)->where('times', '>=', $dayTime)->sum('body_stake');
      $info->bunko = $win - $draw;
      $data = [
        'id_introducer' => $info->id_introducer,
        'username' => $info->id_wechat,
        'balance' => $info->body_balance,
        'bunko' => $info->bunko
      ];
      return response()->json(['status' => '200', 'msg' => '获取成功', 'data' => $data]);
    } else {
      return response()->json(['status' => '500', 'msg' => '该用户不存在']);
    }
  }
  
  public function object()
  {
    $objects = Object::where('is_disabled', '0')->orderBy('body_rank', 'desc')->get();
    foreach ($objects as $vo) {
      $vo->body_price_previous > $vo->body_price ? $vo->status = 0 : $vo->status = 1;
      $vo->body_price = sprintf('%.' . $vo->body_price_decimal . 'f', $vo->body_price);
      $vo->url = route('api.objectInfo', array('id' => $vo->id, 'period' => 60));
    }
    if (empty($objects)) {
      return response()->json(['status' => '500', 'msg' => '获取信息错误']);
    } else {
      return response()->json(['status' => '200', 'body' => $objects]);
    }
  }
  
  public function conduct(Request $request)
  {
    $tel = $request->get('tel');
    $pages = $request->get('pages');
    $pages == null ? 15 : $pages;
    if (empty($tel)) {
      return response()->json(['status' => '500', 'msg' => '查询用户不能为空']);
    } else {
      $info = Order::with('content')
        ->where('id_user', $tel)
        ->where('body_is_win', null)
        ->where('body_is_draw', null)
        ->select('id', 'id_user', 'id_object', 'body_price_buying', 'body_price_striked', 'body_stake', 'body_bonus', 'body_direction', 'body_time', 'times', 'created_at')
        ->paginate($pages);
      $data = json_decode(json_encode($info), true);
      foreach ($data['data'] as &$vo) {
        if ($vo['body_direction']) {
          //买涨
          $vo['content']['body_price'] >= $vo['body_price_buying'] ? $vo['content']['status'] = 1 : $vo['content']['status'] = 0;
        } else {
          //买跌
          $vo['content']['body_price'] >= $vo['body_price_buying'] ? $vo['content']['status'] = 0 : $vo['content']['status'] = 1;
        }
        //$sumTime = $vo['body_time'] + $vo['times'];
        //$time = strtotime(date('Y-m-d H:i:0', $sumTime)) - time();
        $time = (strtotime($vo['created_at']) + $vo['body_time']) - time();
        $time <= 0 ? $vo['Countdown'] = 0 : $vo['Countdown'] = $time;
      }
      $user = User::where('id_wechat', $tel)->first();
      $data['users']['body_balance'] = $user->body_balance;
      //当日盈亏
      $dayTime = strtotime(date('Y-m-d 00:00:00'));
      $win = Order::where('id_user', $tel)->where('body_is_win', 1)->where('times', '>=', $dayTime)->sum('body_bonus');
      $draw = Order::where('id_user', $tel)->where('body_is_win', 0)->where('times', '>=', $dayTime)->sum('body_stake');
      $data['users']['bunko'] = $win - $draw;
      if ($data['next_page_url'] != null) {
        $data['next_page_url'] = $data['next_page_url'] . '&tel=' . $tel . '&pages=' . $pages;
      }
      if ($data['prev_page_url'] != null) {
        $data['prev_page_url'] = $data['prev_page_url'] . '&tel=' . $tel . '&pages=' . $pages;
      }
      return response()->json(['status' => '200', 'body' => $data]);
    }
  }
  
  public function orderList(Request $request)
  {
    $tel = $request->get('tel');
    $pages = $request->get('pages');
    if (empty($tel) || empty($pages)) {
      return response()->json(['status' => '500', 'msg' => '缺少请求参数']);
    }
    $info = Order::where('id_user', $tel)
      ->whereNotNull('body_is_win')
      ->whereNotNull('body_is_draw')
      ->select('id_user', 'id_object', 'body_price_buying', 'body_price_striked', 'body_stake', 'body_bonus', 'body_direction', 'body_time', 'times')
      ->paginate($pages);
    $data = json_decode(json_encode($info), true);
    if ($data['next_page_url'] != null) {
      $data['next_page_url'] = $data['next_page_url'] . '&tel=' . $tel . '&pages=' . $pages;
    }
    if ($data['prev_page_url'] != null) {
      $data['prev_page_url'] = $data['prev_page_url'] . '&tel=' . $tel . '&pages=' . $pages;
    }
    return response()->json(['status' => '200', 'object' => $data]);
  }
  
  public function getCurrent(Request $request)
  {
    $object_id = $request->get('object_id');
    if (empty($object_id)) {
      return response()->json(['status' => '500', 'msg' => '缺少请求参数']);
    }
    $object = Object::where('id', $object_id)->first();
    if (empty($object)) {
      return response()->json(['status' => '500', 'object' => '数据不存在']);
    } else {
      $object->body_price = sprintf('%.' . $object->body_price_decimal . 'f', $object->body_price);
      return response()->json(['status' => '200', 'body_price' => $object->body_price]);
    }
  }
  
  public function objectInfo($id, $period)
  {
    $period == null ? 60 : $period;
    $object = Object::find($id);
    return view('apps.apiObjects', [
      'navigator' => 'objects',
      'controller' => 'objectsDetailController',
      'item' => $object,
      'period' => $period
    ]);
  }
  
  public function postBalance(Request $request)
  {
    $tel = $request->get('tel');
    $total = $request->get('total');
    if (empty($tel) || empty($total)) {
      return response()->json(['status' => '500', 'msg' => '参数错误']);
    }
    $info = User::where('id_wechat', $tel)->first();
    if ($info->body_balance >= $total) {
      return response()->json(['status' => '200', 'msg' => '余额充足']);
    } else {
      return response()->json(['status' => '500', 'msg' => '余额不足']);
    }
  }
  
  public function postOrders(Request $request)
  {
    if (empty($request->get('id_object')) || empty($request->get('id_user')) || empty($request->get('body_stake')) || $request->get('body_direction') == null || empty($request->get('body_time'))) {
      return response()->json(['status' => '500', 'msg' => '参数错误']);
    }
    $users = User::where('id_wechat', $request->get('id_user'))->first();
    $object = Object::where('id', $request->get('id_object'))->first();
    if ((strtotime($object->updated_at) + 180) < time()) {
      return response()->json(['status' => '500', 'msg' => '休市期間無法進行交易']);
    }
    if ($object->body_status == 0) {
      return response()->json(['status' => '500', 'msg' => '休市期間無法進行交易']);
    }
    if (empty($object)) {
      return response()->json(['status' => '500', 'msg' => '该商品不存在']);
    } else if (empty($users)) {
      return response()->json(['status' => '500', 'msg' => '该用户不存在']);
    }
    if ($users->body_balance < $request->get('body_stake')) {
      return response()->json(['status' => '500', 'msg' => '余额不足']);
    }
    //减少余额
    DB::table('users')->where('id_wechat', $request->get('id_user'))->decrement('body_balance', $request->get('body_stake'));
    //增加消费总额
    DB::table('users')->where('id_wechat', $request->get('id_user'))->increment('body_transactions', $request->get('body_stake'));
    $data = [
      'id_user' => $request->get('id_user'),
      'id_object' => $request->get('id_object'),
      'body_price_buying' => $object->body_price,
      'body_stake' => $request->get('body_stake'),
      'body_bonus' => $object->body_profit * $request->get('body_stake'),
      'body_direction' => $request->get('body_direction'),
      'body_time' => $request->get('body_time'),
      'times' => time()
    ];
    $order = Order::create($data);
    $rel = [
      'id_user' => $request->get('id_user'),
      'id_order' => $order->id,
      'body_name' => $request->get('body_direction') == 1 ? '買入看漲' : '買入看跌',
      'body_direction' => $request->get('body_direction'),
      'body_stake' => $request->get('body_stake')
    ];
    Record::create($rel);
    
    if (!$order) {
      return response()->json(['status' => '500', 'msg' => '下单过于频繁，请稍后再试']);
    } else {
      $this->computeNetwork($users, $order);
      if (env('ORDER_CONTROL')) {
        $this->computePrice($users, $order, $object);
      }
      return response()->json(['status' => '200', 'msg' => '下单成功']);
    }
  }
  
  
  /**
   * 获取短信发送
   * @return string
   */
  public function postSMS(Request $request)
  {
    $tel = $request->get('tel');
    if (empty($request->get('tel'))) {
      return response()->json(['status' => '500', 'msg' => '手机号不能为空!']);
    }
    $config = Config::get('sms');
    $code = rand(pow(10, (6 - 1)), pow(10, 6) - 1);
    session(['code' => $code]);
    $body = '验证码为:' . $code;
    $smsUrl = 'http://utf8.sms.webchinese.cn/?Uid=' . $config['Uid'] . '&Key=' . $config['key'] . '&smsMob=' . $tel . '&smsText=' . $body;
    $rel = file_get_contents($smsUrl);
    if ($rel == 1) {
      return response()->json(['status' => '200', 'msg' => '发送成功']);
    } else {
      return response()->json(['status' => '500', 'msg' => '发送失败']);
    }
  }
  
}
