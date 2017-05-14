<?php include_once 'header.php'; ?>



<div class="hd">
	<img alt="模式二扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($url);?>" style="width:150px;height:150px;"/>
    </div>

    <div class="weui_cells">
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary" style="text-align: center;">
                <p>扫码进行支付</p>
            </div>
        </div>
    </div>




<?php include_once 'footer.php'; ?>