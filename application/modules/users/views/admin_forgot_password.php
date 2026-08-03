<?php include_once(BASE_PATH_CUSTOM . "/themes/default/cms_header.php"); ?>
<div class="login-form">
    <?php echo $this->message(); ?>
    <?php echo form_open(BASE_ADMIN_URL_CUSTOM . 'users/forgot_password', array('id' => 'forgot_password_admin', 'name' => 'forgot_password_admin')); ?>
    <div class="form-group">
        <label>Enter Email</label>
        <div class="input-field">
            <input type="email" name="email" maxlength="50" class="form-control nomargin" placeholder="Enter your Email Address"/>
            <i class="form-icon"><img src="<?php echo BASE_URL_CUSTOM; ?>themes/default/images/user.png" alt="Username"></i>
        </div>
    </div>
    <div class="form-group">                        
        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>users/login" class="pull-left btn-secondary" id="btnCancel" title="Cancel">Cancel</a>
        <button type="submit" class="btn pull-right btn-secondary" name="Login" title="Log In" style="margin-top: 10px;">Submit</button>
    </div>
    <?php echo form_close(); ?>
</div>
<?php include_once(BASE_PATH_CUSTOM . "/themes/default/cms_footer.php"); ?>