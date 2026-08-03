<div class="wrapper">

    <div class="login-container">

        <div class="row">

            <div class="col-sm-6">

                <div class="center-alignment">

                    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>users/login" title="HEP - Hotel Energy Portal" class="login-logo">

                        <img src="<?php echo BASE_URL_CUSTOM; ?>themes/default/images/login-logo-hep.png" alt="HEP - Hotel Energy Portal">

                    </a>

                </div>

                <div class="about-wrap" style="height: 60%;width: 70%;">

                    <?php 

                    /*switch ($cms[0]['c']['slug_url']) {

                        case 'about-us':

                            $image_url = 'images/about-img.png';

                            break;



                        case 'services':

                            $image_url = 'images/service-img.png';

                            break;



                        default:

                            $image_url = 'images/login-img.jpg';

                            break;

                    }*/

                    $image_url = 'images/login-img.png';

                    ?>

                    <img class="img-responsive" alt="About" src="<?php echo $image_url; ?>" style="box-shadow: 0 0px 50px 0px rgba(88, 99, 92, 0.25);">

                </div>

            </div>

            <div class="col-sm-6">

                <img class="img-responsive" alt="About" src="images/logo.jpg" style="width: 35%;margin: 0 auto;margin-bottom: 5%;margin-top: -10%;">