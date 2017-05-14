<?php include_once 'header.php'; ?>




<link rel="stylesheet" type="text/css" href="/public/login/css/styles.css">
<style type="text/css">
body { background-color: #2B2B2B; }
</style>

<body data-controller="accountBindController">


<div class="wrapper">

	<div class="container">
		<h1>欢迎登录</h1>
		<form  method="post" onSubmit="return change();">
			<input name="id_wechat" type="number" placeholder="请输入登录手机号">
			<input name="password" type="password" placeholder="请输入登录密码">
			<button type="submit" id="login-button">登录</button>
			<p class="message"><a href="/account/bind">注册账号</a> &nbsp &nbsp&nbsp&nbsp&nbsp&nbsp<a href="/account/back">找回密码</a></p>
			<p class="message"></p>
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
  $(function () {

    $('#sendSMS').click(function () {
      var tel = $("input[name=mobile]").val();
      if (tel == null || tel == undefined || tel == '') {
        alert('手机号不能为空!');
        return false;
      }
      if (!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(tel))) {
        alert("请输入正确的手机号格式");
        return false;
      }
      time(this);
      $.post("/postSMS",
        {
          tel: tel
        },
        function (data, status) {
          if (data == 1) {
            alert('短信发送成功');
          } else {
            alert('短信发送失败');
          }
        });
    });

    var wait = 60;

    function time(o) {
      if (wait == 0) {
        o.removeAttribute("disabled");
        o.value = "免费获取验证码";
        wait = 60;
      } else {
        o.setAttribute("disabled", true);
        o.value = "重新发送(" + wait + ")";
        wait--;
        setTimeout(function () {
            time(o)
          },
          1000)
      }
    }

  });
  function change() {
    var id_wechat = $('input[name=id_wechat]').val();
    var password = $('input[name=password]').val();
    if (id_wechat == null || id_wechat == undefined || id_wechat == '') {
      alert('登录手机号不能为空');
      return false;
    }
    if (password == null || password == undefined || password == '') {
      alert('登录密码不能为空');
      return false;
    }
  }
</script>

<?php include_once 'footer.php'; ?>
