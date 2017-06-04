<?php include_once 'header.php'; ?>

<body data-controller="accountPayController">
<div class="weui_cells">
  <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary">
      <p>當前等级</p>
    </div>
    <div class="weui_cell_ft"><?php if ($user->grade == 1){ echo '初级经纪人'; }else if ($user->grade == 2){ echo '高级经纪人MIB'; }else if ($user->grade == 3){ echo '白金经纪人PIB'; }else if ($user->grade == 4){ echo '星级白金经纪人'; }else{echo "注册用户";} ?></div>
  </div>
</div>
<form method="post">
  <input id="input_stake" type="hidden" name="stake" value="100" />
  <table class="stacksTable">
    <tr>
      <td><a data-stake="100" class="button_tap weui_btn weui_btn_plain_primary">初级经纪人</a></td>
      <td><a data-stake="200" class="button_tap weui_btn weui_btn_plain_default">高级经纪人MIB</a></td>
    </tr>
    <!--
    <tr>
      <td><a data-stake="300" class="button_tap weui_btn weui_btn_plain_default">白金经纪人PIB</a></td>
      <td><a data-stake="400" class="button_tap weui_btn weui_btn_plain_default">星级白金经纪人</a></td>
    </tr>
    -->
  </table>

  <div class="weui_btn_area">
    <a href="javascript:app.instance.controller.clickedSubmit();" class="weui_btn weui_btn_primary">確認</a>
  </div>

</form>
<?php include_once 'footer.php'; ?>
