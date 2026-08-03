<?php echo add_css('validationEngine.jquery'); ?>
<?php echo add_js(array('jquery-1.9.1.min', 'jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>

<div class="main-container">
    <div id="moduleMiddle" class="grid-data">
        <div class="add-new">
            <?php echo anchor(get_current_section($this).'/languages', lang('languages-view'), 'title="View All Languages" style="text-align:center;width:100%;"'); ?>
        </div>
        <?php echo form_open(get_current_section($this)."/languages/save", array('id' => 'saveform', 'name' => 'saveform')); ?>
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th><?php echo lang('view-data'); ?></th>
                </tr>
                <tr>
                    <td class="add-user-form-box">

                        <table width="300px" cellpadding="5" cellspacing="1" border="0">
                                        <tr>
                                           <td align="left"><?php echo lang('languages-name'); ?>:</td>
                                           <td align="left"><?php echo $data[0]['l']['language_name']?></td>
                                        </tr>
                                        <tr>
                                           <td align="left"><?php echo lang('languages-code'); ?>:</td>
                                           <td align="left"><?php echo $data[0]['l']['language_code']?></td>
                                        </tr>
                                        <tr>
                                           <td align="left"><?php echo lang('languages-direction'); ?>:</td>
                                           <td align="left"><?php if(isset($data[0]['l']['direction']) && $data[0]['l']['direction']=="ltr"){ echo "Left"; } else { echo "Right"; } ?></td>
                                        </tr>
                                        <tr>
                                           <td align="left"><?php echo lang('languages-default'); ?>:</td>
                                           <td align="left"><?php if(isset($data[0]['l']['default'])){ echo ($data[0]['l']['default'] == 1)?'Yes':'No'; } ?></td>
                                        </tr>
                                        <tr>
                                           <td align="left"><?php echo lang('status'); ?>:</td>
                                           <td align="left"><?php if($data[0]['l']['status']=='1'){ echo lang('active'); }else{ echo lang('inactive'); }?></td>
                                        </tr>

                          </table>

                    </td>
                </tr>
            </tbody>
        </table>
        <div class="submit-btns clearfix">
            <input type="hidden" id="id" name="id" value ="<?php echo (isset($id)) ? $id : '0'; ?>" />

        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        jQuery("#saveform").validationEngine(
        {
            promptPosition : '<?php echo VALIDATION_ERROR_POSITION; ?>',
            validationEventTrigger: "submit",
            'custom_error_messages': {
                // Custom Error Messages for Validation Types
                'required': {
                    'message': "<?php echo lang('required'); ?>"
                }
            }
        }
    );

    });
</script>