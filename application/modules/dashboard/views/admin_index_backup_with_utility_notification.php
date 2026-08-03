<?php //echo add_js(array('charts_loader'));  ?>
<?php
?>
<div class="dashboard-boxes row">
    <div class="col-sm-5">
        <article class="card card-left-column">
            <div class="article-header notification_div_header">
                <!-- <i><img src="images/bell.png" alt="Notification"></i> -->
                Last Month Utilities Trends
            </div>
            <div class="article-content notification_div">
                <!-- <ul class="default-listing"> -->
                    <?php
                    if(!empty($utility_cost_calculation))
                    {   ?>
                         <table width="100%" border="0" cellpadding="2" cellspacing="2" style="font-size: medium;">
                            <tbody>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th colspan="2"><strong><?php echo $currentmonth." ".$currentyear." v/s ".$lastmonth." ".$lastyear; ?></strong></th>
                                    </tr>
                                </thead>
                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr>
                                    <td width="28%"><strong>Utilities</strong></td>
                                    <td width="30%"><strong>Consumption</strong></td>
                                    <td width="42%"><strong>Cost</strong></td>
                                </tr>                            
                                <?php
                                foreach ($utility_cost_calculation as $utility_cost_data) {     

                                    // echo $key;
                                    // pre($utility_cost_calculation);
                                    $image = $utility_cost_data['consumption'] < 0 ? 'upArrow.png' : 'downArrow.png'; ?>
                                    <tr>
                                        <td><?php echo $utility_cost_data['title']; ?></td>
                                        <td><?php echo round(abs($utility_cost_data['consumption'])); ?>% <?php if(round(abs($utility_cost_data['consumption'])) == 0) { } else { ?> <img src="images/<?php echo $utility_cost_data['consumption_image']; ?>" style="width: 18px;"> <?php } ?> </td>
                                        <td><?php echo round(abs($utility_cost_data['cost'])); ?>% <?php if (round(abs($utility_cost_data['cost'])) == 0) { } else { ?><img src="images/<?php echo $utility_cost_data['cost_image']; ?>" style="width: 16px;">  <?php } ?> </td>
                                    </tr>

                                <?php                             
                                }   ?>
                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr>
                                 <?php
                                foreach ($utility_cost_calculation_chr as $utility_cost_data) {     

                                    // $image = $utility_cost_data_chr['consumption'] < 0 ? 'upArrow.png' : 'downArrow.png';      ?>
                                    
                                        <td ><?php echo $utility_cost_data['title']; ?>  <?php echo round(abs($utility_cost_data['consumption'])); ?>% <?php if( round(abs($utility_cost_data['consumption'])) == 0 ){ } else { ?> <img src="images/<?php echo $utility_cost_data['consumption_image']; ?>" style="width: 18px;"> <?php } ?></td>                                    
                                <?php                             
                                }   ?>
                                </tr>
                            </tbody>
                        </table>
                        <?php
                    }
                    ?>              
                <!-- </ul> -->
            </div>
        </article>
        <article class="card card-left-column">
            <div class="article-header notification_div_header">
                <i><img src="images/bell.png" alt="Notification"></i>
                Notifications
            </div>
            <div class="article-content notification_div">
                <ul class="default-listing">
                <?php
                if ($notifications or !empty($utilityForLastMonthCompare)) {
                    
                    if ($utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity'] > 0) { ?>
                        <?php
                        if ($utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity'] > 0) {
                            $electricitydifference = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['electricity'] - $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity'];
                            $electricitypercentage = $electricitydifference * 100 / $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity'];
                        } else {
                            $electricitypercentage = 100;
                        }
                        $electricitypercentage = round($electricitypercentage, 2);
                        if ($electricitypercentage < 0) {
                            $difference_status = 'decreased';
                        } else {
                            $difference_status = 'increased';
                        }
                        $electricitypercentage = abs($electricitypercentage);
                        if($utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['electricity'] > 0){
                        ?>
                            <li class="clearfix electricity-notification-div notification-status-<?php echo $difference_status; ?>"><span>Your <strong>Electricity</strong> consumption of <?php echo $filters_notification['currentmonth']; ?> has <?php echo $difference_status; ?> by <?php echo $electricitypercentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li><?php 
                        }
                    }

                    if ($utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['water'] > 0) {
    
                        if ($utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['water'] > 0) {
                            $waterdifference = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['water'] - $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['water'];
                            $waterpercentage = $waterdifference * 100 / $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['water'];
                        } else {
                            $waterpercentage = 100;
                        }
                        $waterpercentage = round($waterpercentage, 2);
                        if ($waterpercentage < 0) {
                            $difference_status = 'decreased';
                        } else {
                            $difference_status = 'increased';
                        }
                        $waterpercentage = abs($waterpercentage);
                        if($utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['water'] > 0){
                        ?>
                        <li class="clearfix water-notification-div notification-status-<?php echo $difference_status; ?>"><span>Your <strong>Water</strong> consumption of <?php echo $filters_notification['currentmonth']; ?> has <?php echo $difference_status; ?> by <?php echo $waterpercentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li><?php 
                        }
                    }

                    //show fuel oil variation
                    if ($site_detials['show_utility_fuel_oil']) {
                        $current_fuel_energy = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['fuel'];
                        if(empty($current_fuel_energy)){
                            $current_fuel_energy = 0;
                        }

                        $previous_fuel_energy = $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['fuel'];
                        if(empty($current_fuel_energy)){
                            $previous_fuel_energy = 0;
                        }

                        if ($previous_fuel_energy > 0) {
                            $fueldifference = $current_fuel_energy - $previous_fuel_energy;
                            $fuelpercentage = $fueldifference * 100 / $previous_fuel_energy;
                        } else {
                            $fuelpercentage = 100;
                        }
                        $fuelpercentage = round($fuelpercentage, 2);
                        if ($fuelpercentage < 0) {
                            $difference_status = 'decreased';
                        } else {
                            $difference_status = 'increased';
                        }
                        $fuelpercentage = abs($fuelpercentage);

                        if ($current_fuel_energy > 0) {?>
                            <li class="clearfix thermal-notification-div notification-status-<?php echo $difference_status; ?>"><span>Your <strong>Fuel Oil</strong> consumption of <?php echo $filters_notification['currentmonth']; ?> has <?php echo $difference_status; ?> by <?php echo $fuelpercentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li><?php 
                        }
                    }

                    //show lpg vatiation
                    if ($site_detials['show_utility_lpg']) {
                        $current_lpg_energy = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['lpg'];
                        if(empty($current_lpg_energy)){
                            $current_lpg_energy = 0;
                        }

                        $previous_lpg_energy = $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['lpg'];
                        if(empty($current_lpg_energy)){
                            $previous_lpg_energy = 0;
                        }

                        if ($previous_lpg_energy > 0) {
                            $lpgdifference = $current_lpg_energy - $previous_lpg_energy;
                            $lpgpercentage = $lpgdifference * 100 / $previous_lpg_energy;
                        } else {
                            $lpgpercentage = 100;
                        }
                        $lpgpercentage = round($lpgpercentage, 2);
                        if ($lpgpercentage < 0) {
                            $difference_status = 'decreased';
                        } else {
                            $difference_status = 'increased';
                        }
                        $lpgpercentage = abs($lpgpercentage);

                        if ($current_lpg_energy > 0) {?>
                            <li class="clearfix thermal-notification-div notification-status-<?php echo $difference_status; ?>"><span>Your <strong>LP Gas</strong> consumption of <?php echo $filters_notification['currentmonth']; ?> has <?php echo $difference_status; ?> by <?php echo $lpgpercentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li><?php 
                        }
                    }

                    //show natural_gas_variation
                    if ($site_detials['show_utility_natural_gas']) {
                        $current_natural_gas_energy = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['natural_gas'];
                        if(empty($current_natural_gas_energy)){
                            $current_natural_gas_energy = 0;
                        }

                        $previous_natural_gas_energy = $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['natural_gas'];
                        if(empty($current_natural_gas_energy)){
                            $previous_natural_gas_energy = 0;
                        }

                        if ($previous_natural_gas_energy > 0) {
                            $natural_gas_difference = $current_natural_gas_energy - $previous_natural_gas_energy;
                            $natural_gas_percentage = $natural_gas_difference * 100 / $previous_natural_gas_energy;
                        } else {
                            $natural_gas_percentage = 100;
                        }
                        $natural_gas_percentage = round($natural_gas_percentage, 2);
                        if ($natural_gas_percentage < 0) {
                            $difference_status = 'decreased';
                        } else {
                            $difference_status = 'increased';
                        }
                        $natural_gas_percentage = abs($natural_gas_percentage);

                        if ($current_natural_gas_energy > 0) {?>
                            <li class="clearfix thermal-notification-div notification-status-<?php echo $difference_status; ?>"><span>Your <strong>Natural Gas</strong> consumption of <?php echo $filters_notification['currentmonth']; ?> has <?php echo $difference_status; ?> by <?php echo $natural_gas_percentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li><?php 
                        }
                    }

                    //show district heating variation
                    if ($site_detials['show_utility_district_heating']) {
                        $current_heating_district_energy = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['heating_district'];
                        if(empty($current_heating_district_energy)){
                            $current_heating_district_energy = 0;
                        }

                        $previous_heating_district_energy = $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['heating_district'];
                        if(empty($current_heating_district_energy)){
                            $previous_heating_district_energy = 0;
                        }

                        if ($previous_heating_district_energy > 0) {
                            $heating_district_difference = $current_heating_district_energy - $previous_heating_district_energy;
                            $heating_district_percentage = $heating_district_difference * 100 / $previous_heating_district_energy;
                        } else {
                            $heating_district_percentage = 100;
                        }
                        $heating_district_percentage = round($heating_district_percentage, 2);
                        if ($heating_district_percentage < 0) {
                            $difference_status = 'decreased';
                        } else {
                            $difference_status = 'increased';
                        }
                        $heating_district_percentage = abs($heating_district_percentage);

                        if ($current_heating_district_energy > 0) {?>
                            <li class="clearfix thermal-notification-div notification-status-<?php echo $difference_status; ?>"><span>Your <strong>District Heating</strong> consumption of <?php echo $filters_notification['currentmonth']; ?> has <?php echo $difference_status; ?> by <?php echo $heating_district_percentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li><?php 
                        }
                    }

                    //show district cooling variation
                    if ($site_detials['show_utility_district_cooling']) {
                        $current_cooling_district_energy = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['cooling_district'];
                        if(empty($current_cooling_district_energy)){
                            $current_cooling_district_energy = 0;
                        }

                        $previous_cooling_district_energy = $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['cooling_district'];
                        if(empty($current_cooling_district_energy)){
                            $previous_cooling_district_energy = 0;
                        }

                        if ($previous_cooling_district_energy > 0) {
                            $cooling_district_difference = $current_cooling_district_energy - $previous_cooling_district_energy;
                            $cooling_district_percentage = $cooling_district_difference * 100 / $previous_cooling_district_energy;
                        } else {
                            $cooling_district_percentage = 100;
                        }
                        $cooling_district_percentage = round($cooling_district_percentage, 2);
                        if ($cooling_district_percentage < 0) {
                            $difference_status = 'decreased';
                        } else {
                            $difference_status = 'increased';
                        }
                        $cooling_district_percentage = abs($cooling_district_percentage);

                        if ($current_cooling_district_energy > 0) {?>
                            <li class="clearfix thermal-notification-div notification-status-<?php echo $difference_status; ?>"><span>Your <strong>District Cooling</strong> consumption of <?php echo $filters_notification['currentmonth']; ?> has <?php echo $difference_status; ?> by <?php echo $cooling_district_percentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li><?php 
                        }
                    }

                    if ($site_detials['show_total_utility_notification']) {

                        if ($utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['revenue'] > 0) {
                            $total_current_utility_revenue = ((100 * $utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['total_utility']) / $utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['revenue']);
                        } else {
                            $total_current_utility_revenue = 100;
                        }

                        if ($utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['revenue'] > 0) {
                            $total_previous_utility_revenue = ((100 * $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['total_utility']) / $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['revenue']);
                        } else {
                            $total_previous_utility_revenue = 100;
                        }

                        $total_current_utility_revenue  = abs(round($total_current_utility_revenue, 2));
                        $total_previous_utility_revenue = abs(round($total_previous_utility_revenue, 2));
                        
                        if ($total_current_utility_revenue) {?>
                            <li class="clearfix notification-status-none"><span>Your <strong>Total Utilities</strong> for <?php echo $filters_notification['currentmonth']; ?> represent <?php echo $total_current_utility_revenue; ?>% of the total <strong>revenue</strong> . <?php echo $filters_notification['previousmonth']; ?> represented <?php echo $total_previous_utility_revenue; ?>%</span></li><?php 
                        }
                    }

                    // Total utilities with room nights
                    if ($utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['total_room_night'] > 0 && $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['total_room_night'] > 0) {
                        $current_utility_by_room_night  = $utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['total_utility'] / $utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['total_room_night'];
                        $previous_utility_by_room_night = $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['total_utility'] / $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['total_room_night'];

                        if ($previous_utility_by_room_night > 0) {
                            $total_utility_room_night_difference = $current_utility_by_room_night - $previous_utility_by_room_night;
                            $total_utility_room_night_percentage = $total_utility_room_night_difference * 100 / $previous_utility_by_room_night;
                        } else {
                            $total_utility_room_night_percentage = 100;
                        }

                        $total_utility_room_night_percentage = round($total_utility_room_night_percentage, 2);
                    }
                        
                    if ($current_utility_by_room_night > 0 && $total_utility_room_night_percentage > 10) {?>
                        <li class="clearfix total-utility-room-night-notification-div"><span>Your <strong>Utilities cost per room nights</strong> of <?php echo $filters_notification['currentmonth']; ?> has increased by <?php echo $total_utility_room_night_percentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li>
                    <?php }

                    if ($sitescustomnotification){
                        foreach ($sitescustomnotification as $key => $value) {
                        ?>
                        <li class="clearfix sitecustomnotification"><span><?php echo $value['notification']; ?></span><a><?php echo date("F", strtotime($value['date'])); ?></a></li>
                        <?php
                        }
                    }

                    if ($notifications) {
                        for ($i = 0; $i < count($notifications); $i++) {
                            echo '<li class="clearfix"><span>' . $notifications[$i]['field_label'] . ' is missing</span><a>' . date("F", mktime(0, 0, 0, $notifications[$i]['month'], 10)) . ' - ' . $notifications[$i]['year'] . '</a></li>';
                        }
                    }
                    ?>
                    </ul><?php 
                } ?>
            </div>
        </article>
    </div>
    <div class="col-sm-7">
        <div class="row block-listing">
            <div class="row">
                <div class="col-lg-4" style="padding-right:  10px;padding-left: 10px;">
                    <article class="card yellow">
                        <div class="article-content clearfix">
                            <div class="article-thumb">
                                <img src="images/energy.png" alt="thumb" class="media-object">
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports"><strong class="common-boxtitle yellow">Utilities Cost</strong></a>
                            <div class="clearfix indicator-container">
                                <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($total_utility_cost_currentMonth)); ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 2, 1, date('Y'))); ?> </div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($total_utility_cost_lastMonth)); ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="indicator-left-content"><?php echo date('F', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?> <?php echo date('Y', strtotime('-1 year -1 month')); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($total_utility_cost_sameMonth_lastYear)); ?></span>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-lg-4" style="padding-right:  10px;padding-left: 10px;">
                    <article class="card blue">
                        <div class="article-content clearfix">
                            <div class="article-thumb">
                                <img src="images/thumb03.png" alt="thumb" class="media-object">
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/roomnight"><strong class="common-boxtitle blue">Utilities Cost/Room Night</strong></a>
                            <div class="clearfix indicator-container">
                                <div class="labelDiv"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($currentMonth_cost_roomNight)); ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="labelDiv"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 2, 1, date('Y'))); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($lastMonth_cost_roomNight)); ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="labelDiv"><?php echo date('F', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?> <?php echo date('Y', strtotime('-1 year -1 month')); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($sameMonth_lastYear_cost_roomNight)); ?></span>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-lg-4" style="padding-right:  10px;padding-left: 10px;">
                    <article class="card cyan">
                        <div class="article-content clearfix">
                            <div class="article-thumb">
                                <img src="images/thumb01.png" alt="thumb" class="media-object">
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/carbon"><strong class="common-boxtitle cyan">Carbon FootPrint</strong></a>

                            <div>Unit: KgCO<sub>2</sub>e</div>
                            <div class="indicator-container">
                                <div class="labelDiv indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>
                                <span><?php echo isset($carbon_footprint_currentMonth) ? number_format(round($carbon_footprint_currentMonth)) : 0; ?></span>
                            </div>
                            <div class="indicator-container">
                                <div class="labelDiv indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y') - 1)); ?></div>
                               <span><?php echo isset($carbon_footprint_SameMonthPreviousYear) ? number_format(round($carbon_footprint_SameMonthPreviousYear)) : 0; ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="labelDiv indicator-left-content">Year To Date</div>
                                <span><?php echo isset($ytd_carbon_footprint_new) ? number_format(round($ytd_carbon_footprint_new)) : 0; ?></span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4" style="padding-right:  10px;padding-left: 10px;">
                    <article class="card green">
                        <div class="article-content clearfix">
                            <div class="article-thumb">
                                <img src="images/budget.png" alt="thumb" class="media-object">
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/budget"><strong class="common-boxtitle green">Utilities Cost v/s Budget</strong></a>
                            <div class="clearfix indicator-container">
                                <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>
                                <?php
                                    $image = $variation < 0 ? 'upArrow.png' : 'downArrow.png';
                                    $color = $variation < 0 ? '#dc2727' : '#2ecc71';

                                    $image_ytd = $variation_ytd < 0 ? 'upArrow.png' : 'downArrow.png';
                                    $color_ytd = $variation_ytd < 0 ? '#dc2727' : '#2ecc71';
                                    ?>
                                <span style="color:<?php echo $color; ?>"><?php echo round(abs($variationPercentage)); ?>% <img src="images/<?php echo $image; ?>"></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="indicator-left-content">Year To Date</div>
                                <span style="color:<?php echo $color_ytd; ?>"><?php echo round(abs($variationPercentage_ytd)); ?>% <img src="images/<?php echo $image_ytd; ?>"></span>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-lg-8" style="padding-right:  10px;padding-left: 10px;">
                    <article class="card dark-green">
                        <div class="ema-article-content article-content clearfix">
                            <div class="article-thumb">
                                <img src="images/project.png" alt="thumb" class="media-object">
                                <span>Total: <?php echo $actionPlanCounts; ?></span>
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/actionplans/<?php echo $site_detials['id']; ?>"><strong class="common-boxtitle dark-green">Efficiency Measures Actions</strong></a>
                            <div class="dashboard-progress-container clearfix">
                                <div class="row"><p class="col-lg-10 title-bar">Awaiting Approval  -  <?php echo $actionPlanAwaitingApprovalCounts; ?></p></div>
                                <div class="progress">
                                  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $actionPlanAwaitingApprovalPercentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $actionPlanAwaitingApprovalPercentage; ?>%">
                                    <span class="sr-only"></span>
                                  </div>
                                </div>
                                <div class="row"><p class="col-lg-10 title-bar">On Hold  -  <?php echo $actionPlanOnholdCounts; ?></p></div>
                                <div class="progress">
                                  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $actionPlanOnholdPercentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $actionPlanOnholdPercentage; ?>%">
                                    <span class="sr-only"></span>
                                  </div>
                                </div>
                                <div class="row"><p class="col-lg-10 title-bar">In Progress  -  <?php echo $actionPlanInProgressCounts; ?></p></div>
                                <div class="progress">
                                  <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?php echo $actionPlanInProgressPercentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $actionPlanInProgressPercentage; ?>%">
                                    <span class="sr-only"></span>
                                  </div>
                                </div>
                                <div class="row"><p class="col-lg-10 title-bar">Completed  -  <?php echo $actionPlanCompleteCounts; ?></p>
                                </div>
                                <div class="progress">
                                  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $actionPlanCompletePercentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $actionPlanCompletePercentage; ?>%">
                                    <span class="sr-only"></span>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Config array for chart
$montharray     = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

$current_month = date('m');
$current_year  = date('Y');

$utility_types = array(
    'electricity'      => 'Electricity',
    'fuel'             => 'Fuel',
    'lpg'              => 'LPG',
    'natural_gas'      => 'Natural Gas',
    'heating_district' => 'Heating District',
    'cooling_district' => 'Cooling District',
    'water'            => 'Water',
);

// Prepare array for loop
$startmonthsarray = array();
$endmonthsarray   = array();

if ($filters["start_year"] == $filters["end_year"]) {
    // If start and end year is same
    for ($i = $filters['start_month']; $i <= $filters["end_month"]; $i++) {
        $startmonthsarray[] = $i;
    }

    $resultkeys                         = array();
    $resultkeys[$filters["start_year"]] = $startmonthsarray;
} else {
    // If start and end year is not same
    for ($i = $filters['start_month']; $i <= 12; $i++) {
        $startmonthsarray[] = $i;
    }

    for ($i = 1; $i <= $filters['end_month']; $i++) {
        $endmonthsarray[] = $i;
    }
    $resultkeys                         = array();
    $resultkeys[$filters["start_year"]] = $startmonthsarray;
    $resultkeys[$filters["end_year"]]   = $endmonthsarray;
}
?>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/gstatic_loader.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>
<script type="text/javascript">
<?php if (!empty($reportdata)) {
    ?>
        google.load("visualization", "1", {packages: ["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', '<?php echo lang("month"); ?>');
            dataTable.addColumn('number', '<?php echo lang("previous-year"); ?>');
            dataTable.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
            dataTable.addColumn('number', '<?php echo lang("current-year"); ?>');
            dataTable.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
            dataTable.addColumn('number', '<?php echo lang("budget"); ?>');
            dataTable.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
        <?php
            $total_sum_current = 0;
            $total_sum_pre     = 0;
            $total_sum_budget  = 0;
            $total_months      = 0;
            $total_months_pre  = 0;

            if ($utility_chart_year == date('Y')) {
                $YTD_total_months = $this->_ci->config->config['YTD_month_count'];
            } else {
                $YTD_total_months = 12;
            }

            foreach ($resultkeys as $year => $value) {
                foreach ($value as $key1 => $month) {
                    $monthdata    = $montharray[$month] . ' ' . $year;
                    $previousdata = (!empty($reportdata[$month][$year - 1][$filters['utility_type']])) ? $reportdata[$month][$year - 1][$filters['utility_type']] : 0;
                    $currentdata  = (!empty($reportdata[$month][$year][$filters['utility_type']])) ? $reportdata[$month][$year][$filters['utility_type']] : 0;
                    $budgetdata   = (!empty($reportdata[$month][$year][$filters['utility_type'] . '_budget'])) ? $reportdata[$month][$year][$filters['utility_type'] . '_budget'] : 0;

                    $previousdata = round($previousdata, 2);
                    $currentdata  = round($currentdata, 2);
                    $budgetdata   = round($budgetdata, 2);

                    // Last year Variant
                    $deference_value = $previousdata - $currentdata;
                    if ($currentdata > 0) {
                        $percentage = (($deference_value * 100) / $currentdata);
                        $percentage = round($percentage, 2);
                    } else {
                        $percentage = 100;
                    }

                    if ($percentage > 0) {
                        $pclass = 'nagetive';
                        $parrow = '<span class=\"fa fa-angle-double-down\"></span>';
                    } else if ($percentage < 0) {
                        $pclass = 'positive';
                        $parrow = '<span class=\"fa fa-angle-double-up\"></span>';
                    } else {
                        $pclass = '';
                        $parrow = '';
                    }

                    // Budget Variant
                    $deference_value = $budgetdata - $currentdata;
                    if ($currentdata > 0) {
                        $budget_percentage = (($deference_value * 100) / $currentdata);
                        $budget_percentage = round($budget_percentage, 2);
                    } else {
                        $budget_percentage = 100;
                    }

                    if ($budget_percentage > 0) {
                        $budget_pclass = 'nagetive';
                        $budget_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
                    } else if ($budget_percentage < 0) {
                        $budget_pclass = 'positive';
                        $budget_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
                    } else {
                        $budget_pclass = '';
                        $budget_parrow = '';
                    }

                    // Remove - sign
                    $percentage        = abs($percentage);
                    $budget_percentage = abs($budget_percentage);

                    $previousdata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $fullmontharray[$month] . ' ' . ($year - 1) . '</strong></div><div>' . lang('actual') . ': ' . $currentdata . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $budget_pclass . '\">' . $budget_parrow . $budget_percentage . '%</span></div><div>' . lang('vs-last-year') . ' : <span class=\"variant-' . $pclass . '\">' . $parrow . $percentage . '%</span></div></div>';
                    $currentdata_tooltip  = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $fullmontharray[$month] . ' ' . $year . '</strong></div><div>' . lang('actual') . ': ' . $currentdata . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $budget_pclass . '\">' . $budget_parrow . $budget_percentage . '%</span></div><div>' . lang('vs-last-year') . ' : <span class=\"variant-' . $pclass . '\">' . $parrow . $percentage . '%</span></div></div>';
                    $budgetdata_tooltip   = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $fullmontharray[$month] . ' ' . $year . '</strong></div><div>' . lang('budget') . ': ' . $budgetdata . '</div></div>';

                    if ($month <= $YTD_total_months) {
                        $total_sum_pre += $previousdata;
                    } /*else {
                    $previousdata = 0;
                    }*/

                    $total_months_pre++;
                    if ($filters['CURRENT_YEAR_MAX_MONTH_ID'] >= $month) {
                        $total_sum_current += $currentdata;
                        $total_sum_budget += $budgetdata;
                        $total_months++;
                    }
                    ?>
                            dataTable.addRow(["<?php echo $monthdata; ?>", <?php echo $previousdata; ?>, "<?php echo $previousdata_tooltip; ?>", <?php echo $currentdata; ?>, "<?php echo $currentdata_tooltip; ?>", <?php echo $budgetdata; ?>, "<?php echo $budgetdata_tooltip; ?>"]);
                    <?php
                }
            }

            // Total months for average is current month
            $currentAvgData  = ($total_sum_current / $YTD_total_months);
            $previousAvgData = ($total_sum_pre / $YTD_total_months);
            $budgetAvgData   = ($total_sum_budget / $total_months);

            $currentAvgData  = round($currentAvgData, 2);
            $previousAvgData = round($previousAvgData, 2);
            $budgetAvgData   = round($budgetAvgData, 2);

            // Last year average Variant
            $deference_value = $previousAvgData - $currentAvgData;
            if ($currentAvgData > 0) {
                $percentage = (($deference_value * 100) / $currentAvgData);
                $percentage = round($percentage, 2);
            } else {
                $percentage = 100;
            }

            if ($percentage > 0) {
                $pclass = 'nagetive';
                $parrow = '<span class=\"fa fa-angle-double-down\"></span>';
            } else if ($percentage < 0) {
                $pclass = 'positive';
                $parrow = '<span class=\"fa fa-angle-double-up\"></span>';
            } else {
                $pclass = '';
                $parrow = '';
            }

            // Budget Average Variant
            $deference_value = $budgetAvgData - $currentAvgData;
            if ($currentAvgData > 0) {
                $budget_percentage = (($deference_value * 100) / $currentAvgData);
                $budget_percentage = round($budget_percentage, 2);
            } else {
                $budget_percentage = 100;
            }

            if ($budget_percentage > 0) {
                $budget_pclass = 'nagetive';
                $budget_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
            } else if ($budget_percentage < 0) {
                $budget_pclass = 'positive';
                $budget_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
            } else {
                $budget_pclass = '';
                $budget_parrow = '';
            }

            // Remove - sign
            $percentage        = abs($percentage);
            $budget_percentage = abs($budget_percentage);

            $previousAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . lang('average') . '</strong></div><div>' . lang('actual') . ': ' . $currentAvgData . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $budget_pclass . '\">' . $budget_parrow . $budget_percentage . '%</span></div><div>' . lang('vs-last-year') . ' : <span class=\"variant-' . $pclass . '\">' . $parrow . $percentage . '%</span></div></div>';
            $currentAvgData_tooltip  = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . lang('average') . '</strong></div><div>' . lang('actual') . ': ' . $currentAvgData . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $budget_pclass . '\">' . $budget_parrow . $budget_percentage . '%</span></div><div>' . lang('vs-last-year') . ' : <span class=\"variant-' . $pclass . '\">' . $parrow . $percentage . '%</span></div></div>';
            $budgetAvgData_tooltip   = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . lang('average') . '</strong></div><br/>' . lang('budget') . ': ' . $budgetAvgData . '</div>';
        ?>
            dataTable.addRow(["<?php echo lang('average'); ?>", <?php echo $previousAvgData; ?>, "<?php echo $previousAvgData_tooltip; ?>", <?php echo $currentAvgData; ?>, "<?php echo $currentAvgData_tooltip; ?>", <?php echo $budgetAvgData; ?>, "<?php echo $budgetAvgData_tooltip; ?>"]);

            var options = {
                title: '<?php echo lang("report-title"); ?>',
                titleTextStyle: {
                    fontName: 'Arial',
                    fontSize: 28
                },
                vAxis: {title: '<?php echo lang("vaxis-title"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}},
                hAxis: {title: '<?php echo lang("haxis-title"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}},
                seriesType: 'bars',
                series: {2: {type: 'line'}},
                animation: {duration: 500, startup: true},
                tooltip: {isHtml: true},
                legend: {'position': 'bottom'}
            };

            var chart = new google.visualization.ComboChart(document.getElementById('utility_chart'));
			google.visualization.events.addListener(chart, 'ready', function () {
				if($('.saveImgUrl').length == 0){
					var download = document.createElement('a');
					download.href = chart.getImageURI();
                    download.download = "utility_chart.png";
					download.onclick = saveAsPng;
					download.text = 'Save as png';
					download.className  = 'btn btn-secondary btn-submit saveImgUrl';
					$('#saveImage').append(download);
				}
			});
            chart.draw(dataTable, options);
        }

        $(window).resize(function() {
            drawChart();
        });
<?php }?>
</script>

<div class="graph-outer">
    <article class="card">
        <div class="article-header">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <i><img src="images/graph.png" alt="Graph"></i>
                    <?php echo lang('total-energy-usage-label'); ?> <?php echo $montharray[$filters['start_month']] . ' ' . $filters['start_year']; ?> <?php echo lang('to-label'); ?> <?php echo $montharray[$filters['end_month']] . ' ' . $filters['end_year']; ?> <?php echo lang('kwh-label'); ?>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12 pull-right">
                    <form name="form-utility" method="POST" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'dashboard' ?>">
                    <label class="control-label col-sm-4">
                        Choose year :
                    </label>
                    <div class="form-dropdown col-sm-8">
                        <select name="utility_chart_year" data-type="custom-dropdown" id="utility_select" onchange="this.form.submit()">
                            <?php
                            for ($i = date('Y') - 3; $i <= date('Y'); $i++) {?>
                                <option value="<?php echo $i; ?>" <?php echo ($i == $utility_chart_year) ? "selected" : ""; ?>
                                    ><?php echo $i; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="article-content">
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="year">
                    <div class="row">
                        <div class="col-sm-12">
							<div id="saveImage"></div>
						</div>
                        <div class="col-sm-12">
                            <div style="width: 100%; height: 500px;" id="utility_chart">
                                <?php if (empty($reportdata)) {?>
                                    <div class="table-responsive">
                                        <table class="table table-striped" >
                                            <tr>
                                                <td><?php echo lang('no-records') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>