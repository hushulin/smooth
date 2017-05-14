<?php include_once 'header.php'; ?>

<link rel="stylesheet" type="text/css" href="/public/login/css/styles.css">
<style type="text/css">
body { background-color: #2B2B2B; }
</style>

<body data-controller="accountBindController">
	
	<div class="wrapper">

	<div class="container">
		<h1>创建用户</h1>
<form method="post" onSubmit="return change();">



                <input name="mobile" type="number" placeholder="请输入您的手机号码">

               
                                <input id="sendSMS" style="margin: 8px 10px;" onClick="sends()"
                       class="weui_btn weui_btn_mini weui_btn_default"
                       type="button" value="发送验证短信"/>





                <input name="vcode" type="number" placeholder="您收到的短信验证码">




                <input name="password"  type="text" placeholder="请输入您的密码"><br>
                
               
                        <div> 推荐人ID</div>
                          <?php if(!empty($tid)){ ?>

      <input name="tid"  type="text" value="<?php echo $tid; ?>">
  <?php } else{ ?>
			   <input name="tid"  type="text" placeholder="请输入推荐人ID">
  <?php }  ?>


                       
              

 <button type="submit">确认注册</button>
			<p class="message"><a href="/login">使用已有的账号</a> &nbsp &nbsp&nbsp&nbsp&nbsp&nbsp<a href="/account/back">找回密码</a></p>
		</form>
	</div>
	
	<ul class="bg-bubbles">
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
		<li></li>
	</ul>
	
</div>

<script type="text/javascript" src="/public/login/js/jquery-2.1.1.min.js"></script>

<script type="text/javascript">

    var curCount;//当前剩余秒数
    var count = 60; //间隔函数，1秒执行

    function sends() {
        var tel = $("input[name=mobile]").val();
        if (tel == null || tel == undefined || tel == '') {
            alert('手机号不能为空!');
            return false;
        }
        if (!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(tel))) {
            alert("请输入正确的手机号格式");
            return false;
        }
        //设置button效果，开始计时
        curCount = count;
        $("#sendSMS").attr("disabled", "true");
        $("#sendSMS").val("重新发送(" + curCount + ")");
        InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
        $.post("/home/postSMS",
            {
                tel: tel
            },
            function (data, status) {
                if (data == 106) {
                    alert('該手機號已存在');
                } else if (data == 1) {
                    alert('短信發送成功');
                } else {
                    alert('短信發送失敗');
                }
            });
    }

    //timer处理函数
    function SetRemainTime() {
        if (curCount == 0) {
            window.clearInterval(InterValObj);//停止计时器
            $("#sendSMS").removeAttr("disabled");//启用按钮
            $("#sendSMS").val("重新发送验证码");
        } else {
            curCount--;
            $("#sendSMS").val("重新发送(" + curCount + ")");
        }
    }

    function change() {
        var mobile = $('input[name=mobile]').val();
        var vcode = $('input[name=vcode]').val();
        var password = $('input[name=password]').val();
        if (mobile == null || mobile == undefined || mobile == '') {
            alert("登錄手機號不能為空");
            return false;
        }
        if (vcode == null || vcode == undefined || vcode == '') {
            alert("驗證碼不能為空");
            return false;
        }
        if (password == null || password == undefined || password == '') {
            alert("登錄密碼不能為空");
            return false;
        }
    }
</script>

<?php include_once 'footer.php'; ?>