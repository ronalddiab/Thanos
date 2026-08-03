<?php echo add_js(array('jquery.validate.min')); ?>     
      <div class="row">
        <div class="col-md-12">	
		<?php
			$attributes = array('name' => 'forgot_password', 'id' => 'forgot_password','class' => 'form-horizontal');
			$current_password_data = array(
			    'name' => 'current_password',
			    'id' => 'current_password',
			    'maxlength' => '50',
			    'value' => set_value('current_password',$cur_pass),
			    "class" => "form-control",
				"required" => "required"
			);
			$password_data = array(
			    'name' => 'password',
			    'id' => 'password',
			     'maxlength' => '50',
			    'value' => set_value('password', ""),
			    "class" => "form-control",
				"required" => "required"
			);
			$passconf_data = array(
			    'name' => 'passconf',
			    'id' => 'passconf',
			     'maxlength' => '50',
			    'value' => set_value('passconf', ""),
			    "class" => "form-control",
				"required" => "required"
			);

			echo form_open($this->_data['section_name']."/users/changepassword", $attributes);
			?>

          
          <div class="panel panel-default">
              <div class="panel-heading">
                <div class="panel-btns">                  
                  <a class="minimize" href="#">-</a>
                </div>
                <h4 class="panel-title">Change Password</h4>
                
              </div>
              <div class="panel-body">
                <div class="form-group has-error">
                  <label class="col-sm-3 control-label">Current Password <span class="asterisk">*</span></label>
                  <div class="col-sm-9">
                    <!--<input type="text" required="" placeholder="Type your name..." class="form-control" name="name"><label for="name" class="error">This field is required.</label>-->
					<?php
            			echo form_password($current_password_data);
            			echo '<span class="validation_error">' . form_error('current_password') . '</span>';
            		?>
                  </div>
                </div>
                
                <div class="form-group has-error">
                  <label class="col-sm-3 control-label">Password <span class="asterisk">*</span></label>
                  <div class="col-sm-9">
                    <?php echo form_password($password_data);
            			echo '<span class="validation_error">' . form_error('password') . '</span>';
            			?>
                  </div>
                </div>

                
                <div class="form-group has-error">
                  <label class="col-sm-3 control-label">Confirm Password <span class="asterisk">*</span></label>
                  <div class="col-sm-9">
                    <?php echo form_password($passconf_data);
            		echo '<span class="validation_error">' . form_error('passconf') . '</span>';
            		?>
                  </div>
                </div>
              </div><!-- panel-body -->
              <div class="panel-footer">
                <div class="row">
                  <div class="col-sm-9 col-sm-offset-3">
				  	<?php
          $passconf_data = array(
              'name' => 'Submit',
              'id' => 'Submit',
              'value' =>'Submit',
              "class" => "btn btn-primary"
          );
          ?>

		<?php echo form_submit($passconf_data); ?>
                    
                 
                  </div>
                </div>
              </div>
            
          </div><!-- panel -->
          <?php echo form_close(); ?>
        
        </div><!-- col-md-6 -->
                
      </div><!--row -->
      
<script>
jQuery(document).ready(function(){
  
  // Basic Form
  jQuery("#forgot_password").validate({
    highlight: function(element) {
      jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    success: function(element) {
      jQuery(element).closest('.form-group').removeClass('has-error');
    }
  });
  
});
</script>