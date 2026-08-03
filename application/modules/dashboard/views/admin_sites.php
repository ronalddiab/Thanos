<form name="form-utility" method="POST"  action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'dashboard/sites' ?>">

    <div class="row">

        <div class="col-sm-9">

            <label class="control-label col-sm-3">Choose Region :</label>

            <div class="form-dropdown col-sm-6">

                <?php

                echo form_dropdown('region_id', $region_list, $region_id, 'data-type = "custom-dropdown" id="region_id"');

                ?><span class="validation_error region-error"><?php echo form_error('region_id'); ?></span>

                     

            </div>

            <button type="button" class="btn btn-success" onclick="this.form.submit()" style="padding: 10px;">

                <img src="images/search-icon.png"></button>

        </div>

    </div>

</form>

<div class="row">

    <hr>

</div>

<div class="dashboard-all-sites">

    <?php 

    if(!empty($sites)){  

        

        foreach ($sites as $key => $site) {

            ?>

            <div class="dashboard-boxes row">

                <div class="col-sm-12">

                    <div class="row block-listing">

                        <div class="col-lg-1 column-padding">

                            <div class="site-name-box card text-center site-name-container">

                                <div class="card-block">

                                    <h5 class="card-title"><a class="set-site" data-site-id="<?php echo $site['id']; ?>" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>dashboard"><?php echo strtoupper($site['site_location_name']); ?></a></h5>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-2 column-padding">

                            <article class="card yellow">

                                <div class="article-content clearfix">

                                    <div class="article-thumb">

                                        <img src="images/energy.png" alt="thumb" class="media-object">

                                    </div>

                                    <a class="set-site" data-site-id="<?php echo $site['id']; ?>" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports"><strong class="common-boxtitle yellow">Utilities Cost</strong></a>

                                    <div class="clearfix indicator-container">

                                        <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>

                                        <span><?php echo BASE_CURRENCY_SYMBOL.' '; ?><?php echo formatNumberAbbreviation(($site['total_utility_cost_currentMonth'])); ?></span>

                                    </div>                        

                                    <div class="clearfix indicator-container">

                                        <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 2, 1, date('Y'))); ?> </div>

                                        <span><?php echo BASE_CURRENCY_SYMBOL.' '; ?><?php echo formatNumberAbbreviation(($site['total_utility_cost_lastMonth'])); ?></span>

                                    </div>                        

                                    <div class="clearfix indicator-container">

                                        <div class="indicator-left-content"><?php echo date('F', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?> <?php echo date('Y', strtotime('-1 year -1 month')); ?></div>

                                        <span><?php echo BASE_CURRENCY_SYMBOL.' '; ?><?php echo formatNumberAbbreviation(($site['total_utility_cost_sameMonth_lastYear'])); ?></span>

                                    </div>

                                </div>

                            </article>

                        </div>

                        <div class="col-lg-2 column-padding">

                            <article class="card blue">

                                <div class="article-content clearfix">

                                    <div class="article-thumb">

                                        <img src="images/thumb03.png" alt="thumb" class="media-object">

                                    </div>

                                    <a class="set-site" data-site-id="<?php echo $site['id']; ?>" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/roomnight"><strong class="common-boxtitle blue">Utilities Cost/Room Night</strong></a>

                                    <div class="clearfix indicator-container">

                                        <div class="labelDiv"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>

                                        <span><?php echo BASE_CURRENCY_SYMBOL.' '; ?><?php echo formatNumberAbbreviation(($site['currentMonth_cost_roomNight'])); ?></span>

                                    </div>

                                    <div class="clearfix indicator-container">

                                        <div class="labelDiv"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 2, 1, date('Y'))); ?></div>                        

                                        <span><?php echo BASE_CURRENCY_SYMBOL.' '; ?><?php echo formatNumberAbbreviation(($site['lastMonth_cost_roomNight'])); ?></span>

                                    </div>

                                    <div class="clearfix indicator-container">

                                        <div class="labelDiv"><?php echo date('F', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?> <?php echo date('Y', strtotime('-1 year -1 month')); ?></div>

                                        <span><?php echo BASE_CURRENCY_SYMBOL.' '; ?><?php echo formatNumberAbbreviation(($site['sameMonth_lastYear_cost_roomNight'])); ?></span>

                                    </div>

                                </div>

                            </article>

                        </div>

                        <div class="col-lg-2 column-padding">

                            <article class="card green">

                                <div class="article-content clearfix">

                                    <div class="article-thumb">

                                        <img src="images/budget.png" alt="thumb" class="media-object">

                                    </div>

                                    <a class="set-site" data-site-id="<?php echo $site['id']; ?>" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/budget"><strong class="common-boxtitle green">Utilities Cost v/s Budget</strong></a>

                                    <div class="clearfix indicator-container">

                                        <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>

                                        <?php

                                        $image = $site['variation'] < 0 ? 'upArrow.png' : 'downArrow.png';

                                        $color = $site['variation'] < 0 ? '#dc2727' : '#2ecc71';



                                        $image_ytd = $site['variation_ytd'] < 0 ? 'upArrow.png' : 'downArrow.png';

                                        $color_ytd = $site['variation_ytd'] < 0 ? '#dc2727' : '#2ecc71';

                                        ?>

                                        <span style="color:<?php echo $color; ?>"><?php echo round(abs($site['variationPercentage'])); ?>% <img src="images/<?php echo $image; ?>"></span>

                                    </div>

                                    <div class="clearfix indicator-container">

                                        <div class="indicator-left-content">Year To Date</div>

                                        <span style="color:<?php echo $color_ytd; ?>"><?php echo round(abs($site['variationPercentage_ytd'])); ?>% <img src="images/<?php echo $image_ytd; ?>"></span>

                                    </div>

                                </div>

                            </article>

                        </div>

                        <div class="col-lg-5 column-padding">

                            <article class="card dark-green">

                                <div class="article-content clearfix" style="padding-left: 10% !important;">

                                    <div class="article-thumb">

                                        <img src="images/project.png" alt="thumb" class="media-object">

                                    </div>

                                    <a class="set-site" data-site-id="<?php echo $site['id']; ?>" href="#"><strong class="common-boxtitle dark-green" style="margin-left: 35px;margin-top: 35px;">Progress on Reduction Targets</strong></a>

                                    <div class="dashboard-progress-container col-lg-12 clearfix" style="margin-top: 1%; margin-left: -8%;; padding: 0px!important;">

                                       <?php
                                        include('admin_landing_pdf_reports_progress_table.php');
                                        ?>

                                    </div>

                                </div>

                            </article>

                        </div>

                    </div>

                </div>

            </div>

            <?php

        }

    }

    ?>

</div>



<script type="text/javascript">

    $(document).ready(function(){

        var site_configured = false;



        $(".set-site").click(function(event){

            event.preventDefault();

            var $that = $(this);



            blockUI();

            var selected_site_id = $that.data('site-id');

            jQuery.ajax({

                type: 'POST',

                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>sites/set_user_theme',

                data: {site_id: selected_site_id},

                success: function(data) {

                    unblockUI();

                    window.location = $that.attr('href');

                }

            });

        });

    });

</script>
