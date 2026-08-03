<div class="main-container">
    <?php echo form_open_multipart(get_current_section($this).'/users/save', array('id' => 'saveform', 'name' => 'saveform')); ?>
    <div class="grid-data">
        <div class="add-new">
            <?php echo anchor(site_url().get_current_section($this).'/urls', lang('view-all-url'), 'title="View All URLs" style="text-align:center;width:100%;"'); ?>
        </div>
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th><?php echo lang('view-url-data') ?></th>
                </tr>
                <tr>
                    <td class="add-user-form-box">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="100%" valign="top">
                                    <table width="500px" cellpadding="5" cellspacing="1" border="0">
                                        <tr>
                                           <td align="left"><?php echo form_label(lang('slug-url'), 'slug_url'); ?>:</td>
                                           <td align="left"><?php echo $data[0]['u']['slug_url']?></td>
                                        </tr>
                                        <tr>
                                           <td align="left"><?php echo form_label(lang('module-name'), 'module_name'); ?>:</td>
                                           <td align="left"><?php echo $data[0]['u']['module_name']?></td>
                                        </tr>
                                        <tr>
                                           <td align="left"><?php echo form_label(lang('core-url'), 'core_url'); ?>:</td>
                                           <td align="left"><?php if(isset($data[0]['u']['core_url'])){ echo $data[0]['u']['core_url']; } ?></td>
                                        </tr>                                    
                                        <tr>
                                           <td align="left"><?php echo form_label(lang('status'), 'Status'); ?>:</td>
                                           <td align="left"><?php if($data[0]['u']['status']=='1'){ echo lang('active'); }else{ echo lang('inactive'); }?></td>
                                        </tr>
                                        <tr>
                                           <td align="left"><?php echo form_label(lang('order'), 'Order'); ?>:</td>
                                           <td align="left"><?php echo $data[0]['u']['order'];?></td>
                                        </tr>
                                    </table>        
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
<?php
echo form_hidden('id', (isset($id)) ? $id : '0' );
echo form_close();
?>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        jQuery("#saveform").validationEngine(
                {promptPosition: '<?php echo VALIDATION_ERROR_POSITION; ?>', validationEventTrigger: "submit"}
        );

    });
</script>



