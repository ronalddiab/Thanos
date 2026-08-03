<article id="ajax_table" class="card">
    <div class="article-header"> <?php echo lang('forum_category'); ?> </div>
    <div class="card-wrap menu-content-box">
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
                <div class="col-sm-3">
                    <a class="btn btn-blue pull-right" href="<?php echo site_url().BASE_ADMIN_URL_CUSTOM . 'forum/action/add/'.$language_code; ?>"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo lang('add-forum');?></a>
                </div>
            </div>

            <?php if (!empty($categories)){ ?>

            <div class="row helprow">
                <div class="col-sm-3">
                <?php echo add_image(array('active.png'), '', '', array('title' => 'Active', 'alt' => "Active")); ?>&nbsp;Active
                <?php echo add_image(array('inactive.png'), '', '', array('title' => 'Inactive', 'alt' => "Inactive")); ?>&nbsp;Inactive
                </div>
                <div class="col-sm-9">&nbsp;</div>
            </div>


            <div class="table-responsive">
                    <table class="table table-striped">

                            <thead>

                            <?php
                            if (!empty($categories))
                            {

                                ?>
                             <tr>
                                <th class="digits-col">
                                    <?php echo lang('no'); ?>
                                </th>
                                <th>
                                    <?php
                                    $field_sort_order = 'asc';
                                    $sort_image = 'srt_down.png';
                                    if (isset($sort_by) && isset($sort_order) && $sort_by == 'title' && $sort_order == 'asc')
                                    {
                                        $sort_image = 'srt_up.png';
                                        $field_sort_order = 'desc';
                                    }
                                    ?>
                                    <a href="javascript:void(0)" onclick="sort_data('title', '<?php echo $field_sort_order; ?>');">

                                        <?php echo lang('categories'); ?>

                                        <?php
                                        if (isset($sort_by) && $sort_by == 'title')
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
                                    <?php echo lang('total_forum'); ?>
                                </th>
                                <th>
                            <?php echo lang('status'); ?>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                                if ($page_number > 1)
                                {
                                    $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                                    //($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                                }
                                else
                                {
                                    $i = 1;
                                }
                                foreach ($categories as $category)
                                {
                                    if ($i % 2 != 0)
                                    {
                                        $class = "odd-row";
                                    }
                                    else
                                    {
                                        $class = "even-row";
                                    }

                                    $category_id = $category['categories']['category_id'];
                                    ?>
                                    <tr class="<?php echo $class; ?> rows" >
                                        <td><?php echo $i; ?></td>
                                        <td><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_listing/<?php echo $category_id . "/" . $language_code; ?>" title="see forums"><?php echo $category['categories']['title']; ?></a></td>
                <?php //echo anchor(site_url() . 'admin/forum/forum_listing'.$category_id, lang('forum'), 'title="See forums" style="text-align:center;width:100%;"');  ?>
                                        <td><?php echo $category['categories']['total_forum' . $category_id]; ?></td>


                                        <td><?php
                                            if ($category['categories']['status'] == 1)
                                            {
                                                echo add_image(array('active.png'),'', '', array('title' => 'Active', 'alt' => "Active"));
                                            }
                                            else
                                            {
                                                echo add_image(array('inactive.png'),'', '', array('title' => 'Inactive', 'alt' => "Inactive"));
                                            }
                                            ?></td>
                                        <!--<td>
                                            <div class="action">
                                                <div class="edit"><a href="<?php echo site_url(); ?>admin/forum/add_category/edit/<?php echo $category_id ?>" title="<?php echo lang('edit') ?>"><?php echo add_image(array('edit.png')); ?></a></div>
                                    <?php $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_category($category_id )'>" . add_image(array('delete.png')) . "</a>"; ?>
                                                <div class="delete"><?php echo $deletelink ?></div>
                                            </div>
                                        </td>-->
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                            else
                            {
                                ?>
                        <table class="norecords">
                                <tr>
                                    <td><?php echo lang('no_records_found'); ?></td>
                                </tr>
                            </table>
            <?php
        }
        echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
        echo form_hidden('page_number', "", "page_number");
        echo form_hidden('per_page_result', "", "per_page_result");
        ?>
                        </tbody>

                    </table>
                </div>

                <?php
        if (!empty($categories))
        {
            $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . $search_term . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "forum/",
                'params' => $querystr,
                'element' => 'ajax_table'
            );
            widget('custom_pagination', $options);
        }
        ?>

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

    $(document).ready(function(){
        $('#menu318').children().removeClass('active');
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
//        if ($('#search_term').val() == '') {
//            $('#search_term').validationEngine('showPrompt', '<?php echo lang('msg_search_req'); ?>', 'error');
//            attach_error_event(); //for remove dynamically populate popup
//            return false;
//        }
        var search_term = jQuery('#search_term').val();
        if (search_term != '') {
            blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/index/<?php echo $language_code; ?>',
                data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: ($('#search_term').val())},
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/index/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: ($('#search_term').val()), sort_by: sort_by, sort_order: sort_order},
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
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/index/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }
</script>
