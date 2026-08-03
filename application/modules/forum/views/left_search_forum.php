
                        <?php
                          $attributes = array('name' => 'myform', 'id' => 'myform');
                          echo form_open(site_url() . 'forum/forum_listing/', $attributes);
                          ?>
                      	  <div class="col-sm-3 listing-left">
                          	<h2><?php echo lang("search_forum"); ?></h2>
                          	 <div class="listing-search-box">
                             	 <div class="listing-search-column">
                                 	<?php echo form_label(lang('keywords')); ?>
                                   	<?php
                                    $input_data = array(
                                        'name' => 'keywords',
                                        'id' => 'keywords',
                                        //'class' => 'validate[required]',
                                         'value' => set_value('keywords', urldecode($keywords)),
                                        'value' => isset($this->_ci->session->userdata['front']['keywords']) ? $this->_ci->session->userdata['front']['keywords'] : "",
                                        'placeholder' => 'keywords ... '
                                    );
                                    echo form_input($input_data);
                                    ?>
                                 </div>


                                 <div class="listing-search-column">
                                 	<?php echo form_label(lang('member')); ?>
                                   	 <?php
                                      $member_data = array(
                                          'name' => 'member',
                                          'id' => 'member',
                                          //'class' => 'validate[required]',
                                          // 'value' => set_value('member', urldecode($search_term)),
                                          'value' => isset($this->_ci->session->userdata['front']['forum_member']) ? $this->_ci->session->userdata['front']['forum_member'] : "",
                                          'placeholder' => 'Member ... '
                                      );
                                      echo form_input($member_data);
                                      ?>
                                 </div>

                                  <div class="listing-search-column">
                                 	<label><?php echo form_label(lang('date-from')); ?></label>
                                   	 <?php
                                      $date_from = array(
                                          'name' => 'date_from',
                                          'id' => 'date_from',
                                          //'class' => 'validate[required]',
                                          // 'value' => set_value('date_from', urldecode($search_term)),
                                          'value' => isset($this->_ci->session->userdata['front']['forum_date_from']) ? $this->_ci->session->userdata['front']['forum_date_from'] : "",
                                          'placeholder' => 'Date From ... '
                                      );
                                      echo form_input($date_from);
                                      ?>
                                 </div>
                                   <div class="listing-search-column">
                                 	<label><?php echo form_label(lang('date-to')); ?></label>
                                   	 <?php
                                      $date_to = array(
                                          'name' => 'date_to',
                                          'id' => 'date_to',
                                          //'class' => 'validate[required]',
                                          // 'value' => set_value('date_to', urldecode($search_term)),
                                          'value' => isset($this->_ci->session->userdata['front']['forum_date_to']) ? $this->_ci->session->userdata['front']['forum_date_to'] : "",
                                          'placeholder' => 'Date To ... '
                                      );
                                      echo form_input($date_to);
                                      ?>
                                 </div>
                                 <div class="listing-search-column">
                                 	<?php echo form_label(lang('industry')); ?>
                                   	<?php echo form_dropdown('industry_id', $industry_list, isset($this->_ci->session->userdata['front']['forum_industry_id']) ? $this->_ci->session->userdata['front']['forum_industry_id'] : '', 'id=industry_id onchange = "load_sector(this.value)"'); ?>

                                 </div>

                                 <div class="listing-search-column">
                                 	<label><?php echo form_label(lang('sector')); ?></label>
                                    <span id="related_sector">
                                   	 <?php echo form_dropdown('sector_id', $sector_list, isset($this->_ci->session->userdata['front']['forum_sector_id']) ? $this->_ci->session->userdata['front']['forum_sector_id'] : "", 'id=sector_id'); ?>
                                    </span>
                                 </div>

                                 <div class="listing-search-column">
                                 	<?php echo form_button(array("type" => "submit", "name" => "mysearch", "id" => "mysearch", "value" => "Search", "class" => "search-btn", 'content' => 'Search')); ?>
                                    <?php echo form_button(array("type" => "reset", "class" => "search-btn", 'content' => 'Reset', "onclick" => "reset_data()")); ?>
                                 </div>


                             </div>
                          </div>
                          <?php echo form_close(); ?>
<!---------------------------------------------js & ajax area(start)----------------------------------------------------->
<script type="text/javascript">

    function attach_error_event() {
        $('div.formError').bind('click', function() {
            $(this).fadeOut(1000, removeError);
        });
    }


    function removeError()
    {
        jQuery(this).remove();
    }

    function sort_data(sort_by, sort_order)
    {
        // blockUI();
        $.ajax({
            type: 'POST',
            //url: '<?php echo base_url(); ?>admin/forum/index/1',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: $('#search_term').val(), sort_by: sort_by, sort_order: sort_order},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        // unblockUI();
    }
    $("#search_term").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            submit_search();
        }
    });
    function submit_search()
    {
        // alert($('#keywords').val());
//        if ($('#keywords').val().trim() == '') {
//            $('#keywords').validationEngine('showPrompt', '<?php echo lang('msg-search-req'); ?>', 'error');
//            attach_error_event(); //for remove dynamically populate popup
//            return false;
//        }
        // blockUI();
        $.ajax({
            type: 'POST',
            //url:'<?php echo base_url(); ?>admin/forum/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', keywords: $('#keywords').val(), date_from: $('#date_from').val(), date_to: $('#date_to').val(), industry_id: $('#industry_id').val(), sector_id: $('#sector_id').val(), member: $('#member').val()},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        // unblockUI();
    }
    function reset_data()
    {
        // blockUI();
        $.ajax({
            type: 'POST',
            //url:'<?php echo base_url(); ?>/forum/index',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: ""},
            success: function(data) {
                $("#ajax_table").html(data);
            }
        });
        //unblockUI();
    }

    $(function() {
        $("#date_from").datepicker({dateFormat: 'yy-mm-dd'});
        $("#date_to").datepicker({dateFormat: 'yy-mm-dd'});
    });


    function load_sector(id, secid) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>admin/users/get_related_sector',
            data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', industry_id: id, secid: secid},
            success: function(data) {
                $("#related_sector").html(data);
            }
        });
    }

    $(document).ready(function() {
       <?php
                if(!empty($this->_ci->session->userdata['front']['forum_industry_id'])){
                    ?>
                     load_sector('<?php echo $this->_ci->session->userdata['front']['forum_industry_id']; ?>', '<?php echo $this->_ci->session->userdata['front']['forum_sector_id']; ?>');
                    <?php
                }
        ?>
    });

</script>