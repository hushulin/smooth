<?php include_once 'header.php'; ?>
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row" style="margin-top: 20px;">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
            <tr>
              <th>#</th>
              <th>获得者</th>
              <th>获得金额</th>
              <th>分红日志</th>
              <th>分红时间</th>
              <th width="600">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $item) { ?>
              <tr id="settlement_<?php echo $item->id; ?>">
                <td><?php echo $item->id; ?></td>
                <td><?php echo $item->uid; ?></td>
                <td><?php echo $item->amount; ?></td>
                <td><?php echo $item->remark; ?></td>
                <td><?php echo date('Y-m-d', $item->time); ?></td>
                <td>
                  <button type="button" onclick="del(<?php echo $item->id; ?>)" class="btn btn-danger">删除信息</button>
                </td>
              </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
              <td colspan="10" style="text-align: center;">
                <ul class="pagination">
                  <li class="paginate_button previous"><a href="<?php echo $data->previousPageUrl(); ?>">上一页</a></li>
                  <li class="paginate_button active"><a href="#"><?php echo $data->currentPage(); ?>
                      / <?php echo $data->lastPage(); ?>, 共 <?php echo $data->total(); ?> 条记录</a></li>
                  <li class="paginate_button next"><a href="<?php echo $data->nextPageUrl(); ?>">下一页</a></li>
                </ul>
              </td>
            </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    function del(id) {
      layer.confirm('确认删除该信息？', {
        btn: ['确定', '取消'] //按钮
      }, function () {
        $.post("/administrator/delSettlement?id=" + id,
          {
            state: <?php echo $status; ?>
          },
          function (data, status) {
            if (data == 1) {
              layer.msg('删除操作成功');
              $('#settlement_' + id).remove();
            } else {
              layer.msg('删除操作失败');
            }
          });
      }, function () {
        layer.msg('取消成功');
      });
    }
  </script>
<?php include_once 'footer.php'; ?>