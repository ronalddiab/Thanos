<?php
echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));
echo add_css(array('MonthPicker.min'));
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
?>
<article class="card">
<div class="article-header"> <?php echo lang('set-notification'); ?> </div>
	<div id="ajax_table" class="card-wrap">
		<form method="post" name="saveform" id="saveform">
			<div class="row">
    			<div class="col-sm-12">
    				<legend><h5><strong>Comments Notifications</strong></h5></legend>
	    			<div class="col-sm-2">
	    				<label class="control-label">
	    					Custom Notes
	    				</label>
	    			</div>
	    			<div class="col-sm-10">
	    				<div class="row col-sm-12 notification-wrapper">
			    			<?php if (!empty($site_custom_notifications)): ?>
			    				<?php foreach ($site_custom_notifications as $year => $monthArray): ?>
			    					<div class="panel panel-primary">
		    							<div class="panel-heading">
		    								<div class="row">
		    									<a class="col-sm-12" href="#year-<?php echo $year; ?>" data-toggle="collapse">
				    				  				<?php echo $year; ?>
			    				  				</a>
		    								</div>
		    							</div>
		    							<div id="year-<?php echo $year; ?>" class="panel-body collapse">
			    					<?php foreach ($monthArray as $month => $notifications): ?>
			    						<div class="panel panel-primary">
			    							<div class="panel-heading">
			    								<div class="row">
			    									<a class="col-sm-12" href="#<?php echo strtolower($fullmontharray[$month]) . "-" . $year; ?>" data-toggle="collapse">
					    				  				<?php echo $fullmontharray[$month]; ?>
				    				  				</a>
			    								</div>
			    							</div>
								    		<div id="<?php echo strtolower($fullmontharray[$month]) . "-" . $year; ?>" class="panel-body collapse">
									    		<?php foreach ($notifications as $k => $notification): ?>
										  			<div class='row add-row'>
														<div class='col-md-5 col-sm-6'>
															<input name='customnotifications[<?php echo $notification['key']; ?>]' value="<?php echo $notification['notification']; ?>" type='text' class='input-control col-sm-10'>
														</div>
														<div class="col-md-3 col-sm-6" style="padding: 7px;">
															<div class='row col-md-6 col-sm-6'>
																<label class='checkbox-outer col-sm-12' style="margin:5px;">
									            					<?php $checked = ($notification['ytd'] == 1) ? 'checked="checked"' : '';?>
									                                <input type='checkbox' name='ytd[<?php echo $notification['key']; ?>]' class='icheck col-sm-3' <?php echo $checked; ?> value='1'/>
									                                <span class='col-sm-9'>YTD</span>
									                            </label>
															</div>
															<div class='row col-md-6 col-sm-6'>
																<label class='checkbox-outer col-sm-12' style="margin:5px;">
									            					<?php $checked = ($notification['annual'] == 1) ? 'checked="checked"' : '';?>
									                                <input type='checkbox' name='annual[<?php echo $notification['key']; ?>]' class='icheck col-sm-3' <?php echo $checked; ?> value='1'/>
									                                <span class='col-sm-9'>Annual</span>
									                            </label>
															</div>
														</div>
														<div class='col-md-3 col-sm-6' style="padding: 7px 7px 7px 15px;">
															<div class="data-info-block input-group col-sm-12">
								            					<?php if (isset($notification['date']) && !empty($notification['date'])): ?>
								            						<input type="text" name="notification_date[<?php echo $notification['key']; ?>]" class='Default MonthFormat' value="<?php echo date('m/Y', strtotime($notification['date'])) ?>">
								            					<?php else: ?>
								            						<input type="text" name="notification_date[<?php echo $notification['key']; ?>]" class='Default MonthFormat' value="">
								            					<?php endif?>
							                                </div>
														</div>
														<div class='col-md-1 col-sm-3'>
															<button type='button' class='btn-control substract' data-month="<?php echo strtolower($fullmontharray[$month]); ?>" data-year="<?php echo $year; ?>"><img src='images/minus-icon.png' alt='Minus'></button>
														</div>
												    </div>
										  		<?php endforeach?>
									  		</div>
								  		</div>
			    					<?php endforeach?>
		    							</div>
		    						</div>
			    				<?php endforeach?>
			    			<?php endif?>
			    			<div class="row col-sm-12" id="custom_notification">
								<div class="row col-sm-12">
		    						<button class="btn btn-secondary btn-submit addition" type="button"><img src="images/plus-icon.png" alt="Plus"> Add Notification</button>
		    						<hr/>
		    					</div>
							</div>
			    		</div>
    				</div>
    			</div>
			</div>
			<div class="table-responsive">
				<legend><h5><strong>Utilities Data Notifications</strong></h5></legend>
	        	<table class="table table-striped">
	            	<?php echo form_open(); ?>
	            	<thead>
	                	<tr>
	                    	<th><?php echo lang('no') ?></th>
	                    	<th><?php echo lang('notification'); ?></th>
	                    	<th>
	                    		<?php $checked = (count($site_notifications) == count($notification_list)) ? 'checked="checked"' : '';?>
	                        	<input type="checkbox" name="check_all" id="check_all" value="0" class="icheck" <?php echo $checked; ?> />
	                    	</th>
	                	</tr>
	            	</thead>
	            	<tbody>
	                <?php
					$i = 1;
					foreach ($notification_list as $key => $value) {
					    ?>
	                    <tr>
	                        <td align="center"><?php echo $i; ?></td>
	                        <td><?php echo $value; ?></td>
	                        <td>
	                        	<?php $checked = (in_array($key, $site_notifications)) ? 'checked="checked"' : '';?>
								<input type="checkbox" name="notifications[]" class="check_box icheck" value="<?php echo $key; ?>" <?php echo $checked; ?> >
	                        </td>
	                    </tr>
	                    <?php
					$i++;
					}
					?>
	            	</tbody>
	        	</table>
	    	</div>
    		<?php echo form_hidden('id', $id); ?>
    		<div class="row form-btn-outer">
        	<button type="submit" id="mysubmit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
        		<a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
    		</div>
		</form>
	</div>
</div>
</article>

<script type="text/template" id="site_template">
	<div class='row add-row'>
		<div class='col-md-5 col-sm-6'>
			<input name='customnotifications[%index%]' type='text' class='input-control'>
		</div>
		<div class="col-md-3 col-sm-6" style="padding: 7px;">
			<div class='row col-md-6 col-sm-6'>
				<label class='checkbox-outer col-sm-12' style="margin:5px;">
	                <input type='checkbox' name='ytd[%index%]' class='icheck col-sm-3' value='1'/>
	                <span class='col-sm-9'>YTD</span>
	            </label>
			</div>
			<div class='row col-md-6 col-sm-6'>
				<label class='checkbox-outer col-sm-12' style="margin:5px;">
	                <input type='checkbox' name='annual[%index%]' class='icheck col-sm-3' value='1'/>
	                <span class='col-sm-9'>Annual</span>
	            </label>
			</div>
		</div>
		<div class='col-md-3 col-sm-6' style="padding: 7px 7px 7px 15px;">
			<div class="data-info-block input-group">
                <input type="text" name="notification_date[%index%]" class='Default MonthFormat' value="">
            </div>
		</div>
		<div class='col-md-1 col-sm-3'>
			<button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button>
		</div>
    </div>
</script>

<script type="text/javascript">
    $(function() {
    	var monthPickerObj;
    	
    	function assignMonthPicker(){
    		monthPickerObj = $(".MonthFormat").MonthPicker();
    	}

    	function removeNotification() {

    		$(".btn-control.substract").click(function(e) {
	            e.preventDefault();
	            var $this = $(this);
	            $this.closest(".row").remove();
    			
    			var month = $(this).data('month');
    			var year = $(this).data('year');

    			if(month != undefined && year != undefined){
    				var data_id = month+"-"+year;
    				var n = $("#"+data_id+" .add-row").length;
		            if(n == 0){
		            	$("#"+data_id).parent('.panel-primary').remove();
		            }
    			}
	        });
    	}
    	assignMonthPicker();
    	removeNotification();

        $(".addition").click(function(){

        	var n = $(".add-row").length;
        	// Cache of the template
	        var template = document.getElementById("site_template");
	        // Get the contents of the template
	        var templateHtml = template.innerHTML;
	        // Final HTML variable as empty string
	        var divHTML = templateHtml.replace(/%index%/g, n);

	        $("#custom_notification").append(divHTML);
	        $('.add-row').iCheck({checkboxClass: 'icheckbox_square'});
	        assignMonthPicker();
    		removeNotification();
        });

        $('#check_all').on('ifChecked', function(event) {
            $('.check_box').iCheck('check');
        });

        $('#check_all').on('ifUnchecked', function(event) {
            if ($('.check_box').filter(':checked').length == $('.check_box').length) {
                $('.check_box').iCheck('uncheck');
            }
        });
        $('.check_box').on('ifUnchecked', function(event) {
            $('#check_all').iCheck('uncheck');
        });

        $('.check_box').on('ifChecked', function(event) {
            if ($('.check_box').filter(':checked').length == $('.check_box').length) {
                $('#check_all').iCheck('check');
            }
        });
    });
</script>