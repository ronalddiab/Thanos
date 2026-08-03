<article class="card" id="ajax_table">
    <div class="article-header">
        <?php echo lang('permission-management'); ?> 
    </div>
    <div class="card-wrap">
        <div class="row site-controls-outer">
           
                <div class="col-sm-9">
                    <form class="default-form form-inline clearfix" method="post">
                        <div class="form-group form-control-outer"> 
                            <input type="text" name="search_term" id="search_term" class="form-control" placeholder="<?php echo lang('search-by-permission-title') ?>" value="<?php echo isset($search_term) && !empty($search_term) ? htmlspecialchars_decode($search_term) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-secondary" type="submit" id="submit_search">
                                <img src="images/search-icon.png" alt="Search">
                                <?php echo lang('btn-search'); ?> 
                            </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-reset" type="reset" onclick="reset_data()">
                                <img src="images/reset-icon.png" alt="Reset">
                                <?php echo lang('btn-reset'); ?>
                            </button>
                        </div>
                    </form>
                </div>
       
            <div class="col-sm-3">
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'permissions/action/add' ?> " title="<?php lang('add-permission') ?>" class="btn btn-blue pull-right"><span><img src="images/plus-icon.png" alt="Add Permission"></span><?php echo lang('add-permission'); ?></a>
            </div>
        </div>
        <div class="row helprow">
            <div class="col-sm-3">
                <?php echo add_image(array('active.png'), '', '', array('title' => 'active', 'alt' => "active")); ?>&nbsp;Active
                <?php echo add_image(array('inactive.png'), '', '', array('title' => 'inactive', 'alt' => "inactive")); ?>&nbsp;Inactive
            </div>
            <div class="col-sm-9">&nbsp;</div>
        </div>
        <div class="con">
            <?php echo form_open(); ?>
            <div class="table-responsive">

                <?php if (!empty($permissions)) { ?>          
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" name="check_all" id="check_all" class="icheck">	
                                </th>
                                <th class="digits-col"><?php echo lang('no') ?></th>
                                <th class="name-col">
                                    <?php
                                    $field_sort_order = 'asc';
                                    $sort_image = 'srt_down.png';
                                    if ($sort_by == 'permission_title' && $sort_order == 'asc') {
                                        $sort_image = 'srt_up.png';
                                        $field_sort_order = 'desc';
                                    }
                                    ?>
                                    <a href="javascript:void(0)" onclick="sort_data('permission_title', '<?php echo $field_sort_order; ?>');" class="sort_field"><?php echo lang('permission-title') ?></a>
                                    <?php
                                    if ($sort_by == 'permission_title') {
                                        ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>

                        </th>
                        <th class="digits-col"><?php echo lang('permission-label') ?></th>
                        <th>

                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'status' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="javascript:void(0)" class="sort_field" onclick="sort_data('status', '<?php echo $field_sort_order; ?>');" ><?php echo lang('status') ?></a>
                            <?php
                            if ($sort_by == 'status') {
                                ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>

                        </th>
                        <th><?php echo lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>

                            <?php
                            if ($page_number > 1) {
                                $i = ($this->session->userdata[get_current_section($this)]['record_per_page'] * ($page_number - 1)) + 1;
                            } else {
                                $i = 1;
                            }
                            foreach ($permissions as $permission) {
                                if ($i % 2 != 0) {
                                    $class = "odd-row";
                                } else {
                                    $class = "even-row";
                                }
                                ?>
                                <tr class="<?php echo $class; ?> rows" id="row-<?php echo $permission['P']['id']; ?>">
                                    <td>
                                        <div class="ckbox ckbox-default">
                                            <input type="checkbox" id="<?php echo $permission['P']['id']; ?>" name="check_box[]" class="icheck check_box" value="<?php echo $permission['P']['id']; ?>"/>
<!--                                            <label for="<?php //echo $role['P']['id']; ?>"></label>-->
                                        </div>
                                    </td>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $permission['P']['permission_title']; ?></td>
                                    <td><?php echo $permission['P']['permission_label']; ?></td>
                                    <td><?php
                                        if ($permission['P']['status'] == 1) {
                                            echo add_image(array('active.png'), '', '', array('title' => 'active', 'alt' => "active"));
                                        } else {
                                            echo add_image(array('inactive.png'), '', '', array('title' => 'inactive', 'alt' => "inactive"));
                                        }
                                        ?></td>

                                    <td>
                                        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/view_data/<?php echo $permission['P']['id'] ?>" title="<?php echo lang('view'); ?>"><img src="images/search-icon-black.png" alt="<?php echo lang('view'); ?>"></a>
                                        <a href="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/action/edit/<?php echo $permission['P']['id']; ?>" title="<?php echo lang('edit'); ?>"><img src="images/edit-icon.png" alt="<?php echo lang('edit'); ?>"></a>
                                        <?php
                                        $permission_id = $permission['P']['id'];
                                        $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_permission($permission_id)'><img src='images/delete-icon.png' alt='Delete'></a>";
                                        echo $deletelink;
                                        ?>
                                    </td>
                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="btn-panel">
                    <button type="button" class="btn btn-custom btn-red" onclick="delete_records()"><?php echo lang('delete'); ?></button>
                    <button type="button" class="btn btn-custom btn-green" onclick="active_records()"><?php echo lang('active'); ?></button>
                    <button type="button" class="btn btn-custom btn-yellow" onclick="inactive_records()"><?php echo lang('inactive'); ?> </button>
                    <button type="button" class="btn btn-custom btn-green" onclick="active_all_records()"><?php echo lang('active-all'); ?></button>
                    <button type="button" class="btn btn-custom btn-yellow" onclick="inactive_all_records()"><?php echo lang('inactive-all'); ?></button>
                </div>

                <?php
                echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
                echo form_hidden('page_number', "", "page_number");
                echo form_hidden('per_page_result', "", "per_page_result");
                ?>
                <?php echo form_close(); ?>
                <!---buttons-->
            </div>
            <?php
        } else {
            ?>
            <div class="table-responsive">          		
                <table class="table table-striped" >
                    <tr>
                        <td><?php echo lang('no-records') ?></td>
                    </tr>
                </table>
            </div>
        <?php } ?>
        <?php
        $querystr = $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
        $options = array(
            'total_records' => $total_records,
            'page_number' => $page_number,
            'isAjaxRequest' => 1,
            'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "permissions/index",
            'params' => $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '',
            'element' => 'ajax_table'
        );
        widget('custom_pagination', $options);
        ?>
    </div>
</article>
<script>
    jQuery(document).ready(function() {
        $('.sort_field').click(function(e) {
            e.preventDefault();
        });

    });
</script> 

<script type="text/javascript">
    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $('#submit_search').trigger('click');
        }
    });
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
        if (val == "")
        {
            alert('Please select at least one record for delete');
            return false;
        }
        //blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
            success: function(data) {
                //for managing same state while record delete
                if ($('.rows') && $('.rows').length > 1) {
                    pageno = "&page_number=<?php echo $page_number; ?>";
                } else {
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";
                }
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
                //unblockUI();
            }
        });
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
        //blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
                //unblockUI();
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
        //blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
                //unblockUI();
            }
        });
    }

    function active_all_records()
    {
        //blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
                //unblockUI();
            }
        });
    }

    function inactive_all_records()
    {
        //blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
                //unblockUI();
            }
        });
    }

    function delete_permission(id) {

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            //blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/delete',
                data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', id: id},
                success: function(data) {

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                    //unblockUI();
                }
            });
        } else {
            return false;
        }
    }

    $('#submit_search').click(function(e) {
        e.preventDefault();
        if ($('#search_term').val() != '') {
            blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index',
                data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: $('#search_term').val()},
                success: function(data) {
                    $("#ajax_table").html(data);
                    unblockUI();
                }
            });
        }
        else {
            alert("Please enter something to search");
        }


    });

    function sort_data(sort_by, sort_order)
    {
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: $('#search_term').val(), sort_by: sort_by, sort_order: sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
        
    }
    function reset_data()
    {
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>permissions/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }
</script>