<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="">

        <meta http-equiv="X-UA-Compatible" content="IE=9">

        <link rel="shortcut icon" href="images/favicon.ico" />

        

        <title>HEP - <?php echo ucwords($ci->router->fetch_module()); ?></title>

        <?php echo add_css(array('reset', 'font-awesome', 'bootstrap.min', 'bootstrap-theme.min', 'easy-responsive-tabs', 'style', 'media', 'jquery-ui', 'custom')); ?>

        <?php echo add_js(array('jquery', 'jquery-ui', 'jquery.blockUI', 'chosen.jquery.min', 'icheck.min', 'bootstrap.min', 'modernizr', 'respond.min', 'dropkick.min', 'general', 'jquery.validate', 'jquery.form', 'custom-file-uplaod', 'common', 'jquery.cookies', 'bootstrap-multiselect')); ?>



        <style type="text/css">

<?php if ($ci->session->userdata[$ci->theme->get('section_name')]['site_id'] != 0) { ?>

                .wrapper{

                    background: none repeat scroll 0 0 <?php echo $ci->session->userdata[$ci->theme->get('section_name')]['site_color']; ?> !important;

                }                

                .nav-menu{

                    background: none repeat scroll 0 0 <?php echo $ci->session->userdata[$ci->theme->get('section_name')]['site_color']; ?> !important;

                }

                .collapsed-menu .menu-left li .sub-menu-nav{background:<?php echo $ci->session->userdata[$ci->theme->get('section_name')]['site_color']; ?>;}

<?php } ?>

        </style>

    </head>

    <body class="<?php echo isset($_COOKIE['collapsed_menu']) && $_COOKIE['collapsed_menu']?'collapsed-menu':''; ?>">

        <?php

        $user_id = $ci->session->userdata[$ci->theme->get('section_name')]['user_id'];



        $ci->load->model('users/users_model');

        $user_info = $ci->users_model->get_user_detail($user_id);

        $name = $user_info['firstname'] . ' ' . $user_info['lastname'];

        ?>

        <div class="wrapper">   

            <div class="left-panel"> 

                <a class="logo" title="" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>">

                    <img alt="Logo" src="images/logo.png" style="height: 50px;">

                </a>

                <a class="nav-menu" id="collapsed_menu" title="" href="javascript:void(0);">

                    <span class="nav-icon"></span>

                </a>  



                <div class="side-bar-menu">

                    <?php

                        widget('menu', array('menu_name' => 'admin_menu', 'section_name' => $ci->theme->get('section_name')));

                    ?>



                    <?php

                    $user_id = isset($ci->session->userdata['admin']['user_id']) ? $ci->session->userdata['admin']['user_id'] : '';

                    $site_id = isset($ci->session->userdata['admin']['site_id']) ? $ci->session->userdata['admin']['site_id'] : '';

                    $role_id = isset($ci->session->userdata['admin']['role_id']) ? $ci->session->userdata['admin']['role_id'] : '';



                    $ci->load->model('sites/sites_model');

                    $ci->sites_model->sort_by = 's.site_location_name';

                    $ci->sites_model->sort_order = 'ASC';

                    $sites = $ci->sites_model->get_site_listing_for_users($site_id, $role_id, $user_id);

                    $displaydiv = (count($sites) > 0) ? '' : 'style="display:none;"';

                    ?>

                    <div id="selectsitelistcontainer" <?php echo $displaydiv; ?>>

                        <span class="component-label">Select Hotel</span>

                        <div id="siteselectionlink">

                            <?php if (!empty($sites)) { ?>

                                <div class="custom-dropdown">

                                    <select id="selectsite" data-type="custom-dropdown-update">

                                        <?php foreach ($sites as $site) { ?>

                                            <option <?php echo ($site['s']['id'] == $site_id) ? 'selected="selected"' : ''; ?> value="<?php echo $site['s']['id']; ?>"><?php echo $site['s']['site_location_name']; ?></option>

                                        <?php } ?>

                                    </select>

                                </div>

                            <?php } ?>

                        </div>

                    </div>

                </div>

            </div><!-- leftpanel -->



            <div class="main-panel">    

                <header class="header clearfix">

                    <a class="nav-menu small-view" title="" href="javascript:void(0);">

                        <span class="nav-icon"></span>

                    </a>

                    <ul class="admin-links pull-right">

                        <li class="dropdown profile-dropdown">

                            <a aria-expanded="true" data-toggle="dropdown" class="dropdown-toggle" title="Settings" href="javascript:void(0);">

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

                                <li style="width:100%;"><a style="width:100%;" href="javascript:" id="Sign_Out_Link"><i class="fa fa-sign-out"></i> <span>Sign Out</span></a></li>

                                <li style="width:100%;"><a style="width:100%;" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>users/action/edit_profile/<?php echo $user_id; ?>" id="Sign_Out_Link"><i class="fa fa-sign-out"></i> <span>Edit My Profile</span></a></li>

                            </ul>

                        </li>

                    </ul>

                </header>

                <div class="fluid-wrap"><!-- fluid-wrap start (close in footer.php) -->

                    <div class="page-header clearfix">

                        <div class="col-sm-6">

                            <?php

                            $if_site_header = true;



                            if($ci->uri->segment(1) == 'dashboard' && $ci->uri->segment(2) == 'sites'){

                                $if_site_header = false;

                            }



                            $ci->load->model('hotels/hotels_model');

                            $site_detail = $ci->sites_model->get_site_detail_custom($site_id);

                            $hotel_detail = $ci->hotels_model->get_hotel_detail(1);

                            $site_name = $site_detail['site_location_name'];

                            $hotel_name = $hotel_detail['hotel_name'];

                            $csr = $site_detail['csr'];

                            $daily_metering = $site_detail['daily_metering'];



                            if($csr != 0)

                            { ?>

                                <script type="text/javascript">                                    
                                    if(document.getElementById("menu48") != null) {
                                        document.getElementById("menu48").style.display = 'block';
                                    }

                                </script>

                                <?php 

                            }

                            else

                            {   ?>

                                <script type="text/javascript">                                    
if(document.getElementById("menu48") != null) {
                                    document.getElementById("menu48").style.display = 'none';
}
                                </script>

                                <?php 

                            }



                            if($daily_metering != 0)

                            { ?>

                                <script type="text/javascript">                                    
if(document.getElementById("menu49") != null) {
                                    document.getElementById("menu49").style.display = 'block';
}
                                </script>

                                <?php 

                            }

                            else

                            {   ?>

                                <script type="text/javascript">                                    
if(document.getElementById("menu49") != null) {
                                    document.getElementById("menu49").style.display = 'none';
}
                                </script>

                                <?php 

                            }

                       

                            // to hide user login log menu 

                            ?>

                            <script type="text/javascript">                                    
if(document.getElementById("menu46") != null) {
                                document.getElementById("menu46").style.display = 'none';
}
                            </script>

                            <?php 

                            if($if_site_header){

                                if ($ci->session->userdata[$ci->theme->get('section_name')]['site_id'] == 0) {

                                    $header_logo_src = site_url() . NOT_AVAILABLE_SITE_LOGO;

                                } else {

                                    if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $ci->session->userdata[$ci->theme->get('section_name')]['site_logo'])) {

                                        $header_logo_src = site_url() . "assets/uploads/" . $ci->session->userdata[$ci->theme->get('section_name')]['site_logo'];

                                    } else {

                                        $header_logo_src = site_url() . NOT_AVAILABLE_SITE_LOGO;

                                    }

                                }                              



                                $hotel_display_name = $site_name;

                            }else{

                                if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $ci->session->userdata[$ci->theme->get('section_name')]['hotel_logo'])) {

                                    $header_logo_src = site_url() . "assets/uploads/" . $ci->session->userdata[$ci->theme->get('section_name')]['hotel_logo'];

                                } else {

                                    $header_logo_src = site_url() . NOT_AVAILABLE_SITE_LOGO;

                                }



                                $hotel_display_name = $hotel_name;

                            }

                            ?>

                            <span class="hotel-logo">

                                <img class="siteImage" alt="" src="<?php echo $header_logo_src; ?>">

                            </span>

                            <h5><?php echo $hotel_display_name; ?>

                            </h5>

                        </div>

                        <div class="col-sm-6">

                            <span class="today_date"><?php echo date('j F, Y'); ?></span>                            

                        </div>

                    </div>

                    <?php

                    if(module_controller_exists('audit_admin','audit')) {

                        if($ci->uri->segment(1) == "dashboard" && $ci->uri->segment(2) == "") {

                            $ci->load->model('audit/audit_model');

                            $ci->audit_model->site_id = $site_id;

                            $audit_detail = $ci->audit_model->get_last_energy_audit_date();                            

                        ?>

                        <div class="clearfix">

                            <div class="col-sm-8 cnt-last-energy-audit">

                                <?php echo $ci->breadcrumb->output(); ?>

                            </div>

                            <div class="col-sm-4 cnt-last-energy-audit">

                                <div class="div-last-energy-audit">

                                    <span class="cls-last-energy-audit"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>audit">Last Energy Audit</a>: <?php echo (empty($audit_detail))?'-':date('d-M-Y',strtotime($audit_detail)); ?></span>

                                </div>

                            </div>

                        </div>

                        <?php } else { echo $ci->breadcrumb->output(); } ?>

                    <?php } else { echo $ci->breadcrumb->output(); } ?>

                    <script type="text/javascript">

                        var base_url = "<?php echo BASE_URL_CUSTOM; ?>";

                       

                        $(document).ready(function() {

                           

                            if ($.cookie("collapsed_menu") != null && $.cookie("collapsed_menu") == 1 && !$("body").hasClass("collapsed-menu")) {

                                $("body").addClass("collapsed-menu")

                                $(".active .sub-menu-nav").hide();

                            }



                            $("#collapsed_menu").click(function() {

                                if ($.cookie("collapsed_menu") != null && $.cookie("collapsed_menu") == 1) {

                                    $.cookie("collapsed_menu", 0);

                                    $(".active .sub-menu-nav").show();

                                } else {

                                    $(".active .sub-menu-nav").hide();

                                    $.cookie("collapsed_menu", 1);

                                }

                            });



                            $('#siteselectionlink').on('change','#selectsite',function() {

                                blockUI();

                                var selected_site_id = $(this).val();

                                jQuery.ajax({

                                    type: 'POST',

                                    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>sites/set_user_theme',

                                    data: {site_id: selected_site_id},

                                    success: function(data) {

                                        unblockUI();

                                        location.reload(true);

                                    }

                                });

                            });



                            $("select[data-type='custom-dropdown-update']").dropkick({

                                mobile: true

                            });

                        });

						function saveAsPng(){

							var url = $(this).attr('href');

                            var imagename = $(this).attr('download');

                            

                            if(imagename == '')

                            {

                                imagename = 'image.png';

                            }



                            $.ajax({

                                type: "POST",

                                url:  base_url+'reports/saveimage',

                                data:  { imagename: imagename, url: url},                                    

                                success: function(result){

                                    if(result == imagename){

                                       window.location.href = base_url+'reports/getimage?image='+result;

                                    }

                                },

                                error: function(result){

                                    alert('Something went wrong.');

                                },

                            });

                            return false;

						}

                    </script>