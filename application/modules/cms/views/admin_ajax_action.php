<?php
$ckeditor = array(
    //ID of the textarea that will be replaced
    'id' => 'description',
    'path' => 'assets/ckeditor',
    //Optionnal values
    'config' => array(
        'toolbar' => "Full", //Using the Full toolbar
        'width' => "550px", //Setting a custom width
        'height' => '100px', //Setting a custom height
    ),
);
$attributes = array('class' => '', 'id' => 'cmsadd', 'name' => 'cmsadd');
echo form_open($this->controller->section_name.'/cms/action/' . $action . "/" . $language_code . "/" . $id, $attributes);
if ($action == "edit" && isset($cms['slug_url']) && $cms['slug_url'] != '')
    echo form_hidden('old_slug_url', $cms['slug_url']);
?>
<div id="one" class="grid-data">
    <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
        <tbody bgcolor="#fff">
            <tr>
                <th><?php echo lang('add_form_fields'); ?> - <?php echo $language_name; ?></th>
            </tr>
            <tr>
                <td class="add-user-form-box">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="100%" valign="top">
                                <table width="100%" cellpadding="5" cellspacing="1" border="0">
                                    <tr>
                                        <td width="300" align="right"><span class="star">*&nbsp;</span><?php echo lang('title'); ?>:</td>
                                        <td>
                                            <?php
                                            $title_data = array(
                                                'name' => 'title',
                                                'id' => 'title',
                                                'value' => '',
                                                'size' => '50',
                                                'maxlength' => '255',
                                                'class' => 'validate[required]',
                                                'value' => set_value('title', ((isset($cms['title'])) ? $cms['title'] : ''))
                                            );
                                            echo form_input($title_data);
                                            ?>
                                            <span class="validation_error"><?php echo form_error('title'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><span class="star">*&nbsp;</span><?php echo lang('slug_url'); ?>:</td>
                                        <td>
                                            <?php
                                            $slug_url_data = array(
                                                'name' => 'slug_url',
                                                'id' => 'slug_url',
                                                'value' => '',
                                                'size' => '50',
                                                'maxlength' => '50',
                                                'class' => 'validate[required]',
                                                'value' => set_value('slug_url', ((isset($cms['slug_url'])) ? $cms['slug_url'] : ''))
                                            );
                                            echo form_input($slug_url_data);
                                            ?>
                                            <span class="validation_error"><?php echo form_error('slug_url'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right" valign="top"></span><?php echo lang('description'); ?>:</td>
                                        <td>
                                            <?php
                                            $slug_url_data = array(
                                                'name' => 'description',
                                                'id' => 'description',
                                                'size' => '50',
                                                'value' => set_value('description', ((isset($cms['description'])) ? $cms['description'] : ''))
                                            );
                                            echo form_textarea($slug_url_data);
                                            echo display_ckeditor($ckeditor);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><strong><?php echo lang('meta_fields'); ?></strong></td>
                                        <td>&nbsp; </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><span class="star"></span><?php echo lang('title'); ?>:</td>
                                        <td>
                                            <?php
                                            $meta_title_data = array(
                                                'name' => 'meta_title',
                                                'id' => 'meta_title',
                                                'size' => '50',
                                                'maxlength' => '255',
                                                'value' => set_value('meta_title', ((isset($cms['meta_title'])) ? $cms['meta_title'] : ''))
                                            );
                                            echo form_input($meta_title_data);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><span class="star"></span><?php echo lang('keywords'); ?>:</td>
                                        <td>
                                            <?php
                                            $meta_keywords_data = array(
                                                'name' => 'meta_keywords',
                                                'id' => 'meta_keywords',
                                                'size' => '50',
                                                'maxlength' => '255',
                                                'value' => set_value('meta_keywords', ((isset($cms['meta_keywords'])) ? $cms['meta_keywords'] : ''))
                                            );
                                            echo form_input($meta_keywords_data);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right" valign="top"><span class="star"></span><?php echo lang('description'); ?>:</td>
                                        <td>
                                            <?php
                                            $meta_description_data = array(
                                                'name' => 'meta_description',
                                                'id' => 'meta_description',
                                                'size' => '50',
                                                'value' => set_value('meta_description', ((isset($cms['meta_description'])) ? $cms['meta_description'] : ''))
                                            );
                                            echo form_textarea($meta_description_data);
                                            ?>
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
                                            echo form_dropdown('status', $options, (isset($cms['status'])) ? $cms['status'] : '');
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
            'name' => 'cmssubmit',
            'id' => 'cmssubmit',
            'value' => lang('save'),
            'title' => lang('save'),
            'class' => 'inputbutton',
        );
        echo form_submit($submit_button);
        $cancel_button = array(
            'content' => lang('cancel'),
            'title' => lang('cancel'),
            'class' => 'inputbutton',
            'onclick' => "location.href='" . site_url(get_current_section($this).'/cms/index/'.$language_code) . "'",
        );
        echo "&nbsp;";
        echo form_button($cancel_button);
        ?>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {
        $('#slug_url').slugify('#title');
        jQuery("#cmsadd").validationEngine(
                {
                    // promptPosition: '<?php echo VALIDATION_ERROR_POSITION; ?>',
                    validationEventTrigger: "submit"
                }
        );
            $("input:text:visible:first").focus();
    });
</script>