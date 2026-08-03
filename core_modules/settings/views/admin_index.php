<article class="card" id="ajax_table">
    <div class="article-header">
        <?php echo lang('setting-management'); ?> 
    </div>
    <div class="card-wrap">
        <div class="row site-controls-outer">
            <div class="col-sm-9">
                <form class="default-form form-inline clearfix" method="post">
                    <div class="form-group form-control-outer"> 
                        <input type="text" name="search_term" id="search_term" class="form-control" placeholder="<?php echo lang('search_by_title') ?>" value="<?php echo isset($search_term) && !empty($search_term) ? htmlspecialchars_decode($search_term) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary" type="submit" id="submit_search">
                            <img src="images/search-icon.png" alt="Search">
                            <?php echo lang('btn-search'); ?> 
                        </button>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-reset" type="reset" onclick="reset_data()">
                            <img src="images/reset-icon.png" alt="Reset">
                            <?php echo lang('btn-reset'); ?>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-sm-3">
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'settings/action/add' ?> " title="<?php lang('settings-add') ?>" class="btn btn-blue pull-right"><span><img src="images/plus-icon.png" alt=" Settings Add"></span><?php echo lang('settings-add'); ?></a>
            </div>
        </div>
        <div class="con">
            <?php echo form_open(); ?>
            <div class="table-responsive"> 
                <?php if (!empty($settings)) { ?>            
                    <table class="table table-striped">
                        <thead>
                            <tr>
    <!--                                <th>
                                    <input type="checkbox" name="check_all" id="check_all" class="icheck">	
                                </th>-->
                                <th class="digits-col"><?php echo lang('setting-id') ?></th>
                                <th class="name-col">
                                    <?php
                                    $field_sort_order = 'asc';
                                    $sort_image = 'srt_down.png';
                                    if ($sort_by == 's.setting_title' && $sort_order == 'asc') {
                                        $sort_image = 'srt_up.png';
                                        $field_sort_order = 'desc';
                                    }
                                    ?>
                                    <a href="javascript:void(0)" class="sort_field" onclick="sort_data('s.setting_title', '<?php echo $field_sort_order; ?>');" ><?php echo lang('setting-title') ?></a>
                                    <?php
                                    if ($sort_by == 's.setting_title') {
                                        ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>
                        </th>

                        <th class="name-col">
                            <?php
                            $field_sort_order = 'asc';
                            $sort_image = 'srt_down.png';
                            if ($sort_by == 's.setting_label' && $sort_order == 'asc') {
                                $sort_image = 'srt_up.png';
                                $field_sort_order = 'desc';
                            }
                            ?>
                            <a href="javascript:void(0)" class="sort_field" onclick="sort_data('s.setting_label', '<?php echo $field_sort_order; ?>');" ><?php echo lang('setting-label') ?></a>
                            <?php
                            if ($sort_by == 's.setting_label') {
                                ?>
                            <div class="sorting">
                                <?php echo add_image(array($sort_image)); ?>
                            </div>
                        <?php }
                        ?>
                        </th>
                        <th><?php echo lang('setting-value'); ?></th>
                        <th class="name-col"><?php echo lang('setting-comment'); ?></th>
                        <th class="name-col"><?php echo lang('setting-action') ?></th>
                        </tr>
                        </thead>
                        <tbody>

                            <?php
                            if ($page_number > 1) {
                                $i = ($this->session->userdata[get_current_section($this)]['record_per_page'] * ($page_number - 1)) + 1;
                            } else {
                                $i = 1;
                            }
                            foreach ($settings as $data) {
                                $alias = end(array_keys($data));
                                if ($i % 2 != 0) {
                                    $class = "odd-row";
                                } else {
                                    $class = "even-row";
                                }
                                ?>

                                <tr class="<?php //echo $class;   ?> rows" id="row-<?php //echo $data[$alias]['id'];   ?>">
        <!--                                    <td>
                                        <div class="ckbox ckbox-default">
                                            <input type="checkbox" id="<?php //echo $data[$alias]['id'];   ?>" name="check_box[]" class="icheck check_box" value="<?php echo $data[$alias]['id']; ?>"/>
                                            <label for="<?php //echo $data[$alias]['id'];   ?>"></label>
                                        </div>

                                    </td>-->
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $data[$alias]['setting_title']; ?></td>
                                    <td><?php echo $data[$alias]['setting_label']; ?></td>                  
                                    <td><?php echo $data[$alias]['setting_value']; ?></td>
                                    <td><?php echo $data[$alias]['comment']; ?></td>
                                    <td>
                                        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM ?>settings/view_data/<?php echo $data[$alias]['id']; ?>" title="<?php echo lang('view'); ?>"><img src="images/search-icon-black.png" alt="<?php echo lang('view'); ?>"></a>
                                        <a href="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/action/edit/<?php echo $data[$alias]['id']; ?>" title="<?php echo lang('edit'); ?>"><img src="images/edit-icon.png" alt="<?php echo lang('edit'); ?>"></a>
                                        <?php
                                        $setting_id = $data[$alias]['id'];
                                        $deletelink = "<a href='javascript:void(0);' title='Delete' onclick='delete_settings($setting_id)'><img src='images/delete-icon.png' alt='Delete'></a>";
                                        echo $deletelink
                                        ?>
                                    </td>
                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!--                <div class="btn-panel">
                                    <button type="button" class="btn btn-custom btn-red" onclick="delete_records()"><?php //echo lang('delete');   ?></button>
                                    <button type="button" class="btn btn-custom btn-green" onclick="active_records()"><?php //echo lang('active');   ?></button>
                                    <button type="button" class="btn btn-custom btn-yellow" onclick="inactive_records()"><?php //echo lang('inactive');   ?> </button>
                                    <button type="button" class="btn btn-custom btn-green" onclick="active_all_records()"><?php //echo lang('active-all');   ?></button>
                                    <button type="button" class="btn btn-custom btn-yellow" onclick="inactive_all_records()"><?php //echo lang('inactive-all');   ?></button>
                                </div>-->

                <?php
                echo form_hidden('search_text', (isset($search_text)) ? $search_text : '' );
                echo form_hidden('page_number', "", "page_number");
                echo form_hidden('per_page_result', "", "per_page_result");
                ?>
                <?php echo form_close(); ?>
                <!---buttons-->
            </div>
            <?php
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
        $querystr = $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';
        $options = array(
            'total_records' => $total_records,
            'page_number' => $page_number,
            'isAjaxRequest' => 1,
            'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "settings/index",
            'params' => $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '',
            'element' => 'ajax_table'
        );
        widget('custom_pagination', $options);
        ?>
    </div>
</article>

<script type="text/javascript">
    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $('#submit_search').trigger('click');
        }
    });
    $(function() {

        $('.sort_field').click(function(e) {
            e.preventDefault();
        });
        $('#check_all').on('ifChecked', function(event) {
            $('.check_box').parent('div').addClass('checked');
        });

        $('#check_all').on('ifUnchecked', function(event) {
            $('.check_box').parent('div').removeClass('checked');
        });
        $('.check_box').on('ifUnchecked', function(event) {
            $('#check_all').iCheck('uncheck');
        });

    });

    /*function delete_records()
     {
     var val = [];
     $(':checkbox:checked').each(function(i) {
     val[i] = $(this).val();
     });
     if (val == "")
     {
     alert('Please select at least one record for delete');
     return false;
     }
     $.ajax({
     type: 'POST',
     url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index',
     data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'delete', ids: val},
     success: function(data) {
     //for managing same state while record delete
     if ($('.rows') && $('.rows').length > 1) {
     pageno = "&page_number=<?php echo $page_number; ?>";
     } else {
     pageno = "&page_number=<?php echo $page_number - 1; ?>";
     }
     ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
     $("#messages").show();
     $("#messages").html(data);
     }
     });
     } */

    /*function active_records()
     {
     var val = [];
     $(':checkbox:checked').each(function(i) {
     val[i] = $(this).val();
     });
     if (val == "")
     {
     alert('Please select at least one record for active');
     return false;
     }
     $.ajax({
     type: 'POST',
     url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index',
     data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'active', ids: val},
     success: function(data) {
     //for managing same state while record delete
     pageno = "&page_number=<?php echo $page_number; ?>";
     ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
     $("#messages").show();
     $("#messages").html(data);
     }
     });
     }
     */

    /*function inactive_records()
     {
     var val = [];
     $(':checkbox:checked').each(function(i) {
     val[i] = $(this).val();
     });
     if (val == "")
     {
     alert('Please select at least one record for inactive');
     return false;
     }
     $.ajax({
     type: 'POST',
     url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index',
     data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'inactive', ids: val},
     success: function(data) {
     //for managing same state while record delete
     pageno = "&page_number=<?php echo $page_number; ?>";
     ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
     $("#messages").show();
     $("#messages").html(data);
     }
     });
     }
     */
    /*  function active_all_records()
     {
     $.ajax({
     type: 'POST',
     url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index',
     data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'active_all'},
     success: function(data) {
     //for managing same state while record delete
     pageno = "&page_number=<?php echo $page_number; ?>";
     ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
     $("#messages").show();
     $("#messages").html(data);
     }
     });
     }*/

    /*  function inactive_all_records()
     {
     $.ajax({
     type: 'POST',
     url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index',
     data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', type: 'inactive_all'},
     success: function(data) {
     //for managing same state while record delete
     pageno = "&page_number=<?php echo $page_number; ?>";
     ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
     $("#messages").show();
     $("#messages").html(data);
     }
     });
     }
     */
    function delete_settings(id) {

        res = confirm('<?php echo lang('delete-alert') ?>');
        if (res) {
            //blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/delete',
                data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', id: id},
                success: function(data) {

                    //for managing same state while record delete
                    if ($('.rows') && $('.rows').length > 1) {
                        pageno = "&page_number=<?php echo $page_number; ?>";
                    } else {
                        pageno = "&page_number=<?php echo $page_number - 1; ?>";
                    }
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index', 'ajax_table', '<?php echo $querystr; ?>' + pageno);
                    $("#messages").show();
                    $("#messages").html(data);
                    //unblockUI();
                }
            });
        } else {
            return false;
        }
    }

    $('#submit_search').click(function(e) {
        e.preventDefault();
        if ($('#search_term').val() != '') {
            blockUI();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index',
                data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: $('#search_term').val()},
                success: function(data) {
                    $("#ajax_table").html(data);
                    unblockUI();
                }
            });
        }
        else {
            alert("Please enter something to search");
        }


    });

    function sort_data(sort_by, sort_order)
    {
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: $('#search_term').val(), sort_by: sort_by, sort_order: sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }
    function reset_data()
    {
        blockUI();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>settings/index',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        });
    }
</script>