<div id="ajax_table">
    <div class="main-container"> 
        <div class="search-box">
            <table cellspacing="2" cellpadding="4" border="0">
                <tbody>

                <td align="right"><?php echo lang('search-by-module'); ?> :</td>
                <td align="left">
                    <?php
                    $input_data = array(
                        'name' => 'search_term',
                        'id' => 'search_term',
                        'value' => set_value('search_term', urldecode($search_term))
                    );
                    ?>
                </td>
                <td>
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
                        'content' => lang('btn-reset'),
                        'title' => lang('btn-reset'),
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
            <div class="add-new"> 
                <span style="float: left;"><?php echo add_image(array('active.png')) . "  " . lang('active') . " " . add_image(array('inactive.png')) . " " . lang('inactive') . ""; ?></span>
                <?php echo anchor(site_url() . 'admin/modulebuilder/generate_module', lang('generate-module'), 'title="Add Module" style="text-align:center;width:100%;"'); ?>
            </div>
            <table cellspacing="1" cellpadding="4" border="0" bgcolor="#e6ecf2" width="100%">
                <tbody bgcolor="#fff">
                    <?php
                    if (!empty($modules))
                    {
                        ?>
                        <?php echo form_open(); ?>
                        <tr>
                            <th width="30px"><input type="checkbox" name="check_all" id="check_all" value="0"></th>
                            <th width="30px"><?php echo lang('no') ?></th>
                            <th><?php echo lang('module-name') ?></th>
                            <th><?php echo lang('status') ?></th>
                            <th><?php echo lang('actions') ?></th>
                        </tr>
                        <?php
                        if ($page_number > 1)
                        {
                            $i = ($this->session->userdata[get_section($this)]['record_per_page'] * ($page_number - 1)) + 1;
                        }
                        else
                        {
                            $i = 1;
                        }
                        foreach ($modules as $module)
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
                            <tr class="<?php echo $class; ?> rows" id="row-<?php echo $module['R']['id']; ?>">
                                <td><input type="checkbox" id="<?php echo $module['R']['id']; ?>" name="check_box[]" class="check_box" value="<?php echo $module['R']['id']; ?>"></td>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $module['R']['module_name']; ?></td>
                                <td><?php
                    if ($module['R']['status'] == 1)
                    {
                        echo add_image(array('active.png'));
                    }
                    else
                    {
                        echo add_image(array('inactive.png'));
                    }
                            ?></td>
                                <td>
                                    <div class="action">
                                        <div style="float:left;padding-right:10px;"><a href="<?php echo site_url();?>admin/modules/view_data/<?php echo $module['R']['id'] ?>"><?php echo  add_image(array('viewIcon.png')); ?></a></div>
                                        <div class="edit"><a href="<?php echo site_url(); ?>admin/modules/action/edit/<?php echo $module['R']['id'] ?>" title="<?php echo lang('edit') ?>"><?php echo add_image(array('edit.png')); ?></a></div>
                                        <?php
                                        $module_id = $module['R']['id'];
                                        $deletelink = "<a href='javascript:;' title='Delete' onclick='delete_module($module_id)'>" . add_image(array('delete.png')) . "</a>";
                                        ?>
                                        <div class="delete"><?php echo $deletelink ?></div>
                                    </div>    
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                        <tr>
                            <td colspan="5">
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
                        <?php
                        echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
                        echo form_hidden('page_number', "", "page_number");
                        echo form_hidden('per_page_result', "", "per_page_result");
                        ?>
                        <?php echo form_close(); ?>
                        <?php
                    }
                    else
                    {
                        ?>
                        <tr>
                            <td><?php echo lang('no-records') ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
            $querystr = $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
            $options = array(
                'total_records' => $total_records,
                'page_number' => $page_number,
                'isAjaxRequest' => 1,
                'base_url' => base_url() . "admin/modules/index",
                'params' => $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '',
                'element' => 'ajax_table'
            );
            widget('custom_pagination', $options);
            ?>

        </div>
    </div>
</div>
<script type="text/javascript">	
    $("#search_term").keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                submit_search();
            }
    });
    $(function () {
        $("#check_all").click(function () {
            if ($("#check_all").is(':checked')) {
                $(".check_box").prop("checked", true);
            } else {
                $(".check_box").prop("checked", false);
            }
        });
    });

    function delete_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        
        $.ajax({
            type:'POST',
            url: '<?php echo base_url(); ?>admin/modules/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',type:'delete',ids:val},
            success: function (data) { 
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";                        
                }else{
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";                        
                }   
                ajaxLink('<?php echo base_url(); ?>admin/modules/index','ajax_table','<?php echo $querystr; ?>'+pageno);
            }
        });
    }
    
    function active_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        
        $.ajax({
            type:'POST',
            url: '<?php echo base_url(); ?>admin/modules/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',type:'active',ids:val},
            success: function (data) { 
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";                        
                }else{
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";                        
                }   
                ajaxLink('<?php echo base_url(); ?>admin/modules/index','ajax_table','<?php echo $querystr; ?>'+pageno);
            }
        });
    }
    
    function inactive_records()
    {
        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        
        $.ajax({
            type:'POST',
            url: '<?php echo base_url(); ?>admin/modules/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',type:'inactive',ids:val},
            success: function (data) { 
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";                        
                }else{
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";                        
                }   
                ajaxLink('<?php echo base_url(); ?>admin/modules/index','ajax_table','<?php echo $querystr; ?>'+pageno);
            }
        });
    }
    
    function active_all_records()
    {
        $.ajax({
            type:'POST',
            url: '<?php echo base_url(); ?>admin/modules/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',type:'active_all'},
            success: function (data) { 
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";                        
                }else{
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";                        
                }   
                ajaxLink('<?php echo base_url(); ?>admin/modules/index','ajax_table','<?php echo $querystr; ?>'+pageno);
            }
        });
    }
    
    function inactive_all_records()
    {
        $.ajax({
            type:'POST',
            url: '<?php echo base_url(); ?>admin/modules/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',type:'inactive_all'},
            success: function (data) { 
                //for managing same state while record delete
                if($('.rows') && $('.rows').length > 1){
                    pageno = "&page_number=<?php echo $page_number; ?>";                        
                }else{
                    pageno = "&page_number=<?php echo $page_number - 1; ?>";                        
                }   
                ajaxLink('<?php echo base_url(); ?>admin/modules/index','ajax_table','<?php echo $querystr; ?>'+pageno);
            }
        });
    }
    
    function delete_module(id){
        
        res = confirm('<?php echo lang('delete-alert') ?>');    
        if(res){
            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>admin/modules/delete',
                data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',id:id},
                success: function() {
                    location.href = "<?php echo base_url(); ?>admin/modules";
                    $("#messages").show();
                    $("#messages").html(data); 
                }
            });                 
        }else{
            return false;
        }
    }
    function submit_search()
    {
        if($('#search_term').val() == ''){
            $('#search_term').validationEngine('showPrompt', '<?php echo lang('msg-search-req'); ?>', 'error');
            attach_error_event(); //for remove dynamically populate popup
            return false;
        }          
        blockUI('removeError');
        $.ajax({
            type:'POST',
            url:'<?php echo base_url(); ?>admin/modules/index',
            data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',search_term:$('#search_term').val()},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        }); 
        unblockUI();
    }
    function sort_data(sort_by,sort_order)
    {
        blockUI();
        $.ajax({
            type:'POST',
            url:'<?php echo base_url(); ?>admin/modules/index',
            data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',search_term:$('#search_term').val(),sort_by:sort_by,sort_order:sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        }); 
        unblockUI();
    }
    function reset_data()
    {        
        blockUI('removeError');
        $.ajax({
            type:'POST',
            url:'<?php echo base_url(); ?>admin/modules/index',
            data:{<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>:'<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>',search_term:""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });         
    }
</script>