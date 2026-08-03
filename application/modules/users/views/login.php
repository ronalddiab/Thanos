<?php
$attributes = array('name' => 'login_form_inner', 'id' => 'login_form_inner');
echo form_open("users/login", $attributes);
?>
<table cellspacing="20px" cellpadding="0" width="100%" border="0">
    <tr>
        <td>
            <h1 style="margin-top: 10px"><?php echo lang('login'); ?></h1>
        </td>
    </tr>
    <tr>
        <td valign="top" width="15%" align="right">
            <?php
            $email_data = array(
                'name' => 'email_w',
                'id' => 'email_w',
                'value' => set_value('email_w', ""),
                'maxlength' => '150',
                'style' => 'width: 200px',
                'class' => "validate[required,custom[email]]"
            );
            $password_data = array(
                'name' => 'password_w',
                'id' => 'password_w',
                'value' => set_value('password_w', ""),
                'maxlength' => '50',
                'style' => 'width: 200px',
                'class' => "validate[required]"
            );
            ?>
            <?php echo form_label(lang('email') . ':', 'Email'); ?>
        </td>
        <td width="90%">
            <?php echo form_input($email_data); ?>
            <span class="warning-msg"><?php echo form_error('email'); ?></span>
        </td>
    </tr>

    <tr>
        <td align="right">
            <?php echo form_label(lang('password') . ':', 'Password'); ?>
        </td>
        <td>
            <?php echo form_password($password_data); ?>
            <span class="warning-msg"><?php echo form_error('password'); ?></span>
        </td>
    </tr>
    
    <tr>
        <td width="20">&nbsp; </td>
        <td>
            <?php echo form_submit("Login", lang('login')); ?>
        </td>
    </tr>
</table>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function()
    {
        jQuery("#login_form_inner").validationEngine(
        {   validationEventTrigger: "submit"}
    );
    });
</script>