<article class="card" id="ajax_table">
    <div class="article-header">
        <?php echo lang('page-title'); ?>
    </div>
    <div class="card-wrap">
        <div class="row site-controls-outer users-list-container">
            <div class="col-sm-12 col-lg-9">
                <form class="default-form clearfix col-sm-12 col-lg-12" method="post">
                    <div class="form-group col-md-3 col-sm-6">
                        <input type="text" readonly="readonly" name="search_startdate" id="search_startdate" class="form-control" placeholder="Start Date" value="<?php echo isset($search_startdate) && !empty($search_startdate) ? htmlspecialchars_decode($search_startdate) : ''; ?>">
                    </div>
                    <div class="form-group col-md-3 col-sm-6">
                        <input type="text" readonly="readonly" name="search_enddate" id="search_enddate" class="form-control" placeholder="End Date" value="<?php echo isset($search_enddate) && !empty($search_enddate) ? htmlspecialchars_decode($search_enddate) : ''; ?>">
                    </div>
                    <div class="form-group col-md-3 col-sm-6">
                        <div class="form-dropdown">
                            <?php $role_selected = (isset($role_select) && !empty($role_select)) ? htmlspecialchars_decode($role_select):0;?>
                            <?php //echo $role_selected;exit; ?>
                            <select name="role_select" data-type="custom-dropdown-update" id="role_select">
                                <option value="">--- Select Role ---</option>
                                <?php foreach ($roles as $role){    ?>
                                    <?php if($role['id'] == $role_selected) {?>
                                <option value="<?php echo $role['id'] ?>" selected="selected"><?php echo $role['role_name'] ?></option>
                                    <?php }else{?>
                                        <option value="<?php echo $role['id'] ?>"><?php echo $role['role_name'] ?></option>
                                    <?php }?>
                                    
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-sm-6">
                        <button class="btn btn-secondary" type="submit" id="submit_search">
                            <img src="images/search-icon.png" alt="Search">
                            <?php echo lang('btn-search'); ?> 
                        </button>
                        <button class="btn btn-reset" type="reset" onclick="reset_data()">
                            <img src="images/reset-icon.png" alt="Reset">
                            <?php echo lang('btn-reset'); ?>
                        </button>
                    </div>
                    <div class="form-group">
                        
                    </div>
                </form>
            </div>
            <div class="con col-lg-12">
                <div class="table-responsive"> 
                    <?php if (!empty($logs)) { ?>            
                        <table class="table table-striped">
                            <thead>
                                <tr>
<!--                                    <th>
                                        <input type="checkbox" name="check_all" id="check_all" class="icheck">	
                                    </th>-->
                                    <th class="digits-col"><?php echo lang('no') ?></th>
                                    <th class="name-col">
                                        <?php echo lang('firstname'); ?>
                                    </th>
                                    <th class="name-col">
                                        <?php echo lang('username'); ?>
                                    </th>
                                    <th class="name-col">
                                        <?php
                                        $field_sort_order = 'asc';
                                        $sort_image = 'srt_down.png';
                                        if ($sort_by == 'l.role_id' && $sort_order == 'asc') {
                                            $sort_image = 'srt_up.png';
                                            $field_sort_order = 'desc';
                                        }
                                        ?>
                                        <a href="javascript:void(0)" onclick="sort_data('l.role_id', '<?php echo $field_sort_order; ?>');" ><?php echo lang('role'); ?></a>
                                        <?php
                                        if ($sort_by == 'l.role_id') {    ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                        <?php } ?>
                                    </th>
                                    <th class="name-col">
                                        <?php
                                        $field_sort_order = 'asc';
                                        $sort_image = 'srt_down.png';
                                        if ($sort_by == 'DATE(logged_in_at)' && $sort_order == 'asc') {
                                            $sort_image = 'srt_up.png';
                                            $field_sort_order = 'desc';
                                        }
                                        ?>
                                        <a href="javascript:void(0)" onclick="sort_data('DATE(logged_in_at)', '<?php echo $field_sort_order; ?>');" ><?php echo lang('date'); ?></a>
                                        <?php
                                        if ($sort_by == 'DATE(logged_in_at)') {    ?>
                                        <div class="sorting">
                                            <?php echo add_image(array($sort_image)); ?>
                                        </div>
                                        <?php } ?>
                                    </th>
                                    <th class="name-col">
                                        <?php echo lang('time'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($page_number > 1) {
                                    $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;
                                } else {
                                    $i = 1;
                                }
                                foreach ($logs as $log) {
                                    if ($i % 2 != 0) {
                                        $class = "odd-row";
                                    } else {
                                        $class = "even-row";
                                    }
                                    ?>
                                    <tr class="<?php echo $class; ?> rows" id="row-<?php echo $log['id']; ?>">
<!--                                        <td>
                                            <div class="ckbox ckbox-default">
                                                <input type="checkbox" id="<?php echo $log['id']; ?>" name="check_box[]" class="icheck check_box" value="<?php echo base64_encode($log['id']); ?>">
                                            </div>
                                        </td>-->
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $log["firstname"]; ?></td>
                                        <td><?php echo $log["username"]; ?></td>
                                        <td><?php echo $log["role_name"]; ?></td>
                                        <?php
                                            $date = new DateTime($log["logged_in_at"]);
                                        ?>
                                        <td><?php echo $date->format('d M, Y'); ?></td>
                                        <td><?php echo $date->format('h:i:s A'); ?></td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
<!--                    <div class="btn-panel">
                        <button type="button" class="btn btn-custom btn-red" onclick="delete_records()"><?php echo lang('delete'); ?></button>
                    </div>-->
                    <?php
                    echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
                    echo form_hidden('page_number', "", "page_number");
                    echo form_hidden('per_page_result', "", "per_page_result");

                    echo form_close();
                } else {
                    ?>
                    <div class="table-responsive">          		
                        <table class="table table-striped" >
                            <tr>
                                <td><?php echo lang('no-records') ?></td>
                            </tr>
                        </table>
                    </div>
                <?php } ?> 
                <?php
                $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_startdate=' . urlencode($search_startdate) . '&search_enddate=' . urlencode($search_enddate) . '&role_select=' . urlencode($role_select) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
                
                $options = array(
                    'total_records' => $total_records,
                    'page_number' => $page_number,
                    'isAjaxRequest' => 1,
                    'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "user_login_log/index",
                    'params' => $querystr,
                    'element' => 'ajax_table'
                );
                widget('custom_pagination', $options);
                ?>
            </div>
        </div>
        
    </div>
</article>
<script type="text/javascript">
    //remove dynamically populate error
    $("#search_term_firstname, #search_term_username").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $('#submit_search').trigger('click');
        }
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
    $(function() {
        var curDate = new Date();
        $("#search_startdate").datepicker({
            dateFormat: 'dd-M-yy',
            'maxDate' : curDate,
            onSelect: function(selected) {
                var dt = new Date(selected);
                dt.setDate(dt.getDate());
                $("#search_enddate").datepicker("option", "minDate", dt);
            }
        });
        $("#search_enddate").datepicker({
            dateFormat: 'dd-M-yy',
        });
        $("select[data-type='custom-dropdown-update']").dropkick({
            mobile: true
        });
        
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
    
    function reset_data()
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>user_login_log/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_startdate: "", search_enddate: "", role_select: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }
    
    function sort_data(sort_by, sort_order)
    {
        $('#error_msg').fadeOut(1000); //hide error message it shown up while search
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>user_login_log/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_startdate: encodeURIComponent($('#search_startdate').val()), search_enddate: encodeURIComponent($('#search_enddate').val()), role_select: encodeURIComponent($('#role_select').val()), sort_by: sort_by, sort_order: sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });

    }
</script>