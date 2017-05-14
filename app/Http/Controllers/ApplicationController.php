<?php

namespace App\Http\Controllers;

use App\Http\Models\Grade;
use App\Http\Models\PayRecharge;
use App\Http\Models\System;
use DB;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;

use App\Http\Models\User;
use App\Http\Models\Order;
use App\Http\Models\Object;
use App\Http\Models\Record;
use App\Http\Models\Captcha;
use App\Http\Models\PayRequest;
use App\Http\Models\WithdrawRequest;
use App\Http\Models\Feedback;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class ApplicationController extends Controller
{
  public $tel;
  public $code;
  
   public function __construct()
  {
    $this->tel = session('tel');
    $this->code = session('code');
  }

  public function home()
  {
    $user = User::where('id_wechat', $this->tel)->first();
    if (!$user) {
      return redirect('/login');
    }

    return redirect('/objects');

  }

  public function objects()
  {

    $user = User::where('id_wechat', $this->tel)->first();


    if ($user->is_disabled > 0) return $this->denialUser();
    $objects = Object::where('is_disabled', '0')->orderBy('body_rank', 'desc')->get();
    return view('apps.objects', [
      'navigator' => 'objects',
      'controller' => 'objectsController',
      'user' => $user,
      'objects' => $objects
    ]);

  }
  
  public function objectsDetail($id, $period)
  {
    
    $user = User::where('id_wechat', $this->tel)->first();
    if ($user->is_disabled > 0) return $this->denialUser();
    $object = Object::find($id);
    return view('apps.objectsDetail', [
      'navigator' => 'objects',
      'controller' => 'objectsDetailController',
      'user' => $user,
      'item' => $object,
      'period' => $period
    ]);
  }
  
  public function ordersHold()
  {
    
    $user = session('wechat.oauth_user');
    $user = User::where('id_wechat', $this->tel)->first();
    if ($user->is_disabled > 0) return $this->denialUser();
    $orders = Order::orderBy('created_at', 'desc')->where('id_user', $this->tel)->where('striked_at', '0000-00-00 00:00:00')->get();
    return view('apps.ordersHold', [
      'navigator' => 'ordersHold',
      'controller' => 'ordersHoldController',
      'user' => $user,
      'orders' => $orders
    ]);
    
  }
  
  public function ordersHistory()
  {
    $user = User::where('id_wechat', $this->tel)->first();
    if ($user->is_disabled > 0) return $this->denialUser();
    $orders = Order::orderBy('created_at', 'desc')->where('id_user', $this->tel)->where('striked_at', '<>', '0000-00-00 00:00:00')->paginate(20);
    return view('apps.ordersHistory', [
      'navigator' => 'ordersHistory',
      'controller' => 'ordersHistoryController',
      'user' => $user,
      'orders' => $orders
    ]);
  }
  
  public function ordersDetail($id)
  {
    
    // $user = session('wechat.oauth_user');
    $user = User::where('id_wechat', $this->tel)->first();
    
    if ($user->is_disabled > 0) return $this->denialUser();
    
    $item = Order::find($id);
    
    return view('apps.ordersDetail', [
      'navigator' => 'ordersHistory',
      'user' => $user,
      'item' => $item
    ]);
    
  }
  
  public function account()
  {
    $user = User::where('id_wechat', $this->tel)->first();
    
    if ($user->is_disabled > 0) return $this->denialUser();
    
    $count_refers = User::where('id_introducer', $this->tel)->count();
    
    $count_bonus = 0;
    $records = Record::where('id_user', $this->tel)->where('id_refer', '>', 0)->get();
    foreach ($records as $record) {
      $count_bonus = $count_bonus + $record->body_stake;
    }
    
    return view('application.account', [
      'title' => '会员中心',
      'user' => $user,
      'count_refers' => $count_refers,
      'count_bonus' => $count_bonus
    ]);
    
  }
  
  public function accountBind(Request $request)
  {
    if ($request->isMethod('post')) {
      $input = Input::all();
      if ($input['vcode'] != $this->code) {
        return view('application.info', [
          'title' => '绑定失败',
          'icon' => 'warn',
          'content' => '您填写的验证码不正确'
        ]);
      }

      if (empty($input['tid'])) {
        $tjr = '0';
    } else{
       $tjr = User::where('id', $input['tid'])->first();
      $tjr = $tjr -> id_wechat;
     }

      $user = User::where('id_wechat', $input['mobile'])->first();
      if (empty($user)) {
        $data = [
          'id_wechat' => $input['mobile'],
          'body_phone' => $input['mobile'],
          'password' => md5($input['password']),
          'id_introducer' => $tjr,
        ];
        if ($id = User::create($data)) {
          session(['tel' => $input['mobile']]);
          return redirect('/');
        }
      } else {
        return view('application.info', [
          'title' => '注册失败',
          'icon' => 'warn',
          'content' => '该手机号已存在'
        ]);
      }

      
    }
     else {
      $id_introducer = User::whereId($request->get('tid'))->first();
      if (empty($request->get('tid'))) {
        return view('application.accountBind', [
          'title' => '账户激活'
        ]);
      } else {
        return view('application.accountBind', [
          'title' => '账户激活',
          'tid' => $id_introducer->id
        ]);
      }
    }
    
  }
  
  /**
   * 用户充值中心
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function accountPay(Request $request)
  {
    $user = User::where('id_wechat', $this->tel)->first();
    if ($user->is_disabled > 0) return $this->denialUser();
    $system = System::findOrFail(1);
    $tel = session('tel');
    return view('application.accountPay', [
      'title' => '我要充值',
      'system' => $system,
      'tel' => $tel
    ]);
  }
  
  public function accountPayStaff(Application $wechat)
  {
    return view('application.accountPayStaff', [
      'title' => '人工充值'
    ]);
  }
  
  /**
   * 提现记录
   * @param Application $wechat
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function accountWithdrawRecords()
  {
    $user = User::where('id_wechat', $this->tel)->first();
    
    if ($user->is_disabled > 0) return $this->denialUser();
    
    $withdrawRequests = WithdrawRequest::where('id_user', $this->tel)->orderBy('created_at', 'desc')->get();
    
    return view('application.accountWithdrawRecords', [
      'title' => '提现记录',
      'withdrawRequests' => $withdrawRequests
    ]);
    
  }
  
     public function Wechatpay($price)
  {

									
									require_once  '../wxpay/WxPay.Config.php';
									require_once  '../wxpay/WxPay.Data.php';
									require_once  '../wxpay/WxPay.NativePay.php';
									require_once  '../wxpay/WxPay.Api.php';
									
									$tijiao='http://' . $_SERVER['SERVER_NAME'] . '/wxpay/notify.php';
 									$notify = new \NativePay();
				
									$money      = $price*100;//自己刚才输入的金额一
											
                  $user = User::where('id_wechat', $this->tel)->first();
              			  
				
									$input = new \WxPayUnifiedOrder();
									$input->SetBody("环球商品汇");
									$input->SetAttach($user->id_wechat);
									$input->SetOut_trade_no((date("YmdHis")));
									$input->SetTotal_fee($money);
									$input->SetTime_start(date("YmdHis"));
									$input->SetTime_expire(date("YmdHis", time() + 600));
									$input->SetGoods_tag("test");
									$input->SetNotify_url($tijiao);
									$input->SetTrade_type("NATIVE");
									$input->SetProduct_id("123456789");
									$result = $notify->GetPayUrl($input);
				
									if($result['return_code'] == 'SUCCESS'){
                 //这个if里面的就是自己定义的内容，比如生成二维码之后你要插入一条数据进入数据库之类  上面组装是必须要有的 下面这个是自己自定义的


											$url = $result["code_url"];
											return view("application.WxPayDir",['url'=>$url]);
										}
										else{	

											
											return view('application.WxPayerror', ['result'=>$result]);
												}//微信支付结尾
  
  
}
  



     public function zypay($price)
     {

            
            $user = User::where('id_wechat', $this->tel)->first();
            $payRequest = new PayRequest;
            $payRequest->id_user = $user->id_wechat;
            $payRequest->body_stake = $price;
            $payRequest->body_gateway = 'online';
            $payRequest->save();

            $parameterForRequest = '';
            $parameterForSign = '';
            $parameters = array(
                'pay_memberid' => env('PAYMENT_PID'),
                'pay_orderid' => date('YmdHis'),
                'pay_amount' => $payRequest->body_stake,
                'pay_applydate' => date('Y-m-d H:i:s'),
                'pay_bankcode' => 'WXZF',
                'pay_notifyurl' => env('PAYMENT_URL_NO'),
                'pay_callbackurl' => env('PAYMENT_URL_RE'),
            );
            ksort($parameters);
            reset($parameters);
            foreach ($parameters as $key => $value) {
                $parameterForSign = $parameterForSign . $key . '=>' . $value . '&';
            }
            $sign = strtoupper(md5($parameterForSign . 'key=' . env('PAYMENT_KEY')));
            $parameters['pay_md5sign'] = $sign;
            
			$pay_reserved1 = $payRequest->id; /*新增*/
			$tongdao = 'WftWx';
            $requestURL = 'http://zf.cnzypay.com/Pay_Index.html';

            return view('application.accountPayRedirect', [
			    'pay_reserved1' => $pay_reserved1,
			    'tongdao' => $tongdao,
                'requestURL' => $requestURL,
                'parameters' => $parameters,
                'sign' => $sign
            ]);

}

     public function zypayb($price)
     {

            
            $user = User::where('id_wechat', $this->tel)->first();
            $payRequest = new PayRequest;
            $payRequest->id_user = $user->id_wechat;
            $payRequest->body_stake = $price;
            $payRequest->body_gateway = 'online';
            $payRequest->save();

            $parameterForRequest = '';
            $parameterForSign = '';
            $parameters = array(
                'pay_memberid' => env('PAYMENT_PID'),
                'pay_orderid' => date('YmdHis'),
                'pay_amount' => $payRequest->body_stake,
                'pay_applydate' => date('Y-m-d H:i:s'),
                'pay_bankcode' => 'alipay',
                'pay_notifyurl' => env('PAYMENT_URL_NO'),
                'pay_callbackurl' => env('PAYMENT_URL_RE'),
            );
            ksort($parameters);
            reset($parameters);
            foreach ($parameters as $key => $value) {
                $parameterForSign = $parameterForSign . $key . '=>' . $value . '&';
            }
            $sign = strtoupper(md5($parameterForSign . 'key=' . env('PAYMENT_KEY')));
            $parameters['pay_md5sign'] = $sign;
            
			$pay_reserved1 = $payRequest->id; /*新增*/
			$tongdao = 'WftZfb';
            $requestURL = 'http://zf.cnzypay.com/Pay_Index.html';

            return view('application.accountPayRedirect', [
			    'pay_reserved1' => $pay_reserved1,
			    'tongdao' => $tongdao,
                'requestURL' => $requestURL,
                'parameters' => $parameters,
                'sign' => $sign
            ]);
            

}

     public function xftali()
     {

    return view('application.xftali', [
      'title' => '个人支付宝充值',
    ]);
            

}









  
  
  /**
   * 提现页面
   * @param Request $request
   * @param Application $wechat
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function accountWithdraw(Request $request, Application $wechat)
  {
    $user = User::where('id_wechat', $this->tel)->first();
    
    if ($user->is_disabled > 0) return $this->denialUser();
    
    if ($request->isMethod('post')) {
      
      if (!$request->input('name', null)
        || !$request->input('number', null)
        || !$request->input('bank', null)
        || !$request->input('deposit', null)
        || !$request->input('stake', null)
        || !$request->input('codes', null)
      ) {
        return view('application.info', [
          'title' => '提现失败',
          'icon' => 'warn',
          'content' => '请将表单填写完整，谢谢'
        ]);
      }
      
      if ($request->input('codes') != $this->code) {
        return view('application.info', [
          'title' => '绑定失败',
          'icon' => 'warn',
          'content' => '您填写的验证码不正确'
        ]);
      }
      
      if (intval($request->input('stake', 0)) < 100) {
        return view('application.info', [
          'title' => '提現失敗',
          'icon' => 'warn',
          'content' => '單次提現金額不得低於 100 元'
        ]);
      }
      
      if (intval($request->input('stake', 0)) > intval($user->body_balance)) {
        return view('application.info', [
          'title' => '提現失敗',
          'icon' => 'warn',
          'content' => '您當前的帳戶餘額不足'
        ]);
      }
      
      if (intval($request->input('stake', 0)) % 100 != 0) {
        return view('application.info', [
          'title' => '提現失敗',
          'icon' => 'warn',
          'content' => '提现金额必须为 100 元的倍数'
        ]);
      }
      
      $orderSum = Order::where('id_user', $this->tel)->sum('body_stake');
      if (intval($orderSum) < 300) {
        return view('application.info', [
          'title' => '提現失敗',
          'icon' => 'warn',
          'content' => '为避免恶意透支，累积交易金额超过 300 元即可提现'
        ]);
      }
      
      DB::beginTransaction();
      
      $user->body_balance = $user->body_balance - $request->input('stake');
      $user->save();
      
      if ($user->body_balance < 0) {
        DB::rollback();
      } else {
        $withdrawRequest = new WithdrawRequest;
        $withdrawRequest->id_user = $this->tel;
        $withdrawRequest->body_stake = $request->input('stake');
        $withdrawRequest->body_name = $request->input('name');
        $withdrawRequest->body_bank = $request->input('bank');
        $withdrawRequest->body_deposit = $request->input('deposit');
        $withdrawRequest->body_number = $request->input('number');
        $withdrawRequest->save();
        
        $record = new Record;
        $record->id_user = $this->tel;
        $record->id_withdrawRequest = $withdrawRequest->id;
        $record->body_name = '结余提现';
        $record->body_direction = 0;
        $record->body_stake = $withdrawRequest->body_stake;
        $record->save();
      }
      
      DB::commit();
      
      return view('application.info', [
        'title' => '申请成功',
        'icon' => 'success',
        'content' => '理我们已经收到您的提现申请，将在24小时内处理'
      ]);
      
    } else {
      return view('application.accountWithdraw', [
        'title' => '我要提现',
        'user' => $user
      ]);
    }
    
  }
  
  public function accountRecords(Application $wechat)
  {
    
    $user = session('wechat.oauth_user');
    $user = User::where('id_wechat', $this->tel)->first();
    
    if ($user->is_disabled > 0) return $this->denialUser();
    
    $records = Record::orderBy('created_at', 'desc')->where('id_user', $this->tel)->paginate(20);
    
    return view('application.accountRecords', [
      'title' => '资金记录',
      'records' => $records
    ]);
    
  }
  
  public function accountOrders(Application $wechat)
  {
    
    $user = session('wechat.oauth_user');
    $user = User::where('id_wechat', $this->tel)->first();
    
    if ($user->is_disabled > 0) return $this->denialUser();
    
    $orders = Order::orderBy('created_at', 'desc')->where('id_user', $this->tel)->paginate(20);
    
    return view('application.accountOrders', [
      'title' => '交易记录',
      'orders' => $orders
    ]);
    
  }
  
  public function accountExpand(Application $wechat, $id)
  {
    
    $url = 'http://' . $_SERVER['SERVER_NAME'] . '/account/bind/?tid=' . $id;
    return view('application.accountExpand', [
      'qrcode' => \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($url),
      'url' => $url,
      'tid' =>$id
    ]);
    
  }
  
  
    public function appdown(Application $wechat)
  {
    
    $url = 'http://' . $_SERVER['SERVER_NAME'] . '/app.apk';
    return view('application.appdown', [
      'qrcode' => \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($url),
      'url' => $url,
    ]);
    
  }
  
  public function support(Application $wechat)
  {
    return view('application.support', [
      'title' => '在线咨询'
    ]);
  }
  
  public function supportFaq(Application $wechat)
  {
    return view('application.supportFaq', [
      'title' => '常见问题'
    ]);
  }
  
  public function supportService(Application $wechat)
  {
    return view('application.supportService', [
      'title' => '在线客服'
    ]);
  }
  
  public function supportFeedback(Request $request, Application $wechat)
  {
    
    if ($request->isMethod('post')) {
      
      if (!$request->input('content', null)
        || !$request->input('tool', null)
        || !$request->input('number', null)
      ) {
        return view('application.info', [
          'title' => '反馈失败',
          'icon' => 'warn',
          'content' => '請將表單填寫完整，謝謝'
        ]);
      }
      
      $feedback = new Feedback;
      $feedback->body_content = $request->input('content');
      $feedback->body_tool = $request->input('tool');
      $feedback->body_number = $request->input('number');
      $feedback->save();
      
      return view('application.info', [
        'title' => '反馈成功',
        'icon' => 'success',
        'content' => '我们已经收到您的反馈，谢谢'
      ]);
      
    } else {
      
      return view('application.supportFeedback', [
        'title' => '意见反馈'
      ]);
      
    }
    
  }
  
  /**
   * 获取短信发送
   * @return string
   */
public function postSMS()
  {
    $input = Input::all();
    $input;
    $tel = $input['tel'];
    $type = empty($input['type']) ? 0 : 1;
    if ($type == 1) {
      $code = rand(pow(10, (6 - 1)), pow(10, 6) - 1);
      session(['code' => $code]);
      $body = urlencode("【庆邦电商】你的验证码是" . $code . "，请在10分钟内输入。");
      $smsUrl = env('SMS_BASE'). env('SMS_KEY') . '&mobile='. $tel .'&content='. $body;
      $rel = explode(',', file_get_contents($smsUrl));
      $rel = explode(':',$rel[0]);
      $rel = $rel[1];
      return $rel;
    } else {
      if (User::where('id_wechat', $tel)->first()) {
        return '106';
      } else {
        $code = rand(pow(10, (6 - 1)), pow(10, 6) - 1);
        session(['code' => $code]);
      $body = urlencode("【庆邦电商】你的验证码是" . $code . "，请在10分钟内输入。");
      $smsUrl = env('SMS_BASE'). env('SMS_KEY') . '&mobile='. $tel .'&content='. $body;
      $rel = explode(',', file_get_contents($smsUrl));
      $rel = explode(':',$rel[0]);
       $rel = $rel[1];
        return $rel;
      }
    }
  }
  
  /**
   * 用户密码修改
   * @param Request $request
   * @param $id
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function updatePassword(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      if (User::where('id', $id)->update(['password' => md5($request->get('password'))])) {
        echo '<script type="text/javascript">';
        echo 'alert("密码修改成功");';
        echo 'window.location.href="/account";';
        echo '</script>';
      } else {
        echo '<script type="text/javascript">';
        echo 'alert("密码修改失败");';
        echo 'window.location.href="/account";';
        echo '</script>';
      }
    } else {
      return view('application.accountPassword');
    }
  }
  
  /**
   * 用户登录
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function login(Request $request)
  {
    if ($request->isMethod('post')) {
      if (User::where('id_wechat', $request->get('id_wechat'))->where('password', md5($request->get('password')))->first()) {
        session(['tel' => $request->get('id_wechat')]);
        return redirect('/objects');
      } else {
        echo '<script type="text/javascript">';
        echo 'alert("手机号或者密码错误");';
        echo 'window.history.back();';
        echo '</script>';
      }
    } else {
      return view('application.login', [
        'title' => '登录中心'
      ]);
    }
  }
  
  /**
   * 密码找回手机验证
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
   */
  public function back(Request $request)
  {
    if ($request->isMethod('post')) {
      if ($request->get('vcode') != $this->code) {
        return view('application.info', [
          'title' => '验证失败',
          'icon' => 'warn',
          'content' => '您填写的验证码不正确'
        ]);
      } else {
        //验证通过(验证手机号是否存在)
        if (User::where('id_wechat', $request->get('mobile'))->first()) {
          return redirect('/account/backPassword/' . $request->get('mobile'));
        } else {
          return view('application.info', [
            'title' => '请注册信息',
            'icon' => 'warn',
            'content' => '无信息，请注册'
          ]);
        }
      }
    } else {
      return view('application.accountBack', [
        'title' => '找回密碼'
      ]);
    }
  }
  
  /**
   * 密码找回
   * @param Request $request
   * @param $tel
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function backPassword(Request $request, $tel)
  {
    if ($request->isMethod('post')) {
      if (User::where('id_wechat', $request->get('tel'))->update(['password' => md5($request->get('password'))])) {
        session(['tel' => $request->get('tel')]);
        echo '<script type = "text/javascript">';
        echo 'alert("密码修改成功");';
        if ($this->is_mobile()) {
          echo 'window . location . href = "/objects";';
        } else {
          echo 'window . location . href = "/account";';
        }
        echo '</script >';
      } else {
        echo '<script type = "text/javascript">';
        echo 'alert("密码修改失败");';
        echo 'window.history.back();';
        echo ' </script > ';
      }
    } else {
      return view('application.accountBackPassword', [
        'title' => '找回密碼',
        'tel' => $tel
      ]);
    }
  }
  
  /**
   * 用户等级审核管理
   * @param Request $request
   * @param $id
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function Grade(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $data = $request->all();
      if ($request->get('stake') == 100) {
        $data['grade'] = 1;
      } else if ($request->get('stake') == 200) {
        $data['grade'] = 2;
      } else if ($request->get('stake') == 300) {
        $data['grade'] = 3;
      } else if ($request->get('stake') == 400) {
        $data['grade'] = 4;
      }
      unset($data['stake']);
      $data['id_wechat'] = $this->tel;
      //判断该用户是否已经申请
      if ($info = Grade::where('id_wechat', $data['id_wechat'])->first()) {
        if ($data['grade'] == $info->grade) {
          return view('application.info', [
            'title' => '等级申请',
            'icon' => 'warn',
            'content' => '申请失败，不能选择自己拥有等级'
          ]);
        } else {
          if ($data['grade'] < $info->grade) {
            return view('application.info', [
              'title' => '等级申请',
              'icon' => 'warn',
              'content' => '申请失败，不能低于当前等级!'
            ]);
          } else {
            $data['old_grade'] = $info->grade;
            $data['status'] = 0;
            if (Grade::where('id', $info->id)->update($data)) {
              echo '<script type = "text/javascript">';
              echo 'alert("申请成功,请等待审核");';
              echo 'window . location . href = "/account";';
              echo '</script >';
            } else {
              return view('application.info', [
                'title' => '等级申请',
                'icon' => 'warn',
                'content' => '申请失败，请重试'
              ]);
            }
          }
        }
      } else {
        if (Grade::create($data)) {
          echo '<script type = "text/javascript">';
          echo 'alert("申请成功,请等待审核");';
          echo 'window . location . href = "/account";';
          echo '</script >';
        } else {
          return view('application.info', [
            'title' => '等级申请',
            'icon' => 'warn',
            'content' => '申请失败，请重试'
          ]);
        }
      }
    } else {
      $user = User::whereId($id)->first();
      return view('application.accountGrade', [
        'title' => '等级申请',
        'user' => $user
      ]);
    }
  }
  
  public function Extend($id)
  {
    $users = User::findOrFail($id);
    //我的上级
    if (!empty($users->id_introducer)) {
      $superior = $this->get_users($users->id_introducer);
    } else {
      $superior = null;
    }
    //我的下级列表
    $subordinate = User::where('id_introducer', $users->id_wechat)->get();
    //各等级的数量
    $data['one'] = User::where('id_introducer', $users->id_wechat)->where('grade', 1)->count();
    $data['two'] = User::where('id_introducer', $users->id_wechat)->where('grade', 2)->count();
    $data['three'] = User::where('id_introducer', $users->id_wechat)->where('grade', 3)->count();
    $data['four'] = User::where('id_introducer', $users->id_wechat)->where('grade', 4)->count();
    return view('application.accountExtend', [
      'title' => '推广统计',
      'superior' => $superior,
      'subordinate' => $subordinate,
      'data' => $data
    ]);
  }
  
  /**
   * 用户退出登录
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
   */
  public function loginOut(Request $request)
  {
    $request->session()->flush();
    return redirect('/login');
  }
  
  //获取用户ID
  public function get_users($id)
  {
    $data = User::where('id_wechat', $id)->first();
    return $data->id_wechat;
  }
  
  public function recharge($dollar, $price)
  {
    header("Content-type:text/html; charset=utf-8");
    $huanxun = Config::get('huanxun');
    //$price = '0.01';
    $MerCode = $huanxun['PAY_MID']; //商户号
    $Account = $huanxun['ACCOUNT']; //账户号
    $Mer_key = $huanxun['PAY_KEY'];  //MD5
//    $currUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
//    $currUrl .= $_SERVER['HTTP_HOST'];
    $Version = "v1.0.0";
    $MerCode = $MerCode;
    $MerName = "";
    $Account = $Account;
    $MsgId = "";
    $ReqDate = date('YmdHis');
    $Signature = "";
    $MerBillNo = $this->get_order_sn();  //商户订单号
    $GatewayType = "01"; //支付方式
    $Date = date('Ymd');  //订单日期
    $CurrencyType = "156";  //币种
    $Amount = number_format($price, 2, '.', ''); //订单金额
    $Lang = "GB";  //语言
    $Merchanturl = $huanxun['RETURN_URL']; //支付结果成功同步返回的url
    $FailUrl = ""; //支付失败返回的url
    $Attach = $_REQUEST['tel'] . '-' . $dollar; //商户数据包
    $OrderEncodeType = "5"; //订单支付接口加密方式
    $RetEncodeType = "17";  //交易返回接口加密方式
    $RetType = "1";  //返回方式
    $ServerUrl = $huanxun['NOTIFY_URL']; //支付结果成功异步返回的url
    $BillEXP = "";  //订单有效期
    $GoodsName = $huanxun['GOODNAME'];  //商品名称
    $IsCredit = "0";  //直连选项
    $BankCode = ""; //银行号
    $ProductType = $IsCredit;  //产品类型
    $xml = "<Ips><GateWayReq>";
    $body = "<body><MerBillNo>$MerBillNo</MerBillNo><Amount>$Amount</Amount><Date>$Date</Date><CurrencyType>$CurrencyType</CurrencyType><GatewayType>$GatewayType</GatewayType><Lang>$Lang</Lang><Merchanturl>$Merchanturl</Merchanturl><FailUrl>$FailUrl</FailUrl><Attach>$Attach</Attach><OrderEncodeType>$OrderEncodeType</OrderEncodeType><RetEncodeType>$RetEncodeType</RetEncodeType><RetType>$RetType</RetType><ServerUrl>$ServerUrl</ServerUrl><BillEXP>$BillEXP</BillEXP><GoodsName>$GoodsName</GoodsName><IsCredit>$IsCredit</IsCredit><BankCode>$BankCode</BankCode><ProductType>$ProductType</ProductType></body>";
    $Signature = md5($body . $MerCode . $Mer_key);
    $head = "<head><Version>$Version</Version><MerCode>$MerCode</MerCode><MerName>$MerName</MerName><Account>$Account</Account><MsgId>$MsgId</MsgId><ReqDate>$ReqDate</ReqDate><Signature>$Signature</Signature></head>";
    $xml .= $head . $body;
    $xml .= "</GateWayReq></Ips>";
    $form_url = $huanxun['POST_URL'];
    return view('application.Pay', [
      'title' => '环迅支付',
      'xml' => $xml,
      'form_url' => $form_url
    ]);
  }
  
  public function Notify()
  {
    header("Content-type:text/html; charset=utf-8");
    $huanxun = Config::get('huanxun');
    $MerCode = $huanxun['PAY_MID'];
    $Account = $huanxun['ACCOUNT'];
    $Mer_key = $huanxun['PAY_KEY'];
    $paymentResult = $_REQUEST['paymentResult'];
    if ($paymentResult != "") {
      $xml = simplexml_load_string($paymentResult);
      $xmlHead = $xml->GateWayRsp->head;
      $xmlBody = $xml->GateWayRsp->body;
      $RspCode = $xmlHead->RspCode;
      $RspMsg = $xmlHead->RspMsg;
      $Signature = $xmlHead->Signature;
      $MerBillNo = $xmlBody->MerBillNo;
      $CurrencyType = $xmlBody->CurrencyType;
      $Amount = $xmlBody->Amount;
      $Date = $xmlBody->Date;
      $Attach = $xmlBody->Attach;
      $Status = $xmlBody->Status;
      $Msg = $xmlBody->Msg;
      $Attach = $xmlBody->Attach;
      $IpsBillNo = $xmlBody->IpsBillNo;
      $IpsTradeNo = $xmlBody->IpsTradeNo;
      $RetEncodeType = $xmlBody->RetEncodeType;
      $BankBillNo = $xmlBody->BankBillNo;
      $ResultType = $xmlBody->ResultType;
      $IpsBillTime = $xmlBody->IpsBillTime;
      if ($RspCode == "000000") {
        if ($Status == "Y") {
          $body = "<body>" . "<MerBillNo>" . $MerBillNo . "</MerBillNo>" . "<CurrencyType>" . $CurrencyType . "</CurrencyType>" . "<Amount>" . $Amount . "</Amount>" . "<Date>" . $Date . "</Date>" . "<Status>" . $Status . "</Status>" . "<Msg><![CDATA[" . $Msg . "]]></Msg>" . "<Attach><![CDATA[" . $Attach . "]]></Attach>" . "<IpsBillNo>" . $IpsBillNo . "</IpsBillNo>" . "<IpsTradeNo>" . $IpsTradeNo . "</IpsTradeNo>" . "<RetEncodeType>" . $RetEncodeType . "</RetEncodeType>" . "<BankBillNo>" . $BankBillNo . "</BankBillNo>" . "<ResultType>" . $ResultType . "</ResultType>" . "<IpsBillTime>" . $IpsBillTime . "</IpsBillTime>" . "</body>";
          $SignatureNew = md5(($body . $MerCode . $Mer_key));
          if (strtolower($Signature) == strtolower($SignatureNew)) {
            $body = explode('-', $Attach);
            if (PayRequest::where('id_user', $body[0])->where('ips_order', $IpsBillNo)->first()) {
              Header("Location: /account");
            } else {
              if ($info = User::where('id_wechat', $body[0])->first()) {
                //获取系统配置
                $system = System::findOrFail(1);
                $poundage = $body[1] * ($system->interest_rate / 100); //手续费
                $total = ($info->body_balance + $body[1]) - $poundage;
                if (User::where('id_wechat', $body[0])->update(['body_balance' => $total])) {
                  PayRequest::create([
                    'id_user' => $body[0],
                    'body_stake' => $body[1],
                    'body_gateway' => '环迅支付',
                    'poundage' => $poundage,
                    'body_transfer_number' => $MerBillNo,
                    'ips_order' => $IpsBillNo,
                    'processed_at' => date('Y-m-d H:i:s', time())
                  ]);
                  echo 'success';
                } else {
                  echo "fail";
                }
              } else {
                echo "fail";
              }
            }
          } else {
            echo "fail";
          }
        } else {
          echo "error3";
        }
      } else {
        echo "error2";
      }
    } else {
      echo "error1";
    }
  }
  
  /**
   * 订单号生成
   * @return string
   */
  public function get_order_sn()
  {
    list($usec, $sec) = explode(" ", microtime());
    $mt = substr($usec, 2, 6);
    $randNum = mt_rand(100, 999);
    $orderSn = chr(date('Y') - 1951) . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5)
      . $mt . sprintf('%03d', $randNum);
    return $orderSn;
  }
  
  public function rechargeRecord()
  {
    //$list = PayRequest::where('id_user', $this->tel)->orderBy('created_at', 'desc')->paginate(10);
    $list = PayRequest::where('id_user', $this->tel)->where('processed_at', '<>', '0000-00-00 00:00:00')->orderBy('created_at', 'desc')->paginate(10);
    return view('application.rechargeRecord', [
      'title' => '充值纪录',
      'list' => $list
    ]);
  }
  
  //判断是否是手机
  public function is_mobile()
  {
    /* $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $is_pc = (strpos($agent, 'windows nt')) ? true : false;
    $is_mac = (strpos($agent, 'mac os')) ? true : false;
    $is_iphone = (strpos($agent, 'iphone')) ? true : false;
    $is_android = (strpos($agent, 'android')) ? true : false;
    $is_ipad = (strpos($agent, 'ipad')) ? true : false;
    if ($is_pc) {
      return false;
    }
    if ($is_mac) {
      return true;
    }
    if ($is_iphone) {
      return true;
    }
    if ($is_android) {
      return true;
    }
    if ($is_ipad) {
      return true;
    } */
	
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
        return true;
    } 
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    { 
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    } 
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
            ); 
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
            return true;
        } 
    } 
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    { 
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        } 
    } 
    return false;
  }
}
