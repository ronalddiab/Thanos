<!-- Banner Section -->
<div id="ajax_table">
    <article class="card">
        <div class="article-header"><?php echo lang('popular-post'); ?></div>
        <div class="card-wrap">
            <div class="cms-conent-column">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="account-column">
                                <div class="row">
                                    <?php widget('forum_search', array('menu_name' => 'forum_search', 'section_name' => $this->_ci->theme->get('section_name')));?>
                                    <div class="col-sm-9 listing-right">
                                        <div class="meessage-block">
                                            <div class="forum-links clearfix">
                                                <a href="<?php echo site_url(); ?>forum/forum_listing"><?php echo lang('forum'); ?></a>
                                                <?php
                                                if (isset($logged_in)) {
                                                    ?>
                                                    <a href="<?php echo site_url(); ?>forum/action"><?php echo lang('add-forum'); ?></a>
                                                    <a href="<?php echo site_url(); ?>forum/myforum"><?php echo lang('my-thread'); ?></a>
                                                    <a href="<?php echo site_url(); ?>forum/mycontribution"><?php echo lang('my-contribution'); ?></a>
                                                    <?php
                                                }
                                                //else {
                                                ?>
                                                <a href="<?php echo site_url(); ?>forum/today_thread"><?php echo lang('today-thread'); ?></a>
                                                <a href="<?php echo site_url(); ?>forum/popular_post" class="activelink"><?php echo lang('popular-post'); ?></a>
                                                <a href="<?php echo site_url().$getForumRules['slug_url']; ?>"><?php echo lang('forum-rules'); ?></a>
                                            </div>

                                            <div class="compose-form-div clearfix">
                                                <div class="forum-title clearfix">
                                                    <p class="post-title"> 	<?php echo lang('forum_post_title'); ?> </p>
                                                    <p class="count"><?php echo lang('rly_count'); ?> </p>
                                                    <p class="update"> 	<?php echo lang('last_update_on'); ?> </p>
                                                </div>
                                                <?php
                                                if (!empty($forums1)) {

                                                    if (isset($page_number) && $page_number > 1) {
                                                        $i = ($this->_ci->session->userdata[$section_name]['record_per_page'] * ($page_number - 1)) + 1;
                                                    } else {
                                                        $i = 1;
                                                    }
                                                    $category = "";

                                                    foreach ($forums1 as $forum) {
                                                        $slug = $forum['forum_post']['slug_url'];
                                                        //$forum['forum_post']['id']
                                                        ?>
                                                        <div class="forum-listing">

                                                            <div class="post-list sub-post clearfix">
                                                                <p class="post-title"><a href="<?php echo site_url(); ?>forum/posts/<?php echo $slug ?>"><?php echo $forum['forum_post']['forum_post_title']; ?> </a></p>
                                                                <p class="count"><?php echo $forum['forum_post']['rly_count']; ?></p>
                                                                <p class="update clearfix">
                <?php
                if ($forum['forum_post']['modified_on'] == "0000-00-00 00:00:00") {
                    echo "-";
                } else {
                    echo "<b>" . date("Y-m-d", strtotime($forum['forum_post']['modified_on'])) . "</b><span>" . date("H:i:s", strtotime($forum['forum_post']['modified_on'])) . "</span>";
                }
                $forum_id = $forum['forum_post']['id'];
                ?>

                                                                </p>

                                                            </div>

                                                        </div>
            <?php
            }
            echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
            echo form_hidden('page_number', "", "page_number");
            echo form_hidden('per_page_result', "", "per_page_result");
            $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&keywords=' . $keywords . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';


            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                //'base_url' => base_url() . "/forum/forum_listing/" . $category_id . "/" . $language_code,
                'params' => $querystr,
                'element' => 'ajax_table'
            );
            widget('custom_pagination', $options);
        } else {
            echo '<span class="norecfound">' . lang('no_records_found') . '</span>';
        }
        ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>
<div class="clearfix"></div>
<!---------------------------------------------js & ajax area(start)----------------------------------------------------->
<script type="text/javascript">

    function attach_error_event() {
        $('div.formError').bind('click', function() {
            $(this).fadeOut(1000, removeError);
        });
    }


    function removeError()
    {
        jQuery(this).remove();
    }

    function sort_data(sort_by, sort_order)
    {
        // blockUI();
        $.ajax({
            type: 'POST',
            //url: '<?php echo base_url(); ?>admin/forum/index/1',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: $('#search_term').val(), sort_by: sort_by, sort_order: sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        // unblockUI();
    }
    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            submit_search();
        }
    });
    function submit_search()
    {
        // alert($('#keywords').val());
//        if ($('#keywords').val().trim() == '') {
//            $('#keywords').validationEngine('showPrompt', '<?php echo lang('msg-search-req'); ?>', 'error');
//            attach_error_event(); //for remove dynamically populate popup
//            return false;
//        }
        // blockUI();
        $.ajax({
            type: 'POST',
            //url:'<?php echo base_url(); ?>admin/forum/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', keywords: $('#keywords').val(), date_from: $('#date_from').val(), date_to: $('#date_to').val(), industry_id: $('#industry_id').val(), sector_id: $('#sector_id').val(), member: $('#member').val()},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        // unblockUI();
    }
    function reset_data()
    {
        // blockUI();
        $.ajax({
            type: 'POST',
            //url:'<?php echo base_url(); ?>/forum/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        //unblockUI();
    }

    $(function() {
        $("#date_from").datepicker({dateFormat: 'yy-mm-dd'});
        $("#date_to").datepicker({dateFormat: 'yy-mm-dd'});
    });


    function load_sector(id, secid) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>admin/users/get_related_sector',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', industry_id: id, secid: secid, flag: 'y'},
            success: function(data) {

                $("#sector_id").html(data);

//                $("#related_sector").html(data);
//                $("#sector_id").css('width', '');
//                $("#sector_id").focus();
            }
        });
    }

    $(document).ready(function() {
<?php
if (!empty($this->_ci->session->userdata['front']['forum_industry_id'])) {
    ?>
            load_sector('<?php echo $this->_ci->session->userdata['front']['forum_industry_id']; ?>', '<?php echo $this->_ci->session->userdata['front']['forum_sector_id']; ?>');
    <?php
}
?>
    });

</script>