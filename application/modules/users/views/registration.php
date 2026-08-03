<?php echo add_css('validationEngine.jquery'); ?>
<?php echo add_js(array('jquery-1.9.1.min', 'jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <h1><?php echo lang("create-new-account"); ?></h1>
    </tr>

    <tr>
        <td class="add-user-form-box">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="100%" valign="top">
                        <?php
                        $attributes = array('name' => 'registration_form', 'id' => 'registration_form');
                        echo form_open('users/registration', $attributes);
                        ?>
                        <table width="100%" cellpadding="5" cellspacing="1" border="0">
                            <?php
                            $firstname_data = array(
                                'name' => 'firstname',
                                'id' => 'firstname',
                                'value' => set_value('firstname', ((isset($firstname)) ? $firstname : '')),
                                'style' => 'width:198px;',
                                'class' => "validate[required,maxSize[50]]",
                                'maxlength' => '50'
                            );
                            ?>
                            <tr>
                                <td align="right" width="40%"><span class="star">*&nbsp;</span><?php echo form_label(lang('first-name'), 'firstname'); ?>:</td>
                                <td><?php echo form_input($firstname_data); ?><span class="warning-msg"><?php echo form_error('firstname'); ?></span></td>
                            </tr>
                            <?php
                            $lastname_data = array(
                                'name' => 'lastname',
                                'id' => 'lastname',
                                'value' => set_value('lastname', ((isset($lastname)) ? $lastname : '')),
                                'style' => 'width:198px;',
                                'maxlength' => '50',
                                'class' => "validate[required,maxSize[50]]"
                            );
                            ?>
                            <tr>
                                <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('last-name'), 'lastname'); ?>:</td>
                                <td><?php echo form_input($lastname_data); ?><span class="warning-msg"><?php echo form_error('lastname'); ?></span></td>
                            </tr>
                            <?php
                            $email_data = array(
                                'name' => 'email',
                                'id' => 'email',
                                'maxlegth' => 150,
                                'value' => set_value('email', ((isset($email)) ? $email : '')),
                                'style' => 'width:198px;',
                                'maxlength' => '150',
                                'class' => "validate[required,custom[email],maxSize[150]]"
                            );
                            ?>
                            <tr>
                                <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('email'), 'email'); ?>:</td>
                                <td><?php echo form_input($email_data); ?><span class="warning-msg"><?php echo form_error('email'); ?></span></td>
                            </tr>

                            <?php
                            $password_data = array(
                                'name' => 'password',
                                'id' => 'password',
                                'style' => 'width:198px;',
                                'maxlength' => '40',
                                'value' => set_value('password', $password),
                                'class' => "validate[required]"
                            );
                            $passconf_data = array(
                                'name' => 'passconf',
                                'id' => 'passconf',
                                'maxlength' => '40',
                                'style' => 'width:198px;',
                                'value' => set_value('passconf'),
                                'class' => "validate[required]"
                            );
                            ?>
                            <tr>
                                <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('password'), 'password'); ?>:</td>
                                <td><?php echo form_password($password_data); ?><span class="warning-msg"><?php echo form_error('password'); ?></span></td>
                            </tr>
                            <tr>
                                <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('c-password'), 'passconf'); ?>:</td>
                                <td><?php echo form_password($passconf_data); ?><span class="warning-msg"><?php echo form_error('passconf'); ?></span></td>
                            </tr>

                            <tr>
                                <td valign="top" align="right">
                                    &nbsp;
                                </td>
                                <td>
                                    <?php
                                    $inputData = array(
                                        'name' => 'captcha',
                                        'id' => 'captcha',
                                        'value' => "",
                                        'rows' => '10',
                                        'cols' => '40',
                                        'style' => 'width:198px;',
                                        'class' => "validate[required]"
                                    );
                                    echo form_input($inputData);
                                    ?>
                                    <span class="warning-msg"><?php echo form_error('captcha'); ?></span>
                                    <div>
                                        <div id="change_captcha" class="float_left" >
                                            <?php echo $captcha; ?>

                                        </div>
                                        <div class="float_left">
                                            &nbsp;<a href="javascript:;" id="new_captcha"><?php echo add_image(array('refresh_captcha.png')); ?></a>
                                        </div>
                                    </div>
<!--                                    <span class="warning-msg"><?php // echo form_error('captcha'); ?></span>-->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center"> <?php
                                    $submit_button = array(
                                        'name' => 'mysubmit',
                                        'id' => 'mysubmit',
                                        'value' => lang('btn-save'),
                                        'title' => lang('btn-save'),
                                        'class' => 'inputbutton',
                                    );
                                    echo form_submit($submit_button);
                                    $cancel_button = array(
                                        'name' => 'cancel',
                                        'value' => lang('btn-cancel'),
                                        'title' => lang('btn-cancel'),
                                        'class' => 'inputbutton',
                                        'onclick' => "location.href='".site_url()."'",
                                    );
                                    echo "&nbsp;";
                                    echo form_reset($cancel_button);
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <?php
                        echo form_hidden('id', (isset($id)) ? $id : '0' );
                        echo form_close();
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script type="text/javascript">
    $(document).ready(function() {

        $("#firstname").focus();

        jQuery("#registration_form").validationEngine(
//                {promptPosition: 'centerRight', validationEventTrigger: "submit"}
                {validationEventTrigger: "submit"}

        );
        $('#new_captcha').click(function() {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_base_url(); ?>index.php/users/recaptcha',
                data: {<?php echo $ci->security->get_csrf_token_name(); ?>: '<?php echo $ci->security->get_csrf_hash(); ?>'},
                success: function(data) {
                    $("#change_captcha").html(data);
                }
            });
        });
    });
</script>
<style>
    .warning-msg p
    {
        display: inline !important;
        padding-left: 15px
    }
</style>