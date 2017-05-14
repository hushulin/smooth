<?php include_once 'header.php'; ?>

<body>

    <div class="hd1">
<?php echo $qrcode; ?>
    </div>

    <div class="weui_cells">
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary" style="text-align: center;">
                <p>一：屏幕截图来保存上方二维码。</p>
            </div>
            <div class="weui_cell_ft"></div>
        </div>
    </div>
    <div class="weui_cells">
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary" style="text-align: center;">
                <p>二：直接填写您的推广编号：<?php echo $tid ;?></p>
            </div>
            <div class="weui_cell_ft"></div>
        </div>
    </div>
        <div class="weui_cells">
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary" style="text-align: center;">
                <p>三长按复制下方链接</p>
            </div>
            <div class="weui_cell_ft"></div>
        </div>
    </div>
    <div class="weui_cells">
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary" style="text-align: center;">
                <p><?php echo $url; ?></p>
            </div>
            <div class="weui_cell_ft"></div>
        </div>
    </div>

<?php include_once 'footer.php'; ?>
