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
                    <th><?php echo lang('languages-add-edit'); ?></th>
                </tr>
                <tr>
                    <td class="add-user-form-box">

                        <table cellspacing="1" cellpadding="5" border="0" width="100%">
                            <tbody>

                                <tr>
                                    <td align="right"><span class="star">*&nbsp;</span><label for="language_name"><?php echo lang('languages-name'); ?></label>:</td>
                                    <td>
                                            <?php
                                            $inputData = array(
                                                'name' => 'language_name',
                                                'id' => 'language_name',
                                                'value' => set_value('language_name', htmlspecialchars_decode($language_name)),
                                                'maxlength' => '100',
                                                'style' => 'width:198px',
                                                'class' => "validate[required,custom[onlyLetterNumber]]"
                                            );

                                            if(isset($id) && $id != 0){
                                                $inputData['readonly'] = 'true';
                                            }
                                            echo form_input($inputData);
                                            ?>
                                            <span class="validation_error"><?php echo form_error('language_name'); ?></span>
                                    </td>

                                </tr>
                                <tr>
                                    <td align="right"><span class="star">*&nbsp;</span><label for="language_code"><?php echo lang('languages-code'); ?></label>:</td>
                                    <td>
                                        <?php
                                        $inputData = array(
                                            'name' => 'language_code',
                                            'id' => 'language_code',
                                            'value' => set_value('language_code', htmlspecialchars_decode($language_code)),
                                            'maxlength' => '100',
                                            'style' => 'width:198px',
                                            'class' => "validate[required]"
                                        );
                                        echo form_input($inputData);
                                        ?>
                                        <span class="validation_error"><?php echo form_error('language_code'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><span class="star">*&nbsp;</span><label for="direction"><?php echo lang('languages-direction'); ?></label>:</td>
                                    <td>
                                        <?php
                                        $dirlist = array('ltr' => 'Left', 'rtl' => 'Right');
                                        echo form_dropdown('direction', $dirlist, array($direction));
                                        ?>
                                        <span class="validation_error"><?php echo form_error('direction'); ?></span>
                                    </td>
                                </tr>
                                <tr style="display: none">
                                    <td align="right"><label for="default"><?php echo lang('languages-default'); ?></label>:</td>
                                    <td>
                                        <?php
                                        echo form_checkbox('default', '1', (isset($default) && $default == '1') ? TRUE : FALSE, $extra = '');
                                        ?>
                                        <span class="validation_error"><?php echo form_error('default'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><label for="status"><?php echo lang('languages-status'); ?></label>:</td>
                                    <td>
                                        <?php
                                        //var_dump($status);exit;

                                        $statuslist = array( '1' => lang('active'),'0' => lang('inactive'));
                                        echo form_dropdown('status', $statuslist, array($status), '');

                                        ?>
                                    </td>
                                </tr>
                        </table>

                    </td>
                </tr>
            </tbody>
        </table>
        <div class="submit-btns clearfix">
            <input type="hidden" id="id" name="id" value ="<?php echo (isset($id)) ? $id : '0'; ?>" />
            <?php
            $submit_button = array(
                'name' => 'saveLanguages',
                'id' => 'saveLanguages',
                'value' => 'Save',
                'title' => 'Save',
                'class' => 'inputbutton',
            );
            echo form_submit($submit_button);
            $siteurl = site_url(get_current_section($this).'/languages') . '/index';
            $reset_button = array(
                'name' => 'cancel',
                'value' => 'Cancel',
                'title' => 'Cancel',
                'class' => 'inputbutton',
                'onclick' => "location.href='" . $siteurl . "'"
            );
            echo "&nbsp;";
            echo form_reset($reset_button);
            ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        jQuery('#language_name').focus();

        jQuery("#saveform").validationEngine(
        {
            // promptPosition : '<?php echo VALIDATION_ERROR_POSITION; ?>',
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