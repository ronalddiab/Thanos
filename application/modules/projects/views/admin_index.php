<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>
<article id="ajax_table" class="card">
    <div class="article-header"><?php echo 'Sub Category & EMAs'; ?></div>

    <div class="card-wrap">
        <div class="row site-controls-outer projects-list-container1">
            <div class="col-sm-9">
                <form class="default-form  clearfix" method="post">
                    <div class="row search-row">
                        <div class="form-group form-control-outer col-sm-1">
                            <input id="search_term" name="search_term" type="text" placeholder="<?php echo lang('search-by-project-name') ?>" class="form-control" value="<?php echo (!empty($search_term)) ? $search_term : ''; ?>">
                        </div>
                        <div class="form-group col-sm-4">
                            <button type="submit" class="btn btn-secondary" id="submit_search">
                                <img alt="Search" src="images/search-icon.png">
                                <?php echo lang('btn-search'); ?> 
                            </button>

                            <button type="submit" class="btn btn-reset" id="reset_data">
                                <img alt="Reset" src="images/reset-icon.png">
                                <?php echo lang('btn-reset'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-3">                
                <a class="btn btn-blue pull-right" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/edit"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo lang('add-project'); ?></a>
            </div>
        </div>

        <?php if (!empty($data['projects'])) { ?>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="digits-col">Sr. No.</th>
                            <th>Category</th>
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'project_name' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <th class="name-col"><?php echo lang('project-name') ?></th>
                            <!-- <th>Hotel</th> -->
                            <?php /* $site_filter_role_allow = array(1, 2);
                              if (in_array($role_id, $site_filter_role_allow)) {
                              ?>
                              <th>Site</th>
                              <?php } */ ?>
                            <th>EMA Name</th>
                            <th>Color</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($page_number > 1) {
                            $srinc = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                        } else {
                            $srinc = 1;
                        }

                        foreach ($data['projects'] as $key => $project) {
                            ?>
                            <tr>                                
                                <td><?php echo $srinc; //$key+1;//$project['p']['id'];    ?></td>
                                <td><?php echo $project['pc']['name']; ?></td>                        
                                <td><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/actionlist/' . $project['p']['id']; ?>"><?php echo $project['p']['project_name']; ?></a></td>
                                <!-- <td><?php echo $project['h']['hotel_name']; ?></td> -->
                                <?php /* $site_filter_role_allow = array(1, 2);
                                  if (in_array($role_id, $site_filter_role_allow)) {
                                  ?>
                                  <td><?php echo isset($project['aps']['site_location_name']) && trim($project['aps']['site_location_name']) != "" ? $project['aps']['site_location_name'] : $project['s']['site_location_name']; ?></td>
                                  <?php } */ ?>
                                <td> <?php echo!empty($project['pdi']['ema_todo_name']) ? $project['pdi']['ema_todo_name'] : ''; ?></td>
                                <td <?php echo (!empty($project['pdi']['ema_todo_color'])) ? 'style="background-color:' . $project['pdi']['ema_todo_color'] . '"' : ''; ?>></td>
                                <td>
                                    <a title="View" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/view/<?php echo $project['p']['id']; ?>"><img alt="Search" src="images/search-icon-black.png"></a>
                                    <?php
                                    $edit_project_permission = check_user_permission_by_label('admin.projects.edit');
                                    if ($edit_project_permission) {
                                        ?>
                                        <a title="Edit" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/edit/<?php echo $project['p']['id']; ?>"><img alt="Edit" src="images/edit-icon.png"></a>
                                    <?php } ?>
                                    <a title="Delete" href="javascript:void(0);" onclick="single_delete(<?php echo $project['pdi']['pdi_id']; ?>,<?php echo $project['p']['id']; ?>)"><img alt="Delete" src="images/delete-icon.png"></a>
                                </td>
                            </tr>
                            <?php
                            $srinc++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <?php
            if (!empty($data['projects'])) {
                $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order;
                $options = array(
                    'total_records' => $total_records,
                    'page_number' => $page_number,
                    'isAjaxRequest' => 1,
                    'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "projects/index/" . $id,
                    'params' => $querystr,
                    'element' => 'ajax_table'
                );

                widget('custom_pagination', $options);
            }
            ?>

        <?php } else {
            ?>
            <div class="table-responsive">                  
                <table class="table table-striped" >
                    <tr>
                        <td><?php echo lang('no-records') ?></td>
                    </tr>
                </table>
            </div>
        <?php }
        ?>

    </div>
</article>

<script type="text/javascript">
    function single_delete(id, pid) {
        var val = [];
        val[0] = id;

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            var delete_cat = 1;
            var total_cat_data = 0;
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'check_delete', pid: pid, ids: val},
                success: function(data) {
                    if (data == '0') {
                        res = confirm('<?php echo 'There is no more Action left once you delete this. Are you sure want to delete this Category too ?'; ?>');
                        if (!res) {
                            delete_cat = 0;
                        }
                        total_cat_data = pid;
                    }

                    if (delete_cat == '1') {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index',
                            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val, catdata: total_cat_data},
                            success: function(data) {
                                //for managing same state while record delete
                                if ($('.rows') && $('.rows').length > 1) {
                                    pageno = "&page_number=<?php echo $page_number; ?>";
                                } else {
                                    pageno = "&page_number=<?php echo $page_number - 1; ?>";
                                }
                                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                                $("#messages").show();
                                $("#messages").html(data);
                            }
                        });
                    }
                }
            });
        } else {
            return false;
        }
    }

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
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                }
            });
        } else {
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function active_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    jQuery(document).ready(function() {
        $("select[data-type='custom-dropdown']").dropkick({
            mobile: true
        });

        jQuery("#submit_search").click(function(event) {
            event.preventDefault();
            var search_term = jQuery('#search_term').val();
            if (search_term != '') {
                blockUI();
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/index',
                    data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: search_term},
                    success: function(data) {
                        jQuery("#ajax_table").html(data);
                        unblockUI();
                    }
                });
            } else {
                alert("Please enter something to search");
            }
        });

        jQuery("#filter_category_id").change(function(event) {
            event.preventDefault();
            var search_term = jQuery('#search_term').val();
            var sort_by = jQuery(this).attr('data-sort-by');
            var sort_order = jQuery(this).attr('data-sort-order');
            var filter_category_id = jQuery('#filter_category_id').val();
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: search_term, sort_by: sort_by, sort_order: sort_order, filter_category_id: filter_category_id},
                success: function(data) {
                    jQuery("#ajax_table").html(data);
                    unblockUI();
                }
            });
        });

        jQuery("#filter_region_id").change(function(event) {
            event.preventDefault();
            var search_term = jQuery('#search_term').val();
            var sort_by = jQuery(this).attr('data-sort-by');
            var sort_order = jQuery(this).attr('data-sort-order');
            var filter_region_id = jQuery('#filter_region_id').val();
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: search_term, sort_by: sort_by, sort_order: sort_order, filter_region_id: filter_region_id},
                success: function(data) {
                    jQuery("#ajax_table").html(data);
                    unblockUI();
                }
            });
        });

        jQuery("#filter_site_id").change(function(event) {
            event.preventDefault();
            var search_term = jQuery('#search_term').val();
            var sort_by = jQuery(this).attr('data-sort-by');
            var sort_order = jQuery(this).attr('data-sort-order');
            var filter_site_id = jQuery('#filter_site_id').val();
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: search_term, sort_by: sort_by, sort_order: sort_order, filter_site_id: filter_site_id},
                success: function(data) {
                    jQuery("#ajax_table").html(data);
                    unblockUI();
                }
            });
        });

        jQuery("#sort_data").click(function(event) {
            event.preventDefault();
            var search_term = jQuery('#search_term').val();
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: search_term, sort_by: sort_by, sort_order: sort_order},
                success: function(data) {
                    jQuery("#ajax_table").html(data);
                    unblockUI();
                }
            });

        });

        jQuery("#reset_data").click(function(event) {
            event.preventDefault();
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: "", filter_category_id: 0, filter_region_id: 0, filter_site_id: ''},
                success: function(data) {
                    jQuery("#ajax_table").html(data);
                    unblockUI();
                }
            });
        });

        jQuery(".sort_data").click(function() {
            var sort_by = jQuery(this).attr('data-sort-by');
            var sort_order = jQuery(this).attr('data-sort-order');
            blockUI();
            jQuery.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: $('#search_term').val(), sort_by: sort_by, sort_order: sort_order},
                success: function(data) {
                    jQuery("#ajax_table").html(data);
                    unblockUI();
                }
            });
        });
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

</script>