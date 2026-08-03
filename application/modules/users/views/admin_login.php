<?php include_once(BASE_PATH_CUSTOM . "/themes/default/cms_header.php"); ?>
<div class="login-form">
    <?php echo $this->message(); ?>
    <?php echo form_open(BASE_ADMIN_URL_CUSTOM . "users/login", array('id' => 'login_form_inner', 'name' => 'login_form_inner')); ?>
    <div class="form-group">
        <label>Username</label>
        <div class="input-field">
            <input type="text" name="username" id="username" maxlength="50" class="form-control nomargin" placeholder="Enter your username">
            <i class="form-icon"><img src="<?php echo BASE_URL_CUSTOM; ?>themes/default/images/user.png" alt="Username"></i>
        </div>
    </div>
    <div class="form-group">
        <label>Password</label>
        <div class="input-field">                                
            <input type="password" name="password" id="password" maxlength="50" class="form-control" placeholder="Password">
            <span class="validation_error" style="color: red; "><?php echo form_error('email'); ?></span>
            <i class="form-icon"><img src="<?php echo BASE_URL_CUSTOM; ?>themes/default/images/lock.png" alt="Password"></i>
        </div>
    </div>
    <div class="form-group">
        <?php
        if (isset($back_url)) {
            echo form_hidden('back_url', $back_url);
        }
        ?>
        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>users/forgot_password" class="pull-left form-link" title="Forgot your password?">Forgot your password?</a>
        <button type="submit" class="btn pull-right btn-secondary" id="btnLogin" name="Login" title="Log In">Log In</button>
    </div>
<?php echo form_close(); ?>
</div>
<?php include_once(BASE_PATH_CUSTOM . "/themes/default/cms_footer.php"); ?>