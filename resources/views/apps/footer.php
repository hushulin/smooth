<div class="navigator clearfix">
	  
        <a class="<?php echo ($navigator=='objects')? 'active' : '';?>" href="/objects" class="active"><span><img src="/public/statics_v2/img/bar01<?php echo ($navigator=='objects')? '' : '_o';?>.png" width="24" height="24" /></span><br><span>商品价格</span></a>
        <a class="<?php echo ($navigator=='ordersHold')? 'active' : '';?>" href="/orders/hold"><span><img src="/public/statics_v2/img/bar02<?php echo ($navigator=='ordersHold')? '' : '_o';?>.png" width="24" height="24" /></span><br><span>在手订单</span></a>
        <a class="<?php echo ($navigator=='ordersHistory')? 'active' : '';?>" href="/orders/history"><span><img src="/public/statics_v2/img/bar03<?php echo ($navigator=='ordersHistory')? '' : '_o';?>.png" width="24" height="24" /></span><br><span>历史订单</span></a>
		<a class="<?php echo ($navigator=='account')? 'active' : '';?>" href="/account"><span><img src="/public/statics_v2/img/bar04<?php echo ($navigator=='account')? '' : '_o';?>.png" width="24" height="24" /></span><br><span>会员中心</span></a>

  </div>

   
   
   

    
    
    
    
    
    
    
    

    <div id="doneToast" style="display: none;">
        <div class="weui_mask_transparent"></div>
        <div class="weui_toast">
            <i class="weui_icon_toast"></i>
            <p class="weui_toast_content">成功</p>
        </div>
    </div>
    <div id="contentToast" style="display: none;">
        <div class="weui_mask_transparent"></div>
        <div class="weui_toast">
            <p class="weui_toast_content"></p>
        </div>
    </div>
    <div id="loadingToast" class="weui_loading_toast" style="display: none;">
        <div class="weui_mask_transparent"></div>
        <div class="weui_toast">
            <div class="weui_loading">
                <div class="weui_loading_leaf weui_loading_leaf_0"></div>
                <div class="weui_loading_leaf weui_loading_leaf_1"></div>
                <div class="weui_loading_leaf weui_loading_leaf_2"></div>
                <div class="weui_loading_leaf weui_loading_leaf_3"></div>
                <div class="weui_loading_leaf weui_loading_leaf_4"></div>
                <div class="weui_loading_leaf weui_loading_leaf_5"></div>
                <div class="weui_loading_leaf weui_loading_leaf_6"></div>
                <div class="weui_loading_leaf weui_loading_leaf_7"></div>
                <div class="weui_loading_leaf weui_loading_leaf_8"></div>
                <div class="weui_loading_leaf weui_loading_leaf_9"></div>
                <div class="weui_loading_leaf weui_loading_leaf_10"></div>
                <div class="weui_loading_leaf weui_loading_leaf_11"></div>
            </div>
            <p class="weui_toast_content">请稍后</p>
        </div>
    </div>
    <script type="text/html" id="templet_dialog_alert">
        <div class="app_dialog weui_dialog_alert">
            <div class="weui_mask"></div>
            <div class="weui_dialog">
                <div class="weui_dialog_hd"><strong class="weui_dialog_title">#TITLE#</strong></div>
                <div class="weui_dialog_bd">#CONTENT#</div>
                <div class="weui_dialog_ft">
                    <a id="app_dialog_close" href="javascript:app.services.dialog.remove();" class="weui_btn_dialog primary">我知道了</a>
                </div>
            </div>
        </div>
    </script>
</body>
</html>