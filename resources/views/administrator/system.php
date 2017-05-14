<?php include_once 'header.php'; ?>
  <style type="text/css">
    .prices {
      float: left;
      width: 200px;
    }

    table th, tr, td {
      text-align: center;
    }
  </style>
  <div id="page-wrapper">
    <div class="container-fluid">
      <form method="post" onsubmit="return change();">
        <table class="table table-striped">
          <tr>
            <th>系统名称</th>
            <th>参数值</th>
          </tr>
          <tbody>
          <tr>
            <th>
              <label for="inputEmail3" class="col-sm-2 control-label"
                     style="width: 100%; line-height: 34px;">美元兑换比例</label>
            </th>
            <td>
              <div class="input-group prices" style="margin-left: 10px;">
                <div class="input-group-addon">min</div>
                <input type="text" name="convert_min" value="<?php echo $data->convert_min; ?>" class="form-control"
                       id="exampleInputAmount"
                       placeholder="最小值">
              </div>
              <div class="input-group prices">
                <div class="input-group-addon">max</div>
                <input type="text" name="convert_max" value="<?php echo $data->convert_max; ?>"
                       class="form-control" id="exampleInputAmount"
                       placeholder="最大值">
              </div>
            </td>
          </tr>
          <tr>
            <th>
              <label for="inputEmail3" class="col-sm-2 control-label"
                     style="width: 100%; line-height: 34px;">充值交易手续费</label>
            </th>
            <td>
              <div class="input-group prices">
                <div class="input-group-addon">￥</div>
                <input type="text" value="<?php echo $data->interest_rate; ?>" name="interest_rate" class="form-control"
                       id="exampleInputAmount"
                       placeholder="请输入百分比（%）">
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <button type="submit" class="btn btn-success">提交保存</button>
            </td>
          </tr>
          </tbody>
        </table>
      </form>
    </div>
  </div>
  <script type="text/javascript">
    function change() {
      var min = $('input[name=convert_min]').val();
      var max = $('input[name=convert_max]').val();
      if (min > max) {
        layer.msg('参数错误,最小值不能操作最大值!!');
        return false;
      }
    }
  </script>
<?php include_once 'footer.php'; ?>