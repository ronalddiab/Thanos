<?php echo add_css('validationEngine.jquery'); ?>
<?php echo add_js(array('jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?> 
<?php
$attributes = array('name' => 'login_form', 'id' => 'login_form');
echo form_open("users/login", $attributes);
?>
<table cellspacing="0" cellpadding="0">
    <tr>
        <td class="content" valign="top">
            <?php
            $email_data = array(
                'name' => 'email_w',
                'id' => 'email_w',
                'value' => set_value('email_w', ""),
                'maxlength' => '150',
                'class' => "validate[required,custom[email]]"
            );
            $password_data = array(
                'name' => 'password_w',
                'id' => 'password_w',
                'value' => set_value('password_w', ""),
                'maxlength' => '50', 
                'class' => "validate[required]"
            );
            ?>
            <?php echo form_label(lang('email').' :', 'Email'); ?> 
            <?php echo form_input($email_data); ?>
        </td>
        <td>&nbsp;</td>
        <td>
            <?php echo form_label(lang('password').':', 'Password'); ?> 
            <?php echo form_password($password_data); ?>

        </td>
        <td>
            <?php echo form_submit('Login', lang('login')); ?>
        </td>
    </tr>
    <tr>
        <td colspan="4" align="right" ><?php echo anchor(base_url().'users/registration', lang('create-account-link'), 'title="'.lang('create-account-link').'" style="text-align:center;width:100%;color:#fff;"'); ?></td>
    </tr>
    <tr>
        <td colspan="4" align="right" ><?php echo anchor(base_url().'users/forgot_password', lang('forgot-password'), 'title="'.lang('forgot-password').'" style="text-align:center;width:100%;color:#fff;"'); ?></td>
    </tr>
</table>
<?php echo form_close(); ?>

<script type="text/javascript">    
    $(document).ready(function(){   
            jQuery("#login_form").validationEngine(
            {promptPosition : 'bottomLeft',validationEventTrigger: "submit"}
        );        
    });        
</script>