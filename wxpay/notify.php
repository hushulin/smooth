<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "WxPay.Api.php";
require_once 'WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			
			
			
$Data = array(
'hdid'=>'123456',
'zfkey'=>'e10adc3949ba59abbe56e057f20f883e',
'shouji'=>$result["attach"],
'orderid'=>$result["out_trade_no"],
'amount'=>($result["total_fee"]/100),
'datetime'=>$result["time_end"],
);
$tijiao='http://' . $_SERVER['SERVER_NAME'] . '/callbacks/xinpay';
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$tijiao);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $Data);
$a = curl_exec($ch);
curl_close($ch);
 
//print_r( $a );
			
//header("Content-type: text/html; charset=utf-8");

//$file  = 'log.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
//$content = $result["attach"];
 
//if($f  = file_put_contents($file, $content,FILE_APPEND)){// 这个函数支持版本(PHP 5) 
// echo "写入成功。<br />";
//}
		
			
	return true;		
			
			
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
