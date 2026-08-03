<article class="card" id="ajax_table">
    <div class="article-header">
        <?php echo lang('site-management'); ?>
        <?php echo anchor(BASE_ADMIN_URL_CUSTOM . 'sites/cron_settings', 'CRON Management', 'class="btn btn-blue pull-right"'); ?>
    </div>
    <div class="card-wrap">
        <div class="row site-controls-outer">
            <div class="col-sm-12 col-lg-9">
                <form class="default-form form-inline clearfix">
                    <div class="form-group form-control-outer">
                        <?php
                        $input_data = array(
                            'name' => 'search_term',
                            'id' => 'search_term',
                            'class' => 'form-control',
                            'placeholder' => lang('search-by-loaction-name'),
                            'value' => set_value('search_term', urldecode($search_term))
                        );
                        echo form_input($input_data);
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        $search_button = array(
                            'content' => add_image(array('search-icon.png')) . ' ' . lang('btn-search'),
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
            <?php $add_site_permission = check_user_permission_by_label('admin.sites.add'); ?>
            <?php if($add_site_permission){ ?>
            <div class="col-lg-3">
                <?php echo anchor(BASE_ADMIN_URL_CUSTOM . 'sites/add', '<span>' . add_image(array('plus-icon.png')) . '</span> ' . lang('add-site'), 'class="btn btn-blue pull-right"'); ?>
            </div>
            <?php } ?>
        </div>

        <?php if (!empty($sites)) { ?>
        <div class="row helprow">
            <div class="col-sm-5 col-xs-7">
                <?php echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "Active")); ?>&nbsp;Active
                <?php echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "Inactive")); ?>&nbsp;Inactive
            </div>
        </div>

        <div class="table-responsive">          		
			<?php echo form_open(); ?>
            <table class="table table-striped">
                
                <thead>
                    <?php
                    if (!empty($sites)) {
                        $site_set_notification_permission = check_user_permission_by_label('admin.sites.set_notification');
                        ?>
                        <tr>
                            <th><input type="checkbox" name="check_all" id="check_all" value="0" class="icheck"></th>
                            <th class="digits-col"><?php echo lang('no') ?></th>
                            <th>
                                <?php
                                $field_sort_order = 'asc';
                                $sort_image = 'srt_down.png';
                                if ($sort_by == 's.site_location_name' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="javascript:void(0)" onclick="sort_data('s.site_location_name', '<?php echo $field_sort_order; ?>');" >
                                    <?php echo lang('site-location-name'); ?>
                                    <?php
                                    if ($sort_by == 's.site_location_name') {
                                        ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                    <?php }
                                    ?>
                                </a>
                            </th>
                            <th class="name-col">
                                <?php
                                $field_sort_order = 'asc';
                                $sort_image = 'srt_down.png';
                                if ($sort_by == 'h.hotel_name' && $sort_order == 'asc') {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="javascript:void(0)" onclick="sort_data('h.hotel_name', '<?php echo $field_sort_order; ?>');" >
                                    <?php echo lang('hotel-name'); ?>
                                    <?php
                                    if ($sort_by == 'h.hotel_name') {
                                        ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                    <?php }
                                    ?>
                                </a>
                            </th>
                            <th><?php echo lang('region') ?></th>
                            <th><?php echo lang('country') ?></th>
                            <th><?php echo lang('status') ?></th>
                            <?php if($site_set_notification_permission){ ?>
                                <th><?php echo lang('notification') ?></th>
                            <?php } ?>
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
                        foreach ($sites as $site) {
                            $site_id = $site['s']['id'];
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" id="<?php echo $site['s']['id']; ?>" name="check_box[]" class="icheck check_box" value="<?php echo $site['s']['id']; ?>">
                                </td>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $site['s']['site_location_name']; ?></td>
                                <td><?php echo $site['h']['hotel_name']; ?></td>
                                <td><?php echo $site['r']['region_name']; ?></td>
                                <td><?php echo $site['c']['country']; ?></td>
                                <td>
                                    <?php
                                    if ($site['s']['status'] == 1) {
                                        echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "Active"));
                                    } else {
                                        echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "Inactive"));
                                    }
                                    ?>
                                </td>
                                <?php if($site_set_notification_permission){ ?>
                                <td>
                                    <a href="<?php echo site_url().BASE_ADMIN_URL_CUSTOM; ?>sites/set_notification/<?php echo $site_id ?>"><?php echo lang('set-notification'); ?></a>
                                </td>
                                <?php } ?>
                                <td>
                                    <a title="View" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/view_data/<?php echo $site_id; ?>"><?php echo add_image(array('search-icon-black.png')); ?></a>
                                    <?php
                                    $edit_site_permission = check_user_permission_by_label('admin.sites.edit');
                                    if($edit_site_permission){
                                    ?>
                                        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/edit/<?php echo $site_id ?>" title="<?php echo lang('edit') ?>"><?php echo add_image(array('edit-icon.png')); ?></a></div>
                                    <?php } ?>
                                    <?php
                                    $encrypted_id = base64_encode($site_id);

                                    $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_site(\"$encrypted_id\")'>" . add_image(array('delete-icon.png')) . "</a>";
                                    ?>
                                    <?php echo $deletelink ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } 
                ?>
                </tbody>
			</table>
			<?php echo form_close(); ?>
        </div>
        <?php }else{
            ?>
            <div class="table-responsive">                  
                <table class="table table-striped" >
                    <tr>
                        <td><?php echo lang('no-records') ?></td>
                    </tr>
                </table>
            </div>
            <?php
        } ?>

        <?php
        if (!empty($data['sites'])) {
            ?>
            <div class = "btn-panel">
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
                   // 'title' => lang('active-all'),
                    'class' => 'btn btn-custom btn-green',
                    'onclick' => "active_all_records()",
                );
                echo form_button($reset_button);
                ?>
                <?php
                $reset_button = array(
                    'content' => lang('inactive-all'),
                   // 'title' => lang('inactive-all'),
                    'class' => 'btn btn-custom btn-yellow',
                    'onclick' => "inactive_all_records()",
                );
                echo form_button($reset_button);
                ?>
            </div>
            <?php
            $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order;
            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "sites/index/" . $id,
                'params' => $querystr,
                'element' => 'ajax_table'
            );

            widget('custom_pagination', $options);
        } ?>
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
    function removeError()
    {
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

        if (val == "")
        {
            alert('Please select at least one record for delete');
            return false;
        }

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {
                    //                    alert(data); return;

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    updatesitelist();
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
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                updatesitelist();
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
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                updatesitelist();
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function active_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                updatesitelist();
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                updatesitelist();
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function delete_site(id) {

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/delete',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', id: id},
                success: function(data) {



                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    updatesitelist();
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
        if ($('#search_term').val() != '' || $('#search_term').val() != '') {
            blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val())},
                success: function(data) {
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
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index',
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
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }

    //updatesitelist();
    function updatesitelist(){
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>sites',
            data: {type:'get_all_sites'},
            dataType:'json',
            success: function(json) {
                if(json.count > 0){
                    site_html = '<div class="custom-dropdown"><select id="selectsite" data-type="custom-dropdown-update">';
                    $.each(json.sites, function (key, data) {
                        site_html += '<option value="'+data.s.id+'">'+data.s.site_location_name+'</option>';
                    });
                    site_html += '</select></div>';
                    $("#siteselectionlink").html(site_html);
                    $("select[data-type='custom-dropdown-update']").dropkick({
                        mobile: true
                    });
                    $("#selectsitelistcontainer").show();
                }else{
                    $("#selectsitelistcontainer").hide();
                }
            }
        });
    }

</script>