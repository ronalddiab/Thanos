<?php 
$utility_types = getUtilityConstant();
$consumption_constants = getConsumptionConstant();
?>
<div id="ajax_table" class="utilities-detail-wrap">
    <?php 
    echo add_js(array('easyResponsiveTabs', 'MonthPicker.min' , 'bootstrap-datepicker-new','bootstrap-multiselect'));
    echo add_css(array('MonthPicker.min', 'bootstrap-datepicker-new', 'custom','bootstrap-multiselect'));
    ?>
    <article class="card">
        <div class="article-header">
            <div class="row">
                <div class="col-md-10"><?php echo 'Site Residences/Private Retreats Utilities Consumption allocation';?></div>
                <div class="col-md-2"><a href="<?php echo SITE_BASE_URL . '/sites/replicate_residence/'.$site_id; ?>" class="btn btn-primary btn-submit" style="width: 100%;">Replicate Data</a></div>
            </div>
        </div>        
        <?php echo form_open_multipart('', array('id' => 'residenceForm', 'name' => 'residenceForm')); ?>
        <div class="data-info-block-outer">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2">
                            <input type="text" id="YearFormat" class='Default' value="<?php echo (!empty($site_residence['year_id'])) ? $site_residence['year_id'] : $year; ?>">
                    </div>
                    <div class="col-sm-10">
                        <?php foreach ($utility_types as $key => $value) {
                            if($site_detail['show_utility_'.$key] == 1) {
                            ?>
                            <div class="col-sm-3">
                                <label class="radio-outer">
                                    <input type="radio" <?php
                                    if ((isset($site_residence['utility_type']) && $site_residence['utility_type'] == $key) || ($utility_type == $key)) {
                                        echo 'checked="checked"';
                                    }
                                    ?> class="icheck" name="utility_type" value="<?php echo $key;?>" id="utility_type"><?php echo $value;?></label>
                                <label>
                            </div>
                        <?php }
                    } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-wrap">
            <?php foreach ($residence_types as $key => $value) {
                if(in_array($value, [1,2])) {
                    if(isset($site_residence) && !empty($site_residence['utility_type'])) {
                        $utilityName =  ' - '.$utility_types[$site_residence['utility_type']];
                    } else {
                        $utilityName =  '';
                    }
                    if($value == RENTAL_PROGRAM_RESIDENCE) {
                        $name = 'rental_program_residence_';
                        $heading = lang('rental-program') . $utilityName;
                    } else if($value == PRIVATE_RESIDENCE){
                        $name = 'private_program_';
                        $heading = lang('rental-private') . $utilityName;
                    }
            ?>
            <div class="panel panel-primary">
                <div class="panel-heading"><strong><?php echo $heading; ?></strong></div>
                <div class="panel-body">
                    <ul class="form-outer-block">
                        <li style="padding-left: 0px; margin-bottom: 0px;">
                            <div class="row">
                                <div class="form-col-12 form-control-block col-sm-12">
                                    <div class="form-control-block col-sm-2">
                                        <?php echo form_label('Consumption allocation method:','Consumption allocation method:' , ["class" => "main-label"]); ?>
                                    </div>
                                    <div class="form-control-block col-sm-3">
                                        <label class="radio-outer">
                                            <input type="radio" <?php
                                            if (isset($site_residence[$name.'consumption']) && $site_residence[$name.'consumption'] == 1) {
                                                echo 'checked="checked"';
                                            }
                                            ?> class="icheck consumption-select" name="<?php echo $name.'consumption'; ?>" value="1" id="consumption_method"><?php echo lang('split-by-m');?></label>
                                        <label>
                                    </div>
                                    <div class="form-control-block col-sm-3">
                                        <?php echo form_label('Is connected to hotel?','Is connected to hotel?' , ["class" => "main-label rightLabel"]); ?>
                                    </div>
                                    <div class="form-control-block col-sm-3">
                                        <label class="radio-outer">
                                            <input type="radio" <?php
                                            if (isset($site_residence[$name.'hotel_connected']) && $site_residence[$name.'hotel_connected'] == 1) {
                                                echo 'checked="checked"';
                                            }
                                            ?> class="icheck" name="<?php echo $name.'hotel_connected';?>" value="1">Yes</label>
                                        <label>
                                        <label class="radio-outer">
                                            <input type="radio" <?php
                                            if (isset($site_residence[$name.'hotel_connected']) && $site_residence[$name.'hotel_connected'] == 2) {
                                                echo 'checked="checked"';
                                            }
                                            ?> class="icheck" name="<?php echo $name.'hotel_connected';?>" value="2">No</label>
                                        <label>
                                    </div>
                                </div>
                                <div class="form-col-12 form-control-block col-sm-12">
                                    <div class="form-control-block col-sm-2">
                                    </div>
                                    <div class="form-control-block col-sm-3">
                                        <label class="radio-outer"><input type="radio" <?php
                                            if (isset($site_residence[$name.'consumption']) && $site_residence[$name.'consumption'] == 2) {
                                                echo 'checked="checked"';
                                            }
                                            ?> class="icheck consumption-select" name="<?php echo $name.'consumption'; ?>" value="2" id="consumption_method"><?php echo lang('split-by-%');?></label>
                                        <label>
                                    </div>
                                    <div class="form-control-block col-sm-3">
                                        <?php echo form_label('Percentage:','Percentage:' , ["class" => "main-label rightLabel"]); ?>
                                    </div>
                                    <div class="form-control-block col-sm-3">
                                        <input type="hidden" name="<?php echo $name.'float';?>" id="<?php echo $name.'float';?>" value="<?php echo isset($site_residence[$name.'float']) ? htmlspecialchars_decode($site_residence[$name.'float']) : ''; ?>" >
                                        <?php $labelPercentValue = isset($site_residence[$name.'float']) ? htmlspecialchars_decode($site_residence[$name.'float']) : 0;
                                              echo form_label($labelPercentValue.'%',$labelPercentValue.'%' , ["class" => "main-label rightLabel"]); ?>
                                    </div>
                                    <div class="form-control-block col-sm-1">
                                        <!-- <label class="main-label rightLabel">%</label> -->
                                    </div>
                                </div>
                                <div class="form-col-12 form-control-block col-sm-12">
                                    <div class="form-control-block col-sm-2">
                                    </div>
                                    <div class="form-control-block col-sm-3">
                                        <label class="radio-outer"><input type="radio" <?php
                                            if (isset($site_residence[$name.'consumption']) && $site_residence[$name.'consumption'] == 3) {
                                                echo 'checked="checked"';
                                            }
                                            ?> class="icheck consumption-select" name="<?php echo $name.'consumption'; ?>" value="3" id="consumption_method"><?php echo lang('split-by-f');?></label>
                                        <label>
                                    </div>                                    
                                    <div class="form-control-block col-sm-3">
                                        <?php echo form_label('Percentage:','Percentage:' , ["class" => "main-label rightLabel"]); ?>
                                    </div>
                                    <div class="form-control-block col-sm-3">
                                        <?php
                                            $data1 = array(
                                                'name' => $name.'fixed',
                                                'id' => $name.'fixed',
                                                'value' => set_value($name.'fixed', ((isset($site_residence[$name.'fixed'])) ? htmlspecialchars_decode($site_residence[$name.'fixed']) : '')),
                                                'class' => 'input-control',
                                                'maxlength' => 20
                                            );
                                        ?>
                                        <?php echo form_input($data1); ?>
                                    </div>
                                    <div class="form-control-block col-sm-1">
                                        <label class="main-label rightLabel">%</label>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <?php 
                }
            }
            ?>
            <input type="hidden" name="year" id="year" value="<?php echo isset($site_residence['year_id']) ? $site_residence['year_id'] : $year; ?>" >
            <div class="form-btn-outer">
                <button type="submit" name="residenceFormSubmit" id="residenceFormSubmit" value="1" class="btn btn-secondary btn-submit">Submit</button>
            </div>       
            <div>
                <div class="article-header">
                    <?php echo 'Residences/Private Retreats utilities allocation for ' . $year;?>
                </div>
                <?php if (!empty($site_residence_list)) { ?>  
                <div class="table-responsive"> 
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="digits-col"><?php echo lang('no') ?></th>
                                <th class="name-col"><?php echo 'Utility Choice' ?></th>
                                <th class="name-col" colspan="3" style="text-align: center"><?php echo 'Rental Program' ?></th>
                                <th class="name-col" colspan="3" style="text-align: center"><?php echo 'Private Program' ?></th>
                            </tr>
                            <tr>
                                <th class="digits-col"><?php echo '' ?></th>
                                <th class="name-col"><?php echo '' ?></th>
                                <th class="name-col"><?php echo 'Consumption Method' ?></th>
                                <th class="name-col"><?php echo 'Split by value' ?></th>
                                <th class="name-col"><?php echo 'Is hotel connected?' ?></th>
                                <th class="name-col"><?php echo 'Consumption Method' ?></th>
                                <th class="name-col"><?php echo 'Split by value' ?></th>
                                <th class="name-col"><?php echo 'Is hotel connected?' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php                        
                        $i = 1;
                        foreach ($site_residence_list as $residence) {
                            if ($i % 2 != 0) {
                                $class = "odd-row";
                            } else {
                                $class = "even-row";
                            }

                            if(!empty($residence['rental_program_residence_consumption']) && $residence['rental_program_residence_consumption'] == 1) {
                                $isRentalProgramHotelConnected = (!empty($residence['rental_program_residence_hotel_connected']) && $residence['rental_program_residence_hotel_connected'] == 1 ? 'Yes' : ($residence['rental_program_residence_hotel_connected'] == 2 ? 'No' : '-'));
                            }
                            
                            if(!empty($residence['private_program_consumption']) && $residence['private_program_consumption'] == 1) {
                                $isPrivateProgramHotelConnected = (!empty($residence['private_program_hotel_connected']) && $residence['private_program_hotel_connected'] == 1 ? 'Yes' : ($residence['private_program_hotel_connected'] == 2 ? 'No' : '-'));
                            }

                            if(!empty($residence['rental_program_residence_consumption']) && $residence['rental_program_residence_consumption'] == 2) {
                                $rental_program_residence_value = !empty($residence['rental_program_residence_float']) ? $residence["rental_program_residence_float"] : '-';
                            } else if(!empty($residence['rental_program_residence_consumption']) && $residence['rental_program_residence_consumption'] == 3) {
                                $rental_program_residence_value = !empty($residence['rental_program_residence_fixed']) ? $residence["rental_program_residence_fixed"] : '-';
                            } else {
                                $rental_program_residence_value = '-';
                            }

                            if(!empty($residence['private_program_consumption']) && $residence['private_program_consumption'] == 2) {
                                $private_program_value = !empty($residence['private_program_float']) ? $residence["private_program_float"] : '-';
                            } else if(!empty($residence['private_program_consumption']) && $residence['private_program_consumption'] == 3) {
                                $private_program_value = !empty($residence['private_program_fixed']) ? $residence["private_program_fixed"] : '-';
                            } else {
                                $private_program_value = '-';
                            }
                            ?>
                            <tr class="<?php echo $class; ?> rows" id="row-<?php echo $residence['id']; ?>">
                                <td><?php echo $i; ?></td>
                                <td><?php echo !empty($residence['utility_type']) ? $utility_types[$residence['utility_type']] : '-'; ?></td>
                                <td><?php echo !empty($residence['rental_program_residence_consumption']) ? $consumption_constants[$residence["rental_program_residence_consumption"]] : '-'; ?></td>
                                <td><?php echo !empty($rental_program_residence_value) ? $rental_program_residence_value : '-'; ?></td>
                                <td><?php echo !empty($isRentalProgramHotelConnected) ? $isRentalProgramHotelConnected : '-'; ?></td>
                                <td><?php echo !empty($residence['private_program_consumption']) ? $consumption_constants[$residence["private_program_consumption"]] : '-'; ?></td>
                                <td><?php echo !empty($private_program_value) ? $private_program_value : '-'; ?></td>
                                <td><?php echo !empty($isPrivateProgramHotelConnected) ? $isPrivateProgramHotelConnected : '-'; ?></td>
                            </tr>
                            <?php
                            $i++;
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php } else {
                ?>
                <div class="table-responsive">          		
                    <table class="table table-striped" >
                        <tr>
                            <td><?php echo lang('no-records') ?></td>
                        </tr>
                    </table>
                </div>
                <?php } ?> 
            </div>
        </div> 
        <?php echo form_close();?>
    </article> 
</div>
<?php $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()); ?>
<script type="text/javascript">
    $(document).ready(function () {
        var currentYear = (new Date()).getFullYear();
        $('#YearFormat').attr('readonly', 'readonly');
        $('#YearFormat').datepicker({
            format: " yyyy",
            viewMode: "years", 
            minViewMode: "years",
            beforeShowYear: function(date) {
                var year = date.getFullYear();
                var disableClass = '';
                if (year >= currentYear) {
                    disableClass = 'disable-year';
                }
                return disableClass;
            },
            endDate : new Date(),
            autoclose:true
        }).on('changeDate', function(ev) {
            var year = ev.date.getFullYear();  
            $("#year").val(year);
            var utility_type = $(".checked input[name=utility_type]").val();
            var year = $("#year").val();
            if(year != '' && utility_type != ''){
                callAjax(year,utility_type);
            }     
        });
    });

    $('body').on('ifChanged', '#utility_type', function (e) {
        var utility_type = $(this).val();
        var year = $("#year").val();
    
        if(year != '' && utility_type != ''){
            callAjax(year,utility_type);
        }        
    });

    function callAjax(year,utility_type)
    {        
        blockUI();
        $.ajax({
            type:'POST',
            url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>sites/residence/<?php echo $site_id;?>',
            data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>',year:year,utility_type:utility_type},
            success: function (data) {
                $("#ajax_table").html(data);
                unblockUI();
            }
        }); 
    }

    $('body').on('ifChanged', '#consumption_method', function (e) {
        $('input[id="consumption_method"]').on('ifChecked', function (event) {
            var consumptionMethod = this.value;
            var name = $(this).attr('name');
            if (name == 'rental_program_residence_consumption') {
                if(consumptionMethod == 1) {
                    $('input[name="rental_program_residence_float"]').val('');
                    $('input[name="rental_program_residence_float"]').siblings('label').html('0%');
                    $('input[name="rental_program_residence_fixed"]').val('');
					$("input[data_type='rental_program_residence_method_2']").iCheck('uncheck');
					$("input[data_type='rental_program_residence_method_3']").iCheck('uncheck');
                } else if (consumptionMethod == 2) {
                    $('input[name="rental_program_residence_fixed"]').val('');
                    $("input[name='rental_program_residence_hotel_connected']").iCheck('uncheck');
					$("input[data_type='rental_program_residence_method_1']").iCheck('uncheck');
					$("input[data_type='rental_program_residence_method_3']").iCheck('uncheck');
                } else if (consumptionMethod == 3) {
                    $('input[name="rental_program_residence_float"]').val('');
                    $('input[name="rental_program_residence_float"]').siblings('label').html('0%');
                    $("input[name='rental_program_residence_hotel_connected']").iCheck('uncheck');
					$("input[data_type='rental_program_residence_method_1']").iCheck('uncheck');
					$("input[data_type='rental_program_residence_method_2']").iCheck('uncheck');
                }
            } else if (name == 'private_program_consumption') {
                if(consumptionMethod == 1) {
                    $('input[name="private_program_float"]').val('');
                    $('input[name="private_program_float"]').siblings('label').html('0%');
                    $('input[name="private_program_fixed"]').val('');
					$("input[data_type='private_program_method_2']").iCheck('uncheck');
					$("input[data_type='private_program_method_3']").iCheck('uncheck');
                } else if (consumptionMethod == 2) {
                    $('input[name="private_program_fixed"]').val('');
                    $("input[name='private_program_hotel_connected']").iCheck('uncheck');
					$("input[data_type='private_program_method_1']").iCheck('uncheck');
					$("input[data_type='private_program_method_3']").iCheck('uncheck');
                } else if (consumptionMethod == 3) {
                    $('input[name="private_program_float"]').val('');
                    $('input[name="private_program_float"]').siblings('label').html('0%');
                    $("input[name='private_program_hotel_connected']").iCheck('uncheck');
					$("input[data_type='private_program_method_1']").iCheck('uncheck');
					$("input[data_type='private_program_method_2']").iCheck('uncheck');
                }
            }
        });
    });
</script>