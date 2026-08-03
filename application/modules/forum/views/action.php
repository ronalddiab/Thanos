<?php
$ckeditor = array(
    //ID of the textarea that will be replaced
    'id' => 'forum_description',
    'path' => 'assets/ckeditor',
    //Optionnal values
    'config' => array(
        'toolbar' => "Basic", //Using the Full toolbar
        'width' => "100%", //Setting a custom width
        'height' => '100px', //Setting a custom height
        //'removeButtons' => 'TextColor,BGColor,FontSize,Font', //remove buttons
        'removePlugins' => 'dialogui,dialog,a11yhelp,about,bidi,blockquote,clipboard,colordialog,menu,contextmenu,dialogadvtab,div,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,flash,floatingspace,forms,horizontalrule,htmlwriter,iframe,image,link,liststyle,magicline,maximize,newpage,pagebreak,pastefromword,pastetext,preview,print,removeformat,resize,save,menubutton,scayt,selectall,showblocks,showborders,smiley,sourcearea,specialchar,stylescombo,tab,templates,undo,wsc,table,tabletools',
        'extraAllowedContent' => 'ul(*)[*]{*};li(*)[*]{*};p(*)[*]{*};span(*)[*]{*};div(*)[*]{*};h1(*)[*]{*};h2(*)[*]{*};h3(*)[*]{*};h4(*)[*]{*};img(*)[*]{*};a(*)[*]{*};table(*)[*]{*};tr(*)[*]{*};td(*)[*]{*};'
    ),
);
?>

<div id="ajax_table" class="forum-action-wrap">
    <article class="card">
    <div class="article-header"><?php echo lang('add-forum'); ?></div>
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
                                    <!--<h2><?php //echo lang('add-forum'); ?></h2>-->
                                    <div class="forum-links clearfix">
                                        <a href="<?php echo site_url(); ?>forum/forum_listing"><?php echo lang('forum'); ?></a>
                                        <?php
                                        if (isset($logged_in)) {
                                            ?>
                                            <a href="<?php echo site_url(); ?>forum/action" class="activelink"><?php echo lang('add-forum'); ?></a>
                                            <a href="<?php echo site_url(); ?>forum/myforum"><?php echo lang('my-thread'); ?></a>
                                            <a href="<?php echo site_url(); ?>forum/mycontribution"><?php echo lang('my-contribution'); ?></a>
                                            <?php
                                        }
                                        ?>
                                        <a href="<?php echo site_url(); ?>forum/today_thread"><?php echo lang('today-thread'); ?></a>
                                        <a href="<?php echo site_url(); ?>forum/popular_post"><?php echo lang('popular-post'); ?></a>
                                        <a href="<?php echo site_url().$getForumRules['slug_url']; ?>"><?php echo lang('forum-rules'); ?></a>
                                    </div>

                                    <div class="compose-form clearfix">

                                        <?php if (!empty($categories)) { ?>

                                            <?php
                                            $attributes = array('name' => 'add_forum_form', 'id' => 'add_forum_form');
                                            echo form_open('forum/action', $attributes);
                                            ?>
                                            <div class="form-blk add-form-blk clearfix">
                                                <div class="input-blk clearfix">
                                                    <?php echo form_label(lang('forum_title') . STAR_MANDATORY); ?>
                                                    <?php
                                                    $title_data = array(
                                                        'name' => 'forum_title',
                                                        'id' => 'forum_title',
                                                        'value' => set_value('forum_title', ((isset($forum_name)) ? $forum_name : '')));
                                                    ?>
                                                    <?php echo form_input($title_data); ?>

                                                    <span class="warning-msg"><?php echo form_error('forum title'); ?></span>
                                                </div>


                                                <div class="input-blk clearfix">
                                                    <?php echo form_label(lang('forum_description')); ?>

                                                    <br /><br />

                                                    <?php
                                                    $description_data = array(
                                                        'name' => 'forum_description',
                                                        'id' => 'forum_description',
                                                        'value' => set_value('forum_description', ((isset($forum_description)) ? html_entity_decode($forum_description) : ''))
                                                    );
                                                    echo form_textarea($description_data);
                                                    echo display_ckeditor($ckeditor);
                                                    ?>
                                                </div>

                                                <div class="add-fomr-blk-input clearfix">
                                                    <div class="input-blk category-select">

                                                        <?php
                                                        echo form_label(lang('category'));

                                                        $statuslist = array();
                                                        $statuslist_value = array();

                                                        foreach ($categories as $category) {
                                                            $statuslist_cat[$category["categories"]["category_id"]] = $category["categories"]["title"];
                                                            $statuslist_cat_value = $category["categories"]["category_id"];
                                                        }
                                                        if (!isset($forum_category)) {
                                                            $forum_category = "";
                                                        }

                                                        echo form_dropdown('forum_category', $statuslist_cat, $forum_category, set_value($statuslist_cat_value));
                                                        ?>
                                                    </div>
                                                    <!-- <div class="input-blk  ">

                                                        <?php echo form_label(lang('industry')); ?>

                                                        <?php echo form_dropdown('industry_id', $industry_list, $industry, 'id=industry_id2 onchange = "load_sector2(this.value)"'); ?>
                                                    </div>
                                                    <div class="input-blk ">

                                                        <?php echo form_label(lang('sector')); ?>
                                                        <span id="related_sector2">
                                                            <?php echo form_dropdown('sector_id', $sector_list1, $sector1, 'id=sector_id2'); ?></span>
                                                    </div> -->
                                                </div>
                                                <div class="add-fomr-blk-input clearfix">

                                                    <!-- <div class="input-blk ">

                                                        <?php echo form_label(lang('tag')); ?>
                                                        <?php echo form_dropdown('tag_id[]', $tag_list, $tags, 'id=tag_id multiple="multiple" size="3" '); ?>
                                                    </div> -->
                                                    <div class="input-blk ">

                                                        <?php echo form_label(lang('is-draft')); ?>
                                                        <?php
                                                        $draftlist = array('0' => 'No', '1' => 'Yes');
                                                        echo form_dropdown('is_draft', $draftlist, '');
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="submit-btn ">
                                                    <?php
                                                    $submit_button = array(
                                                        'name' => 'mysubmit',
                                                        'id' => 'mysubmit',
                                                        'value' => lang('btn-save'),
                                                        'title' => lang('btn-save'),
                                                        'type' => 'submit',
                                                        'content' => 'Save'
                                                    );
                                                    echo form_button($submit_button);
                                                    ?>
                                                </div>

                                                <div class="submit-btn">
                                                    <?php
                                                    $cancel_button = array(
                                                        'name' => 'cancel',
                                                        'value' => lang('btn-cancel'),
                                                        'title' => lang('btn-cancel'),
                                                        'onclick' => "location.href='" . site_url('/forum/forum_listing') . "'",
                                                        'content' => 'Cancel'
                                                    );
                                                    echo form_button($cancel_button);
                                                    ?>
                                                    <!--                                                <button>Cancel</button>-->
                                                </div>
                                            </div>
                                            <?php echo form_close(); ?>
                                        <?php } else { ?>
                                            <span class="norecfound">Sorry there is no category available, so you can't create forum.</span>
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
    function submit_search() {
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
            }
        });
    }

    function load_sector2(id, secid) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>admin/users/get_related_sector',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', industry_id: id, secid: secid, forum_flag: "2"},
            success: function(data) {
                $("#sector_id2").html(data);
            }
        });
    }

    jQuery.validator.addMethod("alphanumeric", function(value, element) {
         var regex1 =  /^[a-zA-Z\ \-']+$/;
        return this.optional(element) || regex1.test(value);
    }, "Only Letters, Space and Dash(-) allowed here.");

    $(document).ready(function() {
        $('#tag_id').css('height', 'auto');
        $('#forum_title').focus();

        // Form Validator PlugIn Starts
        $('#add_forum_form').validate({
            rules: {
                forum_title: {
                    required: true,
                    // alphanumeric: true
                }
            },
            highlight: function(element) {
                // $(element).closest('.row').addClass('has-error');
                $(element).addClass('has-error');
            },
            unhighlight: function(element) {
                // $(element).closest('.row').removeClass('has-error');
                $(element).removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block'
        });
        // Form Validator PlugIn Ends

<?php if (!empty($this->_ci->session->userdata['front']['forum_industry_id'])) { ?>
            load_sector('<?php echo $this->_ci->session->userdata['front']['forum_industry_id']; ?>', '<?php echo $this->_ci->session->userdata['front']['forum_sector_id']; ?>');
<?php } ?>
    });
</script>