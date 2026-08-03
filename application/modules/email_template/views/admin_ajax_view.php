<div id="one" class="grid-data">
    <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
        <tbody bgcolor="#fff">
            <tr>
                <th><?php echo lang('view-email-template'); ?> - <?php echo $language_name; ?></th>
            </tr>
            <tr>
                <td class="add-user-form-box">                    
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="100%" valign="top">
                                <?php
                                if(count($template) > 0)
                                {
                                ?>
                                <table width="100%" cellpadding="5" cellspacing="1" border="0">
                                    <tr>
                                        <td width="150px" align="left"><?php echo lang('template-name'); ?>:</td>
                                        <td>
                                            <?php
                                                if(isset($template[0]['c']['template_name'])) 
                                                    echo $template[0]['c']['template_name'];
                                            ?>
                                        </td>
                                    </tr>                              
                                    <tr>
                                        <td align="left" valign="top"><?php echo lang('template-body'); ?>:</td>
                                        <td class="view_desc">
                                            <?php
                                                if(isset($template[0]['c']['template_body'])) 
                                                    echo nl2br ($template[0]['c']['template_body']);
                                            ?>
                                        </td>
                                    </tr>
                                 
                                    <tr>
                                        <td align="left"><span class="star">*&nbsp;</span><?php echo lang('status'); ?>:</td>
                                        <td>
                                            <?php
                                                if(isset($template[0]['c']['status']) && $template[0]['c']['status'] == '1') 
                                                    echo lang('active');
                                                if(isset($template[0]['c']['status']) && $template[0]['c']['status'] == '0') 
                                                    echo lang('inactive');
                                            ?>
                                        </td>
                                    </tr>
                                </table> 
                                <?php
                                }
                                else
                                {
                                ?>
                                <table>
                                    <tr>
                                        <td align="left"><?php echo lang('no-email-translation');?></td>
                                    </tr>
                                </table>
                                <?
                                }
                                ?>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </tbody>
    </table>    
     
</div>