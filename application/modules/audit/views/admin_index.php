<article id="ajax_table" class="card">

    <div class="article-header"><?php echo "Audits (Energy, Water, Waste)";//lang('audit_management'); ?></div>

    <div class="card-wrap menu-content-box">

        <div class="row site-controls-outer">

            <div class="col-sm-9">

                <?php /* ?>

                <form class="default-form form-inline clearfix">

                    <div class="form-group">

                        <strong><?php echo lang('start_date'); ?></strong>

                        <?php

                        $start_date_data = array(

                            'name' => 'search_term_start_date',

                            'id' => 'search_term_start_date',

                            'value' => isset($search_term_start_date) ? urldecode($search_term_start_date) : '',

                            'class' => 'form-control st datetimepicker',

                            'style' => 'cursor:pointer !important;'

                        );

                        echo form_input($start_date_data);

                        ?>

                        <span class="start_date_err validation_error"></span>

                    </div>&nbsp;

                    <div class="form-group">                        

                        <strong><?php echo lang('end_date'); ?></strong>

                        <?php

                        $end_date_data = array(

                            'name' => 'search_term_end_date',

                            'id' => 'search_term_end_date',

                            'value' => isset($search_term_end_date) ? urldecode($search_term_end_date) : '',

                            'class' => 'form-control ed datetimepicker',

                            'style' => 'cursor:pointer !important;'

                        );

                        ?>

                        <?php echo form_input($end_date_data); ?>

                        <span class="end_date_err validation_error"></span>

                    </div>                            

                    <div class="form-group">

                        <button onclick="submit_search()" class="btn btn-secondary" type="button" name="">

                            <img alt="Search" src="images/search-icon.png"><?php echo lang('search'); ?>

                        </button>

                    </div>

                    <div class="form-group">

                        <button onclick="reset_data()" class="btn btn-reset" type="button" name=""><img src="images/reset-icon.png">Reset</button>                       

                    </div>                    

                </form>

                <?php */ ?>

            </div>

            <div class="col-sm-3">

                <a class="btn btn-blue pull-right" onclick="openlink('add')" href="#"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo "Add Audit";//lang('add_audit'); ?></a>

            </div>

        </div>



        <?php if (!empty($energy_audit_list)) { ?>

            <div class="table-responsive">

                <table class="table table-striped">

                    <thead>

                        <tr>                            

                            <th class="digits-col"><?php echo lang('sr_no') ?></th>

                            <th>

                                <?php

                                $field_sort_order = 'asc';

                                $sort_image = 'srt_down.png';

                                if ($sort_by == 'ea.audit_on' && $sort_order == 'asc') {

                                    $sort_image = 'srt_up.png';

                                    $field_sort_order = 'desc';

                                }

                                ?>

                                <a href="javascript:void(0)" onclick="sort_data('ea.audit_on', '<?php echo $field_sort_order; ?>');" >

                                    <?php echo lang('audit_on'); ?>

                                    <?php

                                    if ($sort_by == 'ea.audit_on') {

                                        ?>

                                        <div class="sorting">

                                            <?php echo add_image(array($sort_image)); ?>

                                        </div>

                                    <?php }

                                    ?>

                                </a>

                            </th>

                            <th class="name-col">

                                <?php

                                $field_sort_order = 'asc';

                                $sort_image = 'srt_down.png';

                                if ($sort_by == 'ea.full_report' && $sort_order == 'asc') {

                                    $sort_image = 'srt_up.png';

                                    $field_sort_order = 'desc';

                                }

                                ?>

                                <a href="javascript:void(0)" onclick="sort_data('ea.full_report', '<?php echo $field_sort_order; ?>');" >

                                    <?php echo lang('full_report'); ?>

                                    <?php

                                    if ($sort_by == 'ea.full_report') {

                                        ?>

                                        <div class="sorting">

                                            <?php echo add_image(array($sort_image)); ?>

                                        </div>

                                    <?php }

                                    ?>

                                </a>

                            </th>

                            <th class="name-col">

                                <?php

                                $field_sort_order = 'asc';

                                $sort_image = 'srt_down.png';

                                if ($sort_by == 'ea.executive_summary' && $sort_order == 'asc') {

                                    $sort_image = 'srt_up.png';

                                    $field_sort_order = 'desc';

                                }

                                ?>

                                <a href="javascript:void(0)" onclick="sort_data('ea.executive_summary', '<?php echo $field_sort_order; ?>');" >

                                    <?php echo lang('executive_summary'); ?>

                                    <?php

                                    if ($sort_by == 'ea.executive_summary') {

                                        ?>

                                        <div class="sorting">

                                            <?php echo add_image(array($sort_image)); ?>

                                        </div>

                                    <?php }

                                    ?>

                                </a>

                            </th>

                            <th class="digits-col"><?php echo lang('actions') ?></th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';



                        if (!empty($energy_audit_list)) {

                            if ($page_number > 1) {

                                $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;

                            } else {

                                $i = 1;

                            }

                            foreach ($energy_audit_list as $audit_list) {

                                if ($i % 2 != 0) {

                                    $class = "odd-row";

                                } else {

                                    $class = "even-row";

                                }

                                ?>

                                <tr class="<?php echo $class; ?>" >                                    

                                    <td><?php echo $i; ?></td>

                                    <td><?php echo isset($audit_list['ea']['audit_on']) && trim($audit_list['ea']['audit_on'])!= ''?date('d-M-Y',strtotime($audit_list['ea']['audit_on'])):''; ?></td>

                                    <td>

                                        <?php if(isset($audit_list['ea']['full_report']) && $audit_list['ea']['full_report'] != "") { ?>

                                        <a target="_blank" href="<?php echo site_url().'assets/uploads/audit/'.$audit_list['ea']['full_report']; ?>" title="<?php echo lang('full_report'); ?>"><?php echo $audit_list['ea']['full_report_title']; ?></a>

                                        <?php } ?>

                                    </td>

                                    <td>

                                        <?php if(isset($audit_list['ea']['executive_summary']) && $audit_list['ea']['executive_summary'] != "") { ?>

                                        <a target="_blank" href="<?php echo site_url().'assets/uploads/audit/'.$audit_list['ea']['executive_summary']; ?>" title="<?php echo lang('executive_summary'); ?>"><?php echo $audit_list['ea']['executive_summary_title']; ?></a>

                                        <?php } ?>

                                    </td>

                                    <td>                                        

                                        <a title="Edit" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/action/edit/<?php echo $audit_list['ea']['id']; ?>" title="<?php echo lang('edit'); ?>"><?php echo add_image(array('edit-icon.png')); ?></a>

                                        <a title="Delete" href='javascript:;' title='<?php echo lang('delete'); ?>' onclick="delete_audit('<?php echo $audit_list['ea']['id']; ?>', '<?php echo $audit_list['ea']['slug_url']; ?>')"><?php echo add_image(array('delete-icon.png')); ?></a>

                                    </td>

                                </tr>

                                <?php

                                $i++;

                            }

                            ?>

                        </tbody>

                    </table>

                    <?php

                } else {

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



            <?php

            if (!empty($energy_audit_list)) {

                $options = array(

                    'total_records' => $total_records,

                    'page_number' => $page_number,

                    'isAjaxRequest' => 1,

                    'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "audit/index/" . $language_code,

                    'params' => $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '',

                    'element' => 'ajax_table'

                );



                widget('custom_pagination', $options);

            }

            ?>

        <?php } else {

            ?>

            <div class="table-responsive">                  

                <table class="table table-striped" >

                    <tr>

                        <td><?php echo lang('no_records_found') ?></td>

                    </tr>

                </table>

            </div>

        <?php }

        ?>    

    </div>

</article>

<!-- Inventory section -->

<article id="ajax_table" class="card">

    <div class="article-header"><?php echo lang('inventory'); ?></div>

    <div class="card-wrap menu-content-box">

        <div class="row site-controls-outer">

            <div class="col-sm-9">

                

            </div>

            <div class="col-sm-3">

                <a class="btn btn-blue pull-right" href="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM.'audit/inventory/add';?>"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo lang('add_inventory'); ?></a>

            </div>

        </div>



        <?php if (!empty($inventory_list)) { ?>

            <div class="table-responsive">

                <table class="table table-striped">

                    <thead>

                        <tr>                            

                            <th class="digits-col"><?php echo lang('sr_no') ?></th>

                            <th width="20%">

                                <?php

                                $field_sort_order = 'asc';

                                $sort_image = 'srt_down.png';

                                if ($sort_by == 'ei.inventory_on' && $sort_order == 'asc') {

                                    $sort_image = 'srt_up.png';

                                    $field_sort_order = 'desc';

                                }

                                ?>

                                <a href="javascript:void(0)" onclick="sort_data('ei.inventory_on', '<?php echo $field_sort_order; ?>');" >

                                    <?php echo lang('inventory_on'); ?>

                                    <?php

                                    if ($sort_by == 'ei.inventory_on') {

                                        ?>

                                        <div class="sorting">

                                            <?php echo add_image(array($sort_image)); ?>

                                        </div>

                                    <?php }

                                    ?>

                                </a>

                            </th>

                            <th class="name-col">

                                <?php

                                $field_sort_order = 'asc';

                                $sort_image = 'srt_down.png';

                                if ($sort_by == 'ei.full_report' && $sort_order == 'asc') {

                                    $sort_image = 'srt_up.png';

                                    $field_sort_order = 'desc';

                                }

                                ?>

                                <a href="javascript:void(0)" onclick="sort_data('ei.full_report', '<?php echo $field_sort_order; ?>');" >

                                    <?php echo lang('load_inventory'); ?>

                                    <?php

                                    if ($sort_by == 'ea.full_report') {

                                        ?>

                                        <div class="sorting">

                                            <?php echo add_image(array($sort_image)); ?>

                                        </div>

                                    <?php }

                                    ?>

                                </a>

                            </th>

                            <th class="digits-col"><?php echo lang('actions') ?></th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '';



                        if (!empty($inventory_list)) {

                            if ($page_number > 1) {

                                $i = ($this->_ci->session->userdata[$this->_data['section_name']]['record_per_page'] * ($page_number - 1)) + 1;

                            } else {

                                $i = 1;

                            }

                            foreach ($inventory_list as $inventory) {

                                if ($i % 2 != 0) {

                                    $class = "odd-row";

                                } else {

                                    $class = "even-row";

                                }

                                ?>

                                <tr class="<?php echo $class; ?>" >                                    

                                    <td><?php echo $i; ?></td>

                                    <td><?php echo isset($inventory['ei']['inventory_on']) && trim($inventory['ei']['inventory_on'])!= ''?date('d-M-Y',strtotime($inventory['ei']['inventory_on'])):''; ?></td>

                                    <td>

                                        <?php if(isset($inventory['ei']['full_report']) && $inventory['ei']['full_report'] != "") { ?>

                                        <a target="_blank" href="<?php echo site_url().'assets/uploads/inventory/'.$inventory['ei']['full_report']; ?>" title="<?php echo lang('full_report'); ?>">

                                            <?php echo $inventory['ei']['inventory_title']; ?>

                                        </a>

                                        <?php } ?>

                                    </td>

                                    <td>                                        

                                        <a title="Delete" href='javascript:;' title='<?php echo lang('delete'); ?>' onclick="delete_inventory('<?php echo $inventory['ei']['id']; ?>')"><?php echo add_image(array('delete-icon.png')); ?></a>

                                    </td>

                                </tr>

                                <?php

                                $i++;

                            }

                            ?>

                        </tbody>

                    </table>

                    <?php

                } else {

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



            <?php

            if (!empty($energy_audit_list)) {

                $options = array(

                    'total_records' => $inventory_count,

                    'page_number' => $page_number,

                    'isAjaxRequest' => 1,

                    'base_url' => base_url() . BASE_ADMIN_URL_CUSTOM . "audit/index/" . $language_code,

                    'params' => $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '',

                    'element' => 'ajax_table'

                );



                widget('custom_pagination', $options);

            }

            ?>

        <?php } else {

            ?>

            <div class="table-responsive">                  

                <table class="table table-striped" >

                    <tr>

                        <td><?php echo lang('no_records_found') ?></td>

                    </tr>

                </table>

            </div>

        <?php }

        ?>    

    </div>

</article>



<!-- Inventory section -->

<?php $csrf_token = $this->ci()->security->get_csrf_token_name(); ?>

<script type="text/javascript">



    $(document).ready(function() {

        openlink = function(type) {

            lang_code = $(".tab-headings li.selected a").attr('title');

            location.href = "<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/action/add/";

        }



        delete_audit = function(id, slug_url)

        {

            res = confirm('<?php echo lang('delete_confirm') ?>');



            if (res) {

                //blockUI();

                $.ajax({

                    type: 'POST',

                    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/delete',

                    data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>', id: id, slug_url: slug_url},

                    error: function() {

                        alert("Server problem. Please try again.");

                        return false;

                    },

                    complete: function() {

                        //	unblockUI();

                    },

                    success: function(data) {

                        blockUI();

                        $.ajax({

                            type: 'POST',

                            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/index',

                            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term_start_date: $('#search_term_start_date').val(), search_term_end_date: $('#search_term_end_date').val()},

                            success: function(data) {

                                $("#ajax_table").html(data);

                            }

                        });

                        unblockUI();

                    }

                });



            } else {

                return false;

            }

        }



        /* Delete Inventory */

        delete_inventory = function(id)

        {

            res = confirm('<?php echo lang('delete_confirm') ?>');



            if (res) {

                //blockUI();

                $.ajax({

                    type: 'POST',

                    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/inventory/delete',

                    data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>', id: id,type:'delete'},

                    error: function() {

                        alert("Server problem. Please try again.");

                        return false;

                    },

                    complete: function() {

                        //  unblockUI();

                    },

                    success: function(data) {

                        blockUI();

                        $.ajax({

                            type: 'POST',

                            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/index',

                            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term_start_date: $('#search_term_start_date').val(), search_term_end_date: $('#search_term_end_date').val()},

                            success: function(data) {

                                location.reload(true);

                            }

                        });

                        unblockUI();

                    }

                });



            } else {

                return false;

            }

        }

    });



    $("#search_term").keypress(function(event) {

        if (event.which == 13) {

            event.preventDefault();

            submit_search();

        }

    });

    function submit_search()

    {

        $('#error_msg').fadeOut(1000); //hide error message it shown up while search



        var search_term_start_date = jQuery('#search_term_start_date').val();

        var search_term_start_date = jQuery('#search_term_end_date').val();

        if (search_term_start_date != '' || search_term_end_date != '') {

            blockUI();

            $.ajax({

                type: 'POST',

                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/index',

                data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term_start_date: $('#search_term_start_date').val(), search_term_end_date: $('#search_term_end_date').val()},

                success: function(data) {

                    $("#ajax_table").html(data);

                }

            });

            unblockUI();

        } else {

            alert("Please enter something to search");

        }

    }



    function sort_data(sort_by, sort_order)

    {



        blockUI();

        $.ajax({

            type: 'POST',

            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/index',

            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term_start_date: $('#search_term_start_date').val(), search_term_end_date: $('#search_term_end_date').val(), sort_by: sort_by, sort_order: sort_order},

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

            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>audit/index',

            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', search_term_start_date: "", search_term_end_date: ""},

            error: function() {

                alert("Server problem. Please try again.");

                return false;

            },

            success: function(data) {

                $("#ajax_table").html(data);

                unblockUI();

            }

        });

    }

    $(function() {

        $("#check_all").click(function() {

            if ($("#check_all").is(':checked')) {

                $(".check_box").prop("checked", true);

            } else {

                $(".check_box").prop("checked", false);

            }

        });

        

        $(".check_box").click(function() {

            if ($(".check_box").length == $(".check_box:checked").length) {

                $("#check_all").prop("checked", true);

                $(".check_box").attr("checked", "checked");

            } else {

                $("#check_all").removeAttr("checked");

            }



        });

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



    $(".st.datetimepicker").datepicker({

        dateFormat: 'yy-mm-dd',

        onSelect: function(selected) {

            var dt = new Date(selected);

            dt.setDate(dt.getDate());

            $(".ed.datetimepicker").datepicker("option", "minDate", dt);

        }

    });

    $(".ed.datetimepicker").datepicker({

        dateFormat: 'yy-mm-dd',

        onSelect: function(selected) {

            var dt = new Date(selected);

            dt.setDate(dt.getDate());

            $(".st.datetimepicker").datepicker("option", "maxDate", dt);

        }

    });

</script>