<?php
if (!isset($sort_order)) {
    $sort_order = "";
}
if (!isset($sort_by)) {
    $sort_by = "";
}
if (!isset($search_term)) {
    $search_term = "";
}
?>
<article id="ajax_table" class="card">
    <div class="article-header"> <?php echo lang('forum-listing'); ?> </div>
    <div id="listing" class="card-wrap forum-content-box">
        <div>
    <div class="row site-controls-outer">
        <div class="col-sm-9">
            <form class="default-form form-inline clearfix">
                <div class="form-group form-control-outer">
                    <input id="search_term" name="search_term" type="text" placeholder="Search by Title" class="form-control" value="<?php echo set_value('search_term', urldecode($search_term)); ?>">
                </div>
                <div class="form-group">
                    <button onclick="submit_search()" class="btn btn-secondary" type="button" name="">
                        <img alt="Search" src="images/search-icon.png"><?php echo lang('search'); ?>
                    </button>
                </div>
                <div class="form-group">
                    <button onclick="reset_data()" class="btn btn-reset" type="button" name=""><img alt="Reset" src="images/reset-icon.png">Reset</button>                       
                </div>
            </form>
        </div>
        <?php $add_forum_permission = check_user_permission_by_label('admin.forum.action.add'); ?>
        <?php if($add_forum_permission){ ?>
        <div class="col-sm-3">
            <a class="btn btn-blue pull-right" href="<?php echo site_url().BASE_ADMIN_URL_CUSTOM . 'forum/action/add'; ?>"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo lang('add-forum');?></a>
        </div>
        <?php } ?>
    </div>


<?php if (!empty($forums)){ ?>

    <div class="row helprow">
        <div class="col-sm-3">
        <?php echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "active")); ?>&nbsp;Active
        <?php echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "inactive")); ?>&nbsp;Archive
        </div>
        <div class="col-sm-9">&nbsp;</div>
    </div>
    <br/>

    <div class="row">
        <div class="col-sm-12">
            <span><strong><?php echo lang('category'); ?> : </strong><?php echo $category['categories']['title']; ?></span>
        </div>
    </div>

    <div class="main-container">
       
<?php echo form_open(); ?>
<div class="table-responsive">
            <table class="table table-striped">

                <thead>

                    <?php
                    if (!empty($forums))
                    {
                        ?>
                         <tr>
                        <th width="30px"><input class="icheck" type="checkbox" name="check_all" id="check_all" value="0"></th>
                        <th class="digits-col">
<?php echo lang('no'); ?>
                        </th>
                        <th>
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 'forum_post_title' && $sort_order == 'asc')
                            {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="javascript:void(0)" onclick="sort_data('forum_post_title', '<?php echo $field_sort_order; ?>');">

                                <?php echo lang('forum_post_title'); ?>
                                <?php
                                if ($sort_by == 'forum_post_title')
                                {
                                    ?>
                                    <div class="sorting">
                                    <?php echo add_image(array($sort_image)); ?>
                                    </div>
<?php }
?>
                            </a>
                        </th>

                        <th>
                            <?php echo lang('rly_count'); ?>
                        </th>

                        <th>
                            <?php echo lang('last_update_on'); ?>
                        </th>
                        <th>
                            <?php echo lang('status'); ?>
                        </th>
                        <th>
                    <?php echo lang('action'); ?>
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($page_number > 1)
                        {
                            $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                        }
                        else
                        {
                            $i = 1;
                        }
                        foreach ($forums as $forum)
                        {

                            if ($i % 2 != 0)
                            {
                                $class = "odd-row";
                            }
                            else
                            {
                                $class = "even-row";
                            }
                            ?>

                            <tr class="<?php echo $class; ?> rows" >
                                <td>
                                    <input type="checkbox" id="<?php echo $forum['forum_post']['id']; ?>" name="check_box[]" class="check_box icheck" value="<?php echo $forum['forum_post']['id']; ?>">
                                </td>
                                <td><?php echo $i; ?> <?php if ($forum['forum_post']['is_private'] == 1) echo add_image(array('imp.png')); ?></td>
                                <td><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $forum['forum_post']['id'] ?>"><?php echo $forum['forum_post']['forum_post_title']; ?></a></td>
                                <td><?php echo $forum['forum_post']['rly_count']; ?></td>

                                <td>
                                    <?php
                                    if ($forum['forum_post']['modified_on'] == "0000-00-00 00:00:00")
                                    {
                                        $forum['forum_post']['modified_on'] = "-";
                                    }
                                    echo $forum['forum_post']['modified_on'];
                                    $forum_id = $forum['forum_post']['id']
                                    ?>
                                </td>
                                <td>
        <?php
        if ($forum['forum_post']['status'] == 1)
        {
            echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "active"));
        }
        else
        {
            echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "active"));
        }
        ?>
                                </td>

                                <td>
                                        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/view_data/<?php echo $forum_id; ?>"><?php echo add_image(array('search-icon-black.png')); ?></a>
                                        <?php $edit_forum_permission = check_user_permission_by_label('admin.forum.action.edit'); ?>
                                        <?php if($edit_forum_permission){ ?>
                                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/action/edit/<?php echo $forum_id; ?>" title="<?php echo lang('Edit') ?>"><?php echo add_image(array('edit-icon.png')); ?></a>
                                        <?php } ?>
                                        <?php $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_forum($forum_id )'>" . add_image(array('delete-icon.png')) . "</a>"; ?>
                                        <?php echo $deletelink ?>
                                </td>


                            </tr>




        <?php
        $i++;
    }
}
else
{
    $querystr = "";
    ?>
                <table class="norecords">
                        <tr>
                            <td><?php echo lang('no_records_found'); ?></td>
                        </tr>
                    </table>
                            <?php
                        }
                        ?>

        <?php
        echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
        echo form_hidden('page_number', "", "page_number");
        echo form_hidden('per_page_result', "", "per_page_result");
        ?>
                </tbody>
            </table>
        </div>
        <?php
        echo form_close();
        ?>

        <div class="btn-panel">
            <?php
                $reset_button = array(
                    'content' => lang('delete'),
                   // 'title' => lang('delete'),
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
                    'content' => lang('archive'),
                   // 'title' => lang('archive'),
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
                // echo form_button($reset_button);
                ?>
            <?php
            $reset_button = array(
                'content' => lang('archive-all'),
               // 'title' => lang('archive-all'),
                'class' => 'btn btn-custom btn-yellow',
                'onclick' => "inactive_all_records()",
            );
            // echo form_button($reset_button);
            ?>
        </div>

        <?php
        if (!empty($forums))
        {
            $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . $search_term . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "forum/forum_listing/" . $category_id . "/" . $language_code,
                'params' => $querystr,
                'element' => 'ajax_table'
            );
            widget('custom_pagination', $options);
        }
        ?>

        
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
                        

</div>
    </div>
</article>  

<script type="text/javascript">

    $(document).ready(function() {
        delete_forum = function(id)
        {
            res = confirm('<?php echo lang('delete-alert') ?>');
            if(res){
              //  blockUI();
                $.ajax({
                    type:'POST',
                    url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>forum/delete_forum/',
                    data:{<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',id:id,category:<?php echo $category_id ?>},
                    success: function(data) {
                        blockUI();
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>',
                            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val())},
                            success: function(data) {
                                $("#ajax_table").html(data);
                            }
                        });
                        unblockUI();
                        // show success message
                      //  unblockUI();
                        $("#messages").show();
                        $("#messages").html(data);
                    }
                });

            }else{
                return false;
            }
        }
    });

    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            submit_search();
        }
    });
    function submit_search()
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        var search_term = $('#search_term').val();
        if (search_term != '') {
            if ($('#search_term').val() == '') {
                $('#search_term').validationEngine('showPrompt', '<?php echo lang('msg_search_req'); ?>', 'error');
                attach_error_event(); //for remove dynamically populate popup
                return false;
            }
        
            blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>',
                data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val())},
                success: function(data) {
                    $("#ajax_table").html(data);
                }
            });
            unblockUI();
        }else{
            alert("Please enter something to search");
        }
    }

    function sort_data(sort_by, sort_order)
    {
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val()), sort_by: sort_by, sort_order: sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        unblockUI();
    }
    function reset_data()
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }
    function delete_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });
        if (val == "")
        {
            alert('Please select atleast one record for delete');
            return false;
        }

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            alert('Please select atleast one record for active');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/archive',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active', ids: val},
            success: function(data) {
                //for managing same state while record delete
                if ($('.rows') && $('.rows').length > 1) {
                    pageno = "&page_number=<?php echo $page_number; ?>";
                } else {
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";
                }
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
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
            alert('Please select atleast one record for archive');
            return false;
        }
        $.ajax({
            type: 'POST',
//            url: '<?php echo base_url() . $this->_data['section_name']; ?>/forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/archive',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', ids: val, type: 'archive'},
            success: function(data) {
                //for managing same state while record delete
                if ($('.rows') && $('.rows').length > 1) {
                    pageno = "&page_number=<?php echo $page_number; ?>";
                } else {
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";
                }
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function active_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active_all'},
            success: function(data) {
                //for managing same state while record delete
                if ($('.rows') && $('.rows').length > 1) {
                    pageno = "&page_number=<?php echo $page_number; ?>";
                } else {
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";
                }
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_all_records()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'archive_all'},
            success: function(data) {
                //for managing same state while record delete
                if ($('.rows') && $('.rows').length > 1) {
                    pageno = "&page_number=<?php echo $page_number; ?>";
                } else {
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";
                }
                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id; ?>/<?php echo $language_code; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
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

</script>