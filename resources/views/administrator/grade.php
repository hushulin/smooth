<?php include_once 'header.php'; ?>
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row" style="margin-top: 20px;">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
            <tr>
              <th>#</th>
              <th>申请人</th>
              <th>申请等级</th>
              <th>用户旧等级</th>
              <th>当前状态</th>
              <th>申请时间</th>
              <th width="600">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $item) { ?>
              <tr id="grade_<?php echo $item->id; ?>">
                <td><?php echo $item->id; ?></td>
                <td><?php echo $item->id_wechat; ?></td>
                <td><?php
                  if ($item->grade == 1) {
                    echo '初级经纪人';
                  } else if ($item->grade == 2) {
                    echo '高级经纪人MIB';
                  } else if ($item->grade == 3) {
                    echo '白金经纪人PIB';
                  } else if ($item->grade == 4) {
                    echo '星级白金经纪人';
                  }
                  ?></td>
                <td><?php
                  if ($item->old_grade == 1) {
                    echo '初级经纪人';
                  } else if ($item->old_grade == 2) {
                    echo '高级经纪人MIB';
                  } else if ($item->old_grade == 3) {
                    echo '白金经纪人PIB';
                  } else if ($item->old_grade == 4) {
                    echo '星级白金经纪人';
                  } else {
                    echo '首次申请';
                  }
                  ?></td>
                <td><?php
                  if ($item->status == 0) {
                    echo '审核中';
                  } else if ($item->status == 1) {
                    echo '已通过';
                  } else {
                    echo '已拒绝';
                  }
                  ?></td>
                <td><?php echo $item->created_at; ?></td>
                <td>
                  <?php if ($item->status == 0) { ?>
                    <button type="button" onclick="examine(<?php echo $item->id ?>)" class="btn btn-success">确认审核
                    </button>
                    <button type="button" onclick="unexamine(<?php echo $item->id ?>)" class="btn btn-warning">拒绝审核
                    </button>
                  <?php } else { ?>
                    <button type="button" onclick="del(<?php echo $item->id ?>)" class="btn btn-danger">删除信息</button>
                  <?php } ?>

                </td>
              </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
              <td colspan="10" style="text-align: center;">
                <ul class="pagination">
                  <li class="paginate_button previous"><a href="<?php echo $list->previousPageUrl(); ?>">上一页</a></li>
                  <li class="paginate_button active"><a href="#"><?php echo $list->currentPage(); ?>
                      / <?php echo $list->lastPage(); ?>, 共 <?php echo $list->total(); ?> 条记录</a></li>
                  <li class="paginate_button next"><a href="<?php echo $list->nextPageUrl(); ?>">下一页</a></li>
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
    function examine(id) {
      $.post("/administrator/grade?id=" + id,
        {
          state: 0
        },
        function (data, status) {
          if (data == 1) {
            layer.msg('审核操作成功');
            setTimeout(function () {
              window.location.reload();
            }, 1500)
          } else {
            layer.msg('审核操作失败');
          }
        });
    }
    function unexamine(id) {
      $.post("/administrator/unGrade?id=" + id,
        {
          state: 1
        },
        function (data, status) {
          if (data == 1) {
            layer.msg('拒绝操作成功');
            setTimeout(function () {
              window.location.reload();
            }, 1500)
          } else {
            layer.msg('拒绝操作失败');
          }
        });
    }
    function del(id) {
      layer.confirm('确认删除该信息？', {
        btn: ['确定', '取消'] //按钮
      }, function () {
        $.post("/administrator/delGrade?id=" + id,
          {
            state: 2
          },
          function (data, status) {
            if (data == 1) {
              layer.msg('删除操作成功');
              $('#grade_' + id).remove();
              setTimeout(function () {
                window.location.reload();
              }, 1500)
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