<!-- Banner Section -->
<div id="ajax_table">
    <article class="card">
        <div class="article-header"><?php echo lang('forum-rules'); ?></div>
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
                                                <a href="<?php echo site_url(); ?>forum/popular_post"><?php echo lang('popular-post'); ?></a>
                                                <a href="<?php echo site_url().$getForumRules['slug_url']; ?>" class="activelink"><?php echo lang('forum-rules'); ?></a>
                                            </div>

                                            <div class="compose-form-div discussion-rules clearfix">
                                            <?php 
                                                echo $getForumRules['description'];
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
        
        