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

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Low</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Lower Quartile</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Mean</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Median</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>Upper Quartile</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>High</strong></td>

                                <td width="8%"  style="background-color:#d8e1f2;" align="center"><strong>SD</strong></td>

                                <td width="14%" style="background-color:#d8e1f2;" align="center"><strong><?php echo $site_detail['site_location_name']; ?></strong></td>

                            </tr>

                            <tr>

                                <td align="left">MEASURE 1: Hotel Carbon Footprint Per Room (kgCO2e)</td>

                                <td align="center"><?php echo $measure_readings[2]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[2]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[2]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[2]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[2]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[2]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[2]['sd']; ?></td>

                                <td align="center" style="color: #FF0000; " ><?php echo ($measures['HotelCarbonFootprintPerRoom'][date("Y")] + $measures['HotelCarbonFootprintPerRoom'][date("Y") - 1]); ?></td>

                            </tr>

                            <tr>

                                <td align="left">MEASURE 2: Hotel Carbon Footprint Per Occupied Room (kgCO2e)</td>

                                <td align="center"><?php echo $measure_readings[3]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[3]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[3]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[3]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[3]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[3]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[3]['sd']; ?></td>

                                <td align="center" style="color: #FF0000; " ><?php echo ($measures['HotelCarbonFootprintPerOccupiedRoom'][date("Y")] + $measures['HotelCarbonFootprintPerOccupiedRoom'][date("Y") - 1]); ?></td>

                            </tr>

                            <tr>

                                <td align="left">MEASURE 3: Hotel Carbon Footprint Per Square Meter (kgCO2e)</td>

                                <td align="center"><?php echo $measure_readings[4]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[4]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[4]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[4]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[4]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[4]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[4]['sd']; ?></td>

                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelCarbonFootprintPerSquareMeter'][date("Y")] + $measures['HotelCarbonFootprintPerSquareMeter'][date("Y") - 1]; ?></td>

                            </tr>

                            <tr>
                                <td align="left">MEASURE 4: Hotel Energy Usage Per Occupied Room (<?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity');?>)</td>
                                <td align="center"><?php echo $measure_readings[5]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[5]['sd']; ?></td>

                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelEnergyUsagePerOccupiedRoom'][date("Y")] + $measures['HotelEnergyUsagePerOccupiedRoom'][date("Y") - 1]; ?></td>

                            </tr>

                            <tr>
                                <td align="left">MEASURE 5: Hotel Energy Usage Per Square Meter (<?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity');?>)</td>
                                <td align="center"><?php echo $measure_readings[6]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[6]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[6]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[6]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[6]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[6]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[6]['sd']; ?></td>

                                <td align="center" style="color: #FF0000; " ><?php echo $measures['HotelEnergyUsagePerSquareMeter'][date("Y")] + $measures['HotelEnergyUsagePerSquareMeter'][date("Y") - 1]; ?></td>

                            </tr>

                            <tr>
                                <td align="left">MEASURE 6: Hotel Water Usage Per Occupied Room (<?php echo GetSiteUtilityUnitName($utility['site_id'],'water');?>)</td>
                                <td align="center"><?php echo $measure_readings[7]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[7]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[7]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[7]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[7]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[7]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[7]['sd']; ?></td>

                                <td align="center" style="color: #FF0000; " ><?php echo ($measures['HotelWaterUsagePerOccupiedRoom'][date("Y")] + $measures['HotelWaterUsagePerOccupiedRoom'][date("Y") - 1])*1000.00;  ?></td>

                            </tr>

                            <tr>
                                <td align="left">MEASURE 7: Hotel Water Usage Per Square Meter (<?php echo GetSiteUtilityUnitName($utility['site_id'],'water');?>)</td>
                                <td align="center"><?php echo $measure_readings[8]['low']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['lower_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['mean']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['median']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['upper_quartile']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['high']; ?></td>

                                <td align="center"><?php echo $measure_readings[8]['sd']; ?></td>

                                <td align="center" style="color: #FF0000; " ><?php echo ($measures['HotelWaterUsagePerSquareMeter'][date("Y")] + $measures['HotelWaterUsagePerSquareMeter'][date("Y") - 1])*1000.00;  ?></td>

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
