<?php
return [
  'PAY_MID' => '194232', //商户号
  'PAY_KEY' => 'VEGOk3P1VQ1rzyRWlLHCNwvccWjDubW4t1JJq0ed7623eVD2u5mnycayoLT6cWwA1s34cJQFckop1UDjc7X9scp9VKvE36TvXAw3Y23DTmZgGdhTuZfhZdbyTxYf8W3A', //MD5密钥
  'ACCOUNT' => '1942320019',  //交易账号
//  'POST_URL' => 'https://mobilegw.ips.com.cn/psfp-mgw/paymenth5.do', //请求链接
  'POST_URL' => 'https://newpay.ips.com.cn/psfp-entry/gateway/payment.do', //请求链接
  'RETURN_URL' => 'http://wh.chinajiepu.com/account',  //同步返回地址
  'NOTIFY_URL' => 'http://ipspay.mengbull.cn/pay/notify',  //异步返回地址
  'GOODNAME' => '货币外汇'  //商品名称
];