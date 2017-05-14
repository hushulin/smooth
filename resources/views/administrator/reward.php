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
      <div class="col-lg-12" style="margin-top: 20px;">
        <div class="panel panel-green">
          <div class="panel-heading">奖励分配</div>
          <div class="panel-body">
            <p style="float:left; margin-right: 30px;">
              <button type="button" onclick="reward(1)" class="btn btn-primary btn-lg">会员日结算</button>
            </p>
            <p>
              <a href="/administrator/settlement/1">
                <button type="button" class="btn btn-success btn-lg">结算记录</button>
              </a>
            </p>
            <br/>
            <p style="float:left; margin-right: 30px;">
              <button type="button" onclick="reward(2)" class="btn btn-primary btn-lg">会员月结算</button>
            </p>
            <p>
              <a href="/administrator/settlement/2">
                <button type="button" class="btn btn-success btn-lg">结算记录</button>
              </a>
            </p>
          </div>
        </div>
        <!-- /.col-lg-4 -->
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function reward(num) {
      if (num == 1) {
        layer.confirm('你确定需要执行会员日结算？', {
          btn: ['确认', '取消'] //按钮
        }, function () {
          $.post("/administrator/postReward",
            {
              num: num
            },
            function (data, status) {
              if (data == 1) {
                layer.alert('结算成功', {
                  icon: 1,
                  skin: 'layer-ext-moon'
                })
              } else {
                layer.alert('结算失败', {
                  icon: 2,
                  skin: 'layer-ext-moon'
                })
              }
            });
        });
      } else {
        layer.confirm('你确定需要执行会员月结算？', {
          btn: ['确认', '取消'] //按钮
        }, function () {
          $.post("/administrator/postReward",
            {
              num: num
            },
            function (data, status) {
              if (data == 1) {
                layer.alert('结算成功', {
                  icon: 1,
                  skin: 'layer-ext-moon'
                })
              } else {
                layer.alert('结算失败', {
                  icon: 2,
                  skin: 'layer-ext-moon'
                })
              }
            });
        });
      }
    }
  </script>
<?php include_once 'footer.php'; ?>