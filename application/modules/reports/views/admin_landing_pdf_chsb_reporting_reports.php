<?php

if((!empty($measures)) && (!empty($measure_readings)))

{
    ?>

<html style="text-align: left;">
    <body width="100%">

        <div style="border:2px solid  #f69546;">

          

                <table width="100%" cellpadding="0" cellspacing="4"  >

                <tr>

                    <td>

                        <table>

                            <tr colspan="2" style="font-size:14px;">

                                <td align="center" >

                                    <strong><center>Cornell Hotel Sustainability Benchmarking Report</center> </strong>

                                </td>

                            </tr>

                        </table> 

                    </td>

                </tr>

                <tr>

                    <td>

                        <table width="100%" cellpadding="5" border="1" cellspacing="0">

                            <tr>

                                <td width="30%" style="background-color:#d8e1f2;" align="center"><strong>Measures</strong></td>

                                <!-- <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Count</strong></td> -->

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Low</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Lower Quartile</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Mean</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Median</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Upper Quartile</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>High</strong></td>

                                <!-- <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>SD</strong></td> -->

                                <td width="14%" style="background-color:#d8e1f2;" align="center"><strong><?php echo $site_detail['site_location_name']; ?></strong></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HCMIRoomsFootprintPerOccupiedRoom']['chsb_value'],
                                    $measure_readings[1]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[1]['measure_name']; ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[1]['count']; ?></td> -->

                                <td align="center"><?php echo number_format($measure_readings[1]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[1]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[1]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[1]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[1]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[1]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[1]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HCMIRoomsFootprintPerOccupiedRoom']['chsb_value']?number_format($measures['HCMIRoomsFootprintPerOccupiedRoom']['chsb_value']):'' ?></td>

                            </tr>
                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HotelCarbonFootprintPerRoom']['chsb_value'],
                                    $measure_readings[2]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[2]['measure_name']; ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[2]['count']; ?></td> -->

                                <td align="center"><?php echo number_format($measure_readings[2]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[2]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[2]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[2]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[2]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[2]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[2]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelCarbonFootprintPerRoom']['chsb_value']?number_format($measures['HotelCarbonFootprintPerRoom']['chsb_value']):''; ?></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HotelCarbonFootprintPerOccupiedRoom']['chsb_value'],
                                    $measure_readings[3]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[3]['measure_name']; ?></td>
                                <!-- <td align="center"><?php echo $measure_readings[3]['count']; ?></td> -->

                                <td align="center"><?php echo number_format($measure_readings[3]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[3]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[3]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[3]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[3]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[3]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[3]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelCarbonFootprintPerOccupiedRoom']['chsb_value']?number_format($measures['HotelCarbonFootprintPerOccupiedRoom']['chsb_value']):''; ?></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HotelCarbonFootprintPerSquareMeter']['chsb_value'],
                                    $measure_readings[4]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[4]['measure_name']; ?></td>
                                <!-- <td align="center"><?php echo $measure_readings[4]['count']; ?></td> -->

                                <td align="center"><?php echo number_format($measure_readings[4]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[4]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[4]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[4]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[4]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[4]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[4]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelCarbonFootprintPerSquareMeter']['chsb_value']?$measures['HotelCarbonFootprintPerSquareMeter']['chsb_value']?number_format($measures['HotelCarbonFootprintPerSquareMeter']['chsb_value']):'':''; ?></td>

                            </tr>

                            <!-- <tr>
                                <?php
                                // $chsb_color = getChsbColor(
                                //     $measures['HotelCarbonFootprintPerSquareFoot']['chsb_value'],
                                //     $measure_readings[5]
                                // );
                                ?>
                                <td align="left"><?php echo $measure_readings[5]['measure_name']; ?> </td>
                                <td align="center"><?php echo $measure_readings[5]['count']; ?></td>
                                <td align="center"><?php echo $measure_readings[5]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['sd']; ?></td>

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelCarbonFootprintPerSquareFoot']['chsb_value']?number_format($measures['HotelCarbonFootprintPerSquareFoot']['chsb_value']):''; ?></td>

                            </tr> -->

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HotelEnergyUsagePerOccupiedRoom']['chsb_value'],
                                    $measure_readings[6]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[6]['measure_name']; ?> </td>
                                <!-- <td align="center"><?php echo $measure_readings[6]['count']; ?></td> -->
                                <td align="center"><?php echo number_format($measure_readings[6]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[6]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[6]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[6]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[6]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[6]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[6]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelEnergyUsagePerOccupiedRoom']['chsb_value']?number_format($measures['HotelEnergyUsagePerOccupiedRoom']['chsb_value']):''; ?></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HotelEnergyUsagePerSquareMeter']['chsb_value'],
                                    $measure_readings[7]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[7]['measure_name']; ?> </td>
                                <!-- <td align="center"><?php echo $measure_readings[7]['count']; ?></td> -->
                                <td align="center"><?php echo number_format($measure_readings[7]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[7]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[7]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[7]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[7]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[7]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[7]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelEnergyUsagePerSquareMeter']['chsb_value']?$measures['HotelEnergyUsagePerSquareMeter']['chsb_value']?number_format($measures['HotelEnergyUsagePerSquareMeter']['chsb_value']):'':'';  ?></td>

                            </tr>

                            <!-- <tr>
                                <?php
                                // $chsb_color = getChsbColor(
                                //     $measures['HotelEnergyUsagePerSquareFoot']['chsb_value'],
                                //     $measure_readings[8]
                                // );
                                ?>
                                <td align="left"><?php echo $measure_readings[8]['measure_name']; ?></td>
                                <td align="center"><?php echo $measure_readings[8]['count']; ?></td>
                                <td align="center"><?php echo $measure_readings[8]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['sd']; ?></td>

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelEnergyUsagePerSquareFoot']['chsb_value']?number_format($measures['HotelEnergyUsagePerSquareFoot']['chsb_value']):'';  ?></td>

                            </tr> -->

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HCMIMeetingFootprintPerMeetingHour']['chsb_value'],
                                    $measure_readings[9]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[9]['measure_name']; ?></td>
                                <!-- <td align="center"><?php echo $measure_readings[9]['count']; ?></td> -->
                                <td align="center"><?php echo number_format($measure_readings[9]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[9]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[9]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[9]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[9]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[9]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[9]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HCMIMeetingFootprintPerMeetingHour']['chsb_value']?number_format($measures['HCMIMeetingFootprintPerMeetingHour']['chsb_value']):'';  ?></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HotelWaterUsagePerOccupiedRoom']['chsb_value'],
                                    $measure_readings[10]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[10]['measure_name']; ?></td>
                                <!-- <td align="center"><?php echo $measure_readings[10]['count']; ?></td> -->
                                <td align="center"><?php echo number_format($measure_readings[10]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[10]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[10]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[10]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[10]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[10]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[10]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelWaterUsagePerOccupiedRoom']['chsb_value']?number_format($measures['HotelWaterUsagePerOccupiedRoom']['chsb_value']):'';  ?></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HotelWaterUsagePerSquareMeter']['chsb_value'],
                                    $measure_readings[11]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[11]['measure_name']; ?></td>
                                <!-- <td align="center"><?php echo $measure_readings[11]['count']; ?></td> -->
                                <td align="center"><?php echo number_format($measure_readings[11]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[11]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[11]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[11]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[11]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[11]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[11]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HotelWaterUsagePerSquareMeter']['chsb_value']?number_format($measures['HotelWaterUsagePerSquareMeter']['chsb_value']):'';  ?></td>

                            </tr>

                            <!-- <tr>
                                <?php
                                // $chsb_color = getChsbColor(
                                //     $measures['HotelWaterUsagePerSquareFoot']['chsb_value'],
                                //     $measure_readings[12]
                                // );
                                ?>
                                <td align="left"><?php echo $measure_readings[12]['measure_name']; ?></td>
                                <td align="center"><?php echo $measure_readings[12]['count']; ?></td>
                                <td align="center"><?php echo $measure_readings[12]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[12]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[12]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[12]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[12]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[12]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[12]['sd']; ?></td>

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo ($measures['HotelWaterUsagePerSquareFoot']['chsb_value']?:'');  ?></td>

                            </tr> -->

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HWMIRoomsWaterUsagePerOccupiedRoom']['chsb_value'],
                                    $measure_readings[13]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[13]['measure_name']; ?></td>
                                <!-- <td align="center"><?php echo $measure_readings[13]['count']; ?></td> -->
                                <td align="center"><?php echo number_format($measure_readings[13]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[13]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[13]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[13]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[13]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[13]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[13]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo $measures['HWMIRoomsWaterUsagePerOccupiedRoom']['chsb_value']?number_format($measures['HWMIRoomsWaterUsagePerOccupiedRoom']['chsb_value']):'';  ?></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['HWMIMeetingWaterUsagePerMeetingHour']['chsb_value'],
                                    $measure_readings[14]
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[14]['measure_name']; ?></td>
                                <!-- <td align="center"><?php echo $measure_readings[14]['count']; ?></td> -->
                                <td align="center"><?php echo number_format($measure_readings[14]['low']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[14]['lower_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[14]['mean']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[14]['median']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[14]['upper_quartile']); ?></td>

                                <td align="center"><?php echo number_format($measure_readings[14]['high']); ?></td>

                                <!-- <td align="center"><?php echo $measure_readings[14]['sd']; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;"><?php echo $measures['HWMIMeetingWaterUsagePerMeetingHour']['chsb_value']?number_format($measures['HWMIMeetingWaterUsagePerMeetingHour']['chsb_value']):'';  ?></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['RenewableEnergyPercentage']['chsb_value'],
                                    $measure_readings[15],
                                    true
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[15]['measure_name']; ?> </td>
                                <!-- <td align="center"><?php echo $measure_readings[15]['count']; ?></td> -->
                                <td align="center"><?php echo number_format(($measure_readings[15]['low']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[15]['lower_quartile']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[15]['mean']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[15]['median']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[15]['upper_quartile']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[15]['high']*100)).'%'; ?></td>

                                <!-- <td align="center"><?php echo number_format(($measure_readings[15]['sd']*100)).'%'; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;" ><?php echo ($measures['RenewableEnergyPercentage']['chsb_value']*100).'%'; ?></td>

                            </tr>

                            <tr>
                                <?php
                                $chsb_color = getChsbColor(
                                    $measures['RenewableElectricityPercentage']['chsb_value'],
                                    $measure_readings[16],
                                    true
                                );
                                ?>
                                <td align="left"><?php echo $measure_readings[16]['measure_name']; ?> </td>
                                <!-- <td align="center"><?php echo $measure_readings[16]['count']; ?></td> -->
                                <td align="center"><?php echo number_format(($measure_readings[16]['low']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[16]['lower_quartile']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[16]['mean']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[16]['median']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[16]['upper_quartile']*100)).'%'; ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[16]['high']*100)).'%'; ?></td>

                                <!-- <td align="center"><?php echo number_format(($measure_readings[16]['sd']*100)).'%'; ?></td> -->

                                <td align="center" style="background-color: <?php echo $chsb_color['background']; ?>;
           color: <?php echo $chsb_color['font']; ?>;"><?php echo number_format(($measures['RenewableElectricityPercentage']['chsb_value']*100)).'%'; ?></td>

                            </tr>

                            <tr>
                                <td align="left"><?php echo $measure_readings[17]['measure_name']; ?> </td>
                                <!-- <td align="center"><?php echo $measure_readings[17]['count']; ?></td> -->
                                <td align="center"><?php echo number_format(($measure_readings[17]['low']),2); ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[17]['lower_quartile']),2); ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[17]['mean']),2); ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[17]['median']),2); ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[17]['upper_quartile']),2); ?></td>

                                <td align="center"><?php echo number_format(($measure_readings[17]['high']),2); ?></td>

                                <!-- <td align="center"><?php echo number_format(($measure_readings[17]['sd'])); ?></td> -->

                                <td align="center" ><?php echo $measures['ElectricityToNonElectricEnergy']['chsb_value']?number_format(($measures['ElectricityToNonElectricEnergy']['chsb_value']),2):''; ?></td>

                            </tr>

                        </table>

                    </td>

                </tr>

                

            </table>  

           

        </div>

        <div style="font-size: 11px; line-height: 20px;">

            <span style="background-color:#008000; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Better than every hotel in the peer group

            &nbsp;&nbsp;&nbsp;

            <span style="background-color:#90EE90; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Top quartile — among the best 25%

            <br>

            <span style="background-color:#C6E0B4; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Second quartile — better than the peer median

            &nbsp;&nbsp;&nbsp;

            <span style="background-color:#BDD7EE; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Exactly at the peer median

            <br>

            <span style="background-color:#FFFF00; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Third quartile — worse than the peer median

            &nbsp;&nbsp;&nbsp;

            <span style="background-color:#FFA500; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Bottom quartile — among the worst 25%

            <br>

            <span style="background-color:#FF0000; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Worse than every hotel in the peer group

            &nbsp;&nbsp;&nbsp;

            <span style="background-color:#D3D3D3; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Not enough data to calculate

        </div>

    </body>

</html>

<?php

}
