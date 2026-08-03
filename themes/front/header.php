<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?php
$ci = $this->_ci;
?>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo $ci->theme->get_page_title(SITE_NAME); ?></title>
        <link rel="shortcut icon" href="favicon.ico" />

        <!--Meta Section-->
        <?php echo display_meta(); ?>

        <!--Style Section-->
        <link href='https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,300' rel='stylesheet' type='text/css' />
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css' />        
        <?php echo add_css(array('forum_jquery-ui', 'font-awesome', 'forum_bootstrap', 'forum_style', 'forum_stylesheet', 'forum_media')); ?>
        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries-->
        <!--[if lt IE 9]>
            <script src="js/html5shiv.js"></script>
            <script src="js/respond.min.js"></script>
        <![endif]-->

        <!--[if IE 8]>
              <link rel="stylesheet" href="css/ie8.css">
        <![endif]-->
        <!--Script Section-->
        <?php echo add_js(array('jquery-1.9.1.min', 'forum_bootstrap', 'forum_bootstrap.validate.min', 'forum_common', 'forum_jquery.placeholder', 'forum_jquery-ui', 'dropkick.min')); ?>
        
        <style type="text/css">
<?php if ($ci->session->userdata[$ci->theme->get('section_name')]['site_id'] != 0) {?>
                .wrapper{
                    background: none repeat scroll 0 0 <?php echo $ci->session->userdata[$ci->theme->get('section_name')]['site_color']; ?> !important;
                }
<?php } ?>
        </style>
    </head>
    <body>
        <?php
        $user_id = $ci->session->userdata[$ci->theme->get('section_name')]['user_id'];
        $name = $ci->session->userdata[$ci->theme->get('section_name')]['firstname'] . ' ' . $ci->session->userdata[$ci->theme->get('section_name')]['lastname'];
        ?>
        <div class="wrpapper wrapper">
            <!-- Header Section -->
            <div class="left-panel"> 
                <a class="logo" title="" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>">
                    <img alt="Logo" src="images/logo.png">
                </a>
            </div>

            <div class="main-panel">    
                <header class="header clearfix">
                    <ul class="admin-links pull-right">
                        <li class="dropdown profile-dropdown">
                            <a aria-expanded="true" data-toggle="dropdown" class="dropdown-toggle" title="Settings" href="javascript:void(0);">                                
<!--                                <span class="admin-pic"><img alt="Profile" src="images/profile.png" class="img-circle"></span>-->
                                <?php
                                if ((isset($name)) && !empty($name)) {
                                    echo $name;
                                } else {
                                    echo '';
                                }
                                ?> 
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu user-right-menu" role="menu">
                                <li style="width:100%;"><a style="width:100%;" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>users/logout" id="Sign_Out_Link"><i class="fa fa-sign-out"></i> <span>Sign Out</span></a></li>
                                <li style="width:100%;"><a style="width:100%;" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>users/action/edit/<?php echo $user_id; ?>" id="Sign_Out_Link"><i class="fa fa-sign-out"></i> <span>Edit My Profile</span></a></li>
                                <!--
                                <li style="width:100%;"><a style="width:100%;" href="#" title="My Profile">My Profile</a></li>
                                <li style="width:100%;"><a style="width:100%;" href="#" title="Account Settings">Account Settings</a></li>
                                <li style="width:100%;"><a style="width:100%;" href="#" title="Help">Help</a></li>
                                -->
                            </ul>
                        </li>
                        <!-- <li class="dropdown">
                            <a data-toggle="dropdown" class="dropdown-toggle" title="Settings" href="#">
                                <img aria-expanded="true" alt="Settings" src="images/settings.png">
                                Settings
                            </a>
                        </li> -->
                    </ul>
                </header>
                
                <div class="fluid-wrap"><!-- fluid-wrap start (close in footer.php) -->
                    <div class="page-header">

                        <?php
                        if ($ci->session->userdata[$ci->theme->get('section_name')]['site_id'] == 0) {
                            $header_logo_src = site_url() . NOT_AVAILABLE_SITE_LOGO;
                        } else {
                            if(file_exists(BASE_PATH_CUSTOM."/assets/uploads/" . $ci->session->userdata[$ci->theme->get('section_name')]['site_logo'])){
                                $header_logo_src = site_url() . "assets/uploads/" . $ci->session->userdata[$ci->theme->get('section_name')]['site_logo'];
                            }else{
                                $header_logo_src = site_url() . NOT_AVAILABLE_SITE_LOGO;
                            }                            
                        }

                        $ci->load->model('hotels/hotels_model');
                        //$site_detail = $ci->sites_model->get_site_detail_custom($site_id);
                        //$hotel_detail = $ci->hotels_model->get_hotel_detail(1);
                        $site_name = $site_detail['site_location_name'];
                        $hotel_name = $hotel_detail['hotel_name'];
                        ?>
                        <span class="hotel-logo">
                            <img alt="Four Seasons Hotel Beirut" src="<?php echo $header_logo_src; ?>">
                        </span>
                        <h5><?php 
                                echo $hotel_name;
                                if(!empty($site_name)){
                                    echo ' - '.$site_name;
                                }
                            ?></h5>
                    </div>
                    <?php echo $ci->breadcrumb->output(); ?>
            <!-- Header Section End -->