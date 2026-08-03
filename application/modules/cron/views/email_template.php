<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif;  color: #414141;  font-size: 15px;  line-height: 22px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"  style="font-family: Arial, Helvetica, sans-serif;  color: #414141;  font-size: 15px;  line-height: 22px;" >
        <tbody>
            <tr>
                <td class="border_cs" valign="top" style="border: 5px solid #000000;  padding: 20px 28px;  background-color: #FFF;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                        <tr style="line-height: 25px;  font-size: 14px;"><?php echo $html; ?></tr>
                        <tr>
                            <td><hr></td>
                        </tr>
                        <?php
                        if (isset($more_info) && $more_info == true) {?>
                            <tr>
                                <td align="center" colspan="2" valign="center">
                                    <span style="font-size: 10pt;">If you want more information, log in to <strong><a href="<?php echo base_url(); ?>"> HEP </a></strong></span>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td align="center" colspan="2" valign="center">
                                <span style="font-size: 10pt;">HEP - Hotel Energy Portal | Copyright &copy; <?php echo date('Y'); ?> EEG - Energy Efficiency Group. All Rights Reserved</span>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
        </table>
    </body>
</html>