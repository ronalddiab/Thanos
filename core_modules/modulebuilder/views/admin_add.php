<div class="main-container">
    <?php echo form_open_multipart(get_current_section($this).'/modulebuilder/add/' . $count, array('id' => 'saveform', 'name' => 'saveform')); ?>
    <div class="grid-data">
        <!--        <div class="add-new">
        <?php echo anchor(site_url() . 'admin/modulebuilder', lang('view-all-module'), 'title="View All Modules" style="text-align:center;width:100%;"'); ?>
                </div>-->
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th><?php echo lang('generate-module') ?></th>
                </tr>
                <tr>
                    <td class="add-user-form-box">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="100%" valign="top">
                                    <table width="100%" cellpadding="5" cellspacing="1" border="0">
                                        <?php
                                        $module_data = array(
                                            'name' => 'module_name',
                                            'id' => 'module_name',
                                            'value' => set_value('module_name', ((isset($module_name)) ? $module_name : '')),
                                            'style' => 'width:198px;',
                                            'class' => 'validate[required]'
                                        );
                                        ?>
                                        <tr>
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('module-name'), 'module_name'); ?>:</td>
                                            <td><?php echo form_input($module_data); ?><br/><span class="warning-msg"><?php echo form_error('module_name'); ?></span></td>
                                            </td>
                                        </tr>
                                        <?php
                                        $controller_data = array(
                                            'name' => 'controller_name',
                                            'id' => 'controller_name',
                                            'value' => set_value('controller_name', ((isset($controller_name)) ? $controller_name : '')),
                                            'style' => 'width:198px;',
                                            'readonly' => 'readonly',
                                            'class' => 'validate[required]'
                                        );
                                        ?>
                                        <tr>
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('controller-name'), 'controller_name'); ?>:</td>
                                            <td><?php echo form_input($controller_data); ?><br/><span class="warning-msg"><?php echo form_error('controller_name'); ?></span></td>
                                            </td>
                                        </tr>
                                        <?php
                                        $model_data = array(
                                            'name' => 'model_name',
                                            'id' => 'model_name',
                                            'value' => set_value('model_name', ((isset($model_name)) ? $model_name : '')),
                                            'style' => 'width:198px;',
                                            'readonly' => 'readonly',
                                            'class' => 'validate[required]'
                                        );
                                        ?>
                                        <tr>
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('model-name'), 'model_name'); ?>:</td>
                                            <td><?php echo form_input($model_data); ?><br/><span class="warning-msg"><?php echo form_error('model_name'); ?></span></td>
                                            </td>
                                        </tr>
                                        <?php
                                        $table_data = array(
                                            'name' => 'table_name',
                                            'id' => 'table_name',
                                            'value' => set_value('table_name', ((isset($table_name)) ? $table_name : '')),
                                            'style' => 'width:198px;',
                                            'class' => 'validate[required]'
                                        );
                                        ?>
                                        <tr>
                                            <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('table-name'), 'table_name'); ?>:</td>
                                            <td><?php echo form_input($table_data); ?><br/><span class="warning-msg"><?php echo form_error('table_name'); ?></span></td>
                                            </td>
                                        </tr>
                                        <?php
                                        for ($i = 1; $i <= $count; $i++)
                                        {
                                            ?>
                                            <table class="container_blue">
                                                <tr>
                                                    <td align="right"><strong><?php echo lang('field-details'); ?></strong></td>
                                                    <td>&nbsp; </td>
                                                </tr>
                                                <?php
                                                $field_label_data = array(
                                                    'name' => 'field_label' . $i,
                                                    'id' => 'field_label' . $i,
                                                    'value' => set_value('field_label' . $i, ((isset($field_label{$i})) ? $field_label{$i} : '')),
                                                    'style' => 'width:198px;',
                                                    'class' => 'validate[required]'
                                                );
                                                ?>
                                                <tr>
                                                    <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('label'), 'field_label' . $i); ?>:</td>
                                                    <td><?php echo form_input($field_label_data); ?><br/><span class="warning-msg"><?php echo form_error("field_label{$i}"); ?></span></td>
                                                    </td>
                                                </tr>
                                                <?php
                                                $field_name_data = array(
                                                    'name' => 'field_name' . $i,
                                                    'id' => 'field_name' . $i,
                                                    'value' => set_value('field_label' . $i, ((isset($field_name{$i})) ? $field_name{$i} : '')),
                                                    'style' => 'width:198px;',
                                                    'class' => 'validate[required]'
                                                );
                                                ?>
                                                <tr>
                                                    <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('name'), 'field_name' . $i); ?>:</td>
                                                    <td><?php echo form_input($field_name_data); ?><br/><span class="warning-msg"><?php echo form_error("field_name{$i}"); ?></span></td>
                                                    </td>
                                                </tr>
                                                <?php
                                                $field_type_data = array(
                                                    'input' => 'INPUT',
                                                    'textarea' => 'TEXTAREA',
                                                    'select' => 'SELECT',
                                                    'radio' => 'RADIO',
                                                    'checkbox' => 'CHECKBOX'
                                                );
                                                ?>
                                                <tr>
                                                    <td align="right"><?php echo form_label(lang('type'), 'field_type'); ?>:</td>
                                                    <td>
                                                        <?php
                                                        echo form_dropdown('field_type' . $i, $field_type_data, ((isset($field_type)) ? $field_type : ''));
                                                        ?>
                                                        <span class="warning-msg"><?php echo form_error('field_type' . $i); ?></span>
                                                    </td>
                                                </tr>
                                                <tr><td>&nbsp;</td></tr>
                                                <tr>
                                                    <td align="right"><strong><?php echo lang('db-schema'); ?></strong></td>
                                                    <td>&nbsp; </td>
                                                </tr>
                                                <?php
                                                $db_type_data = array(
                                                    'VARCHAR' => 'VARCHAR',
                                                    'TINYINT' => 'TINYINT',
                                                    'TEXT' => 'TEXT',
                                                    'DATE' => 'DATE',
                                                    'SMALLINT' => 'SMALLINT',
                                                    'MEDIUMINT' => 'MEDIUMINT',
                                                    'INT' => 'INT',
                                                    'BIGINT' => 'BIGINT',
                                                    'FLOAT' => 'FLOAT',
                                                    'DOUBLE' => 'DOUBLE',
                                                    'DECIMAL' => 'DECIMAL',
                                                    'DATETIME' => 'DATETIME',
                                                    'TIMESTAMP' => 'TIMESTAMP',
                                                    'TIME' => 'TIME',
                                                    'YEAR' => 'YEAR',
                                                    'CHAR' => 'CHAR',
                                                    'TINYBLOB' => 'TINYBLOB',
                                                    'TINYTEXT' => 'TINYTEXT',
                                                    'BLOB' => 'BLOB',
                                                    'MEDIUMBLOB' => 'MEDIUMBLOB',
                                                    'MEDIUMTEXT' => 'MEDIUMTEXT',
                                                    'LONGBLOB' => 'LONGBLOB',
                                                    'LONGTEXT' => 'LONGTEXT',
                                                    'ENUM' => 'ENUM',
                                                    'SET' => 'SET',
                                                    'BIT' => 'BIT',
                                                    'BOOL' => 'BOOL',
                                                    'BINARY' => 'BINARY',
                                                    'VARBINARY' => 'VARBINARY'
                                                );
                                                ?>
                                                <tr>
                                                    <td align="right"><?php echo form_label(lang('type'), 'db_type' . $i); ?>:</td>
                                                    <td>
                                                        <?php
                                                        echo form_dropdown('db_type' . $i, $db_type_data, ((isset($db_type)) ? $db_type : ''));
                                                        ?>
                                                        <span class="warning-msg"><?php echo form_error('db_type' . $i); ?></span>
                                                    </td>
                                                </tr>
                                                <?php
                                                $db_length_data = array(
                                                    'name' => 'db_length_value' . $i,
                                                    'id' => 'db_length_value' . $i,
                                                    'value' => set_value('db_length_value' . $i, ((isset($db_length_value)) ? $db_length_value : '')),
                                                    'style' => 'width:100px;',
                                                    'class' => 'validate[required]'
                                                );
                                                ?>
                                                <tr>
                                                    <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('length-value'), 'db_length_value' . $i); ?>:</td>
                                                    <td><?php echo form_input($db_length_data); ?><br/><span class="warning-msg"><?php echo form_error('db_length_value' . $i); ?></span></td>
                                                    </td>
                                                </tr>
                                                <tr><td>&nbsp;</td></tr>
                                                <tr>
                                                    <td align="right"><strong><?php echo lang('validation-rules'); ?></strong></td>
                                                    <td>&nbsp; </td>
                                                </tr>
                                                <tr>
                                                    <td class="checkbox" colspan="2" style="padding: 10px 15px; ">
                                                        <p><?php echo form_checkbox('validation_rules' . $i . '[]', lang('required'), ''); ?> <label><?php echo lang('required'); ?></label></p>
                                                        <p><?php echo form_checkbox('validation_rules' . $i . '[]', lang('trim'), ''); ?> <label><?php echo lang('trim'); ?></label></p>
                                                        <p><?php echo form_checkbox('validation_rules' . $i . '[]', lang('xss-clean'), ''); ?> <label><?php echo lang('xss-clean'); ?></label></p>
                                                        <p><?php echo form_checkbox('validation_rules' . $i . '[]', lang('valid-email'), ''); ?> <label><?php echo lang('valid-email'); ?></label></p>
                                                        <p><?php echo form_checkbox('validation_rules' . $i . '[]', lang('is-numeric'), ''); ?> <label><?php echo lang('is-numeric'); ?></label></p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <?php
                                        }
                                        ?>
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
            'onclick' => "location.href='" . site_url(get_current_section($this).'/modulebuilder/generate_module') . "'",
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
        jQuery("#saveform").validationEngine(
        {
            //promptPosition : '<?php echo VALIDATION_ERROR_POSITION; ?>',
            validationEventTrigger: "submit"
        }
    );
    });

    $('#module_name').keyup(function(e) {
        var $th = $(this);
        $th.val( $th.val().replace(/[^a-zA-Z_]/g, function(str) { alert('You have typed " ' + str + ' ".\n\nPlease use only alphabets.'); return ''; } ) );
        $('#controller_name').val($(this).val());
        $('#model_name').val($(this).val());
    });
</script>


