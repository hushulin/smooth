<?php include_once 'header.php'; ?>

<body>

<div class="weui_cells weui_cells_access">
	  <a class="weui_cell" href="/objects">
    <div class="weui_cell_bd weui_cell_primary">
      <p>返回首页</p>
    </div>
    <div class="weui_cell_ft">
    </div>
  </a>




  <div class="weui_cell">

    <div class="weui_cell_bd weui_cell_primary">
      <p>当前结余</p>
    </div>
    <div class="weui_cell_ft"><?php echo $user->body_balance; ?> CNY</div>
  </div>

</div>

<div class="weui_cells weui_cells_access">

  <a class="weui_cell" href="/account/grade/<?php echo $user->id; ?>">
    <div class="weui_cell_bd weui_cell_primary">
      <p>等级申请</p>
    </div>
    <div class="weui_cell_ft">
    </div>
  </a>

  <a class="weui_cell" href="/account/pay">
    <div class="weui_cell_bd weui_cell_primary">
      <p>我要充值</p>
    </div>
    <div class="weui_cell_ft">
    </div>
  </a>

  <a class="weui_cell" href="/account/withdraw">
    <div class="weui_cell_bd weui_cell_primary">
      <p>我要提现</p>
    </div>
    <div class="weui_cell_ft">
    </div>
  </a>
  <a class="weui_cell" href="/account/records">
    <div class="weui_cell_bd weui_cell_primary">
      <p>资金记录</p>
    </div>
    <div class="weui_cell_ft">
    </div>
  </a>
  <a class="weui_cell" href="/account/orders">
    <div class="weui_cell_bd weui_cell_primary">
      <p>交易记录</p>
    </div>
    <div class="weui_cell_ft">
    </div>
  </a>
</div>

<div class="weui_cells weui_cells_access">
  <a class="weui_cell" href="/account/expand/<?php echo $user->id; ?>">
    <div class="weui_cell_bd weui_cell_primary">
      <p>我的推广链接</p>
    </div>
    <div class="weui_cell_ft"></div>
  </a>
  <a class="weui_cell" href="/account/myextend/qrcode">
    <div class="weui_cell_bd weui_cell_primary">
      <p>我的推广二维码</p>
    </div>
    <div class="weui_cell_ft"></div>
  </a>
    <a class="weui_cell" href="/appdown">
    <div class="weui_cell_bd weui_cell_primary">
      <p>APP下载</p>
    </div>
    <div class="weui_cell_ft"></div>
  </a>
</div>


<div class="weui_cells">
  <a href="/account/extend/<?php echo $user->id; ?>" style="color: #000">
  <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary">
      <p>我已推广用户</p>
    </div>
      <div class="weui_cell_ft"><?php echo $count_refers; ?> 位</div>
  </div>
  </a>
  <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary">
      <p>我已获得奖金</p>
    </div>
    <div class="weui_cell_ft"><?php echo $count_bonus; ?> 元</div>
  </div>
</div>
<div class="weui_cells weui_cells_access">
	  <a class="weui_cell" href="/account/updatePassword/<?php echo $user->id; ?>">
    <div class="weui_cell_bd weui_cell_primary">
      <p>密码修改</p>
    </div>
    <div class="weui_cell_ft"></div>
  </a>
    <a class="weui_cell" href="/account/loginOut">
        <div class="weui_cell_bd weui_cell_primary">
            <p>退出登录</p>
        </div>
        <div class="weui_cell_ft"></div>
    </a>
</div>
<?php include_once 'footer.php'; ?>
