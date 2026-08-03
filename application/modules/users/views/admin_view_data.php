<article class="card">
    <div class="article-header">
        <?php echo lang('view-user-data') ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form')); ?>
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('first-name'); ?> </label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="firstname" disabled="disabled" id="firstname" class="input-control" value="<?php echo isset($firstname) && !empty($firstname) ? htmlspecialchars_decode($firstname) : ''; ?>">
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('last-name'); ?> </label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="lastname" disabled="disabled" id="lastname" class="input-control" value="<?php echo isset($lastname) && !empty($lastname) ? htmlspecialchars_decode($lastname) : ''; ?>">
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('user-name'); ?> </label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" disabled="disabled" name="username" id="username" class="input-control" value="<?php echo isset($username) && !empty($username) ? htmlspecialchars_decode($username) : ''; ?>">
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('email'); ?> </label>
                <div class="row">
                    <div class="form-col-3">
                        <input type="text" name="email" disabled="disabled" id="email" class="input-control" value="<?php echo isset($email) && !empty($email) ? htmlspecialchars_decode($email) : ''; ?>">

                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('avtar'); ?></label>
                <div class="row">
                    <div class="form-col-3">
                        <?php
                            if (!empty($avtar) && file_exists(BASE_PATH_CUSTOM.'/assets/uploads/avtar/thumb/'.$avtar)) {
                                echo "<img src='" . site_url() . "assets/uploads/avtar/thumb/" . $avtar . "' />";
                            } else {
                                echo add_image(array('default_pic.jpg'));
                            }
                        ?>
                    </div>
                </div>
            </li>
            <?php if (in_array($role_id, array(3,4))) { ?>
                <li>
                    <?php
                    $disable = 'disabled="disabled"';
                    $additional_info = $disable . ' style="width:105px;"';
                    ?> 
                    <label class="main-label"><?php echo lang('site'); ?></label> 
                    <div class="row">
                        <div class="form-col-3">
                            <div class="form-dropdown">
                                <?php echo form_dropdown('site_id', $hotel_sites, ((isset($site_id)) ? $site_id : 0), 'data-type="custom-dropdown" id="site_id"', $additional_info); ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?> 

            <li>
                <?php
                $disable = 'disabled="disabled"';
                $additional_info = $disable . ' style="width:105px;"';
                ?> 
                <label class="main-label"><?php echo lang('role'); ?></label> 
                <div class="row">
                    <div class="form-col-3">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('role_id', $role_list, ((isset($role_id)) ? $role_id : 0), 'data-type="custom-dropdown" id="role_id"', $additional_info); ?>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <?php
                $statuslist = array('1' => 'Active', '0' => 'Inactive');
                $disable = 'disabled="disabled"';
                ?> 
                <label class="main-label"><?php echo lang('status'); ?></label> 
                <div class="row">
                    <div class="form-col-3">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown" id="status"', $disable); ?>
                            <label class="input-label validation_error"><?php echo form_error('status'); ?></label>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="button" class="btn btn-secondary reset-btn btn-submit" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'users' ?>'"><?php echo lang('btn-back'); ?></button>
        </div>
        <?php
        echo form_hidden('id', (isset($id)) ? $id : '0' );
        echo form_close();
        ?>
    </div>
</article>

<script>
    $(document).ready(function(e) {

        var select = new Dropkick("#status");
        var select_role = new Dropkick("#role_id");
        var select_site = new Dropkick("#site_id");
        select.disable();
        select_role.disable();
        select_site.disable();

    });

</script>