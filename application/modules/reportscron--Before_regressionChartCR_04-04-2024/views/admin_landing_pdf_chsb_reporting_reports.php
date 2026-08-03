 <?php

if((!empty($measures)) && (!empty($measure_readings)))
{   ?> 
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid  #f69546;">
                    
            <table width="100%" cellpadding="0" cellspacing="4"  >
                <tr>
                    <td>
                        <table>
                             <tr>
                                <td align="center" style="background-color:#d8e1f2;" >&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" style="background-color:#d8e1f2;" ><strong><center>Cornell Hotel Sustainability Benchmarking Report</center> </strong></td>
                            </tr>
                            <tr>
                                <td align="center" style="background-color:#d8e1f2;" >&nbsp;</td>
                            </tr>
                        </table> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" cellpadding="5" border="1" cellspacing="0">
                            <tr>
                                <td width="30%"><strong>Measures</strong></td>
                                <td width="8%"><strong>Low</strong></td>
                                <td width="8%"><strong>Lower Quartile</strong></td>
                                <td width="8%"><strong>Mean</strong></td>
                                <td width="8%"><strong>Median</strong></td>
                                <td width="8%"><strong>Upper Quartile</strong></td>
                                <td width="8%"><strong>High</strong></td>
                                <td width="8%"><strong>SD</strong></td>
                                <td width="14%"><strong><?php echo $site_detail['site_location_name']; ?></strong></td>
                            </tr>
                            <tr>
                                <td align="center">MEASURE 1: Hotel Carbon Footprint Per Room (kgCO2e)</td>
                                <td align="center"><?php echo $measure_readings[2]['low']; ?></td>
                                <td align="center"><?php echo $measure_readings[2]['lower_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[2]['mean']; ?></td>
                                <td align="center"><?php echo $measure_readings[2]['median']; ?></td>
                                <td align="center"><?php echo $measure_readings[2]['upper_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[2]['high']; ?></td>
                                <td align="center"><?php echo $measure_readings[2]['sd']; ?></td>
                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelCarbonFootprintPerRoom']; ?></td>
                            </tr>
                            <tr>
                                <td align="center">MEASURE 2: Hotel Carbon Footprint Per Occupied Room (kgCO2e)</td>
                                <td align="center"><?php echo $measure_readings[3]['low']; ?></td>
                                <td align="center"><?php echo $measure_readings[3]['lower_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[3]['mean']; ?></td>
                                <td align="center"><?php echo $measure_readings[3]['median']; ?></td>
                                <td align="center"><?php echo $measure_readings[3]['upper_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[3]['high']; ?></td>
                                <td align="center"><?php echo $measure_readings[3]['sd']; ?></td>
                                <td align="center"  style="color: #FF0000; " ><?php echo $measures['HotelCarbonFootprintPerOccupiedRoom']; ?></td>
                            </tr>
                            <tr>
                                <td align="center">MEASURE 3: Hotel Carbon Footprint Per Square Meter (kgCO2e)</td>
                                <td align="center"><?php echo $measure_readings[4]['low']; ?></td>
                                <td align="center"><?php echo $measure_readings[4]['lower_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[4]['mean']; ?></td>
                                <td align="center"><?php echo $measure_readings[4]['median']; ?></td>
                                <td align="center"><?php echo $measure_readings[4]['upper_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[4]['high']; ?></td>
                                <td align="center"><?php echo $measure_readings[4]['sd']; ?></td>
                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelCarbonFootprintPerSquareMeter']; ?></td>
                            </tr>
                            <tr>
                                <td align="center">MEASURE 4: Hotel Energy Usage Per Occupied Room (<?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity');?>)</td>
                                <td align="center"><?php echo $measure_readings[5]['low']; ?></td>
                                <td align="center"><?php echo $measure_readings[5]['lower_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[5]['mean']; ?></td>
                                <td align="center"><?php echo $measure_readings[5]['median']; ?></td>
                                <td align="center"><?php echo $measure_readings[5]['upper_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[5]['high']; ?></td>
                                <td align="center"><?php echo $measure_readings[5]['sd']; ?></td>
                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelEnergyUsagePerOccupiedRoom']; ?></td>
                            </tr>
                            <tr>
                                <td align="center">MEASURE 5: Hotel Energy Usage Per Square Meter (<?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity');?>)</td>
                                <td align="center"><?php echo $measure_readings[6]['low']; ?></td>
                                <td align="center"><?php echo $measure_readings[6]['lower_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[6]['mean']; ?></td>
                                <td align="center"><?php echo $measure_readings[6]['median']; ?></td>
                                <td align="center"><?php echo $measure_readings[6]['upper_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[6]['high']; ?></td>
                                <td align="center"><?php echo $measure_readings[6]['sd']; ?></td>
                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelEnergyUsagePerSquareMeter']; ?></td>
                            </tr>
                            <tr>
                                <td align="center">MEASURE 6: Hotel Water Usage Per Occupied Room (<?php echo GetSiteUtilityUnitName($utility['site_id'],'water');?>)</td>
                                <td align="center"><?php echo $measure_readings[7]['low']; ?></td>
                                <td align="center"><?php echo $measure_readings[7]['lower_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[7]['mean']; ?></td>
                                <td align="center"><?php echo $measure_readings[7]['median']; ?></td>
                                <td align="center"><?php echo $measure_readings[7]['upper_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[7]['high']; ?></td>
                                <td align="center"><?php echo $measure_readings[7]['sd']; ?></td>
                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelWaterUsagePerOccupiedRoom']; ?></td>
                            </tr>
                            <tr>
                                <td align="center">MEASURE 7: Hotel Water Usage Per Square Meter (<?php echo GetSiteUtilityUnitName($utility['site_id'],'water');?>)</td>
                                <td align="center"><?php echo $measure_readings[8]['low']; ?></td>
                                <td align="center"><?php echo $measure_readings[8]['lower_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[8]['mean']; ?></td>
                                <td align="center"><?php echo $measure_readings[8]['median']; ?></td>
                                <td align="center"><?php echo $measure_readings[8]['upper_quartile']; ?></td>
                                <td align="center"><?php echo $measure_readings[8]['high']; ?></td>
                                <td align="center"><?php echo $measure_readings[8]['sd']; ?></td>
                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelWaterUsagePerSquareMeter']; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
            </table>  

        </div>
    </body>
</html>

  <?php
}
