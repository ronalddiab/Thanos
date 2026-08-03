<div id="ajax_table">
        <div class="row site-controls-outer">
            <div class="col-sm-9">
                <form class="default-form form-inline clearfix">
                    <div class="form-group form-control-outer">
                        <input id="search_term" name="search_term" type="text" placeholder="Search by Title" class="form-control" value="<?php echo set_value('search_term', urldecode($search_term)); ?>">
                    </div>
                    <div class="form-group">
                        <button onclick="submit_search()" class="btn btn-secondary" title="<?php echo lang('search'); ?>" type="button" name="">
                            <img alt="Search" src="images/search-icon.png"><?php echo lang('search'); ?>
                        </button>
                    </div>
                    <div class="form-group">
                        <button onclick="reset_data()" class="btn btn-reset" title="Reset" type="button" name=""><img alt="Reset" src="images/reset-icon.png">Reset</button>                       
                    </div>
                </form>
            </div>
            <div class="col-sm-3">
                <a class="btn btn-blue pull-right" onclick="openlink('add')" href="#"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo lang('add_category');?></a>
            </div>
        </div>

        <?php if (!empty($category_list)){ ?>

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
                            <th width="30px"><input class="icheck" type="checkbox" name="check_all" id="check_all" value="0"></th>
                            <th><?php echo lang('sr_no') ?></th>
                            <th>
                                <?php
                                $field_sort_order = 'asc';
                                $sort_image = 'srt_down.png';
                                if($sort_by == 'c.title' && $sort_order == 'asc')
                                {
                                    $sort_image = 'srt_up.png';
                                    $field_sort_order = 'desc';
                                }
                                ?>
                                <a href="javascript:void(0)" onclick="sort_data('c.title', '<?php echo $field_sort_order;?>');" >
                                    <?php echo lang('title'); ?>
                                    <?php if($sort_by == 'c.title')
                                    {
                                        ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                        <?php
                                    } ?>
                                </a>
                            </th>
                            <th><?php echo lang('status') ?></th>
                            <th><?php echo lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';

                        if (!empty($category_list))
                        {
                            if ($page_number > 1) {
                                $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                            } else {
                                $i = 1;
                            }
                            foreach ($category_list as $category_page)
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
                                <tr class="<?php echo $class; ?>" >
                                    <td><input type="checkbox" id="<?php echo $category_page['c']['id']; ?>" name="check_box[]" class="check_box icheck" value="<?php echo $category_page['c']['id']; ?>"></td>
                                    <td><?php echo $i; ?></td>
                                    <td>
                                        <?php
                                        if(isset($category_page['c']['parent_id']) && $category_page['c']['parent_id'] != 0)
                                        {
                                            echo '&nbsp;&nbsp;&nbsp; - ';
                                        }
                                        echo $category_page['c']['title']; ?></td>
        <!--                            <td><?php // echo $category_page['c']['slug_url']; ?></td>-->

                                    <td>
                                        <?php
                                        if ($category_page['c']['status'] == 1)
                                        {
                                            echo add_image(array('active.png'));
                                        }
                                        else
                                        {
                                            echo add_image(array('inactive.png'));
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        
                                                <a href="<?php echo site_url () . BASE_ADMIN_URL_CUSTOM; ?>categories/view/<?php echo $category_page['l']['language_code'] . "/" . $category_page['c']['category_id']; ?>"><?php echo add_image (array ('search-icon-black.png')); ?></a>                              
                                                <a href="<?php echo site_url().BASE_ADMIN_URL_CUSTOM; ?>categories/action/edit/<?php echo $category_page['l']['language_code'] . "/" . $category_page['c']['category_id']; ?>" title="<?php echo lang('edit'); ?>"><?php echo add_image(array('edit-icon.png')); ?></a>
                                                <a href='javascript:;' title='<?php echo lang('delete'); ?>' onclick="delete_category('<?php echo $category_page['c']['id']; ?>', '<?php echo $category_page['c']['slug_url']; ?>')"><?php echo add_image(array('delete-icon.png')); ?></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
                <?php
                }
                else
                {
                ?>
                <table>
                    <tr>
                        <td><?php echo lang('no_records_found') ?></td>
                    </tr>
                </table>
                <?php
                }
                ?>
            </div>

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
                'class' => 'btn btn-custom btn-green',
                'onclick' => "active_records()",
            );
            echo form_button($reset_button);
            ?>
            <?php
            $reset_button = array(
                'content' => lang('inactive'),
                'title' => lang('inactive'),
                'class' => 'btn btn-custom btn-yellow',
                'onclick' => "inactive_records()",
            );
            echo form_button($reset_button);
            ?>
            <?php
            $reset_button = array(
                'content' => lang('active-all'),
                'title' => lang('active-all'),
                'class' => 'btn btn-custom btn-green',
                'onclick' => "active_all_records()",
            );
            echo form_button($reset_button);
            ?>
            <?php
            $reset_button = array(
                'content' => lang('inactive-all'),
                'title' => lang('inactive-all'),
                'class' => 'btn btn-custom btn-yellow',
                'onclick' => "inactive_all_records()",
            );
            echo form_button($reset_button);
            ?>
            </div>


            <?php
            if (!empty($category_list)){
                $options = array(
                    'total_records' => $total_records,
                    'page_number' => $page_number,
                    'isAjaxRequest' => 1,
                    'base_url' => base_url().BASE_ADMIN_URL_CUSTOM."categories/ajax_index/" . $language_code,
                    'params' => $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term). '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '',
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
                        <td><?php echo lang('no_records_found') ?></td>
                    </tr>
                </table>
            </div>
            <?php 
        } ?>
</div>
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
//        if($('#search_term').val() == ''){
//            $('#search_term').validationEngine('showPrompt', '<?php echo lang('msg_search_req'); ?>', 'error');
//            attach_error_event(); //for remove dynamically populate popup
//            return false;
//        }
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index/<?php echo $language_code; ?>',
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
            url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index/<?php echo $language_code; ?>',
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
            url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term: ""},
            error: function(){
		        alert("Server problem. Please try again.");
       			return false;
	        },
       		complete: function(){
	        	unblockUI();
       		},
            success: function(data) {
                $("#ajax_table").html(data);
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
            alert('Please select atleast one record for active');
            return false;
        }
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index/<?php echo $language_code; ?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>',type:'active',ids:val},
            success: function (data) {
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";
                }else{
                    pageno = "&page_number=<?php echo $page_number; ?>";
                }
                ajaxLink('<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index','ajax_table','<?php echo $querystr; ?>'+pageno);
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
            alert('Please select atleast one record for inactive');
            return false;
        }
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',type:'inactive',ids:val},
            success: function (data) {
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";
                }else{
                    pageno = "&page_number=<?php echo $page_number; ?>";
                }
                ajaxLink('<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index','ajax_table','<?php echo $querystr; ?>'+pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }
    function active_all_records()
    {
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',type:'active_all'},
            success: function (data) {
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";
                }else{
                    pageno = "&page_number=<?php echo $page_number; ?>";
                }
                ajaxLink('<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index','ajax_table','<?php echo $querystr; ?>'+pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }

    function inactive_all_records()
    {
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',type:'inactive_all'},
            success: function (data) {
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";
                }else{
                    pageno = "&page_number=<?php echo $page_number; ?>";
                }
                ajaxLink('<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index','ajax_table','<?php echo $querystr; ?>'+pageno);
                $("#messages").show();
                $("#messages").html(data);
            }
        });
    }
     function delete_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        if(val=="")
        {
            alert('Please select atleast one record for delete');
            return false;
        }
        res = confirm('<?php echo lang('delete_confirm') ?>');
        if(res){
            $.ajax({
                type:'POST',
                url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index',
                data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>:'<?php echo $this->_ci->security->get_csrf_hash(); ?>',type:'delete',ids:val},
                success: function (data) {

                    //for managing same state while record delete
                    if($('.rows') && $('.rows').length > 1){
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    }else{
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    }
                    ajaxLink('<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>categories/ajax_index','ajax_table','<?php echo $querystr; ?>'+pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                }
            });
        }else
        {
            return false;
        }
       }

       $('.icheck').iCheck({
            checkboxClass: 'icheckbox_square',
            radioClass: 'iradio_square',
            increaseArea: '20%' // optional
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