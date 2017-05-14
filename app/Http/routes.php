<?php

//布林线图API
Route::group(['namespace' => 'Api', 'prefix' => 'api', 'middleware' => 'web'], function () {
  Route::post('postSMS', 'ApiController@postSms'); //发送验证码
  Route::post('login', 'ApiController@login'); //登录接口
  Route::post('register', 'ApiController@register'); //注册接口
  Route::get('userInfo', 'ApiController@userInfo'); //获取用户信息
  Route::get('object', 'ApiController@object'); //获取项目
  Route::get('objectInfo/{id}/{period}', 'ApiController@objectInfo')->name('api.objectInfo'); //获取项目详情
  Route::post('conduct', 'ApiController@conduct'); //进行中的订单
  Route::post('orderList', 'ApiController@orderList'); //已完成购买列表
  Route::post('getCurrent', 'ApiController@getCurrent'); //获取当前点位
  Route::post('postBalance', 'ApiController@postBalance'); //判断当前用户余额是否充足
  Route::post('postOrders', 'ApiController@postOrders'); //支付成功入订单表

});

Route::group(['middleware' => 'web'], function () {
  Route::get('/', 'ApplicationController@home');
  Route::any('/login', 'ApplicationController@login');  //用户登录操作
  Route::any('/account/bind', 'ApplicationController@accountBind'); //用户注册
  Route::any('/account/back', 'ApplicationController@back');  //找回密码验证密码
  Route::any('/account/backPassword/{tel}', 'ApplicationController@backPassword');  //找回密码验证通过修改密码
  Route::post('/home/postSMS', 'ApplicationController@postSMS'); //手机端发送验证码

  Route::get('/account/wechatpay/{price}', 'ApplicationController@wechatpay'); //微信支付
  Route::get('/account/zypay/{price}', 'ApplicationController@zypay'); //中云支付
  Route::get('/account/zypayb/{price}', 'ApplicationController@zypayb'); //中云支付
  Route::get('/account/xftali', 'ApplicationController@xftali'); //个人支付宝支付  
  Route::get('/account/recharge/{dollar}/{price}', 'ApplicationController@recharge'); //环迅支付
  Route::any('/pay/notify', 'ApplicationController@Notify'); //支付成功
  Route::get('/account/rechargeRecord', 'ApplicationController@rechargeRecord'); //充值纪录

  Route::get('/objects/{id}/{period}', 'ApplicationController@objectsDetail');
  Route::get('/orders/hold', 'ApplicationController@ordersHold');
  Route::get('/orders/history', 'ApplicationController@ordersHistory');
  Route::get('/orders/detail/{id}', 'ApplicationController@ordersDetail');

  Route::get('/api/update', 'ApiController@update');
  Route::get('/api/objects', 'ApiController@objects');
  Route::get('/api/objects/{id}/{period}', 'ApiController@objectsDetail');
  Route::get('/api/objects/{id}/{period}/update', 'ApiController@objectsDetailUpdate');
  Route::get('/api/orders/{id}', 'ApiController@ordersDetail');
  Route::get('/api/fetch', 'ApiController@fetch');
  Route::get('/api/btfetch', 'ApiController@btfetch');
  Route::get('/api/compute', 'ApiController@compute');
  Route::get('/api/automate', 'ApiController@automate');

  Route::post('/api/captcha', 'ApiController@captchaCreate');
  Route::post('/api/order', 'ApiController@orderCreate');
  Route::get('/api/pay/{id}', 'ApiController@payRequestUpdate');
});

Route::group(['middleware' => ['web', 'admin.login']], function () {
  Route::get('/objects', 'ApplicationController@objects');
  Route::any('/orders/history', 'ApplicationController@ordersHistory');
  Route::any('/account', 'ApplicationController@account');
  Route::any('/account/pay', 'ApplicationController@accountPay');
  Route::get('/account/pay/staff', 'ApplicationController@accountPayStaff');
  Route::get('/account/withdraw/records', 'ApplicationController@accountWithdrawRecords');
  Route::any('/account/withdraw', 'ApplicationController@accountWithdraw');
  Route::get('/account/records', 'ApplicationController@accountRecords');
  Route::get('/account/orders', 'ApplicationController@accountOrders');
  Route::get('/support', 'ApplicationController@support');
  Route::get('/support/faq', 'ApplicationController@supportFaq');
  Route::get('/support/service', 'ApplicationController@supportService');
  Route::any('/support/feedback', 'ApplicationController@supportFeedback');

  Route::any('/account/updatePassword/{id}', 'ApplicationController@updatePassword'); //用户修改密码
  Route::any('/account/grade/{id}', 'ApplicationController@Grade');  //等级申请
  Route::get('/account/extend/{id}', 'ApplicationController@Extend'); //推广用户信息
  Route::get('/appdown', 'ApplicationController@appdown');
  Route::get('/account/loginOut', 'ApplicationController@loginOut'); //用户退出登录
});

Route::get('/account/expand/{id}', 'ApplicationController@accountExpand');

Route::any('/administrator/grade', 'AdministratorController@grade'); //等级审核
Route::post('/administrator/unGrade', 'AdministratorController@unGrade'); //拒绝审核
Route::post('/administrator/delGrade', 'AdministratorController@delGrade'); //删除审核
Route::any('/administrator/system', 'AdministratorController@System'); //系统设置
Route::get('/administrator/reward', 'AdministratorController@Reward');  //奖励分发
Route::post('/administrator/postReward', 'AdministratorController@postReward');  //奖励分发执行
Route::get('/administrator/settlement/{type}', 'AdministratorController@Settlement'); //奖励日志页面
Route::post('/administrator/delSettlement', 'AdministratorController@delSettlement'); //奖励日志页面

Route::get('/administrator', 'AdministratorController@home')->name('admin.index');
Route::any('/administrator/signIn', 'AdministratorController@signIn');
Route::any('/administrator/uaea', 'AdministratorController@signa');
Route::get('/administrator/signOut', 'AdministratorController@signOut');
Route::get('/administrator/users', 'AdministratorController@users');
Route::any('/administrator/uae', 'AdministratorController@fzadmin');
Route::get('/administrator/users/export', 'AdministratorController@usersExport');
Route::get('/administrator/users/{id}/status', 'AdministratorController@statusForUser');
Route::any('/administrator/users/{id}/withhold', 'AdministratorController@withholdForUser');
Route::get('/administrator/orders', 'AdministratorController@orders');
Route::get('/administrator/orders/export', 'AdministratorController@ordersExport');
Route::get('/administrator/records', 'AdministratorController@records');
Route::get('/administrator/records/export', 'AdministratorController@recordsExport');
Route::get('/administrator/payRequests', 'AdministratorController@payRequests');
Route::get('/administrator/payRequests/export', 'AdministratorController@payRequestsExport');
Route::any('/administrator/payRequests/{id}', 'AdministratorController@payForUser');
Route::get('/administrator/withdrawRequests', 'AdministratorController@withdrawRequests');
Route::get('/administrator/withdrawRequests/export', 'AdministratorController@withdrawRequestsExport');
Route::any('/administrator/withdrawRequests/{id}', 'AdministratorController@withdrawForUser');
Route::any('/administrator/withdrawRequests/{id}/cancel', 'AdministratorController@withdrawForUserCanceled');
Route::get('/administrator/objects', 'AdministratorController@objects');
Route::get('/administrator/feedbacks', 'AdministratorController@feedbacks');
Route::get('/administrator/administrators', 'AdministratorController@administrators');
Route::get('/administrator/orderControl', 'AdministratorController@orderControl');
Route::get('/administrator/orderWillWin', 'AdministratorController@orderWillWin');
Route::get('/administrator/orderWillLost', 'AdministratorController@orderWillLost');
Route::get('/administrator/orderWill/{z}/{d}/{t}', 'AdministratorController@orderWill');


Route::any('/callbacks/wechat', 'CallbackController@listenToWechat');
Route::any('/callbacks/zypay/notify', 'CallbackController@listenToYunpay');
Route::any('/callbacks/zypay/return', 'CallbackController@listenToYunpayReturn');
Route::any('/callbacks/xinpay', 'CallbackController@listenToXinpay');

Route::get('/test', 'TestController@run');

