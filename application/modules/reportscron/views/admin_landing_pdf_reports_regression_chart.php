<html style="text-align: left;">

<body width="100%">
    <?php if($utility_regression_electricity_Img) { ?>
        <div style="border:2px solid  #f69546;padding:10px;">
            <h4 style="text-align: center; font-size: 16; color:#0400FF"><?php echo $regression['utility_array']['electricity']['Label'];?></h4>
            <table width="100%" cellpadding="4" cellspacing="4">
                <tr>
                    <td width="100%">
                        <img src="<?php echo $utility_regression_electricity_Img; ?>" />
                    </td>
                </tr>
                </td>
                </tr>
            </table>
        </div>
    <?php }
    if($utility_regression_lpg_Img) { ?>
        <div style="border:2px solid  #f69546;padding:10px;">
            <h4 style="text-align: center; font-size: 16; color:#0400FF"><?php echo $regression['utility_array']['lpg']['Label'];?></h4>
            <table width="100%" cellpadding="4" cellspacing="4">
                <tr>
                    <td width="100%">
                        <img src="<?php echo $utility_regression_lpg_Img; ?>" />
                    </td>
                </tr>
                </td>
                </tr>
            </table>
        </div>
    <?php } 
    if($utility_regression_fuel_oil_Img) { ?>
        <div style="border:2px solid  #f69546;padding:10px;">
            <h4 style="text-align: center; font-size: 16; color:#0400FF"><?php echo $regression['utility_array']['fuel_oil']['Label'];?></h4>
            <table width="100%" cellpadding="4" cellspacing="4">
                <tr>
                    <td width="100%">
                        <img src="<?php echo $utility_regression_fuel_oil_Img; ?>" />
                    </td>
                </tr>
                </td>
                </tr>
            </table>
        </div>
    <?php }  
    if($utility_regression_natural_gas_Img) { ?>
        <div style="border:2px solid  #f69546;padding:10px;">
            <h4 style="text-align: center; font-size: 16; color:#0400FF"><?php echo $regression['utility_array']['natural_gas']['Label'];?></h4>
            <table width="100%" cellpadding="4" cellspacing="4">
                <tr>
                    <td width="100%">
                        <img src="<?php echo $utility_regression_natural_gas_Img; ?>" />
                    </td>
                </tr>
                </td>
                </tr>
            </table>
        </div>
    <?php }   
    if($utility_regression_district_cooling_Img) { ?>
        <div style="border:2px solid  #f69546;padding:10px;">
            <h4 style="text-align: center; font-size: 16; color:#0400FF"><?php echo $regression['utility_array']['district_cooling']['Label'];?></h4>
            <table width="100%" cellpadding="4" cellspacing="4">
                <tr>
                    <td width="100%">
                        <img src="<?php echo $utility_regression_district_cooling_Img; ?>" />
                    </td>
                </tr>
                </td>
                </tr>
            </table>
        </div>
    <?php }  
    if($utility_regression_district_heating_Img) { ?>
        <div style="border:2px solid  #f69546;padding:10px;">
            <h4 style="text-align: center; font-size: 16; color:#0400FF"><?php echo $regression['utility_array']['district_heating']['Label'];?></h4>
            <table width="100%" cellpadding="4" cellspacing="4">
                <tr>
                    <td width="100%">
                        <img src="<?php echo $utility_regression_district_heating_Img; ?>" />
                    </td>
                </tr>
                </td>
                </tr>
            </table>
        </div>
    <?php } ?>

</body>

</html>