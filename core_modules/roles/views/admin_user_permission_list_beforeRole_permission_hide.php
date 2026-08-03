<article class="card" id="ajax_table">

    <div class="article-header"><?php echo lang('permission-listing');?><p style="float: right;"><?php echo $first_name.' '. $last_name ." - ". $role_name;?></p></div>

    

        <div class="card-wrap">

            <?php

            if (is_array($matrix_permissions)) {

                ?>

                <div class="btn-panel">

                    <button type="button" class="btn btn-custom btn-green" onclick="check_all()"><?php echo lang('check-all'); ?></button>

                    <button type="button" class="btn btn-custom btn-yellow" onclick="uncheck_all()"><?php echo lang('uncheck-all'); ?></button>

                    <button type="button" class="btn btn-custom btn-green" onclick="save_records(<?php echo $user_id; ?>)"><?php echo lang('btn-save'); ?></button>

                    <button type="button" class="btn btn-custom btn-red" onclick="delete_permissions(<?php echo $user_id; ?>)"><?php echo lang('reset-user-permission'); ?></button>

                </div>

            <?php } ?>

            <?php
    // pre($matrix_permissions);
            if (!empty($matrix_permissions)) {

                ?>

                <div class="table-responsive">



                    <table class="table table-striped">

                        <?php echo form_open(); ?>

                        <thead>

                            <tr>

                                <th class="name-col"><?php echo lang('permissions'); ?></th>

                                <th class="digits-col">

        <!--                                <input type="checkbox" name="check_all" id="check_all" value="0" class="icheck" <?php echo count($records) == count($SetPerm) ? 'checked' : '' ?> />-->

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php

                            $j = 1;

                            $matrix_permissions_tree = array();

                            foreach ($matrix_permissions as $key=>$value) {
                                if($value['parent_id'] == 0){
                                    $matrix_permissions_tree[$key] = $value;
                                }
                            }

                            foreach ($matrix_permissions as $key=>$value) {
                                if($value['parent_id'] != 0) {
                                    $matrix_permissions_tree[$value['parent_id']]['children'][] = $value;
                                }
                            }

                            foreach ($matrix_permissions_tree as $value) {

                                ?>

                                <tr>

                                    <td style="color:green;"><strong><?php echo $value['permission_title']; ?></strong></td>

                                    <?php 

                                    $checkbox_value = $user_id . ',' . $value['id'];

                                    $checked = '';

                                    if (!empty($matrix_user_permissions)) {

                                        $checked = in_array($checkbox_value, $matrix_user_permissions) ? ' "checked"' : '';

                                    }

                                    if ($checked != '') {

                                        $delete = 1;

                                    } else {

                                        $delete = 0;

                                    }

                                    ?>

                                    <?php

                                    $permission_id = $value['id'];

                                    $check_box = array(

                                        'value' => $permission_id,

                                        'checked' => $checked,

                                        'class' => 'check_box icheck',

                                    );

                                    ?>

                                    <td class="text-center">

                                        <?php echo form_checkbox($check_box); ?>

                                    </td>

                                </tr>

                                <?php

                                if(!empty($value['children'])){

                                    foreach ($value['children'] as $value1) {

                                        ?>

                                        <tr>

                                            <td><?php echo "&nbsp;&nbsp;&nbsp;" . $value1['permission_title']; ?></td>

                                            <?php 

                                            $checkbox_value = $user_id . ',' . $value1['id'];

                                            $checked = '';

                                            if (!empty($matrix_user_permissions)) {

                                                $checked = in_array($checkbox_value, $matrix_user_permissions) ? ' "checked"' : '';

                                            }

                                            if ($checked != '') {

                                                $delete = 1;

                                            } else {

                                                $delete = 0;

                                            }

                                            ?>

                                            <?php

                                            $permission_id = $value1['id'];

                                            $check_box = array(

                                                'value' => $permission_id,

                                                'checked' => $checked,

                                                'class' => 'check_box icheck',

                                            );

                                            ?>

                                            <td class="text-center">

                                                <?php echo form_checkbox($check_box); ?>

                                            </td>

                                        </tr>

                                        <?php



                                        

                                    }

                                }



                            }

                            



                            /*foreach ($matrix_permissions as $matrix_permission) {

                                if ($matrix_permission['parent_id'] != 0) {

                                    $class = "odd-row";

                                } else {

                                    $class = "even-row";

                                }

                                ?>

                                <tr class="<?php echo $class; ?>" >



                                    <td><?php

                                        if ($matrix_permission['parent_id'] == 0) {

                                            echo $matrix_permission['permission_title'];

                                        } else {

                                            echo "&nbsp;&nbsp;&nbsp;" . $matrix_permission['permission_title'];

                                        }

                                        ?></td>

                                    <?php

                                    $checkbox_value = $user_id . ',' . $matrix_permission['id'];

                                    $checked = '';

                                    if (!empty($matrix_user_permissions)) {

                                        $checked = in_array($checkbox_value, $matrix_user_permissions) ? ' "checked"' : '';

                                    }

                                    if ($checked != '') {

                                        $delete = 1;

                                    } else {

                                        $delete = 0;

                                    }

                                    ?>

                                    <?php

                                    $permission_id = $matrix_permission['id'];

                                    $check_box = array(

                                        'value' => $permission_id,

                                        'checked' => $checked,

                                        'class' => 'check_box icheck',

                                    );

                                    ?>

                                    <td class="text-center">

                                        <?php echo form_checkbox($check_box); ?>

                                    </td>

                                </tr>

                                <?php

                                $j++;

                            }*/

                            ?>



                        </tbody>

                    </table>

                </div>

                <div class="btn-panel">

                    <button type="button" class="btn btn-custom btn-green" onclick="check_all()"><?php echo lang('check-all'); ?></button>

                    <button type="button" class="btn btn-custom btn-yellow" onclick="uncheck_all()"><?php echo lang('uncheck-all'); ?></button>

                    <button type="button" class="btn btn-custom btn-green" onclick="save_records(<?php echo $user_id; ?>)"><?php echo lang('btn-save'); ?></button>

                    <button type="button" class="btn btn-custom btn-red" onclick="delete_permissions(<?php echo $user_id; ?>)"><?php echo lang('reset-user-permission'); ?></button>



                </div>  

                <?php

            } else {

                ?>

                <div class="table-responsive">                  

                    <table class="table table-striped" >

                        <tr>

                            <td><?php echo lang('ci_model_no_data') ?></td>

                        </tr>

                    </table>

                </div>

                <?php

            }

            ?>

            <?php echo form_close(); ?>

        </div>

</article>

<script type="text/javascript">

    //Function check_all to check all checkbox

    function check_all()

    {

        //$(".check_box").prop("checked", true);

        $('.check_box').iCheck('check');

    }



    //Function uncheck_all to uncheck all checkbox

    function uncheck_all()

    {

        //$(".check_box").prop("checked", false);

        $('.check_box').iCheck('uncheck');

    }



//    $('.check_box').on('ifUnchecked', function(event) {

//            $('#check_all').iCheck('uncheck');

//        });

//

//        $('.check_box').on('ifChecked', function(event) {

//            if ($('.check_box').filter(':checked').length == $('.check_box').length) {

//                $('#check_all').iCheck('check');

//            }

//        });



    //Function save_records to save all permissions

    function save_records(user_id)

    {

        var val = [];

        $(':checkbox:checked').each(function(i) {

            val[i] = $(this).val();

        });

        $.ajax({

            type: 'POST',

            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>roles/update_user_permission',

            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', user_id: '<?php echo $user_id; ?>', permission_id: val},

            success: function(data) {

                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>roles/user_permission_matrix/' + user_id, 'ajax_table', '<?php echo $querystr; ?>');

                $("#messages").show();

                $("#messages").html(data);



            }

        });

    }



    //Function to delete userwise matrix permission

    function delete_permissions(user_id) {

        blockUI();

        $.ajax({

            type: 'POST',

            url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>roles/delete_user_permission',

            data: {<?php echo $this->theme->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->theme->ci()->security->get_csrf_hash(); ?>', user_id: user_id},

            success: function(data) {

                unblockUI();

                ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>roles/user_permission_matrix/' + user_id, 'ajax_table', '<?php echo $querystr; ?>');

                $("#messages").show();

                $("#messages").html(data);

                $('input:checkbox').removeAttr('checked');

            }

        });

    }

    function hide_msg() {

        $('#success_msg').hide();

    }

</script>