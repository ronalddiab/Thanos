<?php
echo add_css('validationEngine.jquery');
echo add_js(array('jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine'));

$ckeditor = array(
    'id' => 'topic_text',
    'path' => 'assets/ckeditor',
    'config' => array(
        'toolbar' => "Full", //Using the Full toolbar
        'width' => "1100px", //Setting a custom width
        'height' => '100px', //Setting a custom height
        //'removePlugins' => 'dialogui,dialog,a11yhelp,about,bidi,blockquote,clipboard,colordialog,menu,contextmenu,dialogadvtab,div,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,flash,floatingspace,forms,horizontalrule,htmlwriter,iframe,image,link,liststyle,magicline,maximize,newpage,pagebreak,pastefromword,pastetext,preview,print,removeformat,resize,save,menubutton,scayt,selectall,showblocks,showborders,smiley,sourcearea,specialchar,stylescombo,tab,templates,undo,wsc,table,tabletools',
        'extraAllowedContent' => 'ul(*)[*]{*};li(*)[*]{*};p(*)[*]{*};span(*)[*]{*};div(*)[*]{*};h1(*)[*]{*};h2(*)[*]{*};h3(*)[*]{*};h4(*)[*]{*};img(*)[*]{*};a(*)[*]{*};table(*)[*]{*};tr(*)[*]{*};td(*)[*]{*};'
    ),
);
?>

<article id="ajax_table" class="card">
    <div class="card-wrap">
        <div class="main-content grid-data">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr style="height: 50px">
                            <th><input class="icheck" type="checkbox" name="check_all" id="check_all" value="0"></th>
                            <th><?php echo lang('author'); ?></th>
                            <th><?php echo lang('comment'); ?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($page_number > 1) {
                            $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                        } else {
                            $i = 1;
                        }
                        ?>

                        <tr style="border: none" class="odd-row rows" >
                            <td></td>
                            <td valign="top" width="100px;"><?php echo add_image(array('default_pic.jpg')); ?>
                                <br/><?php echo $forum_first_post['u']['firstname'] . "&nbsp;" . $forum_first_post['u']['lastname']; ?>
                                <br/><?php echo lang('posted_on'); ?><br/><?php echo $forum_first_post['fp']['created_on']; ?>
                            </td>
                            <td valign="top" ><b><?php echo $forum_first_post['fp']['forum_post_title']; ?></b>
                                <br/><br/><?php echo $forum_first_post['fp']['forum_post_text']; ?>
                            </td>
                            <td valign="top">
                                <?php
                                if ($forum_first_post['fp']['status'] == 1) {
                                    echo add_image(array('active.png'));
                                } else {
                                    echo add_image(array('inactive.png'));
                                }
                                ?>

                            </td>
                            <td valign="top">
                                <table>
                                    <tr>
                                        <td><h4><?php echo lang('main_post'); ?></h4>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td><h5><?php echo lang('total_comment'); ?></h5>
                                        </td>
                                        <td><?php echo $total_records; ?></td>
                                    </tr>
                                    <tr>
                                        <td><h5><?php echo lang('total_view'); ?></h5>
                                        </td>

                                        <td><?php echo ($view_count['custom']['total']); ?></td>
                                    </tr>
                                    <?php
                                    if (isset($last_post['custom']['lastupdate']) && $last_post['custom']['lastupdate'] != "") {
                                        ?>
                                        <tr>

                                            <td><h5><?php echo lang('last_comment_on'); ?></h5>
                                            </td>
                                            <td><?php
                                                echo $last_post['custom']['lastupdate'];
                                                ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </td>

                        </tr>
                        <?php
                        if ($page_number > 1) {
                            $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                        } else {
                            $i = 1;
                        }
                        if (isset($forum_post_comments) && $forum_post_comments != "") {
                            foreach ($forum_post_comments as $forum_post_comment) {
                                ?>
                                <tr style="border: none" class="even-row rows">
                                    <td valign="top"><input type="checkbox" id="<?php echo $forum_post_comment['ft']['id']; ?>" name="check_box[]" class="icheck check_box" value="<?php echo $forum_post_comment['ft']['id']; ?>"></td>
                                    <td valign="top"><?php echo add_image(array('default_pic.jpg')); ?>
                                        <br/><?php echo $forum_post_comment['u']['firstname'] . "&nbsp;" . $forum_post_comment['u']['lastname']; ?>
                                        <br/><?php echo lang('posted_on'); ?><br/><?php echo $forum_post_comment['ft']['created_on']; ?>
                                    </td>
                                    <td valign="top" ><b><?php echo $forum_post_comment['ft']['topic_title']; ?></b>
                                        <br/><br/>
                                        <?php echo $forum_post_comment['ft']['topic_text']; ?>
                                        <?php // echo $forum_post_comment['ft']['topic_text'];  ?>
                                        <br/><br/>
                                        <?php if (isset($forum_post_comment['ft']['attachment']) && $forum_post_comment['ft']['attachment'] != "") {
                                            ?>  <?php echo lang('attachment'); ?>: <a  href="<?php echo site_url(); ?>assets/uploads/forum_files/<?php echo $forum_post_comment['ft']['attachment']; ?>" target="_blank"> <?php echo $forum_post_comment['ft']['attachment']; ?> </a><?php }
                                        ?>
                                    </td>
                                    <td valign="top">
                                        <?php
                                        if ($forum_post_comment['ft']['status'] == 1) {
                                            echo add_image(array('active.png'));
                                        } else {
                                            echo add_image(array('inactive.png'));
                                        }
                                        ?>
                                    </td>

                                    <td valign="top">
                                        <br/><h4>#<?php echo lang('reply'); ?> <?php
                                            echo $i;
                                            $topic_id = $forum_post_comment['ft']['id'];
                                            ?></h4>

                                        <div class="action" style="padding-top: 10px">
                                            <div class="edit"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/topic_edit/<?php echo $topic_id . "/" . $id ?>" title="<?php echo lang('edit') ?>"><?php echo add_image(array('edit.png')); ?></a></div>
                                            <?php $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_topic($topic_id )'>" . add_image(array('delete.png')) . "</a>"; ?>
                                            <div class="delete"><?php echo $deletelink ?></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                        <table>
                            <tr>
                                <td><?php echo lang('no_records_found'); ?></td>
                            </tr>
                        </table>

                        <?php
                    }

                    echo form_hidden('hid_category', $forum_first_post['fp']['category_id']);

                    echo form_hidden('page_number', "", "page_number");
                    echo form_hidden('per_page_result', "", "per_page_result");
                    ?>
                    </tbody>
                    <?php echo form_close(); ?>
                </table>
            </div>

            <?php
            if (!empty($forum_post_comments) && $this->_ci->session->userdata['admin']['user_id'] == '1') {
                ?>
                <div class="btn-panel">
                    <?php
                    $reset_button = array(
                        'content' => lang('delete'),
                        'title' => lang('delete'),
                        'class' => 'btn btn-custom btn-red',
                        'onclick' => "delete_records()",
                    );
                    echo form_button($reset_button);
                    ?>
                    <?php
                    $reset_button = array(
                        'content' => lang('active'),
                        'title' => lang('active'),
                        'class' => 'inputbutton',
                        'onclick' => "active_records()",
                    );
                    //echo form_button($reset_button);
                    ?>
                    <?php
                    $reset_button = array(
                        'content' => lang('inactive'),
                        'title' => lang('inactive'),
                        'class' => 'inputbutton',
                        'onclick' => "inactive_records()",
                    );
                    // echo form_button($reset_button);
                    ?>
                    <?php
                    $reset_button = array(
                        'content' => lang('active-all'),
                        'title' => lang('active-all'),
                        'class' => 'inputbutton',
                        'onclick' => "active_all_records()",
                    );
                    // echo form_button($reset_button);
                    ?>
                    <?php
                    $reset_button = array(
                        'content' => lang('inactive-all'),
                        'title' => lang('inactive-all'),
                        'class' => 'inputbutton',
                        'onclick' => "inactive_all_records()",
                    );
                    //echo form_button($reset_button);
                    ?>
                </div>
                <?php
            }
            
            $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash());
            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => site_url() . BASE_ADMIN_URL_CUSTOM . "forum/forum_post/" . $id,
                'params' => $querystr,
                'element' => 'ajax_table'
            );
            widget('custom_pagination', $options);
            ?>
        </div>

        <div class="grid-data grid-data-table">
            <div class="article-header"><?php echo lang('reply_this_post'); ?></div>
            <br/>
            <?php
            $attributes = array('name' => 'add_forum_post', 'id' => 'add_forum_post', 'class' => 'site-info-form');
            echo form_open_multipart("", $attributes);
            ?>
            <ul class="form-outer-block">
                <li>
                    <?php
                    $title_data = array(
                        'name' => 'topic_title',
                        'id' => 'topic_title',
                        'value' => set_value('topic_title', ((isset($forum_name)) ? $forum_name : '')),
                        'class' => 'input-control',
                    );
                    ?>
                    <label for="inputName" class="main-label"><?php echo lang('reply_title'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <input type="text" name="topic_title" class="input-control" id="inputName" value="<?php echo set_value('topic_title'); ?>">
                            <?php if (form_error('topic_title')) { ?>
                                <label class="input-label validation_error"><?php echo form_error('topic_title'); ?></label>
                            <?php } ?>
                        </div>
                    </div>
                </li>
                <li>
                    <?php
                    $reply_data = array(
                        'name' => 'topic_text',
                        'id' => 'topic_text',
                        'value' => '',
                        'class' => 'input-control'
                    );
                    ?>
                    <label for="inputDesc" class="main-label"><?php echo lang('topic_text'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            echo form_textarea($reply_data);
                            echo display_ckeditor($ckeditor);
                            ?>
                            <?php if (form_error('topic_text')) { ?><label class="input-label validation_error"><?php echo form_error('topic_text'); ?></label> <?php } ?>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="form-btn-outer">
                <button type="submit" name="mysubmit" value="<?php echo lang('Reply'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('Reply'); ?></button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'forum/forum_listing/' . $forum_first_post['fp']['category_id']; ?>" class="btn btn-secondary reset-btn btn-submit">Cancel</a>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</article>

<script type="text/javascript">

    $(function() {
        $('#ajax_table').on('ifChecked', '#check_all', function(event) {
            $('.check_box').iCheck('check');
        });

        $('#ajax_table').on('ifUnchecked', '#check_all', function(event) {
            if ($('.check_box').filter(':checked').length == $('.check_box').length) {
                $('.check_box').iCheck('uncheck');
            }
        });
        $('#ajax_table').on('ifUnchecked', '.check_box', function(event) {
            $('#check_all').iCheck('uncheck');
        });

        $('#ajax_table').on('ifChecked', '.check_box', function(event) {
            if ($('.check_box').filter(':checked').length == $('.check_box').length) {
                $('#check_all').iCheck('check');
            }
        });
    });

    $(document).ready(function() {
        $("#add_forum_post").validate({
            rules: {
                topic_title: {
                    required: true
                },
                topic_text: {
                    required: true
                }
            }
        });
    });
    $(function() {
        $("#check_all").click(function() {
            if ($("#check_all").is(':checked')) {
                $(".check_box").prop("checked", true);
            }
            else
            {
                $(".check_box").prop("checked", false);
            }
        });
        $(".check_box").click(function() {

            if ($(".check_box").length == $(".check_box:checked").length) {
                $("#check_all").prop("checked", true);
                $(".check_box").attr("checked", "checked");
            }
            else
            {
                $("#check_all").removeAttr("checked");
            }

        });
    });

    function attach_error_event() {
        $('div.formError').bind('click', function() {
            $(this).fadeOut(1000, removeError);
        });
    }
    
    function removeError()
    {
        jQuery(this).remove();
    }
    
    function delete_topic(id) {
        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/delete_topic',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', id: id},
                success: function(data) {

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    //set responce message
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
            alert('Please select atleast one record for delete');
            return false;
        }

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
                success: function(data) {

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                        $("#messages").show();
                        $("#messages").html(data);
                    }
                });
            } else {
                return false;
            }
        }

        function active_records() {
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
                url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active', ids: val},
                success: function(data) {

                //for managing same state while record delete
                if ($('.rows') && $('.rows').length > 1) {
                    pageno = "&page_number=<?php echo $page_number; ?>";
                } else {
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";
                }
                ajaxLink('<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                }
            });
        }
        
        function inactive_records() {
            var val = [];
            $(':checkbox:checked').each(function(i) {
                val[i] = $(this).val();
            });
            if (val == "")
            {
                alert('Please select atleast one record for inactive');
                return false;
            }
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                        $("#messages").show();
                        $("#messages").html(data);
                    }
                });
        }

        function active_all_records() {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'active_all'},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                }
            });
        }

        function inactive_all_records()
        {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', type: 'inactive_all'},
                success: function(data) {
                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>forum/forum_post/<?php echo $id; ?>', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                }
            });
        }

</script>