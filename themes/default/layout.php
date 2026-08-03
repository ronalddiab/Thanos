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
        <title>Login</title>
        <?php echo add_css(array('reset', 'font-awesome', 'bootstrap.min', 'bootstrap-theme.min', 'dropkick', 'style', 'media', 'custom')); ?>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="login-wrap">

        <!-- Preloader -->
        <?php /* ?>
        <div id="preloader">
            <div id="status"><i class="fa fa-spinner fa-spin"></i></div>
        </div>
        <?php */ ?>
        
        <!--<section>-->
        <?php echo $this->content(); ?>
        <!--</section>-->

        <?php //echo add_js(array('jquery', 'bootstrap.min', 'modernizr', 'respond.min', 'dropkick.min', 'general', 'jquery.sparkline.min', 'toggles.min', 'jquery.cookies', 'custom', 'bootstrapValidator')); ?>
        <?php echo add_js(array('jquery', 'jquery.blockUI','bootstrap.min', 'dropkick.min', 'icheck', 'admin-login', 'bootstrapValidator','common')); ?>
        
        <script type="text/javascript">            
            $(document).ready(function() {
                $('#login_form_inner').bootstrapValidator({
                    message: 'This value is not valid',
                    fields: {
                        username: {
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {
                                    message: 'The username is required and can\'t be empty'
                                }
                            }
                        },
                        password: {
                            validators: {
                                notEmpty: {
                                    message: 'The password is required and can\'t be empty'
                                }
                            }
                        }
                    }
                });                
                
                $('#forgot_password_admin').bootstrapValidator({
                    message: 'This value is not valid',
                    fields: {
                        email: {
                            message: 'The Email is not valid',
                            validators: {
                                notEmpty: {
                                    message: 'The Email is required and can\'t be empty'
                                },
                                emailAddress: {
                                    message: 'Invalid Email Address'
                                }

                            }
                        }
                    }
                });
                
            });
        </script>
    </body>
</html>