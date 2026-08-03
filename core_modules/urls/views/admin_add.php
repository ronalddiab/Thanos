<div class="main-container">
    <?php echo form_open_multipart(get_current_section($this).'/urls/save/', array('id' => 'saveform', 'name' => 'saveform')); ?>
    <div class="grid-data">
        <div class="add-new">
            <?php echo anchor(site_url().get_current_section($this).'/urls', lang('view-all-url'), 'title="View All URLs" style="text-align:center;width:100%;"'); ?>
        </div>
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th><?php echo lang('add-edit-url') ?></th>
                </tr>
                <tr>
                    <td class="add-user-form-box">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="100%" valign="top">
                                    <table width="100%" cellpadding="5" cellspacing="1" border="0">
                                        <?php
                                        $slug_url_data = array(
                                            'name' => 'slug_url',
                                            'id' => 'slug_url',
                                            'value' => set_value('slug_url', ((isset($slug_url)) ? $slug_url : '')),
                                            'style' => 'width:198px;',
                                            'maxlength' => '50',
                                            'class' => 'validate[required]'
                                        );
                                        ?>
                                        <tr>
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('slug-url'), 'slug_url'); ?>:</td>
                                            <td><?php echo form_input($slug_url_data); ?><span class="validation_error"><?php echo form_error('slug_url'); ?></span></td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('module-name'), 'module_name'); ?>:</td>
                                            <td>
                                                <?php
                                                echo form_dropdown('module_name', $modules_list, ((isset($module_name)) ? $module_name : ''), ' onChange = change_module(this.value); class = validate[required]');
                                                ?>
                                                <span class="validation_error"><?php echo form_error('module_name'); ?></span>
                                            </td>
                                        </tr>
                                        <tr id="related_row" style="display: none;">
                                            <td align="right"><?php echo form_label(lang('module-pages'), 'related_id'); ?>:</td>
                                            <td id="related_field">
                                                <?php
                                                echo form_dropdown('related_id', $related_list, ((isset($related_id)) ? $related_id : ''));
                                                ?>
                                                <span class="validation_error"><?php echo form_error('related_id'); ?></span>
                                            </td>
                                        </tr>
                                        <?php
                                        $core_url_data = array(
                                            'name' => 'core_url',
                                            'id' => 'core_url',
                                            'value' => set_value('core_url', ((isset($core_url)) ? $core_url : '')),
                                            'style' => 'width:198px;',
                                            'class' => 'validate[required]',
                                            'maxlength' => '250'
                                        );
                                        ?>
                                        <tr>
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('core-url'), 'core_url'); ?>:</td>
                                            <td><?php echo form_input($core_url_data); ?><span class="validation_error"><?php echo form_error('core_url'); ?></span></td>
                                        </tr>
                                        <tr>
                                            <td align="right"></td>
                                            <td>(<?php echo lang('core-url-msg') ?>)</td>
                                        </tr>
                                        <?php
                                        $order_data = array(
                                            'name' => 'order',
                                            'id' => 'order',
                                            'value' => set_value('order', ((isset($order)) ? $order : '0')),
                                            'class' => 'validate[required, custom[integer], funcCall[checkPinteger]]',
                                            'style' => 'width:50px;',
                                            'maxlength' => '3'
                                        );
                                        ?>
                                        <tr>
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('order'), 'order'); ?>:</td>
                                            <td><?php echo form_input($order_data); ?><span class="validation_error"><?php echo form_error('order'); ?></span></td>
                                        </tr>
                                        <?php $statuslist = array('1' => lang('active'), '0' => lang('inactive')); ?>
                                        <tr>
                                            <td align="right"><?php echo form_label(lang('status'), 'Status'); ?>:</td>
                                            <td>
                                                <?php
                                                echo form_dropdown('status', $statuslist, ((isset($status)) ? $status : ''));
                                                ?>
                                                <span class="validation_error"><?php echo form_error('status'); ?></span>
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
    </div>
    <div class="submit-btns clearfix">
        <?php
        $submit_button = array(
            'name' => 'mysubmit',
            'id' => 'mysubmit',
            'value' => lang('btn-save'),
            'title' => lang('btn-save'),
            'class' => 'inputbutton',
        );
        echo form_submit($submit_button);
        $cancel_button = array(
            'content' => lang('btn-cancel'),
            'title' => lang('btn-cancel'),
            'class' => 'inputbutton',
            'onclick' => "location.href='" . site_url(get_current_section($this).'/urls') . "'",
        );
        echo "&nbsp;";
        echo form_button($cancel_button);
        ?>
    </div>
    <?php
    echo form_hidden('id', (isset($id)) ? $id : '0' );

    echo form_close();
    ?>
</div>
<script type="text/javascript">
    $(document).ready(function(){

        $('#slug_url').focus();

        jQuery("#saveform").validationEngine(
        {
            validationEventTrigger: "submit"
        }
    );

    });

    function checkPinteger(field, rules, i, options)
    {
        if(field.val() < 0)
            return 'Enter a positive number.';
    }

<?php if ($id != '' || $id != 0)
{
    ?>
        change_module("<?php Print($module_name); ?>",'<?php print($related_id); ?>');
<?php } ?>
    //Function to update related dropdown
    function change_module(module_name,related_id){
        $("#core_url").removeAttr('readonly');
        $.ajax({
            type:'POST',
            url:'<?php echo base_url().get_current_section($this); ?>/urls/get_related',
            data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',module_name:module_name},
            success: function(data) {
                if(data == ''){
                    $("#related_row").hide();
                }else{
                    $("#related_row").show();
                    $("#related_field").html(data);
                    $('#related_id').val(related_id);
                }
            }
        });
    }

    //Function to update slug & Core URL
    function change_related(){
        var module_name = $("#related_id :selected").text();

        if (module_name.length != 0)
        {
            $('#core_url').attr("readonly","readonly");
            $("#slug_url").val(module_name);
            $("#core_url").val('index/'+module_name);
        }
        else
        {
            $("#core_url").removeAttr('readonly');
            $("#slug_url").val(module_name);
            $("#core_url").val('index/'+module_name);
        }
    }
</script>


