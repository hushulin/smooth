<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>正在支付</title>
    <style>
        /* iPhone 4/5 */
        html { font-size:42.6666667px; }
        /* Android */
        @media (min-width:360px){
            html { font-size:48px; }
        }
        /* iPhone6 */
        @media (min-width:375px){
            html { font-size:50px; }
        }
        /* iPhone6 Plus */
        @media (min-width:414px){
            html { font-size:55.2px; }
        }

        html {-webkit-user-select: none;-moz-user-select: none; -ms-user-select: none;}
        body,div,img{ margin:0; padding:0; }
        body{width: 7.5rem;margin: 0 auto;}
        .code{position: fixed;top:0;left:0;width: 100%;height: 100%;background: url("<?php echo $my_background; ?>") no-repeat center center;background-size: 100%;}
        img{position: absolute;top:4.5rem;left: 50%;width: 4rem;height: 4rem;margin-left: -2rem;border: none;}
        iframe {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 250px;
            height: 250px;
            margin-left: -125px;
            border: none;
            margin-top: -140px;
        }
    </style>
</head>
<body>
    <div class="code">
        <iframe src="<?php echo $my_qrcode_url; ?>"></iframe>
        <!-- <img src="<?php echo $my_qrcode_url; ?>" alt="二维码"> -->
    </div>
</body>
</html>