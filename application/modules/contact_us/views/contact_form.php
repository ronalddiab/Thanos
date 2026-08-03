<!--<script type="text/javascript" src="<?php echo site_base_url(); ?>assets/js/reload_captcha.js"></script>-->

<tr>
    <td>
        <h1>Contact Us</h1>
    </td>
</tr>
<tr class="content">

    <td>
        <?php
        $attributes = array("name" => "contact_us", "id" => "contact_us");
        echo form_open('contact_us', $attributes);
        ?>
        <table cellspacing="10px" cellpadding="0">
            <tr>
                <td align="right">
                    <span class="star">*&nbsp;</span>Name:
                </td>
                <td> <?php
        $inputData = array(
            'name' => 'name',
            'id' => 'name',
            'value' => set_value('name', $name),
            'maxlength' => '100',
            'style' => 'width:198px',
            'class' => "validate[required]"
        );
        echo form_input($inputData);
        ?>
                    <br><span class="warning-msg"><?php echo form_error('name'); ?></span>
                </td>
            </tr>

            <tr>
                <td align="right"><span class="star">*&nbsp;</span>Email:</td>
                <td> <?php
                    $inputData = array(
                        'name' => 'email',
                        'id' => 'email',
                        'value' => set_value('email', $email),
                        'maxlength' => '100',
                        'style' => 'width:198px',
                        'class' => "validate[required,custom[email]]"
                    );
                    echo form_input($inputData);
        ?>
                    <br><span class="warning-msg"><?php echo form_error('email'); ?></span>
                </td>
            </tr>

            <tr>
                <td align="right">Subject:</td>
                <td> <?php
                    $inputData = array(
                        'name' => 'subject',
                        'id' => 'subject',
                        'value' => set_value('subject', $subject),
                        'maxlength' => '100',
                        'style' => 'width:198px'
                    );
                    echo form_input($inputData);
        ?>

                </td>
            </tr>

            <tr>
                <td align="right"><span class="star">*&nbsp;</span>Message:</td>

                <td> <?php
                    $inputData = array(
                        'name' => 'message',
                        'id' => 'message',
                        'value' => set_value('message', $message),
                        'rows' => '10',
                        'cols' => '40',
                        'class' => "validate[required]"
                    );
                    echo form_textarea($inputData);
        ?>
                    <br><span class="warning-msg"><?php echo form_error('name'); ?></span>
                </td>
            </tr>
            <?php if (CAPTCHA_SETTING) {
                ?>


                <tr>
                    <td align="right"><span class="star">*&nbsp;</span>
                        <label for="captcha">Enter the Letters:</label>
                    </td>
                    <td>
                        <?php
                        $inputData = array(
                            'name' => 'captcha',
                            'id' => 'captcha',
                            'value' => "",
                            'rows' => '10',
                            'cols' => '40',
                            'class' => "validate[required]"
                        );
                        echo form_input($inputData);
                        ?>
                        <a style="float:right;" href="javascript:;" id="new_captcha"><?php echo add_image(array('refresh_captcha.png')); ?></a>
                        <div id="change_captcha" style="float:right;"> <?php echo $captcha; ?></div>

                        <br><span class="warning-msg"><?php echo form_error('captcha'); ?></span>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td>&nbsp;</td>
                <td><?php echo form_submit('contact_submit', 'Submit'); ?></td>
            </tr>
        </table>


    </td>
</tr>

<script type="text/javascript">
    $(document).ready(function(){


        $("#name").focus();

        jQuery("#contact_us").validationEngine(
            {validationEventTrigger: "submit"}
        );

        $('#new_captcha').click(function() {

            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>contact_us/recaptcha',
                data:{<?php echo $ci->security->get_csrf_token_name(); ?>:'<?php echo $ci->security->get_csrf_hash(); ?>'},
                success: function(data) {
                    $("#change_captcha").html(data);
                }
            });

        });

    });

</script>