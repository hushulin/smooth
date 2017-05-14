<?php include_once 'header.php'; ?>

<body>
<form method="post" onsubmit="return change();">
  <div class="weui_cells weui_cells_form">
    <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label" style="width: 7em;">新密码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
        <input name="password" class="weui_input" type="password" placeholder="请输入新密码">
      </div>
    </div>
    <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label" style="width: 7em;">再次输入密码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
        <input name="pwd" class="weui_input" type="password" placeholder="请再次输入新密码">
      </div>
    </div>
  </div>

  <div class="weui_btn_area">
    <button class="weui_btn weui_btn_primary" type="submit">确认修改</button>
  </div>

</form>
<script type="text/javascript">
  function change() {
    var password = $('input[name=password]').val();
    var pwd = $('input[name=pwd]').val();
    if(password.length < 6){
      alert('对不起,密码必须6位以上!');
      return false;
    }
    if (password != pwd) {
      alert('对不起,两次密码不一致!');
      return false;
    }
  }
</script>
<?php include_once 'footer.php'; ?>
