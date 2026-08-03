<article class="card" id="ajax_table">
    <div class="article-header">
        <?php echo lang('region-management'); ?>
    </div>
    <div class="card-wrap">
        <div class="row region-controls-outer">            
            <div class="col-sm-10 col-lg-9">
                <form class="default-form form-inline clearfix">
                    <div class="form-group form-control-outer">
                        <?php
                        $input_data = array(
                            'name' => 'search_term',
                            'id' => 'search_term',
                            'class' => 'form-control',
                            //'title' => 'search',
                            'placeholder' => lang('search-by-region-name'),
                            'value' => set_value('search_term', urldecode($search_term))
                        );
                        echo form_input($input_data);
                        ?>
                    </div>                    
                    <div class="form-group">
                        <?php
                        $search_button = array(
                            'content' => add_image(array('search-icon.png')) . ' ' . lang('btn-search'),
                            //'title' => lang('btn-search'),
                            'class' => 'btn btn-secondary',
                            'onclick' => "submit_search()",
                        );
                        echo form_button($search_button);
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        $reset_button = array(
                            'content' => add_image(array('reset-icon.png')) . ' ' . lang('reset_button'),
                            //'title' => lang('reset_button'),
                            'class' => 'btn btn-reset',
                            'onclick' => "reset_data()",
                        );
                        echo form_button($reset_button);
                        ?>
                    </div>
                </form>
            </div>            
            <?php $add_region_permission = check_user_permission_by_label('admin.regions.action.add'); ?>
            <?php if($add_region_permission){ ?>
            <div class="col-sm-2 col-lg-3">
                <?php echo anchor(BASE_ADMIN_URL_CUSTOM . 'regions/action/add', '<span>' . add_image(array('plus-icon.png')) . '</span> ' . lang('add-region'), 'class="btn btn-blue pull-right"'); ?>
            </div>
            <?php } ?>
        </div>

        <?php if (!empty($regions)) { ?>
        <div class="row helprow">
            <div class="col-sm-5 col-xs-7">
                <?php echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "Active")); ?>&nbsp;Active
                <?php echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "Inactive")); ?>&nbsp;Inactive
            </div>
        </div>
        <?php } ?>

        <div class="table-responsive">          		
			<?php echo form_open(); ?>
            <table class="table table-striped">
                <?php if (!empty($regions)) { ?>
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="check_all" id="check_all" class="icheck"></th>
                            <th class="digits-col"><?php echo lang('no') ?></th>
                            <th class="name-col">
                                <?php
                                $field_sort_order = 'asc';
                                $sort_image = 'srt_down.png';
                                if ($sort_by == 'r.region_name' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="javascript:void(0)" onclick="sort_data('r.region_name', '<?php echo $field_sort_order; ?>');" >
                                    <?php echo lang('region-name'); ?>
                                    <?php
                                    if ($sort_by == 'r.region_name') {
                                        ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                    <?php }
                                    ?>
                                </a>
                            </th>
                            <th><?php echo lang('status') ?></th>
                            <th><?php echo lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($page_number > 1) {
                            $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                        } else {
                            $i = 1;
                        }
                        foreach ($regions as $region) {
                            $region_id = $region['r']['id'];
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" id="<?php echo $region['r']['id']; ?>" name="check_box[]" class="icheck checkboxlist check_box" value="<?php echo $region['r']['id']; ?>">
                                </td>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $region['r']['region_name']; ?></td>
                                <td>
                                    <?php
                                    if ($region['r']['status'] == 1) {
                                        echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "Active"));
                                    } else {
                                        echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "Inactive"));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a title="View" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/view_data/<?php echo $region_id; ?>"><?php echo add_image(array('search-icon-black.png')); ?></a>
                                    <?php $edit_region_permission = check_user_permission_by_label('admin.regions.action.edit'); ?>
                                    <?php if($edit_region_permission){ ?>
                                    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/action/edit/<?php echo $region_id ?>" title="<?php echo lang('edit') ?>"><?php echo add_image(array('edit-icon.png')); ?></a></div>
                                    <?php } ?>
                                    <?php
                                    $encrypted_id = base64_encode($region_id);

                                    $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_region(\"$encrypted_id\")'>" . add_image(array('delete-icon.png')) . "</a>";
                                    ?>
                                    <?php echo $deletelink ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </tbody>
                <?php } else {
                    ?>
                    <tr>
                        <td><?php echo lang('no-records') ?></td>
                    </tr>
                    <?php
                }
                ?>
			</table>
			<?php echo form_close(); ?>
        </div>

        <?php if (!empty($regions)) { ?>
            <div class="btn-panel">
                <?php
                $reset_button = array(
                    'content' => lang('delete'),
                    //'title' => lang('delete'),
                    'class' => 'btn btn-custom btn-red',
                    'onclick' => "delete_records()",
                );
                echo form_button($reset_button);
                ?>
                <?php
                $reset_button = array(
                    'content' => lang('active'),
                    //'title' => lang('active'),
                    'class' => 'btn btn-custom btn-green',
                    'onclick' => "active_records()",
                );
                echo form_button($reset_button);
                ?>
                <?php
                $reset_button = array(
                    'content' => lang('inactive'),
                    //'title' => lang('inactive'),
                    'class' => 'btn btn-custom btn-yellow',
                    'onclick' => "inactive_records()",
                );
                echo form_button($reset_button);
                ?>
                <?php
                $reset_button = array(
                    'content' => lang('active-all'),
                    //'title' => lang('active-all'),
                    'class' => 'btn btn-custom btn-green',
                    'onclick' => "active_all_records()",
                );
                echo form_button($reset_button);
                ?>
                <?php
                $reset_button = array(
                    'content' => lang('inactive-all'),
                    //'title' => lang('inactive-all'),
                    'class' => 'btn btn-custom btn-yellow',
                    'onclick' => "inactive_all_records()",
                );
                echo form_button($reset_button);
                ?>
            </div>
        <?php } ?>
        <?php if (!empty($regions)) { ?>
        <?php
        $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term_firstname=' . urlencode($search_term_firstname) . '&search_term_username=' . urlencode($search_term_username) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
        $options = array(
            'total_records' => $total_records,
            'page_number' => $page_number,
            'isAjaxRequest' => 1,
            'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "regions/index",
            'params' => $querystr,
            'element' => 'ajax_table'
        );
        widget('custom_pagination', $options);
        ?>
        <?php } ?>
    </div>
</article>
<script type="text/javascript">
    //remove dynamically populate error
    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            submit_search();
        }
    });
    function attach_error_event() {
        $('div.formError').bind('click', function() {
            $(this).fadeOut(1000, removeError);
        });
    }
    function removeError() {
        jQuery(this).remove();
    }

    $(function() {
        $('#check_all').on('ifChecked', function(event) {
            $('.check_box').iCheck('check');
        });

        $('#check_all').on('ifUnchecked', function(event) {
            if ($('.check_box').filter(':checked').length == $('.check_box').length) {
                $('.check_box').iCheck('uncheck');
            }
        });
        $('.check_box').on('ifUnchecked', function(event) {
            $('#check_all').iCheck('uncheck');
        });

        $('.check_box').on('ifChecked', function(event) {
            if ($('.check_box').filter(':checked').length == $('.check_box').length) {
                $('#check_all').iCheck('check');
            }
        });
    });

    function delete_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });

        if (val == "") {
            alert('Please select at least one record for delete');
            return false;
        }
        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {
//                    alert(data); return;

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }
    function active_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }
    function inactive_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }
    function delete_region(id) {

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/delete',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', id: id}, success: function(data) {



                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);

                    //set responce message
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
        // e.preventDefault();
        if ($('#search_term').val() != '' || $('#search_term').val() != '') {
            blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val())},
                success: function(data) {
                    console.log(data);
                    $("#ajax_table").html(data);
                    unblockUI();
                }
            });
        } else {
            alert("Please enter something to search");
        }

    }

    function sort_data(sort_by, sort_order)
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val()), sort_by: sort_by, sort_order: sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });

    }
    function reset_data()
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>regions/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }

</script>