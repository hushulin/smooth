<?php include_once 'header.php'; ?>

<body>
<style type="text/css">
  .extend {
    padding: 10px;
    display: block;
    overflow: hidden;
    height: auto;
  }

  .extend a {
    width: 48%;
    height: auto;
    float: left;
    margin: 2% 1%;
  }

  .extend a span {
    font-weight: bold;
  }

  .extend ul li {

  }

  .extend ul li b {
    font-weight: bold;
    float: left;
  }

  .extend ul li span {
    font-weight: bold;
    float: right;
  }

  .weui_btn + .weui_btn {
    margin-top: 2%;
  }
</style>
<table border="0">
  <tbody>
  <!--
  <tr>
    <td>我的上级</td>
  </tr>
  <tr>
    <td style="padding: 10px;"><a href="javascript:;"
                                  class="weui_btn weui_btn_default"><?php echo $superior == null ? '无' : $superior; ?></a>
    </td>
  </tr>
  -->
  <tr>
    <td>各等级人数</td>
  </tr>
  <tr>
    <td class="extend">
      <a href="javascript:;" class="weui_btn weui_btn_default">
        <span>初级经纪人</span>
        <p><?php echo $data['one'] == null ? '0' : $data['one']; ?>人</p>
      </a>
      <a href="javascript:;" class="weui_btn weui_btn_default">
        <span>高级经纪人MIB</span>
        <p><?php echo $data['two'] == null ? '0' : $data['two']; ?>人</p>
      </a>
      <!--
      <a href="javascript:;" class="weui_btn weui_btn_default">
        <span>白金经纪人PIB</span>
        <p><?php echo $data['three'] == null ? '0' : $data['three']; ?>人</p>
      </a>
      <a href="javascript:;" class="weui_btn weui_btn_default">
        <span>星级白金经纪人</span>
        <p><?php echo $data['four'] == null ? '0' : $data['four']; ?>人</p>
      </a>
      -->
    </td>
  </tr>
  <tr>
    <td>我的下级</td>
  </tr>
  <tr>
    <td class="extend">
      <ul>
        <?php foreach ($subordinate as $vo) { ?>
          <li><b><?php echo $vo->id_wechat; ?></b><span><?php echo $vo->created_at->format('Y.m.d'); ?></span></li>
        <?php } ?>
      </ul>
    </td>
  </tr>
  </tbody>
</table>

<?php include_once 'footer.php'; ?>
