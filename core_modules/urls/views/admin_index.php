<div id="ajax_table">
    <div class="main-container">
        <div class="search-box">
            <table cellspacing="2" cellpadding="4" border="0">
                <tbody>

                <td align="right"><?php echo lang('search-by'); ?>:</td>
                <td align="left">
                    <?php
                    $search_options = array('slug_url' => lang('slug-url'), 'module_name' => lang('module-name'));
                    echo form_dropdown('search', $search_options, $search, 'id=search onchange=search_change(this.value)');
                    ?>
                </td>

                <td align="left">

                    <div id="slug_url_id">
                        <?php
                        $input_data = array(
                            'name' => 'search_term',
                            'id' => 'search_term',
                            'title' => 'search',
                            'value' => set_value('search_term', urldecode($search_term))
                        );
                        echo form_input($input_data);
                        ?>
                    </div>
                    <div id="module_list_id">
                        <?php
                        echo form_dropdown('module_name', $modules_list, $module, 'id=module_name');
                        ?>
                    </div>
                </td>

                <td>
                    <?php
                    $search_button = array(
                        'content' => lang('btn-search'),
                        'title' => lang('btn-search'),
                        'class' => 'inputbutton',
                        'onclick' => "submit_search()",
                        'id' => 'search_button'
                    );
                    echo form_button($search_button);
                    ?>
                </td>



                <td>
                    <?php
                    $reset_button = array(
                        'content' => lang('btn-reset'),
                        'title' => lang('btn-reset'),
                        'class' => 'inputbutton',
                        'onclick' => "reset_data()",
                    );
                    echo form_button($reset_button);
                    ?>
                </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="grid-data grid-data-table">
            <div class="add-new">
                <span style="float: left;"><?php echo add_image(array('active.png')) . " " . lang('active') . " " . add_image(array('inactive.png')) . " " . lang('inactive'); ?></span>
                <?php echo anchor(site_url() . get_current_section($this) . '/urls/action/add', lang('add-url'), 'title="Add URLs" style="text-align:center;width:100%;"'); ?>
            </div>

            <br/>
            <?php
            echo form_open('', array('id' => 'url_form'));
            ?>
            <?php
            if (!empty($urls)) {
                ?>
                <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
                    <tbody bgcolor="#fff">
                        <tr>
                            <th width="30px"><input type="checkbox" name="check_all" id="check_all" value="0"></th>
                            <th><?php echo lang('no') ?></th>
                            <th>
                                <?php
                                $field_sort_order = 'asc';
                                $sort_image = 'srt_down.png';
                                if ($sort_by == 'u.slug_url' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="#" onclick="sort_data('u.slug_url', '<?php echo $field_sort_order; ?>');" >
                                    <?php echo lang('slug-url'); ?>
                                    <?php
                                    if ($sort_by == 'u.slug_url') {
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
                                if ($sort_by == 'u.module_name' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="#" onclick="sort_data('u.module_name', '<?php echo $field_sort_order; ?>');" >
                                    <?php echo lang('module-name'); ?>
                                    <?php
                                    if ($sort_by == 'u.module_name') {
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
                                if ($sort_by == 'u.core_url' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="#" onclick="sort_data('u.core_url', '<?php echo $field_sort_order; ?>');" >
                                    <?php echo lang('core-url'); ?>
                                    <?php
                                    if ($sort_by == 'u.core_url') {
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
                                if ($sort_by == 'u.status' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="#" onclick="sort_data('u.status', '<?php echo $field_sort_order; ?>');" ><?php echo lang('status'); ?></a>

                                <?php
                                if ($sort_by == 'u.status') {
                                    ?>
                        <div class="sorting">
                            <?php echo add_image(array($sort_image)); ?>
                        </div>
                    <?php }
                    ?>
                    </th>
                    <th><?php echo lang('actions') ?></th>
                    </tr>
                    <?php
                    if ($page_number > 1) {
                        $i = ($this->session->userdata[get_current_section($this)]['record_per_page'] * ($page_number - 1)) + 1;
                    } else {
                        $i = 1;
                    }

                    foreach ($urls as $url) {
                        if ($i % 2 != 0) {
                            $class = "odd-row";
                        } else {
                            $class = "even-row";
                        }
                        ?>
                        <tr class="<?php echo $class; ?> rows" id="row-<?php echo $url['u']['id']; ?>">
                            <td><input type="checkbox" id="<?php echo $url['u']['id']; ?>" name="check_box[]" class="check_box" value="<?php echo $url['u']['id']; ?>"></td>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $url['u']['slug_url']; ?></td>
                            <td><?php echo $url['u']['module_name']; ?></td>
                            <td><?php echo $url['u']['core_url']; ?></td>
                            <td>
                                <?php
                                if ($url['u']['status'] == 1) {
                                    echo add_image(array('active.png'), '', '', array('title' => 'active', 'alt' => "active"));
                                } else {
                                    echo add_image(array('inactive.png'), '', '', array('title' => 'inactive', 'alt' => "inactive"));
                                }
                                ?>
                            </td>
                            <td>
                                <div class="action">
                                    <div style="float:left;padding-right:10px;"><a title='View' href="<?php echo site_url() . get_current_section($this); ?>/urls/view_data/<?php echo $url['u']['id'] ?>"><?php echo add_image(array('viewIcon.png')); ?></a></div>
                                    <div class="edit"><a href="<?php echo site_url() . get_current_section($this); ?>/urls/action/edit/<?php echo $url['u']['id'] ?>" title="<?php echo lang('edit') ?>"><?php echo add_image(array('edit.png')); ?></a></div>
                                    <?php
                                    $url_id = $url['u']['id'];
                                    $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_url($url_id)'>" . add_image(array('delete.png')) . "</a>";
                                    ?>
                                    <div class="delete"><?php echo $deletelink ?></div>
                                </div>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                    <tr class="odd-row">
                        <td colspan="7">
                            <?php
                            $reset_button = array(
                                'content' => lang('delete'),
                                'title' => lang('delete'),
                                'class' => 'inputbutton',
                                'onclick' => "delete_records()",
                            );
                            echo form_button($reset_button);
                            ?>
                            <?php
                            $reset_button = array(
                                'content' => lang('active'),
                                'title' => lang('active'),
                                'class' => 'inputbutton',
                                'onclick' => "active_records()",
                            );
                            echo form_button($reset_button);
                            ?>
                            <?php
                            $reset_button = array(
                                'content' => lang('inactive'),
                                'title' => lang('inactive'),
                                'class' => 'inputbutton',
                                'onclick' => "inactive_records()",
                            );
                            echo form_button($reset_button);
                            ?>
                            <?php
                            $reset_button = array(
                                'content' => lang('active-all'),
                                'title' => lang('active-all'),
                                'class' => 'inputbutton',
                                'onclick' => "active_all_records()",
                            );
                            echo form_button($reset_button);
                            ?>
                            <?php
                            $reset_button = array(
                                'content' => lang('inactive-all'),
                                'title' => lang('inactive-all'),
                                'class' => 'inputbutton',
                                'onclick' => "inactive_all_records()",
                            );
                            echo form_button($reset_button);
                            ?>
                        </td>
                    </tr>
                    <?php
                    echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
                    echo form_hidden('page_number', "", "page_number");
                    echo form_hidden('per_page_result', "", "per_page_result");
                    ?>
                    </tbody>
                </table>
                <?php
                echo form_close();
            } else {
                ?>
                <table>
                    <tr>
                        <td><?php echo lang('no-records') ?></td>
                    </tr>
                </table>
                <?php
            }


            $querystr = $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '&search=' . $search. '&module=' . $module. '';
            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => base_url() . get_current_section($this) . "/urls/index",
                'params' => $querystr,
                'element' => 'ajax_table'
            );
            widget('custom_pagination', $options);
            ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            submit_search();
        }
    });
    $(function() {
        $("#check_all").click(function() {
            if ($("#check_all").is(':checked')) {
                $(".check_box").prop("checked", true);
            } else {
                $(".check_box").prop("checked", false);
            }
        });
        $(".check_box").click(function() {

            if ($(".check_box").length == $(".check_box:checked").length) {
                $("#check_all").prop("checked", true);
                $(".check_box").attr("checked", "checked");
            } else {
                $("#check_all").removeAttr("checked");
            }

        });
    });
    function delete_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });
        if (val == "")
        {
            alert('Please select at least one record for delete');
            return false;
        }
        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . get_current_section($this); ?>/urls/index',
                data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . get_current_section($this); ?>/urls/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                }
            });
        } else
        {
            return false;
        }
    }

    function active_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });
        if (val == "")
        {
            alert('Please select at least one record for active');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . get_current_section($this); ?>/urls/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . get_current_section($this); ?>/urls/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });
        if (val == "")
        {
            alert('Please select at least one record for inactive');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . get_current_section($this); ?>/urls/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . get_current_section($this); ?>/urls/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function active_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . get_current_section($this); ?>/urls/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . get_current_section($this); ?>/urls/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . get_current_section($this); ?>/urls/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . get_current_section($this); ?>/urls/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function delete_url(id) {
        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . get_current_section($this); ?>/urls/delete',
                data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', id: id},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . get_current_section($this); ?>/urls/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);

                    $("#messages").show();
                    $("#messages").html(data);
                }
            });
        } else {
            return false;
        }
    }
    function submit_search()
    {
        $('#error_msg').fadeOut(1000);
        /*if($('#search_term').val() == ''){
         $('#search_term').validationEngine('showPrompt', '<?php echo lang('msg-search-req'); ?>', 'error');
         attach_error_event(); //for remove dynamically populate popup
         return false;
         }   */
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . get_current_section($this); ?>/urls/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val()), search: encodeURIComponent($('#search').val()), module: encodeURIComponent($('#module_name').val())},
            success: function(data) {

                $("#ajax_table").html(data);
                unblockUI();
            }
        });

    }
    function sort_data(sort_by, sort_order)
    {
        blockUI('removeError');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . get_current_section($this); ?>/urls/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val()), sort_by: sort_by, sort_order: sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });

    }
    function reset_data()
    {
        blockUI('removeError');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . get_current_section($this); ?>/urls/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }
    function search_change(val)
    {
        if (val === 'slug_url')
        {
            // $("#module_list_id").hide("slow", function() {});
            $("#module_list_id").hide();
            $("#slug_url_id").show();
        }

        if (val === 'module_name')
        {
            // $("#slug_url_id").hide("slow", function() {});
            $("#slug_url_id").hide();
            $("#module_list_id").show();
        }
    }

    $(document).ready(function() {
        search_change($("#search").val());
    });
</script>