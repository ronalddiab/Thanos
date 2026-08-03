<article class="card">
<div class="article-header"> <?php echo lang('set-notification'); ?> </div>
	<div id="ajax_table" class="card-wrap">
        	
	<form method="post" name="saveform" id="saveform">
		<div class="table-responsive">
	        <table class="table table-striped">
	            <?php echo form_open(); ?>
	            <thead>
	                <tr>
	                    <th><?php echo lang('no') ?></th>
	                    <th><?php echo lang('notification'); ?></th>
	                    <th>
	                    	<?php $checked = (count($site_notifications) == count($notification_list))?'checked="checked"':''; ?>
	                        <input type="checkbox" name="check_all" id="check_all" value="0" class="icheck" <?php echo $checked; ?> />
	                    </th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php
	                $i = 1;
	                foreach ($notification_list as $key=>$value) {
	                    ?>
	                    <tr>
	                        <td align="center"><?php echo $i; ?></td>
	                        <td><?php echo $value; ?></td>
	                        <td>
	                        	<?php $checked = (in_array($key, $site_notifications))?'checked="checked"':''; ?>
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

	    <ul class="form-outer-block">
		    <li>	
		    	<label class="main-label">Custom Notifications</label>
		    	<?php
		    	if(!empty($site_custom_notifications)){
		    		foreach ($site_custom_notifications as $key => $value) {
		    			?>
		    			<div class='row add-row'>
		            		<div class='form-col-6'>
		            			<input name='customnotifications[]' value="<?php echo $value; ?>" type='text' class='input-control'>
		            		</div>
		            		<?php 
		            		if($key<1){
		            			?>
		            			<div class="form-col-1">
			                        <button class="btn-control addition" type="button" data-row="<div class='row add-row'><div class='form-col-6'><input name='customnotifications[]' type='text' class='input-control'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
			                    </div>
		            			<?php
		            		}else{
		            			?>
		            			<div class='form-col-1'>
		            				<button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button>
		            			</div>
		            			<?php
		            		}
		            		?>
		                </div>
		    			<?php
		    		}
		    	}else{
		    		?>
		    		<div class='row add-row'>
	            		<div class='form-col-6'>
	            			<input name='customnotifications[]' type='text' class='input-control'>
	            		</div>
	                    <div class="form-col-1">
	                        <button class="btn-control addition" type="button" data-row="<div class='row add-row'><div class='form-col-6'><input name='customnotifications[]' type='text' class='input-control'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
	                    </div>
	                </div>
		    		<?php
		    	}
		    	?>
	        </li>
	    </ul>

	    <?php echo form_hidden('id', $id);	?>
	    <div class="form-btn-outer">
	        <button type="submit" id="mysubmit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
	        <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
	    </div>
    </form>
</div>
</article>
<script type="text/javascript">
    $(function() {
    	$(".btn-control.substract").click(function(e) {
            e.preventDefault();
            var $this = $(this);
            $this.closest(".row").remove();
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