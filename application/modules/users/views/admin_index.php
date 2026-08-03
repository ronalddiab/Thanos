<article class="card" id="ajax_table">
    <div class="article-header">
        <?php echo lang('user-management'); ?>
    </div>

    <div class="card-wrap">

        <div class="row site-controls-outer users-list-container">
            <div class="col-sm-12 col-lg-9">
                <form class="default-form form-inline clearfix" method="post">
                    <div class="form-group form-control-outer">
                        <input type="text" name="search_term_firstname" id="search_term_firstname" class="form-control" placeholder="<?php echo lang('search-by-firstname') ?>" value="<?php echo isset($search_term_firstname) && !empty($search_term_firstname) ? htmlspecialchars_decode($search_term_firstname) : ''; ?>">
                    </div>
                    <div class="form-group form-control-outer" style="display: none;">
                        <input type="text" name="search_term_username" id="search_term_username" class="form-control" placeholder="<?php echo lang('search-by-username') ?>" value="<?php echo isset($search_term_username) && !empty($search_term_username) ? htmlspecialchars_decode($search_term_username) : ''; ?>">
                    </div>
                   
                    <div class="form-group form-control-outer">
                        <div class="form-group" style="width: 171px; margin-top: 10px;">
                            <select name="search_site" id="search_site" class="form-control">
                                <option value="">Select Site</option>
                                <?php 
                                foreach ($hotel_sites as $value) {
                                    $selected = ($search_site == $value['id']) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $value['id']; ?>" <?php echo  $selected; ?> ><?php echo $value['site_location_name']; ?></option>
                                    <?php
                                 } ?>
                            </select>
                        </div>
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
            <?php $add_user_permission = check_user_permission_by_label('admin.users.action.add'); ?>
            <?php if ($add_user_permission) { ?>
                <div class="col-sm-3">

                </div>
            <?php } ?>
        </div>
        <div class="row helprow">
            <div class="col-sm-5 col-xs-7">
                <?php echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "Active")); ?>&nbsp;Active
                <?php echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "Inactive")); ?>&nbsp;Inactive
            </div>
            <div class="col-sm-7 col-xs-5">
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'users/action/add' ?> " title="<?php lang('add-user') ?>" class="btn btn-blue pull-right"><span><img src="images/plus-icon.png" alt="Add"></span><?php echo lang('add-user'); ?></a>

            </div>
        </div>
        <div class="con">
            <div class="table-responsive"> 
                <?php if (!empty($users)) { ?>            
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
                                    if ($sort_by == 'u.firstname' && $sort_order == 'asc') {
                                        $sort_image = 'srt_up.png';
                                        $field_sort_order = 'desc';
                                    }
                                    ?>
                                    <a href="javascript:void(0)" onclick="sort_data('u.firstname', '<?php echo $field_sort_order; ?>');" ><?php echo lang('first-name') ?></a>
                                    <?php
                                    if ($sort_by == 'u.firstname') {
                                        ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>
                        </th>
                        <th class="name-col">
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'u.username' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="javascript:vois(0)" onclick="sort_data('u.username', '<?php echo $field_sort_order; ?>');" ><?php echo lang('user-name') ?></a>
                            <?php
                            if ($sort_by == 'u.username') {
                                ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>
                        </th>
                        <th class="">
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'u.email' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="javascript:void(0)" onclick="sort_data('u.email', '<?php echo $field_sort_order; ?>');" ><?php echo lang('email') ?></a>
                            <?php
                            if ($sort_by == 'u.email') {
                                ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>
                        </th>
                        <th class="">
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'r.role_name' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="javascript:void(0)" onclick="sort_data('r.role_name', '<?php echo $field_sort_order; ?>');" ><?php echo lang('role') ?></a>
                            <?php
                            if ($sort_by == 'r.role_name') {
                                ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php } ?>
                        </th>
                        <th class="">
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 's.site_location_name' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="javascript:void(0)" onclick="sort_data('s.site_location_name', '<?php echo $field_sort_order; ?>');" ><?php echo lang('site') ?></a>
                            <?php
                            if ($sort_by == 's.site_location_name') {
                                ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>
                        </th>
                        <th><?php echo lang('permission') ?></th>
                        <th>
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'u.status' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="javascript:void(0)" onclick="sort_data('u.status', '<?php echo $field_sort_order; ?>');" ><?php echo lang('status') ?></a>
                            <?php
                            if ($sort_by == 'u.status') {
                                ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>
                        </th>
                        <th class="actions-col"><?php echo lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($page_number > 1) {
                                $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                            } else {
                                $i = 1;
                            }
                            foreach ($users as $user) {
                                if ($i % 2 != 0) {
                                    $class = "odd-row";
                                } else {
                                    $class = "even-row";
                                }
                                $user_id = $user['u']['id'];
                                $role_id = $user['u']['role_id'];
                                ?>

                                <tr class="<?php echo $class; ?> rows" id="row-<?php echo $user['u']['id']; ?>">
                                    <td>
                                        <?php
                                        //if ($user_id != 1 && $user_id != $this->_ci->session->userdata[$this->_data['section_name']]['user_id']) { 
                                        if ($role_id != 1) {
                                            ?>
                                            <div class="ckbox ckbox-default">
                                                <input type="checkbox" id="<?php echo $user['u']['id']; ?>" name="check_box[]" class="icheck check_box" value="<?php echo base64_encode($user['u']['id']); ?>">
            <!--                                                <label for="<?php //echo $user['u']['id']; ?>"></label>-->
        <?php } ?>
                                        </div>

                                    </td>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $user['u']['firstname']; ?></td>
                                    <td><?php echo $user['u']['username']; ?></td>
                                    <td><?php echo $user['u']['email']; ?></td>
                                    <td><?php echo $user['r']['role_name']; ?></td>
                                    <td><?php echo $user['s']['site_location_name']; ?></td>
                                    <td>
                                        <?php
                                        if ($role_id != 1) {
                                            ?>
                                            <?php echo anchor(site_url() . BASE_ADMIN_URL_CUSTOM . 'roles/user_permission_matrix/' . $user_id, lang('view-permission'), 'title="View Permission" style="text-align:center;width:100%;"'); ?>
        <?php } ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($user['u']['status'] == 1) {
                                            echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "Active"));
                                        } else {
                                            echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "Inactive"));
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM ?>users/view_data/<?php echo $user_id; ?>" title="View"><img src="images/search-icon-black.png" alt="<?php echo lang('view'); ?>"></a>
                                        <?php $edit_user_permission = check_user_permission_by_label('admin.users.action.edit'); ?>
                                        <?php if ($edit_user_permission) { ?>
                                            <a href="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/action/edit/<?php echo $user_id; ?>" title="<?php echo lang('edit'); ?>"><img src="images/edit-icon.png" alt="<?php echo lang('edit'); ?>"></a>
                                        <?php } ?>
                                        <?php
                                        $delete_user_permission = check_user_permission_by_label('admin.users.delete');

                                        $encrypted_id = base64_encode($user_id);
                                        $deletelink = "<a href='javascript:void(0);' title='Delete' onclick='delete_user(\"$encrypted_id\")'><img src='images/delete-icon.png' alt='Delete'></a>";
                                        // can delete only visitor
                                        if ($role_id != 1 && $delete_user_permission) {
                                            ?>
                                            <?php echo $deletelink ?>
        <?php } ?>
                                    </td>
                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php if($_SESSION['admin']['role_id'] == 1) { ?>
                <div class="btn-panel">
                    <button type="button" class="btn btn-custom btn-red" onclick="delete_records()"><?php echo lang('delete'); ?></button>
                    <button type="button" class="btn btn-custom btn-green" onclick="active_records()"><?php echo lang('active'); ?></button>
                    <button type="button" class="btn btn-custom btn-yellow" onclick="inactive_records()"><?php echo lang('inactive'); ?> </button>
                    <button type="button" class="btn btn-custom btn-green" onclick="active_all_records()"><?php echo lang('active-all'); ?></button>
                    <button type="button" class="btn btn-custom btn-yellow" onclick="inactive_all_records()"><?php echo lang('inactive-all'); ?></button>
                </div>
                <?php } ?>
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
        $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term_firstname=' . urlencode($search_term_firstname) . '&search_term_username=' . urlencode($search_term_username) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '&search_site=' . urlencode($search_site) . '';
        $options = array(
            'total_records' => $total_records,
            'page_number' => $page_number,
            'isAjaxRequest' => 1,
            'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "users/index",
            'params' => $querystr,
            'element' => 'ajax_table'
        );
        widget('custom_pagination', $options);
        ?>
    </div>
</article>

<script>
    jQuery(document).ready(function() {
        // Chosen Select
//        jQuery("select").chosen({
//            'min-width': '100px',
//            'white-space': 'nowrap',
//            disable_search_threshold: 10
//        });
    });
</script> 


<script type="text/javascript">
    //remove dynamically populate error
    $("#search_term_firstname, #search_term_username").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $('#submit_search').trigger('click');
        }
    });
    function attach_error_event() {
        $('div.formError').bind('click', function() {
            $(this).fadeOut(1000, removeError);
        });
    }
    function removeError()
    {
        jQuery(this).remove();
    }
    $(function() {
//        $('#check_all').on('ifChecked', function(event) {
//            $('.check_box').parent('div').addClass('checked');
//        });
//
//        $('#check_all').on('ifUnchecked', function(event) {
//            $('.check_box').parent('div').removeClass('checked');
//        });
//        $('.check_box').on('ifUnchecked', function(event) {
//            $('#check_all').iCheck('uncheck');
//        });

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

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            //blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {
//                    alert(data); return;

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                    //unblockUI();
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
        //blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
                //unblockUI();
            }
        });
    }

    function delete_user(id) {

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            //blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/delete',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', id: id},
                success: function(data) {



                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);

                    //set responce message
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
        if ($('#search_term_firstname').val() != '' || $('#search_term_username').val() != '' || $('#search_site').val() != '') {
            blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term_firstname: $('#search_term_firstname').val(), search_term_username: $('#search_term_username').val(), search_site: $('#search_site').val()},
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
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term_firstname: encodeURIComponent($('#search_term_firstname').val()), search_term_username: encodeURIComponent($('#search_term_username').val()), sort_by: sort_by, sort_order: sort_order},
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>users/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term_firstname: "", search_term_username: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }

</script>