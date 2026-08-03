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
                                        'value' => isset($ci->session->userdata['front']['forum_keywords']) ? htmlspecialchars_decode($ci->session->userdata['front']['forum_keywords']) : "",
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
                                          'value' => isset($ci->session->userdata['front']['forum_member']) ? htmlspecialchars_decode($ci->session->userdata['front']['forum_member']) : "",
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
                                          'value' => isset($ci->session->userdata['front']['forum_date_from']) ? $ci->session->userdata['front']['forum_date_from'] : "",
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
                                          'value' => isset($ci->session->userdata['front']['forum_date_to']) ? $ci->session->userdata['front']['forum_date_to'] : "",
                                          'placeholder' => 'Date To ... '
                                      );
                                      echo form_input($date_to);
                                      ?>
                                 </div>
<!--                                 <div class="listing-search-column">
                                 	<?php echo form_label(lang('industry')); ?>
                                   	<?php echo form_dropdown('industry_id', $industry_list, isset($ci->session->userdata['front']['forum_industry_id']) ? $ci->session->userdata['front']['forum_industry_id'] : '', 'id=industry_id onchange = "load_sector(this.value)"'); ?>

                                 </div>

                                 <div class="listing-search-column">
                                 	<label><?php echo form_label(lang('sector')); ?></label>
                                    <span id="related_sector">
                                   	 <?php echo form_dropdown('sector_id', $sector_list, isset($ci->session->userdata['front']['forum_sector_id']) ? $ci->session->userdata['front']['forum_sector_id'] : "", 'id=sector_id'); ?>
                                    </span>
                                 </div>-->

                                 <div class="listing-search-column">
                                 	<?php echo form_button(array("type" => "submit", "name" => "mysearch", "id" => "mysearch", "value" => "Search", "class" => "search-btn", 'content' => 'Search')); ?>
                                    <?php echo form_button(array("type" => "reset", "class" => "search-btn", 'content' => 'Reset', "onclick" => "reset_data()")); ?>
                                 </div>


                             </div>
                          </div>

<?php
echo form_close();
