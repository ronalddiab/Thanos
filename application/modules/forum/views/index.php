<!---------------------------------------------html(start)----------------------------------------------------->
<div id="ajax_table">
    <div class="main-container">
        <div  class="content-center">
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
            <div class="add-new" style="padding-bottom: 5px">

                <!--<?php echo anchor(site_url() . 'admin/forum/add_category/add', lang('add-category'), 'title="Add Category" style="text-align:center;width:100%;"'); ?>-->

                <span style="float: right; margin: 13px 25px 0px 0px">
                    <?php
                    if (isset($logged_in)) {
                        echo anchor(site_url() . 'forum/action', lang('add-forum'), 'title="Add Forum" style="text-align:center;width:100%;"');
                        ?>|
                        <a href="<?php echo site_url(); ?>forum/myforum">My Threads</a> |
                        <a href="<?php echo site_url(); ?>forum/mycontribution">My Contribution</a>|
                        <?php
                    }
                    //else {
                    ?>

                    <a href="<?php echo site_url(); ?>forum/today_thread">Today's Threads</a> |
                    <a href="<?php echo site_url(); ?>forum/popular_post">Popular Post</a>
                </span>
                <?php
// }
                ?>
            </div>
            <br/>
            <div class="search-box">
                <table cellspacing="2" cellpadding="4" border="0">
                    <tbody>
                        <tr>
                            <td align="right"><?php echo lang('search_by_category'); ?>:</td>
                            <td align="left">
                                <?php
                                $input_data = array(
                                    'name' => 'search_term',
                                    'id' => 'search_term',
                                    'value' => set_value('search_term', urldecode($search_term))
                                );
                                echo form_input($input_data);
                                ?>
                            </td>
                            <td>
                                <?php
                                $search_button = array(
                                    'content' => lang('search'),
                                    'title' => lang('search'),
                                    'class' => 'inputbutton',
                                    'onclick' => "submit_search()",
                                );
                                echo form_button($search_button);
                                ?>
                            </td>
                            <td>
                                <?php
                                $reset_button = array(
                                    'content' => lang('reset'),
                                    'title' => lang('reset'),
                                    'class' => 'inputbutton',
                                    'onclick' => "reset_data()",
                                );
                                echo form_button($reset_button);
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="grid-data grid-data-table">
                <table cellspacing="1" cellpadding="4" border="0"  width="100%">
                    <tbody bgcolor="#fff">
                        <tr>

                            <th>
                                <?php echo lang('categories'); ?>
                            </th>
                            <th style="width: 50px">
                                <?php echo lang('total_forum'); ?>
                            </th>


                        </tr>
                        <?php
                        if (!empty($categories)) {
                            ?>
                            <?php
                            $section_name = get_section($this->_ci);
                            if ($section_name == "") {
                                $section_name = "front";
                            }

                            if (isset($page_number) && $page_number > 1) {
                                $i = ($this->_ci->session->userdata[$section_name]['record_per_page'] * ($page_number - 1)) + 1;
                            } else {
                                $i = 1;
                            }
                            foreach ($categories as $category) {
                                if ($i % 2 != 0) {
                                    $color = "lightgray";
                                } else {
                                    $color = "white";
                                }

                                $category_id = $category['categories']['category_id'];
                                $slug = $category['categories']['slug_url'];
                                ?>
                                <tr style="height: 50px; background-color: <?php echo $color; ?>">

                                    <td><a href="<?php echo site_url() ?>forum/forum_listing/<?php echo $slug; ?>" title="see forums"><?php echo $category['categories']['title']; ?></a>
                                        <br><?php echo $category['categories']['description']; ?>

                                    </td>
                                    <?php //echo anchor(site_url() . 'admin/forum/forum_listing'.$category_id, lang('forum'), 'title="See forums" style="text-align:center;width:100%;"');  ?>
                                    <td><?php echo $category['categories']['total_forum' . $category_id]; ?></td>

                                </tr>
                                <?php
                                $i++;
                            }
                            echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
                            echo form_hidden('page_number', "", "page_number");
                            echo form_hidden('per_page_result', "", "per_page_result");
                            ?>

                            <tr>
                                <td colspan="2">
                                    <?php
                                    $options = array(
                                        'total_records' => $total_records,
                                        'page_number' => $page_number,
                                        'isAjaxRequest' => 1,
                                        'base_url' => base_url() . "forum/index",
                                        'params' => $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '',
                                        'element' => 'ajax_table'
                                    );
                                    widget('custom_pagination', $options);
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                } else {
                    ?>

                </div>
                <table>
                    <tr>
                        <td><?php echo lang('no_records_found'); ?></td>
                    </tr>
                </table>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<!---------------------------------------------html(complete)----------------------------------------------------->

<!---------------------------------------------js & ajax area(start)----------------------------------------------------->
<script type="text/javascript">
    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            submit_search();
        }
    });
    function submit_search()
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        if ($('#search_term').val() == '') {
            $('#search_term').validationEngine('showPrompt', '<?php echo lang('msg_search_req'); ?>', 'error');
            attach_error_event(); //for remove dynamically populate popup
            return false;
        }
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>forum/index',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val())},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        unblockUI();
    }
    $(document).ready(function() {
        $(".tab-headings li a").click(function()
        {
            var thisId = $(this).attr("rel");
            $(".tab-headings li").removeClass("selected");
            $(this).parent('li').addClass("selected");
            $(".profile-content").hide();
            $(".add-comment-box").hide();
            var lang_code = thisId.replace("#content_", "");
            load_form(lang_code);
        });

        load_form = function(lang_code) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>forum',
                data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>'},
                success: function(msg) {
                    $("#ajax_table").html(msg);
                }
            });
        }

    });
    function sort_data(sort_by, sort_order)
    {
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>forum/index',
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
            url: '<?php echo base_url(); ?>forum/index',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }
</script>
<!---------------------------------------------js & ajax area(complete)----------------------------------------------------->