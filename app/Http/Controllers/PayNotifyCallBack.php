<?php
namespace App\Http\Controllers;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Material;

use App\Http\Models\User;
use App\Http\Models\PayRequest;
use App\Http\Models\Record;

use App\lib\WxPayNotify;
use App\lib\WxPayNotifyReply;
use App\lib\WxPayData;


class PayNotifyCallBack extends WxPayNotify
{
	//��ѯ����
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
			return true;
		}
		return false;
	}
	
	//��д�ص�������
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "�����������ȷ";
			return false;
		}
		//��ѯ�������ж϶�����ʵ��
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "������ѯʧ��";
			return false;
		}
		

		
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);