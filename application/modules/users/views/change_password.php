<?php echo add_css('validationEngine.jquery'); ?>
<?php echo add_js(array('jquery-1.9.1.min', 'jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>
<h1 style="line-height: 25px !important"><?php echo lang("change-password"); ?></h1><br clear="both" />
<?php
$attributes = array('name' => 'forgot_password', 'id' => 'forgot_password');

$current_password_data = array(
    'name' => 'current_password',
    'id' => 'current_password',
    'value' => set_value('current_password', $current_password),
    "class" => "validate[required]",
    'maxlength' => '40'
);
$password_data = array(
    'name' => 'password',
    'id' => 'password',
    'value' => set_value('password', ""),
    "class" => "validate[required]",
    'maxlength' => '40'
);
$passconf_data = array(
    'name' => 'passconf',
    'id' => 'passconf',
    'value' => set_value('passconf', ""),
    "class" => "validate[required]",
    'maxlength' => '40'
);

//echo $this->session->flashdata( 'changepassword' );
echo form_open("users/change_password", $attributes);
?>
<table cellspacing="10px" cellpadding="0">

    <tr>
        <td align="right" style="width: 300px">
            <span class="star">*&nbsp;</span>
            <?php echo form_label(lang('current-password') . ':', 'password'); ?>
        </td>
        <td>
            <?php
            echo form_password($current_password_data);
            echo '<span style="color: red !important;">' . form_error('current_password') . '</span>';
            ?>
        </td>
    </tr>

    <tr>
        <td align="right"><span class="star">*&nbsp;</span>
            <?php echo form_label(lang('password') . ':', 'password'); ?>
        </td>
        <td>
            <?php
            echo form_password($password_data);
            echo '<span style="color: red !important;">' . form_error('password') . '</span>';
            ?>
        </td>
    </tr>

    <tr>
        <td valign="top" align="right"><span class="star">*&nbsp;</span>
            <?php echo form_label(lang('c-password') . ':', 'passconf'); ?>
        </td>
        <td>
            <?php
            echo form_password($passconf_data);
            echo '<span style="color: red !important;">' . form_error('passconf') . '</span>';
            ?>
        </td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td align="left">
            <?php echo form_submit('Submit', lang('btn-save')); ?>
        </td>
    </tr>
</table>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#current_password').focus();
        jQuery("#forgot_password").validationEngine(
        {validationEventTrigger: "submit"}
    );
    });
</script>
<style>
    span p
    {
        display: inline !important;
        padding-left: 15px
    }
</style>