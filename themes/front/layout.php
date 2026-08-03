<!DOCTYPE html>
<html lang="en">
<?php
$ci = $this->_ci;
?>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <link rel="shortcut icon" href="images/favicon.ico">
        <title>Administrator Login</title>
        <?php echo add_css(array('bootstrap.min', 'bootstrap-theme.min', 'forum_style', 'custom')); ?>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="login-wrap">
        <?php echo $this->content(); ?>
    </body>
</html>