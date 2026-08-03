
<div id="ajax_table" style="text-align: left;">
    <div class="main-container">
        <div class="grid-data grid-data-table">

            <div class="add-new">
                <span style="float: left;"><?php echo add_image(array('active.png')) . "  Active " . add_image(array('inactive.png')) . " Inactive"; ?></span>
                <?php echo anchor(get_current_section($this) . '/languages/action/add', 'Add Language', 'title="Add Language" style="text-align:center;width:100%;"'); ?>
            </div>
            <?php
            if (!empty($languages)) {
                ?>
                <?php //echo form_open();  //you can add form tag here   ?>
                <?php echo form_open_multipart(get_current_section($this) . '/languages/default_save', array('id' => 'default_save', 'name' => 'default_save')); ?>
                <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
                    <tbody bgcolor="#ffffff">
                        <tr>
                            <th><?php echo lang('languages-no'); ?></th>
                            <th>
                                <?php
                                $field_sort_order = 'asc';
                                $sort_image = 'srt_down.png';
                                if ($sort_by == 'l.language_name' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="javascript:;" onclick="sort_data('l.language_name', '<?php echo $field_sort_order; ?>');" >
                                    <?php echo lang('languages-name'); ?>
                                    <?php
                                    if ($sort_by == 'l.language_name') {
                                        ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                    <?php }
                                    ?>
                                </a>
                            </th>
                            <th>
                                <?php
                                $field_sort_order = 'asc';
                                $sort_image = 'srt_down.png';
                                if ($sort_by == 'l.language_code' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="javascript:;" onclick="sort_data('l.language_code', '<?php echo $field_sort_order; ?>');" >
                                    <?php echo lang('languages-code'); ?>
                                    <?php
                                    if ($sort_by == 'l.language_code') {
                                        ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                    <?php }
                                    ?>
                                </a>
                            </th>
                            <th><?php echo lang('languages-direction'); ?></th>
                            <th><?php echo lang('languages-default'); ?></th>
                            <th>
                                <?php
                                $field_sort_order = 'asc';
                                $sort_image = 'srt_down.png';
                                if ($sort_by == 'l.status' && $sort_order == 'asc')
                                {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="#" onclick="sort_data('l.status', '<?php echo $field_sort_order; ?>');" ><?php echo lang('languages-status'); ?></a>
                                <?php
                                    if ($sort_by == 'l.status')
                                    {
                                        ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                    <?php }
                                    ?>
                            </th>
                            <th><?php echo lang('languages-action'); ?></th>
                        </tr>
                        <?php
                        if ($page_number > 1)
                        {
                            $i = ($this->session->userdata[get_current_section($this)]['record_per_page'] * ($page_number - 1)) + 1;
                        }
                        else
                        {
                            $i = 1;
                        }


                        foreach ($languages as $key => &$data) {
                            //take alias from an array
                            $alias = end(array_keys($data));

                            if ($i % 2 != 0) {
                                $class = "odd-row";
                            } else {
                                $class = "even-row";
                            }
                            ?>
                            <tr class="<?php echo $class; ?> rows" id="row-<?php echo $data[$alias]['id']; ?>">
                                <td><?php echo $i; ?></td>
                                <td><?php echo $data[$alias]['language_name']; ?></td>
                                <td><?php echo $data[$alias]['language_code']; ?></td>
                                <td><?php echo ($data[$alias]['direction'] == 'ltr') ? 'Left' : 'Right'; ?></td>
                                <td>
                                    <?php
                                    //echo ($data[$alias]['default'] == '1') ? 'Yes' : 'No';

                                    // Set Deault from listing starts
                                    $checked = '';
                                    $value_chkbox = $data[$alias]['id'];
                                    if ($data[$alias]['default'] == '1') {
                                        $checked = 'checked="checked"';
                                    }


                                    //echo '&nbsp;&nbsp;&nbsp;';

                                    if ($data[$alias]['status'] == 1) {
                                        //echo "IF";
                                        echo form_radio('chk_default', $value_chkbox, $checked);
                                    } else {
                                        //echo "NOHTING TO DO HERE";
                                    }

                                    // Set Deault from listing ends
                                    ?>
                                </td>
                                <td><?php
                            if ($data[$alias]['status'] == 1) {
                                echo add_image(array('active.png'), '', '', array('title' => 'active', 'alt' => "active"));
                            } else {
                                echo add_image(array('inactive.png'), '', '', array('title' => 'inactive', 'alt' => "inactive"));
                            }
                                    ?></td>
                                <!-- td>
                                <?php
                                $statuslist = array(0 => lang('inactive'), 1 => lang('active'), -1 => lang('deleted'));
                                if (in_array($data[$alias]['status'], array_keys($statuslist))) {
                                    echo $statuslist[$data[$alias]['status']];
                                }
                                ?>
                                </td -->
                                <td>
                                    <div class="action">
                                        <div style="float:left;padding-right:10px;"><a title='View' href="<?php echo site_url() . get_current_section($this); ?>/languages/view_data/<?php echo $data[$alias]['id'] ?>"><?php echo add_image(array('viewIcon.png')); ?></a></div>
                                        <div class="edit"><a href="<?php echo base_url() . get_current_section($this) . '/languages/action/edit/' . $data[$alias]['id']; ?>" title="<?php echo lang('languages-edit') ?>"><?php echo add_image(array('edit.png')); ?></a></div>

                                        <?php if($data[$alias]['default'] != '1'){ ?>
                                        <div class="delete"><a href="javascript:;" title="<?php echo lang('languages-delete') ?>" onclick=" delete_language(<?php echo $data[$alias]['id'] ?>);"><?php echo add_image(array('delete.png')); ?></a></div>
                                        <?php }?>
                                    </div>
                                </td>
                            </tr>



                            <?php
                            $i++;
                        }
                        ?>
                            <tr>
                                <td colspan="7">
                                    <?php
                    $submit_button = array(
                        'name' => 'mysubmit',
                        'id' => 'mysubmit',
                        'value' => lang('btn-default'),
                        'title' => lang('btn-default'),
                        'class' => 'inputbutton',
                    );
                    echo form_submit($submit_button);
                    ?>
                                </td>
                            </tr>
                    </tbody>
                </table>

                <?php echo form_close();  //you can add form tag here    ?>
                <?php
            } else {
                ?>
                <table>
                    <tr>
                        <td><?php echo lang('languages-message-norec') ?></td>
                    </tr>
                </table>
                <?php
            }
            $querystr = $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash()) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => base_url() . get_current_section($this) . "/languages/index",
                'params' => $querystr,
                'element' => 'ajax_table'
            );
            widget('custom_pagination', $options);
            ?>
        </div>
    </div>
    <script type="text/javascript">
        function delete_language(id){
            res = confirm('<?php echo lang('confirm-delete-msg'); ?>');
            if(res){
                blockUI();
                $.ajax({
                    type:'POST',
                    url:'<?php echo base_url() . get_current_section($this); ?>/languages/delete',
                    data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',id:id},
                    success: function() {
                        location.href= "<?php echo base_url() . get_current_section($this); ?>/languages/index";
                        unblockUI();
                    }
                });
            }else{
                return false;
            }
        }

        function sort_data(sort_by,sort_order)
        {
            $('#error_msg').fadeOut(1000); //hide error message it shown up while search
            blockUI('removeError');
            $.ajax({
                type:'POST',
                url:'<?php echo base_url() . get_current_section($this); ?>/languages/index',
                data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',sort_by:sort_by,sort_order:sort_order},
                success: function(data) {
                    $("#ajax_table").html(data);
                    unblockUI();
                }
            });
        }
    </script>
</div>
