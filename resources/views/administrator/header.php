<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>管理后台</title>
  <link href="/public/admin/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/admin/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
  <link href="/public/admin/dist/css/sb-admin-2.css" rel="stylesheet">
  <link href="/public/admin/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  <script src="//cdn.bootcss.com/jquery/1.12.3/jquery.min.js"></script>
  <script src="/public/layer/layer.js"></script>
</head>
<body>
<div id="wrapper">
  <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/administrator">管理后台</a>
    </div>

    <ul class="nav navbar-top-links navbar-right">
      <li>
        <a href="/administrator/users/export">
          导出所有用户
        </a>
      </li>
      <li>
        <a href="/administrator/orders/export">
          导出所有订单
        </a>
      </li>
      <li>
        <a href="/administrator/records/export">
          导出所有资金
        </a>
      </li>
      <li>
        <a href="/administrator/payRequests/export">
          导出所有充值
        </a>
      </li>
      <li>
        <a href="/administrator/withdrawRequests/export">
          导出所有提现
        </a>
      </li>
  <li>
                    <a href="/administrator/orderControl">
                        <?php if(env('ORDER_CONTROL')) echo '订单调控已开'; else echo '订单调控已关'; ?>
                    </a>
                </li>
                <li>
                    <a href="/administrator/orderWillWin">
                        <?php if(env('ORDER_WILL_WIN')) { echo '强制盈利已开'; } else echo '强制盈利已关'; ?>
                    </a>
                    <?php if(env('ORDER_WILL_WIN')) { echo "<input id=\"my-rate\" value=\"1\">"; } else echo ''; ?>
                </li>
                <li>
                    <a href="/administrator/orderWillLost">
                        <?php if(env('ORDER_WILL_LOST')) echo '强制亏损已开'; else echo '强制亏损已关'; ?>
                    </a>
                </li>
    </ul>

    <div class="navbar-default sidebar" role="navigation">
      <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
          <li class="sidebar-search">
            <form method="get" action="/administrator/users">
              <div class="input-group custom-search-form">
                <input name="body_phone" type="text" class="form-control" placeholder="根据手机号搜索用户...">
                <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
              </div>
            </form>
          </li>
          <li>
            <a class="<?php echo ($active == 'home') ? 'active' : ''; ?>" href="/administrator"><i
                  class="fa fa-dashboard fa-fw"></i> 总览</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'users') ? 'active' : ''; ?>" href="/administrator/users"><i
                  class="fa fa-user fa-fw"></i> 用户</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'grade') ? 'active' : ''; ?>" href="/administrator/grade"><i
                  class="fa fa-user fa-fw"></i> 等级申请</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'orders') ? 'active' : ''; ?>" href="/administrator/orders"><i
                  class="fa fa-table fa-fw"></i> 订单</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'records') ? 'active' : ''; ?>"
               href="/administrator/records"><i
                  class="fa fa-list-alt fa-fw"></i> 资金</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'payRequests') ? 'active' : ''; ?>"
               href="/administrator/payRequests"><i
                  class="fa fa-rmb fa-fw"></i> 充值</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'withdrawRequests') ? 'active' : ''; ?>"
               href="/administrator/withdrawRequests"><i class="fa fa-briefcase fa-fw"></i> 提现</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'objects') ? 'active' : ''; ?>"
               href="/administrator/objects"><i
                  class="fa fa-cloud fa-fw"></i> 标的</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'feedbacks') ? 'active' : ''; ?>"
               href="/administrator/feedbacks"><i
                  class="fa fa-bug fa-fw"></i> 反馈</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'reward') ? 'active' : ''; ?>"
               href="/administrator/reward"><i
                  class="fa fa-bug fa-fw"></i> 奖励分发</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'system') ? 'active' : ''; ?>"
               href="/administrator/system"><i
                  class="fa fa-bug fa-fw"></i>系统设置</a>
          </li>
          <li>
            <a class="<?php echo ($active == 'system') ? 'active' : ''; ?>"
               href="http://web.92yw.com/xtgx.html"><i
                  class="fa fa-bug fa-fw"></i>更新日志</a>
          </li>
          <li style="display: none;">
            <a class="<?php echo ($active == 'administrators') ? 'active' : ''; ?>"
               href="/administrator/administrators"><i class="fa fa-bug fa-fw"></i> 管理</a>
          </li>
          <li>
            <a href="/administrator/signOut"><i class="fa fa-sign-out fa-fw"></i> 退出</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<script type="text/javascript">

(function (factory) {

  if (typeof define === 'function' && define.amd) {
    // AMD. Register as anonymous module.
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    // Node / CommonJS
    factory(require('jquery'));
  } else {
    // Browser globals.
    factory(jQuery);
  }
})(function ($) {
  // 严格模式 
  'use strict';
  
  // 常用变量 常量 
  var $window = $(window),
    $document = $(document);

  var console = window.console || { log: function () {} };
  
  // 类
  function MyOrderControl() {
    this.$input = $('#my-rate');
    this.$rate = this.$input.val();
    this.$input.on('blur' , $.proxy(this.setRate , this));
  }
  
  // 原型 业务逻辑 
  MyOrderControl.prototype = {

    // body...
    
    // 声明构造方法，new时会调用      
    constructor: MyOrderControl,

    // body...
    setRate: function() {
      var $rate = this.$input.val();
      if ($rate > 1 || $rate < 0) {
        alert('强制盈利机率设置必须在0-1之间');
        return ;
      }
      window.location.href = '/administrator/orderWill/' + $rate + '/0/0';
    }
  };

  $(function () {
    return new MyOrderControl();
  });

});
</script>