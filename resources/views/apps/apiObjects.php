<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="wap-font-scale" content="no">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=7" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" >
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" >
    <title><?php echo isset($title) ? $title : env('APP_TITLE'); ?></title>
    <link rel="stylesheet" href="/public/statics_v2/css/app.css">
    <script src="/public/statics_v2/js/libs/jquery.min.js"></script>
    <script src="/public/statics_v2/js/libs/fastclick.js"></script>
    <script src="/public/statics_v2/js/app.js"></script>
</head>
<body<?php if (isset($controller)) echo ' data-controller="' . $controller . '"'; ?> ontouchstart>

<script src="/public/statics_v2/js/libs/highstock/highstock.js"></script>
<script src="/public/statics_v2/js/libs/technical-indicators.js"></script>
<div class="container objectsDetail">
    <table data-name="<?php echo $item->body_name; ?>" data-id="<?php echo $item->id; ?>"
           data-period="<?php echo $period; ?>" class="objectsDetail">
        <thead>
        <tr>
            <td colspan="4" width="50%">商品</td>
            <td colspan="2">买入</td>
            <td colspan="2">卖出</td>
        </tr>
        </thead>
        <tbody>
        <tr data-id="<?php echo $item->id; ?>" class="clearLine">
            <td colspan="4"><?php echo $item->body_name; ?><?php echo($item->body_name_english); ?></td>
            <td colspan="2" class="price <?php
            if ($item->body_price_previous > $item->body_price) echo 'green';
            else echo 'red';
            ?>"><?php echo(sprintf('%.' . $item->body_price_decimal . 'f', $item->body_price)); ?></td>
            <td colspan="2" class="price <?php
            if ($item->body_price_previous > $item->body_price) echo 'green';
            else echo 'red';
            ?>"><?php echo(sprintf('%.' . $item->body_price_decimal . 'f', $item->body_price)); ?></td>
        </tr>
        <tr data-id="<?php echo $item->id; ?>">
            <td colspan="5"><p>更新时间: <span
                            class="updateTime"><?php echo date('Y-m-d H:i:s', strtotime($item->updated_at)); ?></span>
                </p></td>
        </tr>
        </tbody>
    </table>
</div>
<div id="liveChart" style="width: 100%; position: fixed; top: 197px;"></div>
</body>
</html>
