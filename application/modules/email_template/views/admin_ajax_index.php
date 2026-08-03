<div id="ajax_table">
    <div class="main-container">
        <div class="search-box">
            <table cellspacing="2" cellpadding="4" border="0">
                <tbody>
                    <tr>
                        <td align="right"><?php echo lang('search-by-title') ?> :</td>
                        <?php
                        $input_data = array(
                            'name' => 'search_term',
                            'id' => 'search_term',
                            'title' => 'search',
                            'value' => set_value('search_term', urldecode($search_term))
                        );
                        ?>
                        <td align="left" colspan="2">
                            <?php
                            echo form_input($input_data);

                            ?>
                        </td>
                        <td>
                            <?php
                            $search_button = array(
                                'content' => lang('btn-search'),
                                'title' => lang('btn-search'),
                                'class' => 'inputbutton',
                                'onclick' => "submit_search()",
                            );
                            echo form_button($search_button);
                            ?>
                        </td>
                        <td>
                        <?php
                        $reset_button = array(
                            'content' => lang('reset_button'),
                            'title' => lang('reset_button'),
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

            <?php
            $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
            if (count($email_template_list) > 0)
            {

                ?>
        <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
            <tbody bgcolor="#fff">
                <tr>
                    <th width="30px"><input type="checkbox" name="check_all" id="check_all" value="0"></th>
                    <th><?php echo lang('sr_no'); ?></th>
                    <th>
                        <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if($sort_by == 'c.template_name' && $sort_order == 'asc')
                            {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                        ?>
                        <a href="#" onclick="sort_data('c.template_name', '<?php echo $field_sort_order;?>');" >
                            <?php echo lang('template-name'); ?>
                            <?php if($sort_by == 'c.template_name')
                            {
                                ?>
                                <div class="sorting">
                                    <?php echo add_image(array($sort_image)); ?>
                                </div>
                                <?php
                            } ?>
                        </a>
                    </th>
                    <th>
                         <?php
                            $field_sort_order = 'asc';
                            if($sort_by == 'c.status' && $sort_order == 'asc')
                            {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                        ?>

                        <a href="#" onclick="sort_data('c.status', '<?php echo $field_sort_order;?>');" ><?php echo lang('status'); ?></a>
                     <?php if($sort_by == 'c.status') { ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                            <?php } ?>
                    </th>
                    <th>
                        <?php
                            $field_sort_order = 'asc';
                            if($sort_by == 'c.modified' && $sort_order == 'asc')
                            {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                        ?>
                        <a href="#" onclick="sort_data('c.modified', '<?php echo $field_sort_order;?>');" ><?php echo lang('last_modified'); ?></a></th>
                    <th><?php echo lang('actions'); ?></th>
                    </tr>
                    <?php
                    if ($page_number > 1)
                        {
                            $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                        }
                        else
                        {
                            $i = 1;
                        }

               // $i = 1;
                foreach ($email_template_list as $template)
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
                    <tr class="<?php echo $class; ?>">
                        <td><input type="checkbox" id="<?php echo $template['c']['id']; ?>" name="check_box[]" class="check_box" value="<?php echo $template['c']['id']; ?>"></td>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $template['c']['template_name']; ?></td>
                        <td>
                            <?php
                            if ($template['c']['status'] == '1')
                            {
                                ?>
                                <?php echo add_image(array('active.png'), "", "", array("title" => "active")); ?>
                                <?php
                            }
                            elseif ($template['c']['status'] == '0')
                            {
                                ?>
                                <?php echo add_image(array('inactive.png'), "", "", array("title" => "inactive")); ?>
                            <?php } ?>
                        </td>
                        <td><?php echo $template['c']['modified']; ?></td>
                        <td>
                            <div class="action">
                                <div class="edit">
                                    <a title='View' href="<?php echo site_url().$this->_data['section_name']; ?>/email_template/view/<?php echo $template['l']['language_code'] . "/" . $template['c']['template_id']; ?>"><?php echo add_image(array('viewIcon.png')); ?></a>
                                </div>
                                <div class="edit">
                                    <a href="<?php echo site_url().$this->_data['section_name']; ?>/email_template/action/edit/<?php echo $template['l']['language_code'] . "/" . $template['c']['template_id']; ?>" title="<?php echo lang('edit'); ?>"><?php echo add_image(array('edit.png')); ?></a>
                                </div>
                                <div class="delete"><a href='javascript:;' title='<?php echo lang('delete'); ?>' onclick="delete_cms('<?php echo $template['c']['id']; ?>')"><?php echo add_image(array('delete.png')); ?></a></div>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                <tr class="odd-row">
                            <td colspan="9">
                                <?php
                                $reset_button = array(
                                    'content' => lang('delete'),
                                    'title' => lang('delete'),
                                    'class' => 'inputbutton',
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
                                echo form_button($reset_button);
                                ?>
                                <?php
                                $reset_button = array(
                                    'content' => lang('inactive'),
                                    'title' => lang('inactive'),
                                    'class' => 'inputbutton',
                                    'onclick' => "inactive_records()",
                                );
                                echo form_button($reset_button);
                                ?>
                                <?php
                                $reset_button = array(
                                    'content' => lang('active-all'),
                                    'title' => lang('active-all'),
                                    'class' => 'inputbutton',
                                    'onclick' => "active_all_records()",
                                );
                                echo form_button($reset_button);
                                ?>
                                <?php
                                $reset_button = array(
                                    'content' => lang('inactive-all'),
                                    'title' => lang('inactive-all'),
                                    'class' => 'inputbutton',
                                    'onclick' => "inactive_all_records()",
                                );
                                echo form_button($reset_button);
                                ?>
                            </td>
                        </tr>
                </tbody>
            </table>
            <?php

            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => base_url() .$this->_data['section_name']."/email_template/ajax_index/" . $language_code,
                'params' => $querystr,
                'element' => 'ajax_table'
            );
            widget('custom_pagination', $options);
        }
        else
        {
            ?>
            <table>
                <tr><td colspan="6"><?php echo lang('no_record_found'); ?></td></tr>
            </table>
            <?php
        }
        ?>
    </div>

<script>
    $("#search_term").keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                submit_search();
            }
    });
    function submit_search()
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        /*if($('#search_term').val() == ''){
            $('#search_term').validationEngine('showPrompt', '<?php echo lang('msg-search-req'); ?>', 'error');
            attach_error_event(); //for remove dynamically populate popup
            return false;
        } */
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term:encodeURIComponent($('#search_term').val())},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        unblockUI();
    }
    function sort_data(sort_by, sort_order)
    {
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term:encodeURIComponent($('#search_term').val()), sort_by: sort_by, sort_order: sort_order},
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
            type:'POST',
            url: '<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }

    $(function () {
        $("#check_all").click(function () {
            if ($("#check_all").is(':checked')) {
                $(".check_box").prop("checked", true);
            } else {
                $(".check_box").prop("checked", false);
            }
        });
        $(".check_box").click(function(){

            if($(".check_box").length == $(".check_box:checked").length) {
                $("#check_all").prop("checked", true);
                $(".check_box").attr("checked", "checked");
            } else {
                $("#check_all").removeAttr("checked");
            }

        });
    });

    function active_records()
    {

        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        if(val=="")
        {
            alert('Please select at least one record for active');
            return false;
        }
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>',type:'active',ids:val},
            success: function (data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>','ajax_table','<?php echo $querystr; ?>'+pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        if(val=="")
        {
            alert('Please select at least one record for inactive');
            return false;
        }
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',type:'inactive',ids:val},
            success: function (data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>','ajax_table','<?php echo $querystr; ?>'+pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }
    function active_all_records()
    {
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',type:'active_all',lang:'<?php echo $language_id ?>'},
            success: function (data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>','ajax_table','<?php echo $querystr; ?>'+pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_all_records()
    {
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',type:'inactive_all',lang:'<?php echo $language_id ?>'},
            success: function (data) {
                //for managing same state while record delete
                pageno = "&page_number=<?php echo $page_number; ?>";
                ajaxLink('<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>','ajax_table','<?php echo $querystr; ?>'+pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function delete_user(id){
        res = confirm('<?php echo lang('delete-alert') ?>');
        if(res){
            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>admin/email_template/delete',
                data:{<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',id:id},
                success: function(data) {
                    //for managing same state while record delete
                    if($('.rows') && $('.rows').length > 1){
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    }else{
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url(); ?>admin/email_template/index/<?php echo $language_code; ?>','ajax_table','<?php echo $querystr; ?>'+pageno);

                    //set responce message
                    $("#messages").show();
                    $("#messages").html(data);
                }
            });

        }else{
            return false;
        }
    }
    function delete_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        if(val=="")
        {
            alert('Please select at least one record for delete');
            return false;
        }
        if(confirm("Are you sure you want to delete this record"))
        {
                $.ajax({
                    type:'POST',
                    url: '<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index',
                    data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',type:'delete',ids:val},
                    success: function (data) {

                        //for managing same state while record delete
                        if($('.rows') && $('.rows').length > 1){
                            pageno = "&page_number=<?php echo $page_number; ?>";
                        }else{
                            pageno = "&page_number=<?php echo $page_number - 1; ?>";
                        }
                        ajaxLink('<?php echo base_url().$this->_data['section_name']; ?>/email_template/ajax_index/<?php echo $language_code; ?>','ajax_table','<?php echo $querystr; ?>'+pageno);
                        $("#messages").show();
                        $("#messages").html(data);
                    }
                });
         }
    }
</script>
</div>