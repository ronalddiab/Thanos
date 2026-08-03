<article class="card">
    <div class="article-header">
        <?php echo ($action == 'add') ? lang('add-user') : lang('edit-user'); ?>
    </div>
    <div class="card-wrap">
        <?php echo form_open_multipart(site_url() . BASE_ADMIN_URL_CUSTOM . 'users/save', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form')); ?>
        <ul class="form-outer-block">
            <li>
                <label class="main-label"><?php echo lang('first-name'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="firstname" id="firstname" class="input-control" value="<?php echo isset($firstname) && !empty($firstname) ? htmlspecialchars_decode($firstname) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('firstname'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('last-name'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="lastname" id="lastname" class="input-control" value="<?php echo isset($lastname) && !empty($lastname) ? htmlspecialchars_decode($lastname) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('lastname'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('user-name'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php if ($id == 0 || $id == '') { ?> 
                            <input type="text" name="username" id="username" class="input-control" value="<?php echo isset($username) && !empty($username) ? htmlspecialchars_decode($username) : ''; ?>">
                        <?php } else { ?>
                            <input type="text" disabled="disabled" name="username" id="username" class="input-control" value="<?php echo isset($username) && !empty($username) ? htmlspecialchars_decode($username) : ''; ?>">
                        <?php } ?>
                        <label class="input-label validation_error"><?php echo form_error('username'); ?></label>
                    </div>
                </div>
            </li>
            <li>
                <label class="main-label"><?php echo lang('email'); ?> <span class="asterisk">*</span></label>
                <div class="row">
                    <div class="form-col-12">
                        <input type="text" name="email" id="email" class="input-control" value="<?php echo isset($email) && !empty($email) ? htmlspecialchars_decode($email) : ''; ?>">
                        <label class="input-label validation_error"><?php echo form_error('email'); ?></label>
                    </div>
                </div>
            </li>
            <?php
            $password_data['name'] = 'password';
            $password_data['id'] = 'password';
            $password_data['maxlength'] = '40';
            $password_data['value'] = set_value('password', ((isset($password)) ? $password : ''));
            // if ($id == "" || $id == 0) {
            $password_data['class'] = 'validate[required] input-control';
            // } else {
            //    $password_data['onblur'] = 'addClassforemail(this);';
            // }
            $passconf_data['name'] = 'passconf';
            $passconf_data['id'] = 'passconf';
            $passconf_data['maxlength'] = '40';
            $passconf_data['value'] = set_value('passconf', ((isset($passconf)) ? $passconf : ''));
            //if ($id == "" || $id == 0)
            $passconf_data['class'] = 'validate[required] input-control';
            ?>

            <li>
                <?php if ($id == '' || $id == 0) { ?>
                    <label class="main-label"><?php echo lang('password'); ?> <span class="asterisk">*</span></label>
                <?php } else { ?>
                    <label class="main-label"><?php echo lang('password'); ?></label>
                <?php } ?>
                <div class="row">
                    <div class="form-col-12">
                        <?php echo form_password($password_data); ?>
                        <label class="input-label validation_error"><?php echo form_error('password'); ?></label>
                    </div>
                </div>
            </li>
            <?php //} ?>

            <?php // if ($id == '' || $id == 0) { ?>
            <li>
                <?php if ($id == '' || $id == 0) { ?>
                    <label class="main-label"><?php echo lang('c-password'); ?> <span class="asterisk">*</span></label>
                <?php } else { ?>
                    <label class="main-label"><?php echo lang('c-password'); ?></label>
                <?php } ?>

                <div class="row">
                    <div class="form-col-12">
                        <?php echo form_password($passconf_data); ?>
                        <label class="input-label validation_error"><?php echo form_error('passconf'); ?></label>
                    </div>
                </div>
            </li>
            <?php // } ?>

            <li>
                <label class="main-label"><?php echo lang('avtar'); ?></label>
                <div class="row">
                    <div class="form-col-12">
                        <?php
                        $avtar_data = array(
                            'name' => 'avtar',
                            'id' => 'avtar',
                            'class' => 'form-control'
                        );
                        echo form_upload($avtar_data, '', $disabled);
                        ?>
                        <?php
                        if (!empty($avtar) && file_exists(BASE_PATH_CUSTOM . '/assets/uploads/avtar/thumb/' . $avtar)) {
                            echo "<br/><img src='" . site_url() . "assets/uploads/avtar/thumb/" . $avtar . "' />";
                        } else {
                            echo "<br/>";
                        }
                        ?>
                        <label class="input-label validation_error"><?php echo form_error('avtar'); ?></label>                        
                    </div>
                </div>
            </li>

            <?php if (!empty($role_list)) { ?>
                <li>
                    <?php
					$role_edit_permission = check_user_permission_by_label('admin.roles.action.edit');
					if ($action == 'edit' || $action == 'edit_profile') {
						if ($role_edit_permission) {
							$disable = "";
							$cursur = '';
						} else {
							$disable = "disabled = 'disabled'";
							$cursur = 'cursor:not-allowed;';
						}
                    } else {
                        $disable = "";
                    }

					$additional_info = $disable . ' style="width:105px;' . $cursur . '"';
                    ?> 
                    <label class="main-label"><?php echo lang('role'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-dropdown">
								<?php echo form_dropdown('role_id', $role_list, ((isset($role_id)) ? $role_id : 0), 'data-type="custom-dropdown" id="for_role"' . $additional_info); ?>
                                <label class="input-label validation_error"><?php echo form_error('role_id'); ?></label>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>

            <?php // if (isset($is_superadmin) && $is_superadmin == 1) { ?> 
            <li class="for_site_div">
                <?php
				$site_edit_permission = check_user_permission_by_label('admin.sites.edit');
				if ($action == 'edit' || $action == 'edit_profile') {
					if ($site_edit_permission) {
						$disable = "";
						$cursur = '';
					} else {
						$disable = "disabled = 'disabled'";
						$cursur = 'cursor:not-allowed;"';
					}
				} else {
					$disable = "";
				}

				$additional_info = $disable . ' style="width:105px;' . $cursur . '"';
                ?>
                <label class="main-label"><?php echo lang('site'); ?></label> 

                <div class="row">
                    <div class="form-col-10">
                        <div class="form-dropdown">
							<?php echo form_dropdown('site_id[]', $hotel_sites, $site_id, 'data-type="custom-dropdown" id="site_action"' . $additional_info); ?>
                            <label class="input-label validation_error"><?php echo form_error('site_id'); ?></label>
                        </div>
                    </div>
					<?php $dropdown = form_dropdown('site_id[]', $hotel_sites, '', 'data-type="custom-dropdown-addmore" id="site_action"' . $additional_info); ?>
                    <div id="if_multi_site" class="form-col-1">
						<button type="button" class="btn-control addition additiondropdown" <?php echo $disable; ?> style="<?php echo $cursur; ?>" data-row="<div class='row add-row sit_dynaminc-row'><div class='form-col-10'><div class='form-dropdown'><?php echo str_replace('"', "'", $dropdown); ?></div></div><div class='form-col-1'><button type='button' class='btn-control substract substracttoggle'<?php echo $disable; ?> ><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
                    </div> 
                </div>

                <?php if (!empty($sites)) { ?>
                    <?php
                    foreach ($sites as $key => $value) {
                        if ($key <= 0) { // Start from 2nd site because first is already added 
                            continue;
                        }
                        ?>
                        <div class="row add-row sit_dynaminc-row">
                            <div class="form-col-10">
                                <div class="form-dropdown">
									<?php echo form_dropdown('site_id[]', $hotel_sites, $value, 'data-type="custom-dropdown" id="site_action"' . $additional_info); ?>
                                </div>
                            </div>
                            <div class="form-col-1 if_corporate_user">
								<button type="button" class="btn-control substract substracttoggle" <?php echo $disable; ?> style="<?php echo $cursur; ?>">
                                    <img src='images/minus-icon.png' alt='Minus'>
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                <?php } ?>
            </li>  

            <?php // }  ?> 
<?php //if($role_id == 6) {?>
            <li class="for_region_div">
                <?php
                $additional_info = ' style="width:105px;"';
                ?>
                <label class="main-label"><?php echo lang('region'); ?></label> 

                <div class="row">
                    <div class="form-col-10">
                        <div class="form-dropdown">
                            <?php 
								echo form_dropdown('region_id[]', $region_list, $region_id, 'data-type="custom-dropdown" id="region_action"' . $additional_info); ?>
                            <label class="input-label validation_error"><?php echo form_error('region_id'); ?></label>
                        </div>
                    </div>
						<?php $dropdown = form_dropdown('region_id[]', $region_list, '', 'data-type="custom-dropdown-addmore" id="region_action"' . $additional_info); ?>
                    <div id="if_multi_region" class="form-col-1">
                        <button type="button" class="btn-control addition additiondropdown" data-row="<div class='row add-row region_dynaminc-row'><div class='form-col-10'><div class='form-dropdown'><?php echo str_replace('"', "'", $dropdown); ?></div></div><div class='form-col-1'><button type='button' class='btn-control substract substracttoggle'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
                    </div> 
                </div>

                <?php if (!empty($regions)) { ?>
                    <?php
                    foreach ($regions as $key => $value) {
                        if ($key <= 0) { // Start from 2nd region because first is already added 
                            continue;
                        }
                        ?>
                        <div class="row add-row region_dynaminc-row">
                            <div class="form-col-10">
                                <div class="form-dropdown">
										<?php echo form_dropdown('region_id[]', $region_list, $value, 'data-type="custom-dropdown" id="region_action"' . $additional_info); ?>
                                </div>
                            </div>
                            <div class="form-col-1">
                                <button type="button" class="btn-control substract substracttoggle">
                                    <img src='images/minus-icon.png' alt='Minus'>
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                <?php } ?>
            </li>  
<?php //} ?>
            <?php if ($user_id != 1) { ?> 
                <li>
                    <label class="main-label"><?php echo lang('reports'); ?></label> 
                    <div class="row">
                        <div class="form-col-12">
                            <div class="form-control-block">
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="monthly_ytd" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'monthly_ytd') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('monthly_ytd'); ?></span>
                                </label>
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="annual" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'annual') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('annual'); ?></span>
                                </label>
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="upper_management" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'upper_management') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('upper_management'); ?></span>
                                </label>
                            </div><br/>
                            <div class="form-control-block">
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="monthly_alert" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'monthly_alert') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('monthly_alert'); ?></span>
                                </label>
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="comparision_alert" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'comparision_alert') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('comparision_alert'); ?></span>
                                </label>
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="budget_comparision_alert" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'budget_comparision_alert') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('budget_comparision_alert'); ?></span>
                                </label>
                            </div><br/>
                            <div class="form-control-block">
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="cumulative_comparision_alert" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'cumulative_comparision_alert') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('cumulative_comparision_alert'); ?></span>
                                </label>
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="ytd_budget_alert" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'ytd_budget_alert') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('ytd_budget_alert'); ?></span>
                                </label>
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="daily_trends_alert" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == 'daily_trends_alert') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('daily_trends_alert'); ?></span>
                                </label>
							</div><br/>
							<div class="form-control-block">
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="7_days_average_consumption" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == '7_days_average_consumption') {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?>/>
                                    <span class="col-sm-12"><?php echo lang('7_days_average_consumption'); ?></span>
                                </label>

                                <!-- Quarterly Cron -->
                                <label class="checkbox-outer col-sm-4">
                                    <input type="checkbox" name="reports[]" class="icheck col-sm-3" value="<?php echo QUARTERLY_REPORT;?>" <?php
                                    if (!empty($user_report)) {
                                        foreach ($user_report as $key => $report) {
                                            if ($report['reports'] == QUARTERLY_REPORT) {
                                                echo "checked = 'checked'";
                                                break;
                                            }
                                        }
                                    }
                                    ?> />
                                    <span class="col-sm-12"><?php echo lang('quarterly_cron'); ?></span>
                                </label>
                                <!-- Quarterly Cron -->
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>

            <li>
                <?php
                $statuslist = array('1' => 'Active', '0' => 'Inactive');
                if ($user_id == 1) {
                    $disable = "disabled='disabled'";
                } else {
                    $disable = "";
                }
                ?> 
                <label class="main-label"><?php echo lang('status'); ?></label> 
                <div class="row">
                    <div class="form-col-12">
                        <div class="form-dropdown">
                            <?php echo form_dropdown('status', $statuslist, $status, 'data-type="custom-dropdown" id="status_action"', $disable); ?>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="form-btn-outer">
            <button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="mysubmit"><?php echo lang('btn-save'); ?></button>
            <button type="button" class="btn btn-secondary reset-btn btn-submit" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'users' ?>'"><?php echo lang('btn-cancel'); ?></button>
        </div>
        <?php
        echo form_hidden('id', (isset($id)) ? $id : '0' );
        echo form_hidden('u_id', (isset($user_id)) ? $user_id : '0' );
        echo form_hidden('login_user_id', (isset($login_user_id)) ? $login_user_id : '0' );
        echo form_hidden('login_role_list', (isset($login_role_list)) ? $login_role_list : '0' );
        echo form_hidden('sites_length', (isset($sites)) ? count($sites) : '0' , 'id="sites_length"');
        echo form_hidden('action_page', $action);
        echo form_close();
        ?>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function () {

        $('.for_site_div').hide();
        $('.for_region_div').hide();
        //var role_id =<?php //echo $login_role_list;      ?>;
        var role_id = $('input[name="login_role_list"]').val();
        var user_id = $('input[name="u_id"]').val();
        var login_user_id = $('input[name="login_user_id"]').val();

        var selected_role_ready = new Dropkick("#for_role");

        if (selected_role_ready.value == 2 || selected_role_ready.value == 3 || selected_role_ready.value == 4) {
            $('.for_site_div').show();

            if (selected_role_ready.value == 2) {
                $('#if_multi_site').show();
            } else {
                $('#if_multi_site').hide();
            }
        }

        if (selected_role_ready.value == 6) {
            $('.for_region_div').show();
            $('#if_multi_region').show();
            var sitesLength = $("[name='sites_length']").val();
            if(sitesLength > 0) {
                $('.for_site_div').show();
                $('#if_multi_site').hide();
                $('.if_corporate_user').hide();
            } else {
                $('.for_site_div').hide();
            }
        } else {
            $('.for_site_div').show();

            if (selected_role_ready.value == 2) {
                $('#if_multi_site').show();
            } else {
                $('#if_multi_site').hide();
            }
        }


        if (login_user_id == user_id) {
            var select_role = new Dropkick("#for_role");
            select_role.disable();
            var status_action = new Dropkick("#status_action");
            status_action.disable();
            var site_action = new Dropkick("#site_action");
            site_action.disable();
            var region_action = new Dropkick("#region_action");
            region_action.disable();
        }
        //var role_values = ['1', '2'];
//        if (!inArray(role_id, role_values)) {
//            var select_role = new Dropkick("#for_role");
//            select_role.disable();
//        }

        if (user_id == 1) {
            var status_action = new Dropkick("#status_action");
            status_action.disable();
        }

        var id =<?php echo $id; ?>;
        $(":input").each(function (i) {
            $(this).attr('tabindex', i + 1);
        })

        $("input:text:visible:first").focus().val($('input:text:visible:first').val());


        if (id == 0) {
            $("#saveform").validate({
                rules: {
                    firstname: {
                        required: true
                    },
                    lastname: {
                        required: true
                    },
                    username: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 4
                    },
                    passconf: {
                        required: true,
                        equalTo: "#password"
                    },
                    role_id: {
                        required: true
                    },
                    status: {
                        required: true
                    }
                }
            });
        } else {
            $("#saveform").validate({
                rules: {
                    firstname: {
                        required: true
                    },
                    lastname: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    role_id: {
                        required: true
                    },
                    password: {
                        minlength: 4
                    },
                    passconf: {
                        equalTo: "#password"
                    },
                    status: {
                        required: true
                    }
                }
            });
        }
    });
//    function inArray(needle, haystack) {
//        var length = haystack.length;
//        for (var i = 0; i < length; i++) {
//            if (haystack[i] == needle)
//                return true;
//        }
//        return false;
//    }

    function addClassforemail()
    {
        var email_text = $('#password').val();
        if (email_text != "")
        {
            // $('#email').removeClass();
            $('#passconf').addClass("validate[required]");
        } else
        {
            $('#passconf').removeClass();
            // $('#email').addClass("input validate[required]");
        }


    }

    //var selected_role = new Dropkick("#for_role");

    $("#for_role").change(function () {
        $(".sit_dynaminc-row").remove();
        $(".region_dynaminc-row").remove();
        var for_role_value = $(this).val();

        if (for_role_value == 2 || for_role_value == 3 || for_role_value == 4) {
            $('.for_site_div').show();
            $('.for_region_div').hide();
            $('#if_multi_region').hide();
        }
        if (for_role_value == 1 || for_role_value == 5 || for_role_value == 6) {
            $('.for_site_div').hide();
            if (for_role_value == 6) {
                $('.for_region_div').show();
                $('#if_multi_region').show();
            } else {
                $('.for_region_div').hide();
                $('#if_multi_region').hide();
            }
        }
        if (for_role_value == 2) {
            $('#if_multi_site').show();
        } else {
            $('#if_multi_site').hide();
        }

    });

    $(".additiondropdown").click(function () {
        setTimeout(function () {
            $("select[data-type='custom-dropdown-addmore']").dropkick({
                mobile: true
            });
        });
    });

    $(".btn-control.substract").click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.closest(".row").remove();
    });

//    $(function() {
//        $("saveform:not(.filter) :input:visible:enabled:first").focus();
//      });
</script>



