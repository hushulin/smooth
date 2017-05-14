<html>
<head><title>环迅支付中</title>
  <meta http-equiv="content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<form action="<?php echo $form_url; ?>" method="post" id="frm1"><input name="pGateWayReq" type="hidden"
                                                                      value="<?php echo $xml; ?>"/> <input type="submit"
                                                                                                           value="支付中..."/>
</form>
<script language="javascript"> document.getElementById("frm1").submit(); </script>
</body>
</html>