<article class="card" id="ajax_table">
    <div class="article-header">
        <?php echo lang('account-management'); ?>
    </div>
    <div class="card-wrap">

        <div class="table-responsive">          		
			<?php echo form_open(); ?>
            <table class="table table-striped">
                <thead>
                <tr>
                            <th class="digits-col"><?php echo lang('no') ?></th>
                            <th class="name-col">
                                <?php echo lang('hotel-name'); ?>
                            </th>
                            <th>
                                <?php echo lang('hotel-address'); ?>
                            </th>
                            <th>
                                <?php echo lang('hotel-phone'); ?>
                            </th>
                            <th><?php echo lang('actions') ?></th>
                        </tr>
                    </thead>
                    <?php
                    if (!empty($hotels)) {
                        ?>
                        <tbody>
                        <?php
                        if ($page_number > 1) {
                            $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                        } else {
                            $i = 1;
                        }
                        foreach ($hotels as $hotel) {
                            $hotel_id = $hotel['h']['id'];
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $hotel['h']['hotel_name']; ?></td>
                                <td><?php echo $hotel['h']['hotel_address']; ?></td>
                                <td><?php echo $hotel['h']['hotel_phone']; ?></td>
                                <td>
                                    <?php echo BASE_ADMIN_URL_CUSTOM;?>
                                    <a title="<?php echo lang('View') ?>" href="<?php echo site_url().BASE_ADMIN_URL_CUSTOM; ?>hotels/view_data/<?php echo $hotel_id; ?>"><?php echo add_image(array('search-icon-black.png'), '', '', array('title' => 'View', 'alt' => "View")); ?></a>
                                    <?php
                                    $edit_hotel_permission = check_user_permission_by_label('admin.hotels.edit');
                                    if($edit_hotel_permission){
                                    ?>
                                        <a href="<?php echo site_url().BASE_ADMIN_URL_CUSTOM; ?>hotels/edit/<?php echo $hotel_id ?>" title="<?php echo lang('Edit') ?>"><?php echo add_image(array('edit-icon.png'), '', '', array('title' => 'Edit', 'alt' => "Edit")); ?></a></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </tbody>
                <?php } else {
                    ?>
                    <tbody>
                    <tr>
                        <td colspan="7"><?php echo lang('no-records') ?></td>
                    </tr>
                    </tbody>
                    <?php
                }
                ?>
            </table>
			<?php echo form_close(); ?>
        </div>
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

        if (val == "" || val == "0")
        {
            alert('Please select at least one record for delete');
            return false;
        }

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
        if (val == "" || val == "0")
        {
            alert('Please select at least one record for active');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
        
        if (val == "" || val == "0")
        {
            alert('Please select at least one record for inactive');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function active_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function delete_hotel(id) {

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/delete',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', id: id},
                success: function(data) {



                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);

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
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val())},
            success: function(data) {
                console.log(data);
                $("#ajax_table").html(data);
                unblockUI();
            }
        });

    }

    function sort_data(sort_by, sort_order)
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index',
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
            url: '<?php echo BASE_ADMIN_URL_CUSTOM; ?>hotels/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }

</script>