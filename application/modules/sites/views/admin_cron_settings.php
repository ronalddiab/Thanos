<article class="card" id="ajax_table">

    <div class="article-header">

        <?php echo "CRON management"; ?>

    </div>

    <div class="card-wrap">

        <div class="form-outer-block">

            <div class="row">

                <form name="form-utility" method="POST"  action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'sites/cron_settings' ?>">

                    <div class="row">

                        <div class="col-sm-12">

                            <label class="control-label col-sm-3">Choose Region :</label>

                            <div class="form-dropdown col-sm-6">

                                <?php

                                echo form_dropdown('region_id', $region_list, $region_id, 'data-type = "custom-dropdown" id="region_id"');

                                ?><span class="validation_error region-error"><?php echo form_error('region_id'); ?></span>

                            </div>

                            <input type="hidden" id="region_id_hidden" name="region_id_hidden" value="<?php echo $region_id; ?>" ></input>

                            <Button type="button" class="btn btn-success" name="region_submit" id="region_submit" onclick="this.form.submit()" style="padding: 10px;">



                                <img src="http://localhost/hotel_energy_portal/themes/default/images/search-icon.png"></Button>

                            <Button type="button" class="btn btn-success custom-uncheck" style="padding: 10px;">Uncheck All</a>

                        </div>

                    </div>

                </form>

            </div>

            <div class="row"><hr></div>

            <?php echo form_open(); ?>

            <input type="hidden" id="ci_csrf_token" name="ci_csrf_token" value="<?php echo $this->_ci->security->get_csrf_hash(); ?>" ></input> 

             <input type="hidden" id="region_id" name="region_id" value="<?php echo $region_id; ?>" ></input>

            <div class="row">

                <div class="form-col-1"><label>Site Id</label></div>

                <div class="form-col-2"><label>Site Name</label></div>

                <div class="form-col-1"><label>Annual</label></div>

                <div class="form-col-1"><label>Monthly</label></div>

                <div class="form-col-2"><label>Daily Trends</label></div>

            </div>

            <div class="row"><hr></div>

            <?php

            foreach ($sites as $site) {

                $site_id = $site['s']['id'];

                ?>

                <div class="row">

                    <?php 

                    $isMonthlyChecked = 0;

                    $isAnnualChecked = 0;

                    $isDailyTrendsChecked = 0;

                    $isMonthlyChecked = in_array($site_id, $monthly);

                    $isAnnualChecked = in_array($site_id, $annual);

                    $isDailyTrendsChecked = in_array($site_id, $daily_trends);

                    ?>
                    <div class="form-col-1">

                        <label> <?php echo $site['s']['id']; ?></label>

                    </div> 

                    <div class="form-col-2">

                        <label> <?php echo $site['s']['site_location_name']; ?></label>

                    </div>     

                    <div class="form-col-1">

                        <input type="checkbox" id="checkbox_annual_<?php echo $site_id; ?>" name="annual[]" value="<?php echo $site_id; ?>" <?php if ($isAnnualChecked) { echo "checked"; } ?>></input>

                    </div>

                    <div class="form-col-1">

                        <input type="checkbox" id="checkbox_monthly_<?php echo $site_id; ?>" name="monthly[]" value="<?php echo $site_id; ?>" <?php if ($isMonthlyChecked) { echo "checked"; } ?>></input>

                    </div>

                    <div class="form-col-2">

                        <input type="checkbox" id="checkbox_daily_<?php echo $site_id; ?>" name="daily_trends[]" value="<?php echo $site_id; ?>" <?php if ($isDailyTrendsChecked) { echo "checked"; } ?>></input>

                    </div>

                </div>

            <?php 

            } ?>

            <div class="row"><hr></div>

            <div class="row">

                <div class="form-group">

                    <div class="form-col-2"></div>

                    <div class="form-col-2"></div>

                    <div class="form-col-2">

                        <!--<button class="btn btn-success" type="submit" id="site_submit" name="site_submit">-->

                        <!--    <?php echo lang('btn-submit'); ?> -->

                        <!--</button>-->

                        <input class="btn btn-success" type="submit" id="site_submit" name="site_submit">

                           

                        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'sites'; ?>" class="btn btn-default reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>

                        </div>

                    </div>

                </div>

            </div>

            

            <?php echo form_close(); ?>

        </div>

    </div>

</article>

<script>

    $(document).ready(function(){

        $('.custom-uncheck').click(function(){

            $('input:checkbox').removeAttr('checked');

        })

    })

</script>