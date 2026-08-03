<div id="ajax_table">
    <div class="panel panel-default form-panel">
        <div id="success_msg" style="display: none;" class="msg-content-box" onclick="hide_msg();">
            <div class="alert alert-success">
                <button data-dismiss="alert" class="close" type="button">×</button>
                <?php echo lang('permission-update-success'); ?>
            </div>
        </div> 
        <div class="scroll_top"></div>
        <?php
        if (is_array($matrix_permissions) && is_array($matrix_roles)) {
            ?>


            <div class="form-btn-outer">
                <button type="button" onclick="save_records()" class="btn btn-secondary btn-submit" title="<?php echo lang('btn-save') ?>"><?php echo lang('btn-save'); ?></button>
                <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-cancel'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'roles' ?>'"><?php echo lang('btn-cancel'); ?></button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped mb30 dataTable">
                    <thead>
                        <tr>
                            <th><span style="margin-left: 10px;"><?php echo lang('permissions') ?></span></th>
                            <?php
                            foreach ($matrix_roles as $matrix_role) {
                                $role_id = $matrix_role['R']['id'];
                                $check_box = array(
                                    'value' => '0',
                                    'class' => 'icheck checkboxlist check_box check_for_all',
                                    'id' => $role_id,
                                    'onclick' => 'check_check(' . $role_id . ')',
                                );
                                ?>
                                <th class="text-center"><span style="float: left;"><?php echo form_checkbox($check_box); ?></span><span style="margin-left: 10px;"><?php echo $matrix_role['R']['role_name']; ?></span></th>
                                <?php
                                $cols[] = array('role_id' => $matrix_role['R']['id'], 'role_name' => $matrix_role['R']['role_name']);
                            }
                            ?>
                        </tr>
                    </thead>
                    <?php
                    $j = 1;
                    foreach ($matrix_permissions as $matrix_permission) {
                        if ($matrix_permission['parent_id'] != 0) {
                            $class = "odd-row";
                        } else {
                            $class = "even-row";
                        }
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td class="text-center">
                                <?php
                                if ($matrix_permission['parent_id'] == 0) {
                                    echo $matrix_permission['permission_title'];
                                } else {
                                    echo "&nbsp;&nbsp;&nbsp;" . $matrix_permission['permission_title'];
                                }
                                ?>
                            </td>
                            <?php
                            for ($i = 0; $i < count($cols); $i++) {
                                $checkbox_value = $cols[$i]['role_id'] . ',' . $matrix_permission['id'];
                                $checked = '';

                                if (!empty($matrix_role_permissions)) {
                                    $checked = in_array($checkbox_value, $matrix_role_permissions) ? ' "checked"' : '';
                                }

                                if ($checked != '') {
                                    $delete = 1;
                                } else {
                                    $delete = 0;
                                }

                                $role_id = $cols[$i]['role_id'];
                                $permission_id = $matrix_permission['id'];
                                $check_box = array(
                                    'value' => $checkbox_value,
                                    'checked' => $checked,
                                    'class' => ' icheck checkboxlist check_box' . $role_id,
                                );
                                ?>
                                <td class="text-center" title="<?php echo $cols[$i]['role_name']; ?>">
                                    <?php echo form_checkbox($check_box); ?>
                                </td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                        $j++;
                    }
                    ?>
                </table>
            </div>
            <!--            <div class="row row-pad-5" style="margin-left: 10px; margin-top: 10px;">-->
            <div class="form-btn-outer">
                <button type="button" onclick="save_records()" class="btn btn-secondary btn-submit" title="<?php echo lang('btn-save') ?>"><?php echo lang('btn-save'); ?></button>
                <button type="button" class="btn btn-secondary reset-btn btn-submit" title="<?php echo lang('btn-cancel'); ?>" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'roles' ?>'"><?php echo lang('btn-cancel'); ?></button>
            </div>
            <!--            </div>-->

            <?php
        } else {
            ?>

            <div class="table-responsive">
                <table class="table table-striped mb30 dataTable">
                    <tr>
                        <td><?php echo lang('ci_model_no_data'); ?></td>
                    </tr>
                </table>
            </div>
            <?php
        }
        ?>
        <?php
        $querystr = $this->theme->ci()->security->get_csrf_token_name() . '=' . urlencode($this->theme->ci()->security->get_csrf_hash());
        ?>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    //Function check to check all checkbox by role
//    function check_check(role_id)
//    {
//        if ($(".check_box").is(':checked')) {
//            $(".check_box" + role_id).prop("checked", true);
//        } else {
//            $(".check_box" + role_id).prop("checked", false);
//        }
//        //$(".check_box"+role_id).prop("checked", false);
//    }
    $('.check_for_all').on('ifChecked', function(event) {
        var role_id = $(this).attr('id');
        $(".check_box" + role_id).parent('div').addClass('checked');
    });

    $('.check_for_all').on('ifUnchecked', function(event) {
        var role_id = $(this).attr('id');
        $(".check_box" + role_id).parent('div').removeClass('checked');
    });

    //Function check_all to check all checkbox
//    function check_all()
//    {
//        $(".check_box").prop("checked", true);
//    }
//
//    //Function uncheck_all to uncheck all checkbox
//    function uncheck_all()
//    {
//        $(".check_box").prop("checked", false);
//    }

    //Function save_records to save all permissions
    function save_records()
    {
        var val = [];
        
        var inc = 0;
        $checkboxlist = $('.checkboxlist');
        $checkboxlist.each(function(){
            if($(this).parent().hasClass('checked') == true){
                val[inc] = $(this).val();
                inc++;
            }
        });
      
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM ?>roles/update_matrix_permission',
            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', permission_id: val},
            success: function(data) {
                $("#success_msg").show();
                $('html, body').animate({scrollTop: 0}, 'slow');
            }
        });

    }

    function hide_msg() {
        $('#success_msg').hide();
    }
</script>
