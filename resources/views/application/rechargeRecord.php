<?php include_once 'header.php'; ?>

<body>

<table border="0" class="evenColor">
    <thead>
    <tr>
        <th>充值日期</th>
        <th>充值金额</th>
        <th>充值账户</th>
        <th>充值状态</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($list) == 0) { ?>
        <tr>
            <td colspan="4">暂时还没有任何记录</td>
        </tr>
    <?php } ?>
    <?php foreach ($list as $vo) { ?>
        <tr>
            <td><?php echo date('Y-m-d', strtotime($vo->created_at)); ?></td>
            <td><?php echo $vo->body_stake; ?> 元</td>
            <td><?php echo $vo->id_user; ?></td>
            <td>充值成功</td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php include_once 'footer.php'; ?>
