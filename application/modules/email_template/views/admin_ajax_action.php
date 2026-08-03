<?php
$ckeditor = array(
    //ID of the textarea that will be replaced
    'id' => 'template_body',
    'path' => 'assets/ckeditor',
    //Optionnal values
    'config' => array(
        'toolbar' => "Full", //Using the Full toolbar
        'width' => "550px", //Setting a custom width
        'height' => '100px', //Setting a custom height
    ),
);

$attributes = array('class' => '', 'id' => 'email_template_add', 'name' => 'email_template_add');
echo form_open(get_current_section($this) . '/email_template/action/' . $action . "/" . $language_code . "/" . $id, $attributes);


if ($action == "edit" && isset($template[0]['c']['template_name']) && $template[0]['c']['template_name'] != '')
    echo form_hidden('old_template_name', $template[0]['c']['template_name']);
?>

<div id="one" class="grid-data">
    <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
        <tbody bgcolor="#ffffff">
            <tr>
                <th><?php echo lang('add_form_fields'); ?> - <?php echo $language_name; ?></th>
            </tr>
            <tr>
                <td class="add-user-form-box">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="100%" valign="top">
                                <table width="100%" cellpadding="5" cellspacing="1" border="0">
                                    <tr>
                                        <td width="300" align="right"><span class="star">*&nbsp;</span><?php echo lang('template-name'); ?>:</td>
                                        <td>
                                            <?php
                                            $title_data = array(
                                                'name' => 'template_name',
                                                'id' => 'template_name',
                                                'size' => '50',
                                                'maxlength' => '100',
                                                'class' => 'validate[required]',
                                                'value' => set_value('template_name', ((isset($template[0]['c']['template_name'])) ? $template[0]['c']['template_name'] : ''))
                                            );
                                            echo form_input($title_data);
                                            ?>
                                            <span class="validation_error"><?php echo form_error('template_name'); ?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="300" align="right"><span class="star">*&nbsp;</span><?php echo lang('template-subject'); ?>:</td>
                                        <td>
                                            <?php
                                            $title_data = array(
                                                'name' => 'template_subject',
                                                'id' => 'template_subject',
                                                'size' => '50',
                                                'maxlength' => '100',
                                                'class' => 'validate[required]',
                                                'value' => set_value('template_subject', ((isset($template[0]['c']['template_subject'])) ? $template[0]['c']['template_subject'] : ''))
                                            );
                                            echo form_input($title_data);
                                            ?>
                                            <span class="validation_error"><?php echo form_error('template_subject'); ?></span>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td align="right" valign="top"><?php echo lang('template-body'); ?>:</td>
                                        <td>
                                            <?php
                                            $template_body_data = array(
                                                'name' => 'template_body',
                                                'id' => 'template_body',
                                                'size' => '50',
                                                'maxlength' => '100',
                                                'class' => 'validate[required]',
                                                'value' => set_value('template_body', ((isset($template[0]['c']['template_body'])) ? $template[0]['c']['template_body'] : $template_body))
//                                                'value' => set_value('template_body', ((isset($template['template_body'])) ? $template['template_body'] : ''))
                                            );
                                            echo form_textarea($template_body_data);
                                            echo display_ckeditor($ckeditor);
                                            ?>
<!--                                            <span class="validation_error"><?php // echo form_error('template_body'); ?></span>-->
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><span class="star">*&nbsp;</span><?php echo lang('status'); ?>:</td>
                                        <td>
                                            <?php
                                            $options = array(
                                                '1' => lang('active'),
                                                '0' => lang('inactive')
                                            );
                                            echo form_dropdown('status', $options, (isset($template[0]['c']['status'])) ? $template[0]['c']['status'] : $status);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </tbody>
    </table>
    <div class="submit-btns clearfix">
        <?php
        $submit_button = array(
            'name' => 'email_template_submit',
            'id' => 'email_template_submit',
            'value' => lang('save'),
            'title' => lang('save'),
            'class' => 'inputbutton',
        );
        echo form_submit($submit_button);
        $cancel_button = array(
            'content' => lang('cancel'),
            'title' => lang('cancel'),
            'class' => 'inputbutton',
            'onclick' => "location.href='" . site_url(get_current_section($this) . '/email_template/index/' . $language_code) . "'"
        );
        echo "&nbsp;";
        echo form_button($cancel_button);
        ?>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {
        jQuery("#email_template_add").validationEngine(
        {
            //promptPosition: '<?php echo VALIDATION_ERROR_POSITION; ?>',
            validationEventTrigger: "submit"
        }
    );
    });
</script>