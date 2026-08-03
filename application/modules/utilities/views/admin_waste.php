<style type="text/css">
.utility_image {
	width: 75px;
    height: 75px;
}
.utility_image_div {
	width: 100px;
    height: 120px;
    border: 2px solid #dbdbdb !important;
    padding: 10px;
}
.whiteColor {
    color:white;
}
.tariffBGDark, .tariffBGDark:hover, .tariffBGDark:active, .tariffBGDark:focus{
    color:white;
    background-color:#22A16D;
    border: 1px solid #dbdbdb;
}
.tariffBGLight , .tariffBGLight:hover, .tariffBGLight:active, .tariffBGLight:focus{
    color:black;
    background-color: #afddca;
    border: 1px solid #dbdbdb;
}
.tariffBG , .tariffBG:hover, .tariffBG:active, .tariffBG:focus{
    color:black;
    background-color: #f5f5f5;
    border: 1px solid #dbdbdb;
}
.form-outer-block {
    max-width: 100%;
}
[data-tip] {
	position:relative;

}
[data-tip]:before {
	content:'';
	/* hides the tooltip when not hovered */
	display:none;
	content:'';
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-bottom: 5px solid #1a1a1a;	
	position:absolute;
	top:30px;
	left:35px;
	z-index:8;
	font-size:0;
	line-height:0;
	width:0;
	height:0;
}
[data-tip]:after {
	display:none;
	content:attr(data-tip);
	position:absolute;
	top:35px;
	left:0px;
	padding:5px 8px;
	background:#1a1a1a;
	color:#fff;
	z-index:9;
	font-size: 0.75em;
	height:25px;
	line-height:25px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	white-space:nowrap;
	word-wrap:normal;
}
[data-tip]:hover:before,
[data-tip]:hover:after {
	display:block;
}
.Tab-block {
    max-width: 100%;
}
</style>
<?php 
echo add_js(array('easyResponsiveTabs', 'MonthPicker.min' ));
echo add_css(array('MonthPicker.min'));
?>
<?php 
$unit_measures = getWasteUnitMeasuresArray();
$typical_destinations = getWasteTypicalDestinationArray(); 
?>
<div id="ajax_table" class="utilities-detail-wrap">
    <?php if(isset($tab_data) && !empty($tab_data)) { ?>
        <article class="card">
            <?php echo form_open_multipart('', array('id' => 'wasteForm', 'name' => 'wasteForm')); ?>
            <div class="article-header"><?php echo lang('utilities-waste-title'); ?> <?php echo "( " . lang('utilities-title-monthly') . " ) "; ?></div>
            <div class="data-info-block-outer">
                <div class="row">
                    <div class="col-sm-12 Tab-block">
                        <div class="col-lg-4 col-sm-6 col-xs-12">
                            <label><?php echo lang('usage-date'); ?></label>
                            <div class="data-info-block">
                                <input type="text" id="MonthFormat" name="MonthFormat" class='Default' value="<?php echo (!empty($utilities_month) && !empty($utilities_year)) ? $utilities_month . '/' . $utilities_year : ''; ?>">
                            </div>
                        </div>
                        <?php $export_waste_permission = check_user_permission_by_label('admin.utilities.export_waste'); ?>
                        <?php if($export_waste_permission){ ?>
                            <div class="col-lg-2 pull-right"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>utilities/export_waste" class="btn btn-secondary btn-submit" style="padding-left:45px;padding-right:45px;"><?php echo lang('export');?></a></div>
                        <?php } ?>  
                    </div>
                </div>
            </div>
            <div class="card-wrap">
                <ul class="form-outer-block">
                    <li style="padding-left: 0px; margin-bottom: 0px;">
                        <div class="row">
                            <div class="form-col-12 form-control-block col-sm-12">
                                <div class="form-control-block col-sm-2">
                                    <label></label>
                                </div>
                                <div class="form-control-block col-sm-1">
                                    <label></label>
                                </div>
                                <div class="form-control-block col-sm-3">
                                    <label style="width:100%;text-align:center;font-weight:bolder;font-size:18px;"><?php echo 'Total Volume/Weight'; ?></label>
                                </div>
                                <div class="form-control-block col-sm-3">
                                    <label style="width:100%;text-align:center;font-weight:bolder;font-size:18px;"><?php echo 'Tariff'; ?></label>
                                </div>
                                <div class="form-control-block col-sm-3">
                                    <label style="width:100%;text-align:center;font-weight:bolder;font-size:18px;"><?php echo 'Total Cost'; ?></label>
                                </div>
                                <div class="form-control-block col-sm-1">
                                    <label></label>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <?php $i = 1; foreach ($tab_data as $key => $valueSection) { ?>
                    <?php $name = isset($key) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($key)))))) : '';
                    if (strpos($name, '_#') !== false) { $data = explode('_#',$name); $name = isset($data[2]) ? $data[2] : $data[0]; }?>
                    <?php if($site_waste_display['is_check_'.$name]) { ?> 
                    <ul class="form-outer-block grand-parent_<?php echo $i; ?>" style="background-color:#22A16D; min-height:65px">
                        <li style="padding-left: 0px; margin-bottom: 0px;">
                            <div class="row">
                                <div class="form-col-12 form-control-block col-sm-12">
                                    <div class="form-control-block col-sm-2">
                                    <?php if (strpos($key, ' #') !== false) { $data = explode(' #',$key); ?>
                                        <label class="whiteColor"><?php echo $data[0]."<br/>(".$typical_destinations[$site_waste_display['typical_destination_'.$name.'']].")";?></label>
                                    <?php } else { ?>
                                        <label class="whiteColor"><?php echo $key."<br/>(".$typical_destinations[$site_waste_display['typical_destination_'.$name.'']].")";?></label>
                                    <?php } ?>
                                    </div>
                                    <div class="form-control-block col-sm-2"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;color:white;width:100%;text-align:right;"><?php echo "Total (".$unit_measures[$site_waste_display['unit_measure_dropdown_'.$name.'']].")"; ?></label>
                                    </div>
                                    <div class="form-control-block col-sm-2">
                                        <input type="text" id="<?php echo 'unit_measure_'.$name.'';?>" onchange="calculate_disposal_cost(`<?php echo $name;?>`)" name="<?php echo 'unit_measure_'.$name.''?>"  class="input-control hr_input" value="<?php echo $site_waste['unit_measure_'.$name.'']; ?>">
                                    </div>
                                    <div class="form-control-block col-sm-2"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;color:white;width:100%;text-align:right;"><?php echo "(". $site_detail['local_currency'].'/'.$unit_measures[$site_waste_display['unit_measure_dropdown_'.$name.'']].")"; ?></label>
                                    </div>
                                    <div class="form-control-block col-sm-2">
                                        <div data-tip="The tariff is automatically calculated, it is equal to Total Volume or Weight / Total Cost."><input type="text" id="<?php echo 'disposal_cost_'.$name.'';?>" name="<?php echo 'disposal_cost_'.$name.'';?>"  class="input-control hr_input tariffBGDark" value="<?php echo $site_waste['disposal_cost_'.$name.'']; ?>" readonly="readonly"></div>
                                    </div>
                                    <div class="form-control-block col-sm-1"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;color:white;width:100%;text-align:right;"><?php echo "(".$site_detail['local_currency'].")"; ?></label>
                                    </div>
                                    <div class="form-control-block col-sm-1">
                                        <input type="text" id="<?php echo 'total_'.$name.''?>" onchange="calculate_disposal_cost(`<?php echo $name;?>`)" name="<?php echo 'total_'.$name.'';?>"  class="input-control hr_input" value="<?php echo $site_waste['total_'.$name.'']; ?>">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <?php } ?>
                    <?php $j = 1; foreach ($valueSection as $keyPanel => $valuePanel) { ?>
                        <?php $name = isset($keyPanel) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($keyPanel)))))) : '';
                        if (strpos($name, '_#') !== false) { $data = explode('_#',$name); $name = isset($data[2]) ? $data[2] : $data[0]; }?>
                        <?php if($site_waste_display['is_check_'.$name]) { ?> 
                        <ul class="form-outer-block parent_<?php echo $i; ?>" style="background-color:#afddca; min-height:65px;">
                            <li style="padding-left: 0px; margin-bottom: 0px;">
                                <div class="row">
                                    <div class="form-col-12 form-control-block col-sm-12">
                                        <div class="form-control-block col-sm-2">
                                        <?php if (strpos($keyPanel, ' #') !== false) { 
                                            $data = explode(' #',$keyPanel); ?>
                                            <label><?php echo $data[0];?></label>
                                        <?php } else { ?>
                                            <label><?php echo $keyPanel;?></label>
                                        <?php } ?>
                                        </div>
                                        <div class="form-control-block col-sm-2"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;width:100%;text-align:right;"><?php echo "Total (".$unit_measures[$site_waste_display['unit_measure_dropdown_'.$name.'']].")"; ?></label>
                                        </div>
                                        <div class="form-control-block col-sm-2">
                                            <input type="text" id="<?php echo 'unit_measure_'.$name.'';?>" onchange="calculate_disposal_cost(`<?php echo $name;?>`)" name="<?php echo 'unit_measure_'.$name.''?>"  class="input-control hr_input" value="<?php echo $site_waste['unit_measure_'.$name.'']; ?>">
                                        </div>
                                        <div class="form-control-block col-sm-2"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;width: 100%;text-align:right;"><?php echo "(". $site_detail['local_currency'].'/'.$unit_measures[$site_waste_display['unit_measure_dropdown_'.$name.'']].")"; ?></label>
                                        </div>
                                        <div class="form-control-block col-sm-2">
                                            <div data-tip="The tariff is automatically calculated, it is equal to Total Volume or Weight / Total Cost."><input type="text" id="<?php echo 'disposal_cost_'.$name.'';?>" name="<?php echo 'disposal_cost_'.$name.'';?>"  class="input-control hr_input tariffBGLight" value="<?php echo $site_waste['disposal_cost_'.$name.'']; ?>" readonly="readonly"></div>
                                        </div>
                                        <div class="form-control-block col-sm-1"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;width: 100%;text-align:right;"><?php echo "(".$site_detail['local_currency'].")"; ?></label>
                                        </div>
                                        <div class="form-control-block col-sm-1">
                                            <input type="text" id="<?php echo 'total_'.$name.''?>" onchange="calculate_disposal_cost(`<?php echo $name;?>`)" name="<?php echo 'total_'.$name.'';?>"  class="input-control hr_input" value="<?php echo $site_waste['total_'.$name.'']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <?php } ?>
                        <?php foreach ($valuePanel as $key => $value) { ?>
                            <?php 
                                $nameMapping = isset($value['name']) ? $value['name'] : $value['label'];
                                $name = isset($value['label']) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($nameMapping)))))) : '';
                            ?>
                            <?php if($site_waste_display['is_check_'.$name]) { ?> 
                            <ul class="form-outer-block child_<?php echo $i."_".$j; ?>" style="background-color:#f5f5f5; min-height:65px; ">
                                <li style="padding-left: 0px; margin-bottom: 0px;">
                                    <div class="row">
                                        <div class="form-col-12 form-control-block col-sm-12">
                                            <div class="form-control-block col-sm-2">
                                                <label><b><?php echo $value['label'];?></b></label>
                                            </div>
                                            <div class="form-control-block col-sm-2"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;width:100%;text-align:right;"><?php echo "Total (".$unit_measures[$site_waste_display['unit_measure_dropdown_'.$name.'']].")"; ?></label>
                                            </div>
                                            <div class="form-control-block col-sm-2">
                                                <input type="text" id="<?php echo 'unit_measure_'.$name.'';?>"  onchange="calculate_disposal_cost(`<?php echo $name;?>`)" name="<?php echo 'unit_measure_'.$name.''?>"  class="input-control hr_input" value="<?php echo $site_waste['unit_measure_'.$name.'']; ?>">
                                            </div>
                                            <div class="form-control-block col-sm-2"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;width:100%;text-align:right;"><?php echo "(". $site_detail['local_currency'].'/'.$unit_measures[$site_waste_display['unit_measure_dropdown_'.$name.'']].")"; ?></label>
                                            </div>
                                            <div class="form-control-block col-sm-2">
                                                <div data-tip="The tariff is automatically calculated, it is equal to Total Volume or Weight / Total Cost."><input type="text" id="<?php echo 'disposal_cost_'.$name.'';?>" name="<?php echo 'disposal_cost_'.$name.'';?>"  class="input-control hr_input tariffBG" value="<?php echo $site_waste['disposal_cost_'.$name.'']; ?>" readonly="readonly"></div>
                                            </div>
                                            <div class="form-control-block col-sm-1"><label style="overflow: hidden;max-height: 45px;overflow-y: auto;width:100%;text-align:right;"><?php echo "(".$site_detail['local_currency'].")"; ?></label>
                                            </div>
                                            <div class="form-control-block col-sm-1">
                                                <input type="text" id="<?php echo 'total_'.$name.''?>" onchange="calculate_disposal_cost(`<?php echo $name;?>`)" name="<?php echo 'total_'.$name.'';?>"  class="input-control hr_input" value="<?php echo $site_waste['total_'.$name.'']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <?php } ?>
                    <?php } ?>
                <?php $j++; } ?>
                <?php $i++; } 
                ?>                 
                <input type="hidden" id="site_id" name="site_id" value="<?php echo $_SESSION['admin']['site_id']; ?>" />
                <input type="hidden" id="month" name="month" value="<?php echo $utilities_month; ?>" />
                <input type="hidden" id="year" name="year" value="<?php echo $utilities_year; ?>" />
                <ul class="form-outer-block">
                    <li style="padding-left: 0px; margin-bottom: 0px;">	    
                        <div class="row">
                            <div class="form-col-12 form-control-block">
                                <label class="form-col-3"><b><?php echo lang('upload-invoice-scan'); ?></b></label>
                                <div class="form-col-6">
                                    <input name="invoice_scan" id="invoice_scan" type="file" class="custom-file-upload form ">
                                </div> 
                                <?php 
                                $extension = substr($wasteInvoice['invoice_scan'], -3);
                                if($wasteInvoice['invoice_scan'] != '') {
                                    if($extension != 'pdf') {
                                        $fileName = $image_path = $wasteInvoice['invoicePath'];
                                    } else {
                                        $fileName = $wasteInvoice['invoicePath'];
                                        $image_path = site_url() . "/assets/uploads/pdf-image.png";
                                    }
                                } else {
                                    $image_path = site_url() . "/assets/uploads/no-image-available.jpg";
                                }
                                $class = 'utility_image_div';
                                ?>
                                <div class="form-col-3 <?php echo $class;?>">
                                    <a class="close delete_utility_image" href="#" style="display: none;" data-feild="invoice_scan">×</a>
                                    <a href="<?php echo $fileName; ?>" target="_blank" >
                                        <img class="utility_image invoice_scan" src="<?php echo $image_path; ?>"/>
                                    </a>
                                </div>                                        
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="form-btn-outer">
                    <button type="submit" name="wasteFormSubmit" id="wasteFormSubmit" value="1" class="btn btn-secondary btn-submit">Submit</button>
                </div>
            </div>
        <?php echo form_close();?>
    </article>
    <?php } else { 
        $path = site_url() . "sites/waste/".$site_detail['id'];
    ?>
    <div class="table-responsive">                  
        <table class="table table-striped" >
            <tr>
                <td><?php echo lang('no-records-for-waste')."<b><a href=".$path.">".$site_detail['site_location_name']."</a></b>"; ?></td>
            </tr>
        </table>
    </div>
<?php } ?>
</div>
<?php $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()); ?>
<script type="text/javascript">
     $(document).ready(function () {
        var date = new Date();
        $('#month').val(date.getMonth() + 1);
        $('#year').val(date.getFullYear());

        $('[data-toggle="tooltip"]').tooltip();
        var query = "<?php echo $querystr; ?>";
        var monthPickerObj = $("#MonthFormat").MonthPicker({
            'OnAfterChooseMonth': function (date) {
                var month = date.getMonth() + 1;
                var year = date.getFullYear();                
                $('#month').val(month);
                $('#year').val(year);
                $('#wasteForm').trigger('submit');
            }
        });

        // for(var i = 1; i<5; i++) {
        //     $('.grand-parent_'+i+'').each(function() {
        //         $grandparent = $(this);
        //         $('.parent_'+i+'').each(function() {
        //             $parent = $(this);
        //             var countChild = $parent.find("ul[class^='child"+i+"']").length;
        //             console.log(countChild);
        //             if($parent.find('ul.child_'+i+'').length == 0) {
        //                 $parent.hide();
        //             } else {
        //                 $parent.show();
        //             }
        //         });
        //         // if($grandparent.find('ul.parent_'+i+'').length == 0) {
        //         //     $grandparent.hide();
        //         // } else {
        //         //     $grandparent.show();
        //         // }
        //     });return false;
        // }
     });

    function calculate_disposal_cost(name) {
        var unit = parseFloat($("#unit_measure_"+name).val());
        var total = parseFloat($("#total_"+name).val());
        if(!isNaN(unit) && !isNaN(total) && unit != 0) {
            var disposal_cost = (total / unit);
            if (!isNaN(disposal_cost)) {
                disposal_cost = parseFloat(disposal_cost);
            } else {
                disposal_cost = '';
            }
            $("#disposal_cost_"+name).val(disposal_cost.toFixed(2));
        }            
    }
</script>