<style type="text/css">
.removePaddingMargin{
    padding-left: 0px!important; margin-bottom: 0px!important;
}
.whiteColor {
    color:white;
}
.darkGreenBG {
    background-color:#22A16D;
}
.lightGreenBG {
    background-color: #afddca;
    margin-left: 5px;
}
.greyBG {
    background-color: #f5f5f5;
    margin-left: 15px;
}
.headingHeight {
    height:65px;
}
.setLeftMargin {
    margin-left:15px;
}
.my-custom-tooltip {
    background-color: #EFEFF0;
    color: #000000;
    border: 1px solid #000000;
    border-radius: 0;
}
.tooltip-arrow {
    color: #EFEFF0;
}
.unit_measure_dropdown > ul.dk-select-options {
    max-height: 240px;
    overflow-y: scroll;
}
</style>
<?php 
echo add_js(array('easyResponsiveTabs', 'MonthPicker.min' , 'bootstrap-datepicker-new','bootstrap-multiselect'));
echo add_css(array('MonthPicker.min', 'bootstrap-datepicker-new', 'custom','bootstrap-multiselect'));
?>
<?php if(isset($tab_data) && !empty($tab_data)) {
$typical_destinations = [
    'None Select',
    'Landfill',
    'Recycling',
    'Donation/Repurposed Consumables',
    'Composting On/Off Site',
    'Waste to Energy/Biodigester',
    'Incineration/Combustion',    
    'Unknown'
]; 
$sources = [
    'None Select',
    'All services',
    'Hotel services',
    'F&B services',
    'Residential services',
    'None',
    'Unknown'
];
$monthly_trackings = [
    'None Select',
    // 'By Waste Stream',
    // 'With Grouping',
    // 'With Destination',
    // 'Unknown',
    'Not Tracked',
    'Tracked'
];
$unit_measures = [
    'None Select',
    'Metric Ton (MT) 1,000 kg',
    'Short Ton (tn) 2,000 Ib',
    'm&#179;',
    'Ft&#179;',
    'Litre',
    'Gallon',
    'Compactor, small (1.5 m&#179;)',
    'Compactor, medium (5 m&#179;)',
    'Compactor, large (9 m&#179;)',
    'Skip, small (3m&#179;)',
    'Skip, medium(6m&#179;)',
    'Skip, large (9m&#179;)',
    'Bin, 2-wheel (0.2 m&#179;)',
    'Bin, 4-wheel (0.8 m&#179;)',
    'Bag, small (0.1 m&#179;)',
    'Bag, large (0.5 m&#179;)',
    'Grease Drum (55gal)',
    'Kilograms',
    'Pounds',
    'Skip container 7m&#179;',
    'Compactor 18m&#179;',
    'Compactor 20m&#179;',
    'Compactor 25m&#179;',
    'Yards'
];
?>
<div id="ajax_table" class="utilities-detail-wrap">
    <article class="card">
        <div class="article-header">
            <div class="row">
                <div class="col-md-10"><?php echo 'Site '.lang('waste');?></div>
                <?php $pdf_path = site_url() . "assets/uploads/WasteInstructions.pdf";?>
                <!-- <div class="col-md-2"><a href="<?php echo $pdf_path; ?>" target="_blank" class="btn btn-primary btn-submit" style="width: 100%;">Instructions</a></div> -->
            </div>    
        </div>
        <div class="card-wrap">
            <div class="row">
                <div class="form-col-12 form-control-block col-sm-12">
                    <!-- <div class="col-sm-1"><h6><b>Instructions:</b></h6></div> 
                    <div class="col-sm-11"><h6>This is a one-time set up for each property. Please complete all details ONLY for the categories where you have monthly data available, selecting the relevant categories in the “Monthly Tracking” column. Data may be available at different levels of detail, including across the Waste Category, Waste Group or Waste Stream. Further guidance is available in the Instructions PDF.</h6></div> -->
                </div>
            </div>
            <div class="row">
                <div class="form-col-12 form-control-block col-sm-12">
                    <div class="col-sm-1"></div>
                    <div class="darkGreenBG col-sm-2"><label class="whiteColor"><b>Waste Category</b></label></div>
                    <div class="lightGreenBG col-sm-2"><label><b>Waste Group</b></label></div>
                    <div class="greyBG col-sm-2"><label><b>Waste Stream</b></label></div>
                </div>
            </div>
            <?php echo form_open_multipart('', array('id' => 'wasteForm', 'name' => 'wasteForm')); ?>
            <ul class="form-outer-block">
                <li class="removePaddingMargin">
                    <div class="row">
                        <div class="form-col-12 form-control-block col-sm-12">
                            <div class="form-control-block col-sm-2">
                                <label></label>
                            </div>
                            <div class="form-control-block col-sm-2">
                                <label><?php echo lang('sources'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="Where does it come from? Select ALL major sources that apply." data-original-title="Where does it come from? Select ALL major sources that apply."><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
                            </div>
                            <div class="form-control-block col-sm-2">
                                <label><?php echo lang('typical-destination'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="Where do you send it when disposed? Select from the options in the dropdown." data-original-title="Where do you send it when disposed? Select from the options in the dropdown."><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
                            </div>
                            <div class="form-control-block col-sm-2">
                                <label><?php echo lang('monthly-tracking'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="To consider the waste stream “tracked” you need to have monthly data, either measured in-house or your vendor/hauler provides a monthly reports of pickups. If this is selected, your team will be expected to report monthly data for this waste stream." data-original-title="To consider the waste stream “tracked” you need to have monthly data, either measured in-house or your vendor/hauler provides a monthly reports of pickups. If this is selected, your team will be expected to report monthly data for this waste stream."><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
                            </div>
                            <div class="form-control-block col-sm-2">
                                <label><?php echo lang('unit-measure'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="What units of measurement do you track and report each month (e.g. kg, tonnes, bins)" data-original-title="What units of measurement do you track and report each month (e.g. kg, tonnes, bins)"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
                            </div>
                            <div class="form-control-block col-sm-2">
                                <label><?php echo lang('tracked-monthly'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="This is marked automatically once the Monthly Tracking column is set to “Tracking”. If checked, this section will appear in your monthly excel data entry sheets." data-original-title="This is marked automatically once the Monthly Tracking column is set to “Tracking”. If checked, this section will appear in your monthly excel data entry sheets."><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        <?php $i = 1; foreach ($tab_data as $key => $valueSection) { ?>
            <?php 
            $name = isset($key) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($key)))))) : '';
            if (strpos($name, '_#') !== false) { $data = explode('_#',$name); $name = isset($data[2]) ? $data[2] : $data[0]; }
            ?>
            <ul class="form-outer-block headingHeight darkGreenBG">
                <li class="removePaddingMargin">
                    <div class="row">
                        <div class="form-col-12 form-control-block col-sm-12">
                            <div class="form-control-block col-sm-2">
                                <?php if (strpos($key, ' #') !== false) { $data = explode(' #',$key); ?>
                                    <label class="whiteColor"><?php echo $data[0];?><?php if(isset($data[1]) && !empty($data[1])) { ?>
                                        <a href="#" data-toggle="tooltip" data-html="true" data-container="article" 
                                            data-placement="right" 
                                            data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner my-custom-tooltip"></div></div>'
                                            title="<?php echo $data[1];?>" 
                                            data-original-title="<?php echo $data[1];?>">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        </a>
                                        <?php } ?>
                                    </label>
                                <?php } else { ?>
                                    <label class="whiteColor"><?php echo $key;?></label>
                                <?php } ?>
                            </div>
                            <div class="form-control-block col-sm-2 form-dropdown-multiselect">
                                <?php echo form_multiselect('source_'.$name.'[]', $sources, explode(',', $site_waste['source_'.$name.'']), 'id="'.'source_'.$name.'"');?>
                            </div>
                            <div class="form-control-block col-sm-2 form-dropdown">
                                <?php echo form_dropdown('typical_destination_'.$name.'', $typical_destinations, $site_waste['typical_destination_'.$name.''], 'data-type = "custom-dropdown" id="typical_destination"');?>
                            </div>
                            <div class="form-control-block col-sm-2 form-dropdown">
                                <?php echo form_dropdown('monthly_tracking_'.$name.'', $monthly_trackings, $site_waste['monthly_tracking_'.$name.''], 'data-type = "custom-dropdown" id="monthly_tracking_'.$name.'" onchange=check_monthly_tracking("'.$name.'")');?>
                            </div>
                            <div class="form-control-block col-sm-2 form-dropdown">
                                <?php echo form_dropdown('unit_measure_dropdown_'.$name.'', $unit_measures, $site_waste['unit_measure_dropdown_'.$name.''], 'data-type = "custom-dropdown" id="unit_measure" class="unit_measure_dropdown"');?>
                            </div>
                            <div class="form-control-block col-sm-2">
                                <input type='checkbox' class='icheck' value='1' id="<?php echo "is_check_".$name;?>" <?php echo ($site_waste["is_check_".$name]) ? 'checked' : ''; ?> disabled>
                                <input type='hidden' name="<?php echo "is_check_".$name;?>" value='0' id="<?php echo "is_check_hidden_".$name;?>">
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <?php foreach ($valueSection as $keyPanel => $valuePanel) { ?>
                <?php
                $name = isset($keyPanel) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($keyPanel)))))) : '';
                if (strpos($name, '_#') !== false) { $data = explode('_#',$name); $name = isset($data[2]) ? $data[2] : $data[0]; }
                ?>   
                <ul class="form-outer-block headingHeight lightGreenBG">
                    <li class="removePaddingMargin">
                        <div class="row">
                            <div class="form-col-12 form-control-block col-sm-12">
                                <div class="form-control-block col-sm-2">
                                    <?php if (strpos($keyPanel, ' #') !== false) { 
                                        $data = explode(' #',$keyPanel); ?>
                                        <label><?php echo $data[0];?><?php if(isset($data[1]) && !empty($data[1])) { ?>
                                            <a href="#" data-toggle="tooltip" data-html="true" data-container="article" 
                                            data-placement="right" 
                                            data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner my-custom-tooltip"></div></div>'
                                            title="<?php echo $data[1];?>" 
                                            data-original-title="<?php echo $data[1];?>">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        </a>
                                            <?php } ?>
                                        </label>
                                    <?php } else { ?>
                                        <label><?php echo $keyPanel;?></label>
                                    <?php } ?>
                                </div>
                                <div class="form-control-block col-sm-2 form-dropdown-multiselect" style="margin-left: -5px;">
                                    <?php echo form_multiselect('source_'.$name.'[]', $sources, explode(',', $site_waste['source_'.$name.'']),'id="'.'source_'.$name.'"');?>
                                </div>
                                <div class="form-control-block col-sm-2 form-dropdown">
                                    <?php echo form_dropdown('typical_destination_'.$name.'', $typical_destinations, $site_waste['typical_destination_'.$name.''], 'data-type = "custom-dropdown" id="typical_destination"');?>
                                </div>
                                <div class="form-control-block col-sm-2 form-dropdown">
                                    <?php echo form_dropdown('monthly_tracking_'.$name.'', $monthly_trackings, $site_waste['monthly_tracking_'.$name.''], 'data-type = "custom-dropdown" id="monthly_tracking_'.$name.'" onchange=check_monthly_tracking("'.$name.'")');?>
                                </div>
                                <div class="form-control-block col-sm-2 form-dropdown">
                                    <?php echo form_dropdown('unit_measure_dropdown_'.$name.'', $unit_measures, $site_waste['unit_measure_dropdown_'.$name.''], 'data-type = "custom-dropdown" id="unit_measure" class="unit_measure_dropdown"');?>
                                </div>
                                <div class="form-control-block col-sm-2">
                                    <input type='checkbox' class='icheck' value='1'  id="<?php echo "is_check_".$name;?>" <?php echo ($site_waste["is_check_".$name]) ? 'checked' : ''; ?> disabled >
                                    <input type='hidden' name="<?php echo "is_check_".$name;?>" value='0' id="<?php echo "is_check_hidden_".$name;?>">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <?php foreach ($valuePanel as $key => $value) { ?>
                    <?php 
                        $nameMapping = isset($value['name']) ? $value['name'] : $value['label'];
                        $name = isset($value['label']) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($nameMapping)))))) : '';
                    ?>
                    <ul class="form-outer-block headingHeight greyBG">
                        <li class="removePaddingMargin">
                            <div class="row">
                                <div class="form-col-12 form-control-block col-sm-12">
                                    <div class="form-control-block col-sm-2">
                                        <label><?php echo $value['label'];?><?php if(isset($value['description']) && !empty($value['description'])) { ?> 
                                            <a href="#" data-toggle="tooltip" data-html="true" data-container="article" 
                                                data-placement="right" 
                                                data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner my-custom-tooltip"></div></div>'
                                                title="<?php echo $value['description'];?>" 
                                                data-original-title="<?php echo $value['description'];?>">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </a>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="form-control-block col-sm-2  form-dropdown-multiselect" style="margin-left: -15px;">
                                        <?php echo form_multiselect('source_'.$name.'[]', $sources, explode(',', $site_waste['source_'.$name.'']),'id="'.'source_'.$name.'"');?>
                                    </div>
                                    <div class="form-control-block col-sm-2 form-dropdown">
                                        <?php echo form_dropdown('typical_destination_'.$name.'', $typical_destinations, $site_waste['typical_destination_'.$name.''], 'data-type = "custom-dropdown" id="typical_destination"');?>
                                    </div>
                                    <div class="form-control-block col-sm-2 form-dropdown">
                                        <?php echo form_dropdown('monthly_tracking_'.$name.'', $monthly_trackings, $site_waste['monthly_tracking_'.$name.''], 'data-type = "custom-dropdown" id="monthly_tracking_'.$name.'" onchange=check_monthly_tracking("'.$name.'")');?>
                                    </div>
                                    <div class="form-control-block col-sm-2 form-dropdown">
                                        <?php echo form_dropdown('unit_measure_dropdown_'.$name.'', $unit_measures, $site_waste['unit_measure_dropdown_'.$name.''], 'data-type = "custom-dropdown" id="unit_measure" class="unit_measure_dropdown"');?>
                                    </div>
                                    <div class="form-control-block col-sm-2">
                                        <input type='checkbox' class='icheck' value='1' id="<?php echo "is_check_".$name;?>" <?php echo ($site_waste["is_check_".$name]) ? 'checked' : ''; ?> disabled>
                                        <input type='hidden' name="<?php echo "is_check_".$name;?>" value='0' id="<?php echo "is_check_hidden_".$name;?>">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                <?php } ?>
            <?php } ?>
        <?php $i++; } ?>
        <div class="form-btn-outer">
            <button type="submit" name="wasteFormSubmit" id="wasteFormSubmit" value="1" class="btn btn-secondary btn-submit">Save</button>
        </div>
        <?php echo form_close();?>
    </article>
</div>
<?php } else { ?>
    <div class="table-responsive">                  
        <table class="table table-striped" >
            <tr>
                <td><?php echo lang('no-records') ?></td>
            </tr>
        </table>
    </div>
<?php } ?>
<script type="text/javascript">
     $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('select[id^="source_"]').multiselect({
            maxHeight: 200,
            buttonWidth: '150px',
            numberDisplayed: 1
        });
        $('[id^="monthly_tracking_"]').each(function (i,element) {
            var elementid = element.getAttribute('id');
            var name = elementid.substring(elementid.indexOf('monthly_tracking_') + 17);
            var value = $("#"+elementid).val();

            if (value != 0) {
                if (value != 2) {
                    $('#is_check_'+name+'').removeAttr('disabled');
                    $('#is_check_'+name+'').iCheck('uncheck');
                    $('#is_check_'+name+'').attr('disabled','disabled');
                    $('#is_check_hidden_'+name+'').val('0');
                    return;
                } else {
                    $('#is_check_'+name+'').removeAttr('disabled');
                    $('#is_check_'+name+'').iCheck('check');
                    $('#is_check_'+name+'').attr('disabled','disabled');
                    $('#is_check_hidden_'+name+'').val('1');
                    return;
                }
            } else {
                $('#is_check_'+name+'').attr('disabled','disabled');
            }
        });
     });
     function check_monthly_tracking(name) {
        var val = $('#monthly_tracking_'+name+'').val();
        if (val != 2) {
            $('#is_check_'+name+'').removeAttr('disabled');
            $('#is_check_'+name+'').iCheck('uncheck');
            $('#is_check_'+name+'').attr('disabled','disabled');
            $('#is_check_hidden_'+name+'').val('0');
            return;
        } else {
            $('#is_check_'+name+'').removeAttr('disabled');
            $('#is_check_'+name+'').iCheck('check');
            $('#is_check_'+name+'').attr('disabled','disabled');
            $('#is_check_hidden_'+name+'').val('1');
            return;
        }
     }
</script>