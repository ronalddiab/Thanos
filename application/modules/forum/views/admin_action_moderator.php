<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>
<?php
echo add_css('validationEngine.jquery');
echo add_js(array('jqvalidation/languages/jquery.validationEngine-en', 'jqvalidation/jquery.validationEngine'));

$action = BASE_ADMIN_URL_CUSTOM . '/forum/action_moderator';
if (!empty($id)){
    $action = BASE_ADMIN_URL_CUSTOM . '/forum/action_moderator/' . $id;
}
?>

<article class="card">
    <div class="article-header"> <?php echo ($id>0)?lang('edit-moderator'):lang('add-moderator'); ?> </div>
    <div class="card-wrap">
        <?php echo form_open_multipart($action, array('id' => 'saveform', 'name' => 'saveform','class'=>'site-info-form')); ?>
            <ul class="form-outer-block">
                <li>
                    <label for="firstname" class="main-label"><?php echo lang('firstname'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $firstname_data = array(
                                'name' => 'firstname',
                                'id' => 'firstname',
                                'value' => set_value('firstname', ((isset($firstname)) ? htmlspecialchars_decode($firstname) : '')),
                                'maxlength' => 50,
                                'class' => 'input-control validate[required, om[onlyLetterSp]]'
                            );
                            ?>
                            <?php echo form_input($firstname_data); ?>
                            <label class="input-label validation_error"><?php echo form_error('firstname'); ?></label>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="lastname" class="main-label"><?php echo lang('lastname'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $lastname_data = array(
                                'name' => 'lastname',
                                'id' => 'lastname',
                                'maxlength' => 50,
                                'value' => set_value('lastname', ((isset($lastname)) ? htmlspecialchars_decode($lastname) : '')),
                                'class' => 'input-control validate[required, om[onlyLetterSp]]'
                            );
                            ?>
                            <?php echo form_input($lastname_data); ?>
                            <label class="input-label validation_error"><?php echo form_error('lastname'); ?></label>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="email" class="main-label"><?php echo lang('email'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $email_data = array(
                                'name' => 'email',
                                'id' => 'email',
                                'value' => set_value('email', ((isset($email)) ? htmlspecialchars_decode($email) : '')),
                                'maxlength' => '150',
                                'class' => 'input-control validate[required,custom[email]]'
                            );
                            ?>
                            <?php echo form_input($email_data); ?>
                            <label class="input-label validation_error"><?php echo form_error('email'); ?></label>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="username" class="main-label"><?php echo lang('username'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $username_data = array(
                                'name' => 'username',
                                'id' => 'username',
                                'value' => set_value('username', ((isset($username)) ? htmlspecialchars_decode($username) : '')),
                                'maxlength' => '150',
                                'class' => 'input-control validate[required, om[onlyLetterSp]]'
                            );
                            ?>
                            <?php echo form_input($username_data); ?>
                            <label class="input-label validation_error"><?php echo form_error('username'); ?></label>
                        </div>
                    </div>
                </li>
                <li>
                    <label for="password" class="main-label"><?php echo lang('password'); ?> <span class="asterisk">*</span></label>
                    <div class="row">
                        <div class="form-col-12">
                            <?php
                            $password_data['name'] = 'password';
                            $password_data['id'] = 'password';
                            $password_data['maxlength'] = '40';
                            $password_data['value'] = set_value('password', ((isset($password)) ? $password : ''));

                            $class = 'input-control';
                            if ($id == "" || $id == 0) {
                                $class .= ' validate[required]';
                            }
                            $password_data['class'] = $class;
                            ?>
                            <?php echo form_password($password_data); ?>
                            <label class="input-label validation_error"><?php echo form_error('password'); ?></label>
                        </div>
                    </div>
                </li>
                <li>
                    <label class="main-label"><?php echo lang('status'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
                                <?php 
                                    $statuslist = array('1' => 'Active', '0' => 'Inactive'); 
                                    echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown" '); 
                                ?>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="form-btn-outer">
                <button type="submit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
                <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
            </div>
        <?php 
        echo form_hidden('id', (isset($id)) ? $id : '0' );
        echo form_close();
        ?>
    </div>
</article>

<script type="text/javascript">
    (function($) {

        $.fn.maxlength = function() {

            $("textarea[maxlength]").keypress(function(event) {
                var key = event.which;

                //all keys including return.
                if (key >= 33 || key == 13 || key == 32) {
                    var maxLength = $(this).attr("maxlength");
                    var length = this.value.length;
                    if (length >= maxLength) {
                        event.preventDefault();
                    }
                }
            });
        };

    })(jQuery);

    $(document).ready(function($) {
        //Set maxlength of all the textarea (call plugin)
        $().maxlength();

        $("#menu1").children().removeClass("active");
        $("#menu311").children().removeClass("active");
        $("#menu2").children().removeClass("active");

    });

    $(document).ready(function() {
        $(":input").each(function(i) {
            $(this).attr('tabindex', i + 1);
        });
        /*jQuery("#saveform").validationEngine({
            validationEventTrigger: "submit"
        });*/
        $("#saveform").validate({
            rules: {
                firstname:{
                    required: true
                }
                ,lastname:{
                    required: true
                }
                ,email:{
                    required: true,
                    email: true
                }
                ,username:{
                    required: true
                }
                ,password:{
                    required: true
                }
            }
        });
        $("input:text:visible:first").focus().val($('input:text:visible:first').val());
    });
</script>



