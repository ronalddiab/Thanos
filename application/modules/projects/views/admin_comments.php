<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>
<article id="ajax_table" class="card">
    <div class="article-header"><?php echo lang("project-comment-management"); ?></div>
    
    <div class="card-wrap">
        <div class="row site-controls-outer">
            <div class="col-sm-9">
                <form class="default-form form-inline clearfix" method="post">
                        <div class="form-group form-control-outer">
                            <input id="search_term" name="search_term" type="text" placeholder="<?php echo lang('search-by-comment') ?>" class="form-control" value="<?php echo (!empty($search_term))?$search_term:''; ?>">
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
                    <a class="btn btn-blue pull-right" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comment_edit"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo lang('add-comment'); ?></a>
            </div>
        </div>

        <?php if(!empty($data['comments'])){ ?>
        <div class="row helprow">
            <div class="col-sm-3">
            <?php echo add_image(array('active.png'), '', '', array('title' => 'active', 'alt' => "active")); ?>&nbsp;Active
            <?php echo add_image(array('inactive.png'), '', '', array('title' => 'inactive', 'alt' => "inactive")); ?>&nbsp;Inactive
            </div>
            <div class="col-sm-9">&nbsp;</div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <input id="check_all" type="checkbox" class="icheck">	
                        </th>
                        <th class="digits-col"><?php echo lang('id'); ?></th>
                        <th class="name-col">
                            <?php echo lang('user'); ?>
                        </th>
                        <th class="name-col">
                            <?php echo lang('project'); ?>
                        </th>
                        <th class="name-col">
                            <?php echo lang('comment'); ?>
                        </th>
                        <th>
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'status' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="#" class="sort_data" data-sort-by="status" data-sort-order="<?php echo $field_sort_order; ?>" ><?php echo lang('status') ?></a>
                            <?php if ($sort_by == 'status') { ?>
                                <div class="sorting">
                                    <?php echo add_image(array($sort_image)); ?>
                                </div>
                            <?php }  ?>
                        </th>
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
                    if(!empty($data['comments'])){
                        foreach($data['comments'] as $key=>$comment){
                            ?>
                            <tr>
                                <td><input type="checkbox" name="check_box[]" value="<?php echo $comment['c']['id']; ?>" class="icheck check_box"></td>
                                <td><?php echo $srinc;//$key+1; ?></td>
                                <td><?php echo $comment['u']['firstname'].' '.$comment['u']['lastname']; ?></td>
                                <td><?php echo $comment['p']['project_name']; ?></td>
                                <td><?php echo $comment['c']['comments']; ?></td>
                                <td><?php 
                                    if ($comment['c']['status'] == 1) {
                                        echo add_image(array('active.png'), '', '', array('title' => 'active', 'alt' => "active"));
                                    } else {
                                        echo add_image(array('inactive.png'), '', '', array('title' => 'inactive', 'alt' => "inactive"));
                                    }
                                ?></td>
                                <td>
                                    <a title="Search" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comment_view/<?php echo $comment['c']['id']; ?>"><img alt="Search" src="images/search-icon-black.png"></a>
                                    <a title="Edit" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comment_edit/<?php echo $comment['c']['id']; ?>"><img alt="Edit" src="images/edit-icon.png"></a>
                                    <a title="Delete" href="#" onclick="single_delete(<?php echo $comment['c']['id']; ?>)"><img alt="Delete" src="images/delete-icon.png"></a>
                                </td>
                            </tr>
                            <?php
                            $srinc++;
                        }
                    }else{
                        ?>
                        <tr>
                            <td colspan="7">
                                No Record Found
                            </td>
                        </tr>
                        <?php
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
        if(!empty($data['comments'])){
            $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order;
            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "projects/comments/".$id,
                'params' => $querystr,
                'element' => 'ajax_table'
            );

            widget('custom_pagination', $options);
        }

        ?>
        <?php } ?>
    </div>
</article>

<script type="text/javascript">
    function single_delete(id){
        var val = [];
        val[0] = id;

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete','subtype':'category', ids: val},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
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
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete','subtype':'category', ids: val},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active','subtype':'category', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>','subtype':'category', type: 'inactive', ids: val},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function active_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>','subtype':'category', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>','subtype':'category', type: 'inactive_all'},
            success: function(data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/comments', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    jQuery(document).ready(function(){
        jQuery("#submit_search").click(function(event){
            event.preventDefault();
            var search_term = jQuery('#search_term').val();
            if (search_term != '') {
                blockUI();
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/comments',
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
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/comments',
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
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/comments',
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
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>projects/comments',
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