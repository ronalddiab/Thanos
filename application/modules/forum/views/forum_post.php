


<?php
$ckeditor = array(
    //ID of the textarea that will be replaced
    'id' => 'topic_text',
    'path' => 'assets/ckeditor',
    //Optionnal values
    'config' => array(
        'toolbar' => "Basic", //Using the Full toolbar
        'width' => "100%", //Setting a custom width
        'height' => '100px', //Setting a custom height
        //'removeButtons' => 'TextColor,BGColor,FontSize,Font', //remove buttons
        //  'removePlugins' => 'image,flash,forms,contextmenu,clipboard,pastefromword,pastetext,colordialog,dialogadvtab,entities,format,indent,iframe,indentblock,indentlist,smiley,stylescombo,div', //remove buttons
        'removePlugins' => 'dialogui,dialog,a11yhelp,about,bidi,blockquote,clipboard,colordialog,menu,contextmenu,dialogadvtab,div,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,flash,floatingspace,forms,horizontalrule,htmlwriter,iframe,image,link,liststyle,magicline,maximize,newpage,pagebreak,pastefromword,pastetext,preview,print,removeformat,resize,save,menubutton,scayt,selectall,showblocks,showborders,smiley,sourcearea,specialchar,stylescombo,tab,templates,undo,wsc,table,tabletools',
        'extraAllowedContent' => 'ul(*)[*]{*};li(*)[*]{*};p(*)[*]{*};span(*)[*]{*};div(*)[*]{*};h1(*)[*]{*};h2(*)[*]{*};h3(*)[*]{*};h4(*)[*]{*};img(*)[*]{*};a(*)[*]{*};table(*)[*]{*};tr(*)[*]{*};td(*)[*]{*};'
    ),
);
?>
<!-- Banner Section -->
<div id="ajax_table">
    <article class="card">
        <div class="article-header"><?php echo lang('forum-topics'); ?></div>
        <div class="card-wrap">
            <div class="cms-conent-column">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="account-column">
                                <div class="row">
                                    <?php widget('forum_search', array('menu_name' => 'forum_search', 'section_name' => $this->_ci->theme->get('section_name'))); ?>
                                    <div class="col-sm-9 listing-right">
                                        <?php //echo $this->_ci->theme->message(); ?>
                                        <div class="meessage-block">
                                            <div class="forum-links clearfix">
                                                <a href="<?php echo site_url(); ?>forum/forum_listing"><?php echo lang('forum'); ?></a>
                                                <?php
                                                if (isset($logged_in)) {
                                                    ?>
                                                    <a href="<?php echo site_url(); ?>forum/action"><?php echo lang('add-forum'); ?></a>
                                                    <a class="<?php echo $postback_url == 'myforum' ? 'activelink' : ''; ?>" href="<?php echo site_url(); ?>forum/myforum"><?php echo lang('my-thread'); ?></a>
                                                    <a class="<?php echo $postback_url == 'mycontribution' ? 'activelink' : ''; ?>" href="<?php echo site_url(); ?>forum/mycontribution"><?php echo lang('my-contribution'); ?></a>
                                                    <?php
                                                }
                                                //else {
                                                ?>
                                                <a class="<?php echo $postback_url == 'today_thread' ? 'activelink' : ''; ?>" href="<?php echo site_url(); ?>forum/today_thread"><?php echo lang('today-thread'); ?></a>
                                                <a class="<?php echo $postback_url == 'popular_post' ? 'activelink' : ''; ?>" href="<?php echo site_url(); ?>forum/popular_post"><?php echo lang('popular-post'); ?></a>
                                                <a href="<?php echo site_url() . $getForumRules['slug_url']; ?>"><?php echo lang('forum-rules'); ?></a>
                                            </div>
                                            <div class="compose-form-post">
                                                <div class="back-to-listing"><!--<a href="#">Back to listing</a>--></div>
                                                <div class="clearfix"></div>
                                                <div class="comment-post clearfix">
                                                    <div class="post-userinfo col-sm-3">
                                                        <div class="user-img ">
                                                            <?php
                                                            if (empty($forum_first_post['up']['avtar'])) {
                                                                echo add_image(array('default_pic.jpg'));
                                                            } else {
                                                                echo "<img src='" . site_url() . "assets/uploads/avtar/thumb/" . $forum_first_post['up']['avtar'] . "' class='avtar_thumb img-responsive'  />";
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="user-name ">
                                                            <p>   <?php
                                                                if (empty($forum_first_post['up']['forumname'])) {
                                                                    echo $forum_first_post['u']['firstname'] . "&nbsp;" . $forum_first_post['u']['lastname'];
                                                                } else {
                                                                    echo $forum_first_post['up']['forumname'];
                                                                }
                                                                ?></p>
                                                            <div class="post-date"> <?php echo lang('posted_on'); ?> :<span><?php echo date("Y-m-d", strtotime($forum_first_post['fp']['created_on'])); ?> </span><span><?php echo date("H:i:s", strtotime($forum_first_post['fp']['created_on'])); ?></span> 	</div>


                                                        </div>
                                                    </div>
                                                    <div class="post-user-detail col-sm-9 clearfix">
                                                        <div class="right-arrow top-arrow"></div>
                                                        <div class="post-sr-name clearfix">
                                                            <div class="title-post"><h2><?php echo $forum_first_post['fp']['forum_post_title']; ?> </h2></div>

                                                        </div>


                                                        <div class="dec-post-text">
                                                            <p><?php echo $forum_first_post['fp']['forum_post_text']; ?></p>

                                                        </div>
                                                        <div class="tag-comment">
                                                            <div class="clearfix">

                                                                <!--                                                        <div class="comment-box">
                                                                                                                            <b><?php echo lang('industry'); ?> :</b>
                                                                                                                            <span><?php echo!empty($forum_first_post['i']['industry_name']) ? $forum_first_post['i']['industry_name'] : '---'; ?></span>
                                                                                                                        </div>
                                                            
                                                                                                                        <div class="comment-box">
                                                                                                                            <b><?php echo lang('sector'); ?> :</b>
                                                                                                                            <span><?php echo!empty($forum_first_post['s']['sector_name']) ? $forum_first_post['s']['sector_name'] : '---'; ?></span>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="comment-box-tag">
                                                                                                                        <b><?php echo lang('tags'); ?> :</b>
                                                                                                                        <span><?php echo!empty($forum_tags) ? $forum_tags : '---'; ?></span>
                                                                                                                    </div>-->
                                                            </div>
                                                            <div class="total-comment">
                                                                <div class="comment-box"><b><?php echo lang('total_comment'); ?> :</b><span><?php echo $total_records ?></span></div>
                                                                <div class="comment-box"><b><?php echo lang('total_view'); ?> :</b><span><?php echo ($view_count['custom']['total']); ?></span></div>
                                                                <?php
                                                                if (isset($last_post['custom']['lastupdate']) && $last_post['custom']['lastupdate'] != "") {
                                                                    ?>
                                                                    <div class="comment-box"><b><?php echo lang('last_comment_on'); ?> :</b> <span><?php echo $last_post['custom']['lastupdate']; ?></span></div>
                                                                <?php } ?>


                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    if (isset($page_number) && $page_number > 1) {
                                                        $i = ($this->_ci->session->userdata[$section_name]['record_per_page'] * ($page_number - 1)) + 1;
                                                    } else {
                                                        $i = 1;
                                                    }
                                                    if (isset($forum_post_comments) && $forum_post_comments != "") {
                                                        foreach ($forum_post_comments as $forum_post_comment) {
                                                            ?>
                                                            <div class="comment-post  reply-post clearfix">
                                                                <div class="post-userinfo col-sm-3">
                                                                    <div class="user-img ">
                                                                        <?php
                                                                        if (empty($forum_post_comment['up']['avtar'])) {
                                                                            echo add_image(array('default_pic.jpg'));
                                                                        } else {
                                                                            echo "<img src='" . site_url() . "assets/uploads/avtar/thumb/" . $forum_post_comment['up']['avtar'] . "' class='avtar_thumb img-responsive' />";
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <div class="user-name ">
                                                                        <p> <?php
                                                                            if (empty($forum_post_comment['up']['forumname'])) {
                                                                                echo $forum_post_comment['u']['firstname'] . "&nbsp;" . $forum_post_comment['u']['lastname'];
                                                                            } else {
                                                                                echo $forum_post_comment['up']['forumname'];
                                                                            }
                                                                            ?></p>

                                                                        <div class="post-date"> <?php echo lang('posted_on'); ?> :<span><?php echo date("Y-m-d", strtotime($forum_post_comment['ft']['created_on'])); ?> </span><span><?php echo date("H:i:s", strtotime($forum_post_comment['ft']['created_on'])); ?></span> 	</div>

                                                                    </div>
                                                                </div>
                                                                <div class="post-user-detail col-sm-9 clearfix">
                                                                    <div class="right-arrow top-arrow"></div>
                                                                    <div class="post-sr-name clearfix">
                                                                        <?php /* ?><div class="title-post"><h2><?php echo $forum_post_comment['ft']['topic_title']; ?> </h2></div><?php */ ?>
                                                                        <div class="post-nub">#<?php echo lang('reply') . " " . $i ?></div>
                                                                    </div>

                                                                    <div class="dec-post-text">
                                                                        <p><?php echo $forum_post_comment['ft']['topic_text']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                            $i++;
                                                        }
                                                    } else {
                                                        echo lang('no_records_found');
                                                    }
                                                    ?>

                                                    <?php if (isset($logged_in)) { ?>
                                                        <?php
                                                        $attributes = array('name' => 'add_forum_post', 'id' => 'add_forum_post');
                                                        //echo form_open_multipart("", $attributes);
                                                        echo form_open_multipart("", $attributes);


                                                        // echo $slug_url; exit;

                                                        echo form_hidden('id', $id);
                                                        echo form_hidden('slug_url', $slug_url);
                                                        ?>
                                                        <?php
                                                        $title_data = array(
                                                            'name' => 'topic_title',
                                                            'id' => 'topic_title',
                                                            'value' => set_value('forum_title', ((isset($topic_title)) ? $topic_title : '')),
                                                            //'style' => 'width:198px;',
                                                            'class' => ""
                                                        );
                                                        ?>
                                                        <div class="form-blk reply-textarea reply-box clearfix">
                                                            <h3> Reply to this post  </h3>
                                                            <?php /* ?><div class="input-blk clearfix">
                                                                <?php echo form_label(lang('reply_title') . STAR_MANDATORY, 'Forum title'); ?>
                                                                <?php echo form_input($title_data); ?>
                                                                <div style="clear: both"></div>
                                                                <label class="input-label validation_error"><?php echo form_error('topic_title'); ?></label>
                                                            </div><?php */ ?>
                                                            <div class="input-blk clearfix">
                                                                <?php
                                                                $reply_data = array(
                                                                    'name' => 'topic_text',
                                                                    'id' => 'topic_text',
                                                                    'value' => set_value('topic_text', ((isset($topic_text)) ? $topic_text : '')),
                                                                    'style' => 'width:198px;',
                                                                    'class' => "validate[required]"
                                                                );
                                                                ?>
                                                                <?php echo form_label(lang('topic_text') . STAR_MANDATORY, 'Reply text'); ?>  <br><br>
                                                                <?php
                                                                echo form_textarea($reply_data);
                                                                echo display_ckeditor($ckeditor);
                                                                ?>
                                                                <label class="input-label validation_error"><?php echo form_error('topic_text'); ?></label>
                                                                <!--<div class="submit-btn "><button>Reply</button></div>
                                                                <div class="submit-btn "><button>Cancel</button></div>-->
                                                                <?php
                                                                $submit_button = array(
                                                                    'name' => 'mysubmit',
                                                                    'id' => 'mysubmit',
                                                                    'value' => lang('Reply'),
                                                                    'title' => lang('Reply'),
                                                                    'class' => 'submit-btn',
                                                                );
                                                                // echo form_submit($submit_button);
                                                                $cancel_button = array(
                                                                    'name' => 'cancel',
                                                                    'value' => lang('btn-cancel'),
                                                                    'title' => lang('btn-cancel'),
                                                                    'class' => 'submit-btn',
                                                                    'onclick' => "location.href='" . site_url('forum/posts/' . $id . "/" . $language_code) . "'",
                                                                );
                                                                //  echo "&nbsp;";
                                                                //echo form_reset($cancel_button);
                                                                ?>
                                                                <br/>
                                                                <div class="submit-btn">
                                                                    <?php echo form_button(array("type" => "submit", "name" => "mysubmit", "id" => "mysubmit", "value" => lang('Reply'), 'content' => lang('Reply'))); ?>                         </div>
                                                                <div class="submit-btn">
                                                                    <?php echo form_button(array("type" => "reset", "class" => "search-btn", 'content' => lang('btn-cancel'), "onclick" => "location.href  = '" . site_url('forum/' . $postback_url . '/') . "' ")) ?>


                                                                    <?php echo form_close(); ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <?php } ?>

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
<script type="text/javascript">
    $("#add_forum_post").submit(function() {
        var messageLength = CKEDITOR.instances['topic_text'].getData().replace(/<[^>]*>/gi, '').length;
        if (!messageLength) {
            var top_px = $('#cke_topic_text').offset().top;
            var final_px = parseInt(top_px, 10) + 100;
            $(".topic_textformError").css("top", final_px);
            $("#ck_validation").css("display", "block");
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        /* $("#add_forum_post").validationEngine(
         {promptPosition: '<?php echo VALIDATION_ERROR_POSITION; ?>', validationEventTrigger: "submit"}
         );
         */

        setTimeout(validate_fileds, 5000);

        function validate_fileds() {
            
            // For validate ck editor : ===
            $('#cke_topic_text').attr('name', 'topic_text');
            $('#cke_topic_text').addClass('validate[required]');
            // ====
            $("#add_forum_post").validate({
                rules: {
                    topic_text: {
                        required: true
                    }

                }
            });
        }


    });

    function attach_error_event() {
        $('div.formError').bind('click', function() {
            $(this).fadeOut(1000, removeError);
            $("#ck_validation").css("display", "none");
        });
    }
    function removeError()
    {
        $(this).remove();
    }

</script>
