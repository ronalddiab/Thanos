<article class="card">
<div class="article-header"> <?php echo lang('permissions'); ?> </div>

<div id="ajax_table" class="card-wrap">

<!-- <div class="row site-controls-outer">
    <div class="col-sm-9">
        &nbsp;
    </div>
    <div class="col-sm-3">
        <a class="btn btn-blue pull-right" title="" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list'; ?>"><span><img alt="Add" src="images/plus-icon.png"></span><?php echo lang('view-all-moderators'); ?></a>
    </div>
</div> -->


        <?php echo form_open(site_url().BASE_ADMIN_URL_CUSTOM . 'forum/save_permission/'.$id, array('id' => 'saveform', 'name' => 'saveform'));

        echo form_hidden('id', $id);

        ?>
        <?php
            if (!empty($records)) {
                ?>
    <div class="table-responsive">
            
                <table class="table table-striped">
                    <?php echo form_open(); ?>
                    <thead>
                        <tr>
                            <th><?php echo lang('no') ?></th>
                            <th><?php echo lang('permissions'); ?></th>
                            <th>
                                <input type="checkbox" name="check_all" id="check_all" value="0" class="icheck" <?php echo count($records) == count($SetPerm) ? 'checked': '' ?> />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;

                        foreach ($records as $user) {
                            if ($i % 2 != 0) {
                                $class = "odd-row";
                            } else {
                                $class = "even-row";
                            }
                            $user_id = $user['P']['id'];
                            $role_id = $user['P']['role_id'];

                            ?>
                            <tr class="<?php echo $class; ?> rows" >

                                <td align="center"><?php echo $i; ?></td>
                                <td><?php echo $user['P']['permission_title']; ?></td>

                                <td>

                                    <?php if ($role_id != 1 && $user_id != $this->_ci->session->userdata[$this->_data['section_name']]['user_id']) {

                                        ?>
                                        <input type="checkbox" id="<?php echo $user['P']['id']; ?>" name="check_box[]" class="check_box icheck" value="<?php echo $user['P']['id']; ?>" <?php echo in_array($user['P']['id'], $SetPerm) ? 'checked': ''; ?>>
                                    <?php } ?>
                                </td>



                            </tr>
                            <?php
                            $i++;
                        }
                        ?>


                    </tbody>
                    
                </table>
                
    </div>
    <div class="form-btn-outer">
        <button type="submit" id="mysubmit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
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
                <?php
            }
                ?>
    <?php echo form_close(); ?>
</div>
</article>
<script type="text/javascript">

    //remove dynamically populate error
    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            submit_search();
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
        $("#reg_date").datepicker({dateFormat: 'yy-mm-dd'});
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

    $(document).ready(function() {
        $("#menu1").children().removeClass("active");
        $("#menu311").children().removeClass("active");
        $("#menu2").children().removeClass("active");
    });

</script>