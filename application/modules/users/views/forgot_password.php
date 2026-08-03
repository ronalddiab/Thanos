<?php  echo add_css('validationEngine.jquery'); ?>
<?php  echo add_js(array('jquery-1.9.1.min', 'jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>


<?php
                $attributes = array('id' => 'forgot_password', 'name' => 'forgot_password');
                echo form_open("users/forgot_password", $attributes) ?>

<table cellspacing="1" cellpadding="4" border="0" width="1000" align="center">
<!--    <tbody bgcolor="#fff">-->
    <tbody>
        <tr>
            <td colspan="2"><h1><?php echo lang('forgot_password'); ?></h1></td>
        </tr>
        <tr>
            <td style="width: 150px" valign="top">

                <span class="star">*&nbsp;</span>
                <?php
                $email_data = array(
                    'name' => 'email',
                    'id' => 'email',
                    'value' => set_value('email', ""),
                    'maxlength' => '150',
                    'size' => '30',
                    'class' => "validate[required,custom[email]]"
                );
                echo form_label(lang('enter-email'), 'email');
                ?>
            </td>
            <td>
                <?php echo form_input($email_data);?>
                <span class="warning-msg"><?php echo form_error('email'); ?></span>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <?php echo form_submit('Submit', lang('btn-save')); ?>

            </td>
        </tr>
</table>
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function(){

        $('#email').focus();

        jQuery("#forgot_password").validationEngine(
            {validationEventTrigger: "submit"}
    );
    });

//    function check()
//    {
//        alert("IN");
//        return false;
//    }
</script>
<style>
    .warning-msg p
    {
        display: inline !important;
        padding-left: 15px
    }
</style>