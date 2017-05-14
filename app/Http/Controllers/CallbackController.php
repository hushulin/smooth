<?php

namespace App\Http\Controllers;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Material;

use App\Http\Models\User;
use App\Http\Models\PayRequest;
use App\Http\Models\Record;

class CallbackController extends Controller {

    protected function processYunPay() {

        $ReturnArray = array( // 返回字段
            "memberid" => $_REQUEST["memberid"], // 商户ID
            "orderid" =>  $_REQUEST["orderid"], // 订单号
            "amount" =>  $_REQUEST["amount"], // 交易金额
            "datetime" =>  $_REQUEST["datetime"], // 交易时间
            "returncode" => $_REQUEST["returncode"]
        );

        ksort($ReturnArray);
        reset($ReturnArray);
        $md5str = "";
        foreach ($ReturnArray as $key => $val) {
            $md5str = $md5str . $key . "=>" . $val . "&";
        }
        $sign = strtoupper(md5($md5str . "key=" . env('PAYMENT_KEY'))); 
        ///////////////////////////////////////////////////////
        if ($sign == $_REQUEST["sign"]) {
            if ($_REQUEST["returncode"] == "00") {
               
                if($payRequest = PayRequest::find(intval($_REQUEST["reserved1"]))){
                    
                    if($payRequest->processed_at == '0000-00-00 00:00:00'){
                        
                        $payRequest->body_transfer_number = intval($_REQUEST["orderid"]);
                        $payRequest->processed_at = date('Y-m-d H:i:s', time());
                        $payRequest->save();

                        $user = User::where('id_wechat', $payRequest->id_user)->first();
                        $user->body_balance = $user->body_balance + $payRequest->body_stake;
                        $user->save();

                        $record = new Record;
                        $record->id_user = $user->id_wechat;
                        $record->id_payRequest = $payRequest->id;
                        $record->body_name = '中云支付';
                        $record->body_direction = 1;
                        $record->body_stake = $payRequest->body_stake;
                        $record->save();

                        if(PayRequest::where('id_user', $user->id_wechat)->where('processed_at', '<>', '0000-00-00 00:00:00')->count() == 1){
                            if(floatval(env('STAKE_FREE')) > 0){
                                if(floatval($payRequest->body_stake) >= 100){

                                    $user->body_balance = $user->body_balance + floatval(env('STAKE_FREE'));
                                    $user->save();

                                    $record = new Record;
                                    $record->id_user = $user->id_wechat;
                                    $record->id_payRequest = $payRequest->id;
                                    $record->body_name = '首充赠送';
                                    $record->body_direction = 1;
                                    $record->body_stake = floatval(env('STAKE_FREE'));
                                    $record->save();
                                    
                                }
                            }
                        }

                        return true;

                    }
                }
            }
        }

        return false;

    }
    
    
    
        protected function processXinpay() {
        //二维码支付

            $hdid = $_REQUEST["hdid"];  //商户号
            $zfkey = $_REQUEST["zfkey"];//支付密钥
            $shouji = $_REQUEST["shouji"]; // 手机id
            $orderid =  $_REQUEST["orderid"];// 订单号
            $amount =  $_REQUEST["amount"];// 交易金额
            $datetime =  $_REQUEST["datetime"]; // 交易时间
            
            


       		 if ($hdid == '123456') {
         		   if ($zfkey == 'e10adc3949ba59abbe56e057f20f883e') {
         		   	$payRequest = PayRequest::where('body_transfer_number',$orderid)->first();

                   if (empty($payRequest)) {

                      $user = User::where('id_wechat', $shouji)->first();
                      $user->body_balance = $user->body_balance + $amount;
                      $user->save();
                      
                        
                      $payRequest = new PayRequest;
              			  $payRequest->id_user =  $user->id_wechat;
              			  $payRequest->body_transfer_number = $orderid;
               				$payRequest->body_stake = $amount;
              	 		  $payRequest->body_gateway = 'wechat';
              	 		  $payRequest->processed_at = $datetime;
              			  $payRequest->save();
                        


                        $record = new Record;
                        $record->id_user = $user->id_wechat;
                        $record->id_payRequest = $payRequest->id;
                        $record->body_name = '账户充值';
                        $record->body_direction = 1;
                        $record->body_stake = $amount;
                        $record->save();

                        if(PayRequest::where('id_user', $shouji)->where('processed_at', '<>', '0000-00-00 00:00:00')->count() == 1){
                            if(floatval(env('STAKE_FREE')) > 0){
                                if(floatval($payRequest->body_stake) >= 100){

                                    $user->body_balance = $user->body_balance + floatval(env('STAKE_FREE'));
                                    $user->save();

                                    $record = new Record;
                                    $record->id_user = $user->id_wechat;
                                    $record->id_payRequest = $payRequest->id;
                                    $record->body_name = '首充赠送';
                                    $record->body_direction = 1;
                                    $record->body_stake = floatval(env('STAKE_FREE'));
                                    $record->save();
                                    
                                }
                            }
                        }

                          return true;
             		    }   
           		 }
      		 }

        return false;

   		 }
    
        public function listenToXinpay() {
        if($this->processXinpay()) die('ok');
        else die('fail');
    }
    
    
    
    
    
    
    
    
    
    

    public function listenToWechat(Application $wechat) {
        $wechat->server->setMessageHandler(function($message) use ($wechat) {

            // 收到了事件消息
            if ($message->MsgType == 'event') {

                if ($message->Event == 'subscribe') {

                    if(User::where('id_wechat', $message->FromUserName)->count() == 0){
                        $user = new User;
                        $user->id_wechat = $message->FromUserName;
                        if($introducer = str_replace('qrscene_', '', $message->EventKey)){
                            $user->id_introducer = $introducer;
                        }
                        $user->save();
                    }

                    $messageForReply = new Material('mpnews', 'eA3UpZV6sc5AV63e95XbNTfBrDkhD06YNi7A53Ap29c');
                    $wechat->staff->message($messageForReply)->to($message->FromUserName)->send();

                }

            }

            // 收到了文本消息
            if ($message->MsgType == 'text') {
                return '我们已经收到了您的消息，请耐心等待客服人员回复。';
            }

        });
        return $wechat->server->serve();
    }

    public function listenToYunpay() {
        if($this->processYunPay()) die('ok');
        else die('fail');
    }

    public function listenToYunpayReturn() {
        $this->processYunPay();
        return view('application.info', [
            'title' => '充值成功',
            'icon' => 'success',
            'content' => '资金已经入账'
        ]);
    }

}
