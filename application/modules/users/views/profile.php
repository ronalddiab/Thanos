<?php echo add_css('validationEngine.jquery'); ?>
<?php echo add_js(array('jquery-1.9.1.min', 'jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine')); ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <h1><?php echo lang("edit-profile"); ?></h1>
    </tr>
    <?php
    if (!isset($gender))
        $gender = "M";


    if (!isset($address))
        $address = "";
    if (!isset($hobbies))
        $hobbies = "";
    ?>
    <tr>
        <td class="add-user-form-box">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="100%" valign="top">
                        <?php
                        $attributes = array('name' => 'profile_form', 'id' => 'profile_form','enctype'=>'multipart/form-data');
                        echo form_open('users/profile', $attributes);


                        ?>
                        <?php echo form_hidden('role_id', $role_id); ?>
                        <table width="100%" cellpadding="5" cellspacing="1" border="0">
                            <?php
                            $firstname_data = array(
                                'name' => 'firstname',
                                'id' => 'firstname',
                                'value' => set_value('firstname', ((isset($firstname)) ? $firstname : '')),
                                'style' => 'width:198px;',
                                'maxlength' => '50',
                                'class' => "validate[required,maxSize[50]]"
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
                                'maxlength' => '150',
                                'value' => set_value('email', ((isset($email)) ? $email : '')),
                                'style' => 'width:198px;',
                                'class' => "validate[required,custom[email],maxSize[150]]"
                            );
                            ?>
                            <tr>
                                <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('email'), 'email'); ?>:</td>
                                <td><?php echo form_input($email_data); ?><span class="warning-msg"><?php echo form_error('email'); ?></span></td>
                            </tr>
                            <?php
                            $address_data = array(
                                'name' => 'address',
                                'id' => 'address',
                                'value' => set_value('address', ((isset($address)) ? $address : '')),
                                'style' => 'width:198px;height:50px;',
                                'onkeyup'=>'new do_resize(this);',
                                'onKeyPress'=>'return ( this.value.length < 500);',
                                'onpaste'=>'return ( this.value.length < 500);'
                            );
                            ?>
                            <tr>
                                <td align="right"><?php echo form_label(lang('address'), 'address'); ?>:</td>
                                <td><?php echo form_textarea($address_data); ?><span class="warning-msg"><?php echo form_error('address'); ?></span></td>
                            </tr>
                            <?php
                            $gender_data = array(
                                'name' => 'gender',
                                'id' => 'gender',
                                'value' => set_value('gender', $gender),
                                'class' => "validate[required]"
                            );
                            if ($gender == 'M')
                            {


                                $male_sel = 'checked="checked"';
                                $female_sel = '';
                            }
                            else if($gender == 'F')
                            {
                                $female_sel = 'checked="checked"';
                                $male_sel = '';
                            }
                            ?>
                            <tr>
                                <td align="right"><span class="star">*&nbsp;</span><?php echo form_label(lang('gender'), 'gender'); ?>:</td>
                                <td class="fieldbg">
                                    <?php echo form_radio('gender', 'M', $male_sel); ?>
                                    <label for="M"><?php echo lang("male"); ?></label>
                                    <?php echo form_radio('gender', 'F', $female_sel); ?>
                                    <label for="F"><?php echo lang("female"); ?></label>
                                </td>
                            </tr>


                            <?php
                            $Sport = $Movie = $Travelling = FALSE;
                            if ($hobbies != "")
                            {
                                $Hobbies = explode(",", $hobbies);
                                if (in_array('Sport', $Hobbies))
                                    $Sport = TRUE;
                                if (in_array('Movies', $Hobbies))
                                    $Movie = TRUE;
                                if (in_array('Travelling', $Hobbies))
                                    $Travelling = TRUE;
                            }
                            ?>
                            <tr>
                                <td valign="top" align="right"><?php echo form_label(lang('hobbies'), 'Hobbies'); ?>:</td>
                                <td class="checkbox">
                                    <p><?php echo form_checkbox('hobbies[]', lang('sport'), $Sport); ?> <label><?php echo lang('sport'); ?></label></p>
                                    <p><?php echo form_checkbox('hobbies[]', lang('movies'), $Movie); ?> <label><?php echo lang('movies'); ?></label></p>
                                    <p><?php echo form_checkbox('hobbies[]', lang('travelling'), $Travelling); ?> <label><?php echo lang('travelling'); ?></label></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <td valign="top" align="right"><?php echo form_label(lang('forumavtar'), 'Forum Avtar'); ?>:</td>
                                <td>
                                    <?php
                                    $forumavtar_data = array(
                                        'name' => 'forumavtar',
                                        'id' => 'forumavtar',
                                        'class' => 'form-control'
                                    );
                                    echo form_upload($forumavtar_data,'',$disabled);
                                    ?>
                                    <span class="warning-msg"><?php echo form_error('forumavtar'); ?></span>
                                    <?php
                                    if (!empty($forumavtar)) {
                                        echo "<br /><img src='" . site_url() . "assets/uploads/forum_avtar/" . $forumavtar . "' width='50px' />";
                                    }
                                    ?>
                                </td>
                            </tr>
                            
                            <tr>
                                <td valign="top" align="right"><?php echo form_label(lang('forumname'), 'Forum Name'); ?>:</td>
                                <td class="checkbox">
                                    <?php
                                    $forumname_data = array(
                                        'name' => 'forumname',
                                        'id' => 'forumname',
                                        'value' => set_value('forumname', htmlspecialchars_decode($forumname)),
                                        'class' => 'form-control',
                                        'maxlength' => '50'
                                    );
                                    echo form_input($forumname_data,'',$disabled);
                                    ?>
                                </td>
                            </tr>
                            
                            <!--<tr>
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
--                                    <span class="warning-msg"><?php // echo form_error('captcha'); ?></span>--
                                </td>
                            </tr> -->
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