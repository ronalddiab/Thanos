<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>
<article id="ajax_table" class="card">
    <div class="article-header"> <?php echo lang('moderators'); ?> </div>
    <div class="card-wrap">
        <div class="row site-controls-outer">
            <div class="col-sm-9">
                <form class="default-form form-inline clearfix" method="post">
                        <div class="form-group form-control-outer">
                            <input id="search_term" name="search_term" type="text" placeholder="<?php echo lang('search-by-firstname') ?>" class="form-control" value="<?php echo (!empty($search_term))?$search_term:''; ?>">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-secondary" id="submit_search">
                                <img alt="Search" src="images/search-icon.png">
                                <?php echo lang('btn-search'); ?> 
                            </button>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-reset" id="reset_data">
                                <img alt="Reset" src="images/reset-icon.png">
                                <?php echo lang('btn-reset'); ?>
                            </button>
                        </div>
                </form>
            </div>
            <div class="col-sm-3">
                    <a class="btn btn-blue pull-right" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/action_moderator"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo lang('add-moderator'); ?></a>
            </div>
        </div>
        <?php if (!empty($users)) { ?>
        <div class="row helprow">
            <div class="col-sm-3">
            <?php echo add_image(array('active.png'), '', '', array('title' => 'active', 'alt' => "active")); ?>&nbsp;Active
            <?php echo add_image(array('inactive.png'), '', '', array('title' => 'inactive', 'alt' => "inactive")); ?>&nbsp;Inactive
            </div>
            <div class="col-sm-9">&nbsp;</div>
        </div>

        <div class="table-responsive">
				<?php echo form_open(); ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" name="check_all" id="check_all" value="0" class="icheck">
                            </th>
                            <th class="digits-col"><?php echo lang('no') ?></th>
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'firstname' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <th>
                                <a href="javascript:void(0)" class="sort_data" data-sort-by="firstname" data-sort-order="<?php echo $field_sort_order; ?>" ><?php echo lang('firstname'); ?></a>
                                <?php if ($sort_by == 'firstname') { ?>
                                    <div class="sorting">
                                        <?php echo add_image(array($sort_image)); ?>
                                    </div>
                                <?php }  ?>
                            </th>

                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'username' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <th>
                                <a href="javascript:void(0)" class="sort_data" data-sort-by="username" data-sort-order="<?php echo $field_sort_order; ?>" ><?php echo lang('username'); ?></a>
                                <?php if ($sort_by == 'username') { ?>
                                    <div class="sorting">
                                        <?php echo add_image(array($sort_image)); ?>
                                    </div>
                                <?php }  ?>
                            </th>

                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'email' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <th>
                            <a href="javascript:void(0)" class="sort_data" data-sort-by="email" data-sort-order="<?php echo $field_sort_order; ?>" ><?php echo lang('email'); ?></a>
                            <?php if ($sort_by == 'email') { ?>
                                    <div class="sorting">
                                        <?php echo add_image(array($sort_image)); ?>
                                    </div>
                                <?php }  ?>
                            </th>
                            
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'status' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <th>
                                <a href="javascript:void(0)" class="sort_data" data-sort-by="status" data-sort-order="<?php echo $field_sort_order; ?>" ><?php echo lang('status') ?></a>
                                <?php if ($sort_by == 'status') { ?>
                                    <div class="sorting">
                                        <?php echo add_image(array($sort_image)); ?>
                                    </div>
                                <?php }  ?>
                            </th>
                            <th><?php echo lang('permissions'); ?></th>
                            <th><?php echo lang('actions'); ?></th>
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
                            <tr class="<?php echo $class; ?> rows" >
                                <td>

                                    <?php if ($role_id != 1 && $user_id != $this->_ci->session->userdata[$this->_data['section_name']]['user_id']) {

                                        ?>
                                        <input class="icheck check_box" type="checkbox" id="<?php echo $user['u']['id']; ?>" name="check_box[]" value="<?php echo base64_encode($user['u']['id']); ?>">
                                    <?php } ?>
                                </td>
                                <td align="center"><?php echo $i; ?></td>
                                <td><?php echo $user['u']['firstname']; ?></td>
                                <td><?php echo $user['u']['username']; ?></td>
                                <td><?php echo $user['u']['email']; ?></td>
                                <td style="text-align:center;">
                                    <?php
                                    if ($user['u']['status'] == 1) {
                                        echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "Active"));
                                    } else {
                                        echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "Inactive"));
                                    }
                                    ?>
                                </td>
                                <td style="text-align:center;">
                                    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/set_permission/<?php echo $user_id; ?>"><?php echo lang('view'); ?></a>
                                </td>
                                <td>



                                    <?php if ($this->_ci->session->userdata[$this->_data['section_name']]['user_id'] == 1 || $role_id != 1) {

                                            ?>
                                    <a title="Edit" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/action_moderator/<?php echo $user_id ?>"><img alt="Edit" src="images/edit-icon.png"></a>
                                    <?php } ?>



                                    <?php if ($role_id != 1 && $user_id != $this->_ci->session->userdata[$this->_data['section_name']]['user_id']) {

                                    ?>
                                    <?php
                                    $encrypted_id = base64_encode($user_id);

                                    $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_user(\"$encrypted_id\")'>" . add_image(array('delete-icon.png')) . "</a>";
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
				<?php echo form_close(); ?>
        </div>
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

        <?php
        $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash());
        $options = array(
            'total_records' => $total_records,
            'page_number' => $page_number,
            'isAjaxRequest' => 1,
            'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "forum/moderator_list",
            'params' => $querystr,
            'element' => 'ajax_table'
        );

        widget('custom_pagination', $options);
        ?>
        <?php } else { ?>
            <div class="table-responsive">                  
                <table class="table table-striped" >
                    <tr>
                        <td><?php echo lang('no-records') ?></td>
                    </tr>
                </table>
            </div>
            <?php
            $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash());
        } ?>
    </div>
</article>

<script type="text/javascript">

    //remove dynamically populate error
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
        $("#reg_date").datepicker({dateFormat: 'yy-mm-dd'});
        
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
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {
//                    alert(data); return;

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/delete',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', id: id},
                success: function(data) {



                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/moderator_list', 'ajax_table', '<?php echo $querystr; ?>' + pageno);

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

    jQuery(document).ready(function(){
        $("#menu1").children().removeClass("active");
        $("#menu311").children().removeClass("active");
        $("#menu2").children().removeClass("active");
        $("#menu200").children().removeClass("active");

        jQuery("#submit_search").click(function(event){
            event.preventDefault();
            var search_term = jQuery('#search_term').val();
            if (search_term != '') {
                blockUI();
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>forum/moderator_list',
                    data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: search_term},
                    success: function(data) {
                        jQuery("#ajax_table").html(data);
                        unblockUI();
                    }
                });
            }else{
                alert("Please enter something to search");
            }
        });

        jQuery("#sort_data").click(function(event){
            event.preventDefault();
            var search_term = jQuery('#search_term').val();
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>forum/moderator_list',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: search_term, sort_by: sort_by, sort_order: sort_order},
                success: function(data) {
                    jQuery("#ajax_table").html(data);
                    unblockUI();
                }
            });
            
        });

        jQuery("#reset_data").click(function(event){
            event.preventDefault();
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>forum/moderator_list',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: ""},
                success: function(data) {
                    jQuery("#ajax_table").html(data);
                    unblockUI();
                }
            });
        });

        jQuery(".sort_data").click(function(){
            var sort_by = jQuery(this).attr('data-sort-by');
            var sort_order = jQuery(this).attr('data-sort-order');
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>forum/moderator_list',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: $('#search_term').val(), sort_by: sort_by, sort_order: sort_order},
                success: function(data) {
                   jQuery("#ajax_table").html(data);
                   unblockUI();
                }
            });
        });

        /*$("#search_term").keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                submit_search();
            }
        });*/
    });

</script>