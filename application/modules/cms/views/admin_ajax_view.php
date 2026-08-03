
<div id="one" class="grid-data">
    <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
        <tbody bgcolor="#fff">
            <tr>
                <th><?php echo lang('view-cms'); ?> - <?php echo $language_name; ?></th>
            </tr>
            <tr>
                <td class="add-user-form-box">                    
                    <table width="300px" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="100%" valign="top">
                                <?php  
                                if(count($cms) > 0)
                                {
                                ?>
                                <table width="100%" cellpadding="5" cellspacing="1" border="0">
                                    <tr>
                                        <td align="right"><?php echo lang('title'); ?>:</td>
                                        <td>
                                            <?php
                                            if(isset($cms['title']))
                                                echo $cms['title'];
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?php echo lang('slug_url'); ?>:</td>
                                        <td>
                                            <?php
                                            if(isset($cms['slug_url'])) 
                                                echo $cms['slug_url'];
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right" valign="top"><?php echo lang('description'); ?>:</td>
                                        <td class="view_desc">
                                            <?php
                                            if(isset($cms['description']))
                                                echo $cms['description'];
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><b><?php echo lang('meta_fields'); ?></b></td>
                                        <td>&nbsp; </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><span class="star"></span><?php echo lang('title'); ?>:</td>
                                        <td>
                                              <?php
                                              if(isset($cms['meta_title']))
                                                  echo $cms['meta_title'];
                                              ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><span class="star"></span><?php echo lang('keywords'); ?>:</td>
                                        <td>
                                            <?php
                                            if(isset($cms['meta_keywords']))
                                                echo $cms['meta_keywords'];
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right" valign="top"><span class="star"></span><?php echo lang('description'); ?>:</td>
                                        <td>
                                            <?php
                                            if(isset($cms['meta_description'])) 
                                                echo $cms['meta_description'];
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><span class="star">*&nbsp;</span><?php echo lang('status'); ?>:</td>
                                        <td>
                                            <?php
                                            if(isset($cms['status']) && $cms['status'] == '1') 
                                                echo lang('active');
                                            if(isset($cms['status']) && $cms['status'] == '0') 
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
                                        <td align="right"><?php echo lang('no-cms-translation');?></td>
                                    </tr>
                                </table>
                                <?php
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