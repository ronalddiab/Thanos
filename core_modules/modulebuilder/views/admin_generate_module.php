<div class="main-container">
    <?php echo form_open_multipart(get_current_section($this).'/modulebuilder/generate_module', array('id' => 'saveform', 'name' => 'saveform')); ?>
    <div class="grid-data">
        <?php echo $this->theme->message(); ?>  
        <!--        <div class="add-new">
        <?php echo anchor(site_url() . 'admin/modulebuilder', lang('view-all-module'), 'title="View All Modules" style="text-align:center;width:100%;"'); ?>
                </div>-->
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th><?php echo lang('generate-module') ?><span style="float: right;"><?php echo lang('notice');?> : [ <?php echo lang('id-field-notice'); ?> ]</span></th>
                </tr>
                <tr>
                    <td class="add-user-form-box">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="100%" valign="top">
                                    <table width="100%" cellpadding="5" cellspacing="1" border="0">
                                        <?php
                                        $field_number_data = array(
                                            '' => 'Select',
                                            '1' => '1',
                                            '2' => '2',
                                            '3' => '3',
                                            '5' => '5',
                                            '6' => '6',
                                            '8' => '8',
                                            '10' => '10',
                                            '15' => '15',
                                            '20' => '20'
                                        );
                                        $cancel_button = array(
                                            'content' => lang('change'),
                                            'title' => lang('change'),
                                            'class' => 'inputbutton',
                                            'onclick' => "hide_dropdown()",
                                        );
                                        ?>
                                        <tr class="option-dropdown">
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('select-fields'), 'field_number'); ?>:</td>
                                            <td>
                                                <?php
                                                echo form_dropdown('count', $field_number_data, ((isset($field_number)) ? $field_number : ''), 'id="field_number"')." ".form_button($cancel_button);
                                                ?>
                                                <span class="warning-msg"><?php echo form_error('field_type'); ?></span>
                                            </td>
                                        </tr>
                                        <?php
                                        $field_text_data = array(
                                            'name' => 'count',
                                            'id' => 'count',
                                            'value' => set_value('count', ((isset($count)) ? $count : '')),
                                            'style' => 'width:198px;',
                                            'class' => 'validate[required]'
                                        );
                                        $submit_button = array(
                                            'name' => 'mysubmit',
                                            'id' => 'mysubmit',
                                            'value' => lang('btn-save'),
                                            'title' => lang('btn-save'),
                                            'class' => 'inputbutton',
                                        );
                                        $cancel_button = array(
                                            'content' => lang('change'),
                                            'title' => lang('change'),
                                            'class' => 'inputbutton',
                                            'onclick' => "hide_text()",
                                        );
                                        ?>
                                        <tr class="option-text">
                                            <td align="left"><span class="star">*&nbsp;</span><?php echo form_label(lang('select-fields'), 'count'); ?>:<?php echo form_input($field_text_data)."&nbsp;"; ?><?php echo form_submit($submit_button)."  " . form_button($cancel_button); ?></td>
                                            <td align="left"><br/><span class="warning-msg"><?php echo form_error('count'); ?></span></td>
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
    echo form_close();
    ?>
</div>
<script type="text/javascript">    
    $(document).ready(function(){        
        $('.option-text').hide();
        jQuery("#saveform").validationEngine(
        {promptPosition : '<?php echo VALIDATION_ERROR_POSITION; ?>',validationEventTrigger: "submit"}
    );
        
    });   
    
    $('#field_number').change(function() {
        window.location = "<?php echo base_url(); ?><?php echo get_current_section($this); ?>/modulebuilder/add/"+$(this).val();
    });
    
    function hide_text()
    {
        $('.option-text').hide();
        $('.option-dropdown').show();
    }
    function hide_dropdown()
    {
        $('.option-dropdown').hide();
        $('.option-text').show();        
    }
</script>


