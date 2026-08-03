<style type="text/css">

.datepicker table tr td span {

  width: 100%;

}



.datepicker table tr td span {

    display: block;

    width: 100% !important;

    height: 54px;

    line-height: 54px;

    float: left;

    margin: 1%;

    cursor: pointer;

    -webkit-border-radius: 4px;

    -moz-border-radius: 4px;

    border-radius: 4px;

}



.datepicker table tr td span.active.active {

	background-color: #007856 !important;

}

.datepicker table tr td span.active {

    background-color: #007856 !important;

    background-image: -webkit-gradient(linear,0 0,0 100%,from(#007856),to(#007856));

    background-image: -webkit-linear-gradient(top,#007856,#007856);

}

.hr_input {

   /* margin-top: 10px;*/

}

.waste_image {

	width: 75px;

    height: 75px;

}

.waste_image_div {

	width: 100px;

    height: 120px;

    border: 2px solid #dbdbdb !important;

    padding: 10px;

}



</style>

<link type="text/css" rel="stylesheet" href="<?php echo site_url(); ?>themes/default/css/datepicker.min.css" >

<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/bootstrap-datepicker.js"></script>

<?php

if (!defined('BASEPATH'))

    exit('No direct script access allowed');



// echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));

echo add_js(array('easyResponsiveTabs'));

// echo add_css(array('MonthPicker.min'));



$budget_disable = '';

$current_month = date('m');

$current_year = date('Y');

$current_week = date('W');



$month = date('m');

$year  = date('Y');



if( ($month == '01') || ($month == '02') || ($month == '03'))

{

    $quarter = 'Q1';

}

elseif( ($month == '04') || ($month == '05') || ($month == '06'))

{

    $quarter = 'Q2';

}

elseif( ($month == '07') || ($month == '08') || ($month == '09'))

{

    $quarter = 'Q3';

}

elseif( ($month == '10') || ($month == '11') || ($month == '12'))

{

    $quarter = 'Q4';

}

$utilities_quarter_year = $year;

$utilities_quarter_quarter      = $quarter;



// pre($csr_data);

/*if ($current_week != '1' && $role_id != 1 && $current_year >= $utilities_year) {

    $budget_disable = ' disabled="disabled" style="cursor:not-allowed;" ';

}*/

?>

<div id="ajax_table" class="utilities-detail-wrap">

    <article class="card">

	<div class="article-header"><?php echo lang('utilities-title'); ?> <?php echo "( " . lang('utilities-title-quarterly') . " ) "; ?></div>



	<div class="data-info-block-outer">

	    <div class="row">

		<div class="col-sm-12 Tab-block">

		    <div class="col-lg-6">

			<label><?php echo lang('choose-quarter'); ?></label>

			<div class="data-info-block">

			    <input type="text" id="quarter_picker" class='Default' name="quarter_picker" value="<?php echo (!empty($utilities_quarter_quarter) && !empty($utilities_quarter_year)) ? $utilities_quarter_quarter . ' ' . $utilities_quarter_year : $quarter." ".$year; ?>">

			</div>

		    </div>

		</div>

	    </div>

	</div>



	<div class="card-wrap">

	    <!--Horizontal Tab-->

		<div id="energy-tabs" class="Tab-block">

		    <ul class="resp-tabs-list hor_1 clearfix">

			<li class="tab-custom-id-1"><?php echo lang('tab-social'); ?></li>

			<li class="tab-custom-id-2"><?php echo 'People & Culture';//lang('tab-hr'); ?></li>

			<li class="tab-custom-id-3"><?php echo lang('tab-waste'); ?></li>

			<li class="tab-custom-id-4"><?php echo lang('tab-biodiversity'); ?></li>

		    </ul>

		    <div class="resp-tabs-container hor_1">

			<div id="tab-1" data-tab-id="1">

			    <form id="savengoform" class="site-info-form" method="post" enctype="multipart/form-data">

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo 'NGOs and causes supported';//lang('csr_legal_compliance'); ?></strong></div>

				    <div class="panel-body"  >

					<?php

					$i = 0;

					$n = 0;



					 ?>

					<div id="main_div">

					<ul  style="padding: 10px;" id="main_ul">

					    <?php

					    if(empty($csr_data))

					    { ?>

					    <li class="ngo_block_li ngo_first_li" name="ngo_block[<?php echo $i; ?>]">

					    </li>



					    <?php

					    } ?>

					    <?php



					    foreach ($csr_data as $csr_key => $csr_detail) {

						?>



						<br />



						<li class="ngo_block_li ngo_first_li" name="ngo_block[<?php echo $i; ?>]" style="border: 2px solid #dbdbdb !important; padding: 10px;">

						<ul class="form-outer-block">

						    <li>

													<div class="row">

														<div class="form-col-1" style="float: right;margin-right: 30px;"><button type="button" class="btn btn-default close_ngo" data-id="<?php echo $csr_detail['id']; ?>" data-j ="<?php echo $i; ?>"  >Delete</button> </div>

													</div>

												</li>



						    <li>

							<label class="main-label col-sm-3"><?php echo lang('affiliate-ngo'); ?></label>

							<div class="row">

							    <div class="form-col-6">

								<input type="text" id="ngo_name" name="ngo_name[<?php echo $i;?>]" class="input-control "  value="<?php echo $csr_detail['ngo_name']; ?>" required >



															<input type="hidden" id="ngo_id" name="ngo_id[<?php echo $i;?>]" class="input-control "  value="<?php echo $csr_detail['id']; ?>">



							    </div>

							</div>

							<div style="clear: both !important;"></div>

						    </li>



						    <li>

							<label class="main-label col-sm-1"><?php echo lang('action'); ?></label>

							<?php

							$actions = $csr_detail['actions'];

							$n = 0;

							if (!empty($actions)) {



							    foreach ($actions as $key => $action) {



								$action_id = $action['id'];

								?>

								<div class="action_row add-row" style="border: 1px solid #ccc; padding: 10px; ">

								    <div class="row add-row">

									<div class="form col-sm-2 form-col-add">Description</div>

									<div class="form col-sm-6 form-col-add">

									    <input name="action_text[<?php echo $i;?>][]" type="text" class="input-control action-text-addition "  value="<?php echo $action['text']; ?>">

									</div>

									<div class="form col-sm-1 form-col-add">SDG</div>

									<div class="form col-sm-2 form-col-add">

									    <input name="action_sdg[<?php echo $i;?>][]" type="text" class="input-control  "  value="<?php echo $action['sdg']; ?>">

									</div>

								    </div>



								    <div class="row add-row">

									<div class="form col-sm-1 form-col-add">Photos</div>

								    </div>

															<div class="row add-row">

															    <ul class="thumbnails">

															    <?php

															    $site_id = $action['site_id'];

															    $media_files = $action['media'];

															    if (!empty($media_files)) {

																foreach ($media_files as $key1 => $media) {



																    $action_id = $media['action_id'];

																    $image     = $media['image'];



																    $path = site_url() . "/assets/uploads/site_".$site_id."/actions/".$action_id."/".$image;

																    $k    = strrpos($image, ".");

																    if (!$k) {  $ext = ''; }



																    $l   = strlen($image) - $k;

																    $ext = substr($image, $k + 1, $l);



																    if(($ext == 'png') || ($ext == 'jpg') || ($ext == 'jpeg') || ($ext == 'gif'))

																    {   ?>

																	<div class="form-col-1 form-col-add " style="width: 100px; height: 100px; border: 2px solid #dbdbdb !important;margin: 10px;">



																	    <a class="close delete_media" href="#" data-id="<?php echo $media['id']; ?>">×</a>

																	    <img src="<?php echo $path; ?>" style="width: 80px; height: auto;" >

																	</div>

																<?php

																    }

																}

															    } ?>

															    </ul>

															</div>



															<div class="row add-row">

															    <div class="form-col-1 form-col-add">Videos</div>

															</div>

															<div class="row add-row">

															    <ul class="thumbnails">

															    <?php

															    $site_id = $action['site_id'];

															    $media_files = $action['media'];

															    if (!empty($media_files)) {

																foreach ($media_files as $key1 => $media) {



																    $action_id = $media['action_id'];

																    $image     = $media['image'];



																    $path = site_url() . "/assets/uploads/site_".$site_id."/actions/".$action_id."/".$image;

																    $k    = strrpos($image, ".");

																    if (!$k) {  $ext = ''; }



																    $l   = strlen($image) - $k;

																    $ext = substr($image, $k + 1, $l);



																    if(($ext == 'mp3') || ($ext == 'mp4') || ($ext == 'wma'))

																    {  ?>

																	<div class="form-col-6 form-col-add " style=" border: 2px solid #dbdbdb !important;margin: 10px;">



																	     <a target="_blank" href="<?php echo $path; ?>" >Click here to play video

																		</a>

																	    <a href="#" class="close delete_media" data-id="<?php echo $media['id']; ?>">× </a>

																	</div>

																<?php

																    }

																}

															    } ?>

															    </ul>



															</div>



								    <div class="row add-row">



									<div class="form col-md-10 form-col-add">

									    <input name="action_media[<?php echo $i;?>][<?php echo $n;?>][]" type="file" class="custom-file-upload action-addition " value="<?php echo $action['photo']; ?>" multiple>



									    <input name="action_id[<?php echo $i;?>][]" value="<?php echo $action['id']; ?>" type="hidden" />

									</div>



									<?php if ($key == 0)

									{ ?>

									    <div class="form-col-1">

										<button class="btn-control addition-plus" data-id="<?php echo $i; ?>" data-cnt="<?php echo $n;?>" type='button' ><img src="images/plus-icon.png" alt="Plus"></button>

									    </div>

									<?php

									} else { ?>

									    <div class="form-col-1">

										<button type='button' class="btn-control substract_minus" data-id="<?php echo $action_id; ?>"><img alt="Minus" src="images/minus-icon.png"></button>

									    </div>

									<?php } ?>

								    </div>

								</div>

								<?php

															$n++;

							    }

							} else {

							    ?>

							    <div class="action_row add-row" style="border: 1px solid #ccc; padding: 10px; ">

								<div class="row add-row">

								    <div class="form col-sm-2 form-col-add">Description</div>

								    <div class="form col-sm-6 form-col-add">

									<input name="action_text[<?php echo $i;?>][]" type="text" class="input-control action-text-addition ">

								    </div>

								    <div class="form col-sm-1 form-col-add">SDG</div>

								    <div class="form col-sm-2 form-col-add">

									<input name="action_sdg[<?php echo $i;?>][]" type="text" class="input-control  ">

								    </div>

								</div>



								<div class="row add-row">

								    <div class="form-col-2 form-col-add">Photos</div>

								    <div class="form col-md-10 form-col-add">

									<input name="action_media[<?php echo $i;?>][0][]" type="file" class="custom-file-upload  action-addition " value="<?php echo $action['photo']; ?>" multiple>

								    </div>

								    <div class="form-col-2 form-col-add">

									<button class="btn-control addition-plus" data-id="<?php echo $i; ?>"  type='button' data-cnt="<?php echo $n; ?>" ><img src="images/plus-icon.png" alt="Plus"></button>

								    </div>

								</div>

							    </div>

							<?php } ?>

													<input type="hidden" name="action_count" class="action_count" value="<?php echo $n; ?>">

						    </li>
							
							<li>
								<label class="main-label col-sm-3"><?php echo 'Amount'; ?></label>
								<div class="row">
									<div class="form-col-6">
									<input type="text" name="amount" value="" placeholder="Amount" class="input-control hr_input" />
									</div>
								</div>
							</li>


						</ul>

						    <input type="hidden" name="number_sequence" class="number_sequence" value="<?php echo $i; ?>">

						</li>

						<?php

						$i++;

					    }

					    ?>

					    <input type="hidden" name="ajax_count_opendiv" id="ajax_count_opendiv" class="ajax_count_opendiv" value="<?php echo $i; ?>">

					    <input type="hidden" id="quarter" name="quarter" value="<?php echo $utilities_quarter_quarter; ?>" />

					    <input type="hidden" id="year" name="year" value="<?php echo $utilities_quarter_year; ?>" />

					</ul>

					</div>
					<div style="align-self: center;">

					    <div class="form-col-2" >

						<button class="btn btn-control add-new-ngo" type='button' id="plus_ngo">Add New NGO</button>

					    </div>



					</div>



				    </div>

				    <input type="hidden" name="count_opendiv" id="count_opendiv" value="<?php echo $i; ?>">



				    <input type="hidden" name="id" value="<?php echo $id; ?>" />

				    <div class="form-btn-outer">

					<button type="submit" name="submit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-submit'); ?></button>

				    </div>

				</div>

			    </form>

			</div>

			<div id="tab-2" data-tab-id="2">

			    <form id="savehrform" class="site-info-form" method="post" enctype="multipart/form-data">



				<div class="hr_div">



				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('human-rights'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('human-rights-desc'); ?> </strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('human-rights-field1'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="hr_no_of_hrs" name="hr_no_of_hrs"  class="input-control hr_input" value="<?php echo $csr_hr['hr_no_of_hrs']; ?>">

						    </div>

						    <div>

							<label class="input-label">hours</label>

						    </div>

						</div>

					    </li>

					    <li><br> </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('human-rights-field2'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="hr_no_of_employees" name="hr_no_of_employees"  class="input-control hr_input validate_percentage" value="<?php echo $csr_hr['hr_no_of_employees']; ?>">

						    </div>

						    <div>

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					</ul>

				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('non-discrimination'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('non-discrimination-desc'); ?></strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('non-discrimination-field1'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="nd_no_of_incidents_of_discrimination" name="nd_no_of_incidents_of_discrimination"  class="input-control hr_input" value="<?php echo $csr_hr['nd_no_of_incidents_of_discrimination']; ?>">

						    </div>



						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('non-discrimination-field2'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="nd_incident_reviewed_by_org" name="nd_incident_reviewed_by_org"  class="input-control hr_input" value="<?php echo $csr_hr['nd_incident_reviewed_by_org']; ?>">

						    </div>

						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('non-discrimination-field3'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="nd_remediation_plans_implemented" name="nd_remediation_plans_implemented"  class="input-control hr_input" value="<?php echo $csr_hr['nd_remediation_plans_implemented']; ?>">

						    </div>

						</div>

					    </li>

					</ul>

				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('labour-practices-and-decent-work'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('labour-practices-and-decent-work-desc'); ?></strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('age-group'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="lpd_hires_age_under_thirty" name="lpd_hires_age_under_thirty"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_hires_age_under_thirty']; ?>">

							<label class="input-label"><?php echo lang('under-30-years'); ?></label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="lpd_hires_age_between_thirty_to_fifty" name="lpd_hires_age_between_thirty_to_fifty"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_hires_age_between_thirty_to_fifty']; ?>">

							<label class="input-label"><?php echo lang('between-30to50-years'); ?></label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="lpd_hires_age_more_than_fifty" name="lpd_hires_age_more_than_fifty"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_hires_age_more_than_fifty']; ?>">

							<label class="input-label"><?php echo lang('more-than-50-years'); ?></label>

						    </div>



						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('gender'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="lpd_hires_gender_male" name="lpd_hires_gender_male"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_hires_gender_male']; ?>">

							<label class="input-label"><?php echo lang('male'); ?></label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="lpd_hires_gender_female" name="lpd_hires_gender_female"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_hires_gender_female']; ?>">

							<label class="input-label"><?php echo lang('female'); ?></label>

						    </div>

						</div>

					    </li>



					</ul>

					<strong><?php echo lang('labour-practices-and-decent-work-desc2'); ?></strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('age-group'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="lpd_turnover_age_under_thirty" name="lpd_turnover_age_under_thirty"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_turnover_age_under_thirty']; ?>">

							<label class="input-label"><?php echo lang('under-30-years'); ?></label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="lpd_turnover_age_between_thirty_to_fifty" name="lpd_turnover_age_between_thirty_to_fifty"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_turnover_age_between_thirty_to_fifty']; ?>">

							<label class="input-label"><?php echo lang('between-30to50-years'); ?></label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="lpd_turnover_age_more_than_fifty" name="lpd_turnover_age_more_than_fifty"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_turnover_age_more_than_fifty']; ?>">

							<label class="input-label"><?php echo lang('more-than-50-years'); ?></label>

						    </div>



						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('gender'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="lpd_turnover_gender_male" name="lpd_turnover_gender_male"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_turnover_gender_male']; ?>">

							<label class="input-label"><?php echo lang('male'); ?></label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="lpd_turnover_gender_female" name="lpd_turnover_gender_female"  class="input-control hr_input" value="<?php echo $csr_hr['lpd_turnover_gender_female']; ?>">

							<label class="input-label"><?php echo lang('female'); ?></label>

						    </div>

						</div>

					    </li>



					</ul>

				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('occupational-health-and-safety'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('occupational-health-and-safety-desc'); ?></strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('rate-of-occupational-diseases'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ohs_rate_of_occupational_diseases" name="ohs_rate_of_occupational_diseases"  class="input-control hr_input" value="<?php echo $csr_hr['ohs_rate_of_occupational_diseases']; ?>">

						    </div>

						    <div>

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('lost-day-rates'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ohs_lost_day_rates" name="ohs_lost_day_rates"  class="input-control hr_input" value="<?php echo $csr_hr['ohs_lost_day_rates']; ?>">

						    </div>

						    <div>

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('absentee-rate'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ohs_absentee_rate" name="ohs_absentee_rate"  class="input-control hr_input" value="<?php echo $csr_hr['ohs_absentee_rate']; ?>">

						    </div>

						    <div>

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('gender'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ohs_gender_male" name="ohs_gender_male"  class="input-control hr_input" value="<?php echo $csr_hr['ohs_gender_male']; ?>">

							<label class="input-label"><?php echo lang('male'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="ohs_gender_female" name="ohs_gender_female"  class="input-control hr_input" value="<?php echo $csr_hr['ohs_gender_female']; ?>">

							<label class="input-label"><?php echo lang('female'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					</ul>



				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('training-and-education'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('training-and-education-desc'); ?></strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('gender'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="te_gender_male" name="te_gender_male"  class="input-control hr_input" value="<?php echo $csr_hr['te_gender_male']; ?>">

							<label class="input-label"><?php echo lang('male'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="te_gender_female" name="te_gender_female"  class="input-control hr_input" value="<?php echo $csr_hr['te_gender_female']; ?>">

							<label class="input-label"><?php echo lang('female'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						</div>

					    </li>



					    <li>

						<label class="main-label col-sm-3"><?php echo lang('employee-category'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="te_team_member" name="te_team_member"  class="input-control hr_input" value="<?php echo $csr_hr['te_team_member']; ?>">

							<label class="input-label"><?php echo lang('team-member'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="te_supervisor" name="te_supervisor"  class="input-control hr_input" value="<?php echo $csr_hr['te_supervisor']; ?>">

							<label class="input-label"><?php echo lang('supervisor'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						</div>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="te_manager" name="te_manager"  class="input-control hr_input" value="<?php echo $csr_hr['te_manager']; ?>">

							<label class="input-label"><?php echo lang('manager'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="te_head_of_department" name="te_head_of_department"  class="input-control hr_input" value="<?php echo $csr_hr['te_head_of_department']; ?>">

							<label class="input-label"><?php echo lang('head-of-department'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						</div>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="te_assistant_head_of_department" name="te_assistant_head_of_department"  class="input-control hr_input" value="<?php echo $csr_hr['te_assistant_head_of_department']; ?>">

							<label class="input-label"><?php echo lang('assistant-head-of-department'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="te_general_manager" name="te_general_manager"  class="input-control hr_input" value="<?php echo $csr_hr['te_general_manager']; ?>">

							<label class="input-label"><?php echo lang('general-manager'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						</div>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="te_senior_manager" name="te_senior_manager"  class="input-control hr_input" value="<?php echo $csr_hr['te_senior_manager']; ?>">

							<label class="input-label"><?php echo lang('senior-manager'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">hours</label>

						    </div>

						</div>

					    </li>

					</ul>

				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('training-and-education'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('training-and-education-desc2'); ?></strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('gender'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="tae_gender_male" name="tae_gender_male"  class="input-control hr_input" value="<?php echo $csr_hr['tae_gender_male']; ?>">

							<label class="input-label"><?php echo lang('male'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="tae_gender_female" name="tae_gender_female"  class="input-control hr_input" value="<?php echo $csr_hr['tae_gender_female']; ?>">

							<label class="input-label"><?php echo lang('female'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>



					    <li>

						<label class="main-label col-sm-3"><?php echo lang('employee-category'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="tae_team_member" name="tae_team_member"  class="input-control hr_input" value="<?php echo $csr_hr['tae_team_member']; ?>">

							<label class="input-label"><?php echo lang('team-member'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="tae_supervisor" name="tae_supervisor"  class="input-control hr_input" value="<?php echo $csr_hr['tae_supervisor']; ?>">

							<label class="input-label"><?php echo lang('supervisor'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="tae_manager" name="tae_manager"  class="input-control hr_input" value="<?php echo $csr_hr['tae_manager']; ?>">

							<label class="input-label"><?php echo lang('manager'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="tae_head_of_department" name="tae_head_of_department"  class="input-control hr_input" value="<?php echo $csr_hr['tae_head_of_department']; ?>">

							<label class="input-label"><?php echo lang('head-of-department'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="tae_assistant_head_of_department" name="tae_assistant_head_of_department"  class="input-control hr_input" value="<?php echo $csr_hr['tae_assistant_head_of_department']; ?>">

							<label class="input-label"><?php echo lang('assistant-head-of-department'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="tae_general_manager" name="tae_general_manager"  class="input-control hr_input" value="<?php echo $csr_hr['tae_general_manager']; ?>">

							<label class="input-label"><?php echo lang('general-manager'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="tae_senior_manager" name="tae_senior_manager"  class="input-control hr_input" value="<?php echo $csr_hr['tae_senior_manager']; ?>">

							<label class="input-label"><?php echo lang('senior-manager'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					</ul>

				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('diversity-and-opportunity'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('diversity-and-opportunity-desc'); ?></strong>

					<ul class="form-outer-block">

					    <li>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="diversity_and_opportunity" name="diversity_and_opportunity"  class="input-control hr_input" value="<?php echo $csr_hr['diversity_and_opportunity']; ?>">

						    </div>

						</div>

					    </li>

					</ul>



				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('equal-remuneration-for-men-and-women'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('equal-remuneration-for-men-and-women-desc'); ?></strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('gender'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ermw_gender_male" name="ermw_gender_male"  class="input-control hr_input" value="<?php echo $csr_hr['ermw_gender_male']; ?>">

							<label class="input-label"><?php echo lang('male'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="ermw_gender_female" name="ermw_gender_female"  class="input-control hr_input" value="<?php echo $csr_hr['ermw_gender_female']; ?>">

							<label class="input-label"><?php echo lang('female'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('employee-category'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ermw_team_member" name="ermw_team_member"  class="input-control hr_input" value="<?php echo $csr_hr['ermw_team_member']; ?>">

							<label class="input-label"><?php echo lang('team-member'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="ermw_supervisor" name="ermw_supervisor"  class="input-control hr_input" value="<?php echo $csr_hr['ermw_supervisor']; ?>">

							<label class="input-label"><?php echo lang('supervisor'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ermw_manager" name="ermw_manager"  class="input-control hr_input" value="<?php echo $csr_hr['ermw_manager']; ?>">

							<label class="input-label"><?php echo lang('manager'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="ermw_head_of_department" name="ermw_head_of_department"  class="input-control hr_input" value="<?php echo $csr_hr['ermw_head_of_department']; ?>">

							<label class="input-label"><?php echo lang('head-of-department'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ermw_assistant_head_of_department" name="ermw_assistant_head_of_department"  class="input-control hr_input" value="<?php echo $csr_hr['ermw_assistant_head_of_department']; ?>">

							<label class="input-label"><?php echo lang('assistant-head-of-department'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <!-- <div class="form-col-3">

							<input type="text" id="total_electricity_kwh" name="total_electricity_kwh"  class="input-control hr_input" value="<?php //echo $csr_hr['total_electricity_kwh']; ?>">

							<label class="input-label"><?php //echo lang('general-manager'); ?></label>

						    </div>

						</div>

						<div class="row"> -->

						    <div class="form-col-3">

							<input type="text" id="ermw_senior_manager" name="ermw_senior_manager"  class="input-control hr_input" value="<?php echo $csr_hr['ermw_senior_manager']; ?>">

							<label class="input-label"><?php echo lang('senior-manager'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					</ul>

				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('economic'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('economic-desc'); ?> </strong>

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('gender'); ?></label>

						<div class="row">

						    <div class="form-col-3">

							<input type="text" id="ec_ratios_of_std_gender_male" name="ec_ratios_of_std_gender_male"  class="input-control hr_input" value="<?php echo $csr_hr['ec_ratios_of_std_gender_male']; ?>">

							<label class="input-label"><?php echo lang('male'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						    <div class="form-col-3">

							<input type="text" id="ec_ratios_of_std_gender_female" name="ec_ratios_of_std_gender_female"  class="input-control hr_input" value="<?php echo $csr_hr['ec_ratios_of_std_gender_female']; ?>">

							<label class="input-label"><?php echo lang('female'); ?></label>

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					</ul>

				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('economic'); ?></strong></div>

				    <div class="panel-body">

					<strong><?php echo lang('economic-desc2'); ?></strong>

					<label>&nbsp;</label>

					<ul class="form-outer-block">

					    <li>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="ec_proportion_of_senior_management_hired" name="ec_proportion_of_senior_management_hired"  class="input-control hr_input" value="<?php echo $csr_hr['ec_proportion_of_senior_management_hired']; ?>">

						    </div>

						    <div class="form-col-1">

							<label class="input-label">%</label>

						    </div>

						</div>

					    </li>

					</ul>



				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('team-member-survey-results'); ?></strong></div>

				    <div class="panel-body">

					<ul class="form-outer-block">

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('global-index'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="tmsr_global_index" name="tmsr_global_index"  class="input-control hr_input" value="<?php echo $csr_hr['tmsr_global_index']; ?>">

						    </div>



						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('leadership-index'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="tmsr_leadership_index" name="tmsr_leadership_index"  class="input-control hr_input" value="<?php echo $csr_hr['tmsr_leadership_index']; ?>">

						    </div>

						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('loyalty-index'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="tmsr_loyalty_index" name="tmsr_loyalty_index"  class="input-control hr_input" value="<?php echo $csr_hr['tmsr_loyalty_index']; ?>">

						    </div>

						</div>

					    </li>

					    <li>

						<label class="main-label col-sm-3"><?php echo lang('any-other'); ?></label>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="tmsr_other_index" name="tmsr_other_index"  class="input-control hr_input" value="<?php echo $csr_hr['tmsr_other_index']; ?>">

						    </div>

						</div>

					    </li>

					</ul>

				    </div>

				</div>

				<div class="panel panel-primary">

				    <div class="panel-heading"><strong><?php echo lang('talent-management'); ?></strong></div>

				    <div class="panel-body">

					<ul class="form-outer-block">

					    <li>

						<div class="row">

						    <div class="form-col-6">

							<input type="text" id="talent_management" name="talent_management"  class="input-control hr_input" value="<?php echo $csr_hr['talent_management']; ?>">

						    </div>

						</div>

					    </li>

					</ul>



				    </div>

				</div>



				<div class="form-btn-outer">

				    <input type="hidden" id="hr_id" name="hr_id" value="<?php echo $csr_hr['id']; ?>" />

				    <input type="hidden" id="hr_quarter" name="hr_quarter" value="<?php echo $utilities_quarter_quarter; ?>" />

				    <input type="hidden" id="hr_year" name="hr_year" value="<?php echo $utilities_quarter_year; ?>" />

				    <button type="submit" name="submit" value="2" class="btn btn-secondary btn-submit"><?php echo lang('btn-submit'); ?></button>

				</div>

				</div>

			    </form>

			</div>

			<div id="tab-3" data-tab-id="3">

				<form id="save_waste_form" class="site-info-form" method="post" enctype="multipart/form-data">

					<?php

						$image_path = site_url() . "/assets/uploads/no-image-available.jpg";

						$waste_site_id = $csr_waste['site_id'];

						$path = site_url() . "/assets/uploads/site_".$waste_site_id."/waste_invoices/";

					?>

					<div class="waste_class">

						<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('pete'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pete_waste_kg" name="pete_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pete_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pete_cost_of_waste_removal_per_kg" name="pete_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pete_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="pete_waste_invoice_scan" id="pete_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['pete_waste_invoice_scan'] != '') ? $csr_waste['pete_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="pete_waste_invoice_scan">×</a>

									<a href="<?php echo $image_path; ?>" target="_blank" >

										    <img class="waste_image pete_waste_invoice_scan" src="<?php echo $image_path; ?>">

										    </a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pete_qty_recycled_kg" name="pete_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pete_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pete_revenue_from_recycling_per_kg" name="pete_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pete_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="pete_recycled_invoice_scan" id="pete_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['pete_recycled_invoice_scan'] != '') ? $csr_waste['pete_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="pete_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image pete_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



					<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('hdpe'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="hdpe_waste_kg" name="hdpe_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['hdpe_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="hdpe_cost_of_waste_removal_per_kg" name="hdpe_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['hdpe_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="hdpe_waste_invoice_scan" id="hdpe_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['hdpe_waste_invoice_scan'] != '') ? $csr_waste['hdpe_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="hdpe_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image hdpe_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="hdpe_qty_recycled_kg" name="hdpe_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['hdpe_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="hdpe_revenue_from_recycling_per_kg" name="hdpe_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['hdpe_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="hdpe_recycled_invoice_scan" id="hdpe_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['hdpe_recycled_invoice_scan'] != '') ? $csr_waste['hdpe_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="hdpe_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image hdpe_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>



										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



					<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('pvc'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pvc_waste_kg" name="pvc_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pvc_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pvc_cost_of_waste_removal_per_kg" name="pvc_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pvc_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="pvc_waste_invoice_scan" id="pvc_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['pvc_waste_invoice_scan'] != '') ? $csr_waste['pvc_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="pvc_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image pvc_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pvc_qty_recycled_kg" name="pvc_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pvc_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pvc_revenue_from_recycling_per_kg" name="pvc_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pvc_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="pvc_recycled_invoice_scan" id="pvc_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['pvc_recycled_invoice_scan'] != '') ? $csr_waste['pvc_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="pvc_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image pvc_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



					<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('ldpe'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="ldpe_waste_kg" name="ldpe_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['ldpe_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="ldpe_cost_of_waste_removal_per_kg" name="ldpe_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['ldpe_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="ldpe_waste_invoice_scan" id="ldpe_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['ldpe_waste_invoice_scan'] != '') ? $csr_waste['ldpe_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="ldpe_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image ldpe_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="ldpe_qty_recycled_kg" name="ldpe_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['ldpe_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="ldpe_revenue_from_recycling_per_kg" name="ldpe_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['ldpe_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="ldpe_recycled_invoice_scan" id="ldpe_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['ldpe_recycled_invoice_scan'] != '') ? $csr_waste['ldpe_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="ldpe_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image ldpe_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



					<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('pp'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pp_waste_kg" name="pp_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pp_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pp_cost_of_waste_removal_per_kg" name="pp_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pp_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="pp_waste_invoice_scan" id="pp_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['pp_waste_invoice_scan'] != '') ? $csr_waste['pp_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="pp_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image pp_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pp_qty_recycled_kg" name="pp_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pp_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="pp_revenue_from_recycling_per_kg" name="pp_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['pp_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="pp_recycled_invoice_scan" id="pp_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['pp_recycled_invoice_scan'] != '') ? $csr_waste['pp_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="pp_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image pp_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('ps'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="ps_waste_kg" name="ps_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['ps_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="ps_cost_of_waste_removal_per_kg" name="ps_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['ps_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="ps_waste_invoice_scan" id="ps_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['ps_waste_invoice_scan'] != '') ? $csr_waste['ps_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="ps_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image ps_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="ps_qty_recycled_kg" name="ps_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['ps_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="ps_revenue_from_recycling_per_kg" name="ps_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['ps_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="ps_recycled_invoice_scan" id="ps_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['ps_recycled_invoice_scan'] != '') ? $csr_waste['ps_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="ps_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image ps_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('op'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="op_waste_kg" name="op_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['op_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="op_cost_of_waste_removal_per_kg" name="op_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['op_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="op_waste_invoice_scan" id="op_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['op_waste_invoice_scan'] != '') ? $csr_waste['op_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="op_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image op_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="op_qty_recycled_kg" name="op_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['op_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="op_revenue_from_recycling_per_kg" name="op_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['op_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="op_recycled_invoice_scan" id="op_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['op_recycled_invoice_scan'] != '') ? $csr_waste['op_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="op_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image op_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('fw'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="fw_waste_kg" name="fw_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['fw_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="fw_cost_of_waste_removal_per_kg" name="fw_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['fw_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="fw_waste_invoice_scan" id="fw_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['fw_waste_invoice_scan'] != '') ? $csr_waste['fw_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="fw_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image fw_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="fw_qty_recycled_kg" name="fw_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['fw_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="fw_revenue_from_recycling_per_kg" name="fw_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['fw_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="fw_recycled_invoice_scan" id="fw_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['fw_recycled_invoice_scan'] != '') ? $csr_waste['fw_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="fw_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image fw_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('glass'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="glass_waste_kg" name="glass_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['glass_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="glass_cost_of_waste_removal_per_kg" name="glass_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['glass_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="glass_waste_invoice_scan" id="glass_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['glass_waste_invoice_scan'] != '') ? $csr_waste['glass_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="glass_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image glass_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="glass_qty_recycled_kg" name="glass_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['glass_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="glass_revenue_from_recycling_per_kg" name="glass_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['glass_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="glass_recycled_invoice_scan" id="glass_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['glass_recycled_invoice_scan'] != '') ? $csr_waste['glass_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="glass_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image glass_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('wh'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wh_waste_kg" name="wh_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wh_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wh_cost_of_waste_removal_per_kg" name="wh_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wh_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wh_waste_invoice_scan" id="wh_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wh_waste_invoice_scan'] != '') ? $csr_waste['wh_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wh_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wh_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wh_qty_recycled_kg" name="wh_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wh_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wh_revenue_from_recycling_per_kg" name="wh_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wh_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wh_recycled_invoice_scan" id="wh_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wh_recycled_invoice_scan'] != '') ? $csr_waste['wh_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wh_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wh_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('wg'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wg_waste_kg" name="wg_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wg_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wg_cost_of_waste_removal_per_kg" name="wg_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wg_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wg_waste_invoice_scan" id="wg_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wg_waste_invoice_scan'] != '') ? $csr_waste['wg_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wg_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wg_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wg_qty_recycled_kg" name="wg_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wg_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wg_revenue_from_recycling_per_kg" name="wg_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wg_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wg_recycled_invoice_scan" id="wg_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wg_recycled_invoice_scan'] != '') ? $csr_waste['wg_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wg_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wg_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('wuko'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wuko_waste_kg" name="wuko_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wuko_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wuko_cost_of_waste_removal_per_kg" name="wuko_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wuko_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wuko_waste_invoice_scan" id="wuko_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wuko_waste_invoice_scan'] != '') ? $csr_waste['wuko_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wuko_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wuko_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wuko_qty_recycled_kg" name="wuko_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wuko_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wuko_revenue_from_recycling_per_kg" name="wuko_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wuko_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wuko_recycled_invoice_scan" id="wuko_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wuko_recycled_invoice_scan'] != '') ? $csr_waste['wuko_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wuko_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wuko_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('wp'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wp_waste_kg" name="wp_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wp_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wp_cost_of_waste_removal_per_kg" name="wp_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wp_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wp_waste_invoice_scan" id="wp_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wp_waste_invoice_scan'] != '') ? $csr_waste['wp_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wp_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wp_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wp_qty_recycled_kg" name="wp_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wp_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wp_revenue_from_recycling_per_kg" name="wp_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wp_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wp_recycled_invoice_scan" id="wp_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wp_recycled_invoice_scan'] != '') ? $csr_waste['wp_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wp_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wp_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('wc'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wc_waste_kg" name="wc_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wc_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wc_cost_of_waste_removal_per_kg" name="wc_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wc_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wc_waste_invoice_scan" id="wc_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wc_waste_invoice_scan'] != '') ? $csr_waste['wc_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wc_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wc_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wc_qty_recycled_kg" name="wc_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wc_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="wc_revenue_from_recycling_per_kg" name="wc_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['wc_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="wc_recycled_invoice_scan" id="wc_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['wc_recycled_invoice_scan'] != '') ? $csr_waste['wc_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="wc_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image wc_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('gw'); ?></strong></div>

					    <div class="panel-body">

						<ul class="form-outer-block">

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('waste-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="gw_waste_kg" name="gw_waste_kg"  class="input-control hr_input" value="<?php echo $csr_waste['gw_waste_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('cost-of-waste-removal-per-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="gw_cost_of_waste_removal_per_kg" name="gw_cost_of_waste_removal_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['gw_cost_of_waste_removal_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="gw_waste_invoice_scan" id="gw_waste_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['gw_waste_invoice_scan'] != '') ? $csr_waste['gw_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="gw_waste_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image gw_waste_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						    <li> <label class="main-label col-sm-3 budgetLabel" ><?php echo lang('recycling'); ?></label> <br></li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3"><?php echo lang('qty-recycled-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="gw_qty_recycled_kg" name="gw_qty_recycled_kg"  class="input-control hr_input" value="<?php echo $csr_waste['gw_qty_recycled_kg']; ?>">

							    </div>



								<label class="main-label rightLabel col-sm-3"><?php echo lang('revenue-from-recycling-kg'); ?></label>

							    <div class="form-col-3">

								<input type="text" id="gw_revenue_from_recycling_per_kg" name="gw_revenue_from_recycling_per_kg"  class="input-control hr_input" value="<?php echo $csr_waste['gw_revenue_from_recycling_per_kg']; ?>">

							    </div>

							</div>

						    </li>

						    <li>

							<div class="row">

								<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>

								<div class="form-col-6">

									<input name="gw_recycled_invoice_scan" id="gw_recycled_invoice_scan" type="file" class="custom-file-upload form ">

								</div>

								<?php

								$image_path = ($csr_waste['gw_recycled_invoice_scan'] != '') ? $csr_waste['gw_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

								?>

								<div class="form-col-2 waste_image_div ">

									<a class="close delete_waste_image" href="#" style="display: none;" data-feild="gw_recycled_invoice_scan">×</a>

										    <a href="<?php echo $image_path; ?>" target="_blank" ><img class="waste_image gw_recycled_invoice_scan" src="<?php echo $image_path; ?>"></a>

										</div>

							</div>

						    </li>

						</ul>

					    </div>

					</div>



									<div class="form-btn-outer">

					    <input type="hidden" id="waste_id" name="waste_id" value="<?php echo $csr_waste['id']; ?>" />

					    <input type="hidden" id="waste_quarter" name="waste_quarter" value="<?php echo $utilities_quarter_quarter; ?>" />

					    <input type="hidden" id="waste_year" name="waste_year" value="<?php echo $utilities_quarter_year; ?>" />

					    <button type="submit" name="submit" value="3" class="btn btn-secondary btn-submit"><?php echo lang('btn-submit'); ?></button>

					</div>



					</div>

				</form>

			</div>

			<div id="tab-4" data-tab-id="4">

				<form id="save_biodiversity_form" class="site-info-form" method="post" enctype="multipart/form-data">

					<div class="biodiversity_main_div" id="biodiversity_main_div">

						<div class="panel panel-primary">

					    <div class="panel-heading"><strong><?php echo lang('tab-biodiversity'); ?></strong></div>

					    <div class="panel-body">

						<?php

						$b = 0;

						$bn = 0;

						$id = '';   ?>



						<div class="biodiversity_inner_div">

							<?php

							if(!empty($csr_biodiversity)){



								$b = count($csr_biodiversity);

								foreach ($csr_biodiversity as $key => $csr_bio) {



									$bio_id = $csr_bio['id'];

									?>

											<div class="biodiversity_div add-row" id="biodiversity_div" style="border: 1px solid #ccc; padding: 10px;">



										<div class='row'>

											<div class='form-col-1 form-col-add'>Measure </div>

															    <div class='form col-sm-4 form-col-add'><input type='text' class='input-control' name="measure[<?php echo $bn; ?>]" value="<?php echo $csr_bio['measure']; ?>" ></div>

															    <label class="main-label rightLabel col-sm-1">Partners</label>

															    <div class='form col-sm-4 form-col-add'>

																  <input  type='text' class='input-control ' name="partner[<?php echo $bn; ?>]" value="<?php echo $csr_bio['partner']; ?>" >

															    </div>

															</div>



															<div class="row add-row">

														    <div class="form col-sm-1 form-col-add">Photos</div>

														</div>

															<div class="row">

																<?php

																	$site_id = $csr_bio['site_id'];

														    $media_files = $csr_bio['media'];



														    if (!empty($media_files)) {

														    ?>

														    <ul class="thumbnails">

														    <?php



															foreach ($media_files as $key1 => $media)

															{

															    $bio_id = $media['biodiversity_id'];

															    $image  = $media['image'];



															    $path = site_url() . "/assets/uploads/site_".$site_id."/biodiversity/".$bio_id."/".$image;

															    $k    = strrpos($image, ".");

															    if (!$k) {  $ext = ''; }



															    $l   = strlen($image) - $k;

															    $ext = substr($image, $k + 1, $l);



															    if(($ext == 'png') || ($ext == 'jpg') || ($ext == 'jpeg') || ($ext == 'gif'))

															    {   ?>

																<div class="form-col-1 form-col-add " style="width: 100px; height: 120px; border: 2px solid #dbdbdb !important;margin: 10px;">



																    <a class="close delete_media_bio" href="#" data-id="<?php echo $media['id']; ?>">×</a>

																    <img src="<?php echo $path; ?>" style="width: 80px; height: auto;" >

																</div>

																<?php

															    }

															}

														    } ?>

														    </ul>

													    </div>



													    <div class="row add-row">

														<div class="form-col-1 form-col-add">Videos</div>

													    </div>

													    <div class="row add-row">

														<ul class="thumbnails">

														<?php

														$site_id = $csr_bio['site_id'];

														$media_files = $csr_bio['media'];

														if (!empty($media_files)) {

														    foreach ($media_files as $key1 => $media) {



															$bio_id = $media['biodiversity_id'];

															$image  = $media['image'];



															$path = site_url() . "assets/uploads/site_".$site_id."/biodiversity/".$bio_id."/".$image;

															$k    = strrpos($image, ".");

															if (!$k) {  $ext = ''; }



															$l   = strlen($image) - $k;

															$ext = substr($image, $k + 1, $l);



															if(($ext == 'mp3') || ($ext == 'mp4') || ($ext == 'wma'))

															{  ?>

															    <div class="form-col-6 form-col-add " style=" border: 2px solid #dbdbdb !important;margin: 10px;">



																 <a target="_blank" href="<?php echo $path; ?>" >Click here to play video

																    </a>

																<a href="#" class="close delete_media_bio" data-id="<?php echo $media['id']; ?>">× </a>

															    </div>

														    <?php

															}

														    }

														} ?>

														</ul>

													    </div>



															<div class='row '>

																<div class='form-col-2 form-col-add'>Photos / Videos</div>

																<div class='form col-md-10 form-col-add'><input type='file' class='custom-file-upload file_upload bio_media'  name="bio_media[<?php echo $bn; ?>][]" multiple>

																<input name='bio_id[<?php echo $bn; ?>]' type='hidden' class="bio_id" value="<?php echo $csr_bio['id']; ?>" >

																</div>

																<?php

																if($bn == 0)

																{ ?>

																	<div class='form-col-1'>

																		<button type='button' class='btn-control add-biodiversity' data-id="<?php echo $b; ?>" ><img src="images/plus-icon.png" alt="Plus"></button>

																	</div>

																	<div class="form-col-1">

									    <button type='button' class="btn-control substract_minus_biodiversity top_right_delete_button" data-id="<?php echo $csr_bio['id']; ?>"><img alt="Minus" src="images/minus-icon.png"></button>

									</div>

																	<?php

								    } else { ?>

									<div class='form-col-1' style="display: none;">

																		<button type='button' class='btn-control add-biodiversity' data-id="<?php echo $b; ?>" ><img src="images/plus-icon.png" alt="Plus"></button>

																	</div>

									<div class="form-col-1">

									    <button type='button' class="btn-control substract_minus_biodiversity top_right_delete_button" data-id="<?php echo $csr_bio['id']; ?>"><img alt="Minus" src="images/minus-icon.png"></button>

									</div>

								    <?php } ?>

															</div>

														</div>

									<?php

									$bn++;

								}

							}

							else

							{

							?>

							<?php

							// $b = 1;

							// $bn = 0;

							$id = ''; ?>

							<div class="biodiversity_div add-row" id="biodiversity_div" style="border: 1px solid #ccc; padding: 10px;">

								<div class='row '>

									<div class='form-col-1 form-col-add'>Measure </div>

													    <div class='form col-sm-4 form-col-add'><input type='text' class='input-control ' name="measure[<?php echo $bn; ?>]"></div>

													    <label class="main-label rightLabel col-sm-1">Partners</label>

													    <div class='form col-sm-4 form-col-add'>

														  <input  type='text' class='input-control ' name="partner[<?php echo $bn; ?>]">

													    </div>

													</div>

													<div class='row '>

														<div class='form-col-2 form-col-add'>Photos / Videos</div>

														<div class='form col-md-10 form-col-add'><input type='file' class='custom-file-upload file_upload bio_media'  name="bio_media[<?php echo $bn; ?>][]" multiple>

														<input name='bio_id[<?php echo $bn; ?>]'  type='hidden' class="bio_id"/>

														</div>

														<div class='form-col-1'>

															<button type='button' class='btn-control add-biodiversity' data-id="<?php echo $b; ?>" ><img src="images/plus-icon.png" alt="Plus"></button>

														</div>

													</div>

												</div>

												<?php

							} ?>



					    </div>

						    <div class="form-btn-outer">

							<input type="hidden" name="bio_number_sequence" class="bio_number_sequence" value="<?php echo $b; ?>">

												<input type="hidden" name="bio_count" class="bio_count" value="<?php echo $b; ?>">



												<input type="hidden" name="id" value="<?php echo $id; ?>" />

												<input type="hidden" id="biodiversity_quarter" name="biodiversity_quarter" value="<?php echo $utilities_quarter_quarter; ?>" />

						    <input type="hidden" id="biodiversity_year" name="biodiversity_year" value="<?php echo $utilities_quarter_year; ?>" />

							<button type="submit" name="submit" value="4" class="btn btn-secondary btn-submit"><?php echo lang('btn-submit'); ?></button>

						    </div>



						</div>

					    </div>

					</div>



				</form>

			</div>

		    </div>

		</div>



		<!-- <input type="hidden" id="cur_year" name="cur_year" value="<?php echo $utilities_year; ?>" /> -->



			<div class="ngo_data_div_outer ngo_main_section" style="display: none;"  >

				<li class="ngo_block_li" id="ngo_block" name="ngo_block[]" style="border: 2px solid #dbdbdb !important; padding: 10px;">

					<ul class="form-outer-block ng">

						<li>

							<div class="row">

								<div class="form-col-1" style="float: right;margin-right: 30px;"><button type="button" class="btn btn-default close_ngo" data-id="" data-j =""  >Delete</button> </div>

							</div>

						</li>



						<li>

							<label class="main-label col-sm-3"><?php echo lang('affiliate-ngo'); ?></label>

							<div class="row">

								<div class="form-col-6">

									<input type="text" id="ngo_name" class="input-control ngo_name">

									<input type="hidden" id="ngo_id" class="input-control ngo_id">



								</div>

							</div>

						</li>



						<li>

							<label class="main-label col-sm-3"><?php echo lang('action'); ?></label>

							<div class="action_row add-row" style="border: 1px solid #ccc; padding: 10px; ">

									<div class="row add-row">

										<div class="form col-sm-2 form-col-add">Description</div>

										<div class="form col-sm-6 form-col-add">

											<input name="action_text[][]" type="text" class="input-control action-text-addition inner_action_text">

										</div>

									<!-- </div>



									<div class="row add-row"> -->

										<div class="form col-sm-1 form-col-add">SDG</div>

										<div class="form col-sm-2 form-col-add">

											<input name="action_sdg[][]" type="text" class="input-control action-sdg-addition inner_action_sdg">

										</div>

									</div>



									<div class="row add-row">

										<div class="form-col-2 form-col-add">Photos/Videos</div>



										<div class="form col-md-10 form-col-add">

											<input name="action_media[][][]" type="hidden" class="custom-file-upload action-addition file_upload inner_action_media" multiple>



											<input name="action_id[][]"  type="hidden" class="action_id"/>

										</div>

										<div class="form-col-1 form-col-add">

											<button class="btn-control addition-plus" data-id="" type='button' >

											<img src="images/plus-icon.png" alt="Plus"></button>

									</div>

								</div>

							</div>



						</li>

					</ul>

					<input type="hidden" name="number_sequence" class="number_sequence" value="">

					<input type="hidden" name="action_count" class="action_count" value="">

				 </li>

			</div>



			<div class="ngo_data_div_outer inner_action_row" style="display: none;"  >

				<div class='action_row add-row ' style='border: 1px solid #ccc; padding: 10px;'>

					<div class='row add-row'>

					   <div class='form col-sm-2 form-col-add'>Description</div>

					   <div class='form col-sm-6 form-col-add'><input type='text' class='input-control action-text-addition inner_action_text'></div>

					<!-- </div>

					<div class='row add-row'> -->

					   <div class='form col-sm-1 form-col-add'>SDG</div>

					   <div class='form col-sm-2 form-col-add'>

						  <input  type='text' class='input-control inner_action_sdg'>

					   </div>

					</div>

					<div class='row add-row'>

						<div class='form-col-2 form-col-add'>Photos</div>

						<div class='form col-md-10 form-col-add'><input type='hidden' class='custom-file-upload action-addition inner_action_media file_upload'  multiple><input name='action_id[][]'  type='hidden' class="action_id"/>

						</div>

						<div class='form-col-1'>

							<button type='button' class='btn-control substract-minus'><img src='images/minus-icon.png' alt='Minus'></button>

						</div>

					</div>

				</div>

			</div>



			<div class="new_biodiversity_div_outer" style="display: none;"  >

				<div class="biodiversity_div add-row" style="border: 1px solid #ccc; padding: 10px;">

			<div class='row bio_row'>

				<div class='form-col-1 form-col-add'>Measure </div>

					    <div class='form col-sm-4 form-col-add'><input type='text' class='input-control inner_measure'></div>

					    <label class="main-label rightLabel col-sm-1">Partners</label>

					    <div class='form col-sm-4 form-col-add'>

						  <input  type='text' class='input-control inner_partner'>

					    </div>

					</div>

					<div class='row bio_row'>

						<div class='form-col-2 form-col-add'>Photos / Videos</div>

						<div class='form col-md-10 form-col-add'>

							<input type='file' class='custom-file-upload file_upload inner_bio_media bio_media' multiple>

						<input name='bio_id[][]'  type='hidden' class="bio_id inner_bio_id"/>

						</div>

						<div class='form-col-1'>

							<button type='button' class='btn-control substract-minus'><img src='images/minus-icon.png' alt='Minus'></button>



						</div>

					</div>

				</div>

			</div>



	</div>

    </article>

</div>





<?php $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()); ?>



<script type="text/javascript">

     $(document).ready(function () {



	$("select[data-type='custom-dropdown-maximum-demand']").dropkick({

	    mobile: true

	});



	// delete_waste_image

	$('.waste_image').each(function() {



		var isrc = $(this).attr('src');

		if(isrc.includes("no-image-available.jpg"))

		{

			$(this).parents('.waste_image_div').find('.delete_waste_image').hide();

			$(this).parents('.waste_image_div').css('height', '100px');

		}

		else

		{

			$(this).parents('.waste_image_div').find('.delete_waste_image').show();

		}

	});



	$('.Tab-block').easyResponsiveTabs({

	    type: 'default',

	    width: 'auto',

	    fit: true,

	    tabidentify: 'hor_1',

	    activate: function (event) {

		// If need on tab change

	    }

	});



	$.fn.datepicker.dates['qtrs'] = {

	  days: ["Sunday", "Moonday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],

	  daysShort: ["Sun", "Moon", "Tue", "Wed", "Thu", "Fri", "Sat"],

	  daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],

	  months: ["Q1", "Q2", "Q3", "Q4", "", "", "", "", "", "", "", ""],

	  monthsShort: ["Jan&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Feb&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mar <br> ", "Apr&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;May&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jun <br> ", "Jul&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aug&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sep <br> ", "Oct&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nov&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dec", "", "", "", "", "", "", "", ""],

	  today: "Today",

	  clear: "Clear",

	  format: "mm/dd/yyyy",

	  titleFormat: "MM yyyy",



	  weekStart: 0

	};



	$('#quarter_picker').datepicker({

	  format: "MM yyyy",

	  minViewMode: 1,

	  autoclose: true,

	  language: "qtrs",

	  forceParse: false

	}).on("show", function(event) {

	    $(".month").each(function(index, element) {

		if (index > 3) $(element).hide();

	    });



	});



	/*$("#quarter_picker").click(function(e){



	    e.preventDefault();*/

	    $("#quarter_picker").change(function(){



		var $this = $(this);

		var quarter_picker = $this.val();

		var quarter = quarter_picker.split(' ');



		blockUI();

		$.ajax({

		    type: "POST",

		    async: false,

		    url: '<?php echo base_url() .BASE_ADMIN_URL_CUSTOM; ?>utilities/quarterly',

		    data: '<?php echo $querystr; ?>&quarter=' + quarter[0] + '&year=' + quarter[1]+ '&ajaxpost=1',

		    success: function(list){

			    var data = JSON.parse(list);

			    // for social tab

			    $('#main_div').empty();

			    $('#main_div').html(data.social);



			    // for hr tab

			    var hr = data.hr;

			    if(hr != null){

				$('#hr_id').val(hr.id);

				$('#hr_no_of_hrs').val(hr.hr_no_of_hrs);

				$('#hr_no_of_employees').val(hr.hr_no_of_employees);

				$('#nd_no_of_incidents_of_discrimination').val(hr.nd_no_of_incidents_of_discrimination);

				$('#nd_incident_reviewed_by_org').val(hr.nd_incident_reviewed_by_org);

				$('#nd_remediation_plans_implemented').val(hr.nd_remediation_plans_implemented);

				$('#lpd_hires_age_under_thirty').val(hr.lpd_hires_age_under_thirty);

				$('#lpd_hires_age_between_thirty_to_fifty').val(hr.lpd_hires_age_between_thirty_to_fifty);

				$('#lpd_hires_age_more_than_fifty').val(hr.lpd_hires_age_more_than_fifty);

				$('#lpd_hires_gender_male').val(hr.lpd_hires_gender_male);

				$('#lpd_hires_gender_female').val(hr.lpd_hires_gender_female);

				$('#lpd_turnover_age_under_thirty').val(hr.lpd_turnover_age_under_thirty);

				$('#lpd_turnover_age_between_thirty_to_fifty').val(hr.lpd_turnover_age_between_thirty_to_fifty);

				$('#lpd_turnover_age_more_than_fifty').val(hr.lpd_turnover_age_more_than_fifty);

				$('#lpd_turnover_gender_male').val(hr.lpd_turnover_gender_male);

				$('#lpd_turnover_gender_female').val(hr.lpd_turnover_gender_female);

				$('#ohs_rate_of_occupational_diseases').val(hr.ohs_rate_of_occupational_diseases);

				$('#ohs_lost_day_rates').val(hr.ohs_lost_day_rates);

				$('#ohs_absentee_rate').val(hr.ohs_absentee_rate);

				$('#ohs_gender_male').val(hr.ohs_gender_male);

				$('#ohs_gender_female').val(hr.ohs_gender_female);

				$('#te_gender_male').val(hr.te_gender_male);

				$('#te_gender_female').val(hr.te_gender_female);

				$('#te_team_member').val(hr.te_team_member);

				$('#te_supervisor').val(hr.te_supervisor);

				$('#te_manager').val(hr.te_manager);

				$('#te_head_of_department').val(hr.te_head_of_department);

				$('#te_assistant_head_of_department').val(hr.te_assistant_head_of_department);

				$('#te_general_manager').val(hr.te_general_manager);

				$('#te_senior_manager').val(hr.te_senior_manager);

				$('#tae_gender_male').val(hr.tae_gender_male);

				$('#tae_gender_female').val(hr.tae_gender_female);

				$('#tae_team_member').val(hr.tae_team_member);

				$('#tae_supervisor').val(hr.tae_supervisor);

				$('#tae_manager').val(hr.tae_manager);

				$('#tae_head_of_department').val(hr.tae_head_of_department);

				$('#tae_assistant_head_of_department').val(hr.tae_assistant_head_of_department);

				$('#tae_general_manager').val(hr.tae_general_manager);

				$('#tae_senior_manager').val(hr.tae_senior_manager);

				$('#diversity_and_opportunity').val(hr.diversity_and_opportunity);

				$('#ermw_gender_male').val(hr.ermw_gender_male);

				$('#ermw_gender_female').val(hr.ermw_gender_female);

				$('#ermw_team_member').val(hr.ermw_team_member);

				$('#ermw_supervisor').val(hr.ermw_supervisor);

				$('#ermw_manager').val(hr.ermw_manager);

				$('#ermw_head_of_department').val(hr.ermw_head_of_department);

				$('#ermw_assistant_head_of_department').val(hr.ermw_assistant_head_of_department);

				$('#ermw_senior_manager').val(hr.ermw_senior_manager);

				$('#ec_ratios_of_std_gender_male').val(hr.ec_ratios_of_std_gender_male);

				$('#ec_ratios_of_std_gender_female').val(hr.ec_ratios_of_std_gender_female);

				$('#ec_proportion_of_senior_management_hired').val(hr.ec_proportion_of_senior_management_hired);

				$('#tmsr_global_index').val(hr.tmsr_global_index);

				$('#tmsr_leadership_index').val(hr.tmsr_leadership_index);

				$('#tmsr_loyalty_index').val(hr.tmsr_loyalty_index);

				$('#tmsr_other_index').val(hr.tmsr_other_index);

				$('#talent_management').val(hr.talent_management);

			    }

			    else{

				$('#hr_id').val('');

				$('#hr_no_of_hrs').val('');

				$('#hr_no_of_employees').val('');

				$('#nd_no_of_incidents_of_discrimination').val('');

				$('#nd_incident_reviewed_by_org').val('');

				$('#nd_remediation_plans_implemented').val('');

				$('#lpd_hires_age_under_thirty').val('');

				$('#lpd_hires_age_between_thirty_to_fifty').val('');

				$('#lpd_hires_age_more_than_fifty').val('');

				$('#lpd_hires_gender_male').val('');

				$('#lpd_hires_gender_female').val('');

				$('#lpd_turnover_age_under_thirty').val('');

				$('#lpd_turnover_age_between_thirty_to_fifty').val('');

				$('#lpd_turnover_age_more_than_fifty').val('');

				$('#lpd_turnover_gender_male').val('');

				$('#lpd_turnover_gender_female').val('');

				$('#ohs_rate_of_occupational_diseases').val('');

				$('#ohs_lost_day_rates').val('');

				$('#ohs_absentee_rate').val('');

				$('#ohs_gender_male').val('');

				$('#ohs_gender_female').val('');

				$('#te_gender_male').val('');

				$('#te_gender_female').val('');

				$('#te_team_member').val('');

				$('#te_supervisor').val('');

				$('#te_manager').val('');

				$('#te_head_of_department').val('');

				$('#te_assistant_head_of_department').val('');

				$('#te_general_manager').val('');

				$('#te_senior_manager').val('');

				$('#tae_gender_male').val('');

				$('#tae_gender_female').val('');

				$('#tae_team_member').val('');

				$('#tae_supervisor').val('');

				$('#tae_manager').val('');

				$('#tae_head_of_department').val('');

				$('#tae_assistant_head_of_department').val('');

				$('#tae_general_manager').val('');

				$('#tae_senior_manager').val('');

				$('#diversity_and_opportunity').val('');

				$('#ermw_gender_male').val('');

				$('#ermw_gender_female').val('');

				$('#ermw_team_member').val('');

				$('#ermw_supervisor').val('');

				$('#ermw_manager').val('');

				$('#ermw_head_of_department').val('');

				$('#ermw_assistant_head_of_department').val('');

				$('#ermw_senior_manager').val('');

				$('#ec_ratios_of_std_gender_male').val('');

				$('#ec_ratios_of_std_gender_female').val('');

				$('#ec_proportion_of_senior_management_hired').val('');

				$('#tmsr_global_index').val('');

				$('#tmsr_leadership_index').val('');

				$('#tmsr_loyalty_index').val('');

				$('#tmsr_other_index').val('');

				$('#talent_management').val('');

			    }

			    $('#hr_quarter').val(quarter[0]);

			    $('#hr_year').val(quarter[1]);



			    // for waste tab

			    var waste = data.waste;

			    if(waste != null){

				$('#waste_id').val(waste.id);

				$('#pete_waste_kg').val(waste.pete_waste_kg);

				$('#pete_cost_of_waste_removal_per_kg').val(waste.pete_cost_of_waste_removal_per_kg);

				$('#pete_qty_recycled_kg').val(waste.pete_qty_recycled_kg);

				$('#pete_revenue_from_recycling_per_kg').val(waste.pete_revenue_from_recycling_per_kg);

				$('#hdpe_waste_kg').val(waste.hdpe_waste_kg);

				$('#hdpe_cost_of_waste_removal_per_kg').val(waste.hdpe_cost_of_waste_removal_per_kg);

				$('#hdpe_qty_recycled_kg').val(waste.hdpe_qty_recycled_kg);

				$('#hdpe_revenue_from_recycling_per_kg').val(waste.hdpe_revenue_from_recycling_per_kg);

				$('#pvc_waste_kg').val(waste.pvc_waste_kg);

				$('#pvc_cost_of_waste_removal_per_kg').val(waste.pvc_cost_of_waste_removal_per_kg);

				$('#pvc_qty_recycled_kg').val(waste.pvc_qty_recycled_kg);

				$('#pvc_revenue_from_recycling_per_kg').val(waste.pvc_revenue_from_recycling_per_kg);



				$('#ldpe_waste_kg').val(waste.ldpe_waste_kg);

				$('#ldpe_cost_of_waste_removal_per_kg').val(waste.ldpe_cost_of_waste_removal_per_kg);

				$('#ldpe_qty_recycled_kg').val(waste.ldpe_qty_recycled_kg);

				$('#ldpe_revenue_from_recycling_per_kg').val(waste.ldpe_revenue_from_recycling_per_kg);

				$('#pp_waste_kg').val(waste.pp_waste_kg);

				$('#pp_cost_of_waste_removal_per_kg').val(waste.pp_cost_of_waste_removal_per_kg);

				$('#pp_qty_recycled_kg').val(waste.pp_qty_recycled_kg);

				$('#pp_revenue_from_recycling_per_kg').val(waste.pp_revenue_from_recycling_per_kg);



				$('#ps_waste_kg').val(waste.ps_waste_kg);

				$('#ps_cost_of_waste_removal_per_kg').val(waste.ps_cost_of_waste_removal_per_kg);

				$('#ps_qty_recycled_kg').val(waste.ps_qty_recycled_kg);

				$('#ps_revenue_from_recycling_per_kg').val(waste.ps_revenue_from_recycling_per_kg);



				$('#op_waste_kg').val(waste.op_waste_kg);

				$('#op_cost_of_waste_removal_per_kg').val(waste.op_cost_of_waste_removal_per_kg);

				$('#op_qty_recycled_kg').val(waste.op_qty_recycled_kg);

				$('#op_revenue_from_recycling_per_kg').val(waste.op_revenue_from_recycling_per_kg);



				$('#fw_waste_kg').val(waste.fw_waste_kg);

				$('#fw_cost_of_waste_removal_per_kg').val(waste.fw_cost_of_waste_removal_per_kg);

				$('#fw_qty_recycled_kg').val(waste.fw_qty_recycled_kg);

				$('#fw_revenue_from_recycling_per_kg').val(waste.fw_revenue_from_recycling_per_kg);



				$('#glass_waste_kg').val(waste.glass_waste_kg);

				$('#glass_cost_of_waste_removal_per_kg').val(waste.glass_cost_of_waste_removal_per_kg);

				$('#glass_qty_recycled_kg').val(waste.glass_qty_recycled_kg);

				$('#glass_revenue_from_recycling_per_kg').val(waste.glass_revenue_from_recycling_per_kg);



				$('#wh_waste_kg').val(waste.wh_waste_kg);

				$('#wh_cost_of_waste_removal_per_kg').val(waste.wh_cost_of_waste_removal_per_kg);

				$('#wh_qty_recycled_kg').val(waste.wh_qty_recycled_kg);

				$('#wh_revenue_from_recycling_per_kg').val(waste.wh_revenue_from_recycling_per_kg);



				$('#wg_waste_kg').val(waste.wg_waste_kg);

				$('#wg_cost_of_waste_removal_per_kg').val(waste.wg_cost_of_waste_removal_per_kg);

				$('#wg_qty_recycled_kg').val(waste.wg_qty_recycled_kg);

				$('#wg_revenue_from_recycling_per_kg').val(waste.wg_revenue_from_recycling_per_kg);



				$('#wuko_waste_kg').val(waste.wuko_waste_kg);

				$('#wuko_cost_of_waste_removal_per_kg').val(waste.wuko_cost_of_waste_removal_per_kg);

				$('#wuko_qty_recycled_kg').val(waste.wuko_qty_recycled_kg);

				$('#wuko_revenue_from_recycling_per_kg').val(waste.wuko_revenue_from_recycling_per_kg);

				$('#wp_waste_kg').val(waste.wp_waste_kg);

				$('#wp_cost_of_waste_removal_per_kg').val(waste.wp_cost_of_waste_removal_per_kg);

				$('#wp_qty_recycled_kg').val(waste.wp_qty_recycled_kg);

				$('#wp_revenue_from_recycling_per_kg').val(waste.wp_revenue_from_recycling_per_kg);



				$('#wc_waste_kg').val(waste.wc_waste_kg);

				$('#wc_cost_of_waste_removal_per_kg').val(waste.wc_cost_of_waste_removal_per_kg);

				$('#wc_qty_recycled_kg').val(waste.wc_qty_recycled_kg);

				$('#wc_revenue_from_recycling_per_kg').val(waste.wc_revenue_from_recycling_per_kg);



				$('#gw_waste_kg').val(waste.gw_waste_kg);

				$('#gw_cost_of_waste_removal_per_kg').val(waste.gw_cost_of_waste_removal_per_kg);

				$('#gw_qty_recycled_kg').val(waste.gw_qty_recycled_kg);

				$('#gw_revenue_from_recycling_per_kg').val(waste.gw_revenue_from_recycling_per_kg);



				$('.pete_waste_invoice_scan').attr('src', waste.pete_waste_invoice_scan);

				$('.pete_recycled_invoice_scan').attr('src', waste.pete_recycled_invoice_scan);

				$('.hdpe_waste_invoice_scan').attr('src', waste.hdpe_waste_invoice_scan);

				$('.hdpe_recycled_invoice_scan').attr('src', waste.hdpe_recycled_invoice_scan);

				$('.pvc_waste_invoice_scan').attr('src', waste.pvc_waste_invoice_scan);

				$('.pvc_recycled_invoice_scan').attr('src', waste.pvc_recycled_invoice_scan);

				$('.ldpe_waste_invoice_scan').attr('src', waste.ldpe_waste_invoice_scan);

				$('.ldpe_recycled_invoice_scan').attr('src', waste.ldpe_recycled_invoice_scan);

				$('.pp_waste_invoice_scan').attr('src', waste.pp_waste_invoice_scan);

				$('.pp_recycled_invoice_scan').attr('src', waste.pp_recycled_invoice_scan);

				$('.ps_waste_invoice_scan').attr('src', waste.ps_waste_invoice_scan);

				$('.ps_recycled_invoice_scan').attr('src', waste.ps_recycled_invoice_scan);

				$('.op_waste_invoice_scan').attr('src', waste.op_waste_invoice_scan);

				$('.op_recycled_invoice_scan').attr('src', waste.op_recycled_invoice_scan);

				$('.fw_waste_invoice_scan').attr('src', waste.fw_waste_invoice_scan);

				$('.fw_recycled_invoice_scan').attr('src', waste.fw_recycled_invoice_scan);

				$('.glass_waste_invoice_scan').attr('src', waste.glass_waste_invoice_scan);

				$('.glass_recycled_invoice_scan').attr('src', waste.glass_recycled_invoice_scan);

				$('.wh_waste_invoice_scan').attr('src', waste.wh_waste_invoice_scan);

				$('.wh_recycled_invoice_scan').attr('src', waste.wh_recycled_invoice_scan);

				$('.wg_waste_invoice_scan').attr('src', waste.wg_waste_invoice_scan);

				$('.wg_recycled_invoice_scan').attr('src', waste.wg_recycled_invoice_scan);

				$('.wuko_waste_invoice_scan').attr('src', waste.wuko_waste_invoice_scan);

				$('.wuko_recycled_invoice_scan').attr('src', waste.wuko_recycled_invoice_scan);

				$('.wp_waste_invoice_scan').attr('src', waste.wp_waste_invoice_scan);

				$('.wp_recycled_invoice_scan').attr('src', waste.wp_recycled_invoice_scan);

				$('.wc_waste_invoice_scan').attr('src', waste.wc_waste_invoice_scan);

				$('.wc_recycled_invoice_scan').attr('src', waste.wc_recycled_invoice_scan);

				$('.gw_waste_invoice_scan').attr('src', waste.gw_waste_invoice_scan);

				$('.gw_recycled_invoice_scan').attr('src', waste.gw_recycled_invoice_scan);

			    }

			    else{

				$('#waste_id').val('');

				$('#pete_waste_kg').val('');

				$('#pete_cost_of_waste_removal_per_kg').val('');

				$('#pete_qty_recycled_kg').val('');

				$('#pete_revenue_from_recycling_per_kg').val('');

				$('#hdpe_waste_kg').val('');

				$('#hdpe_cost_of_waste_removal_per_kg').val('');

				$('#hdpe_qty_recycled_kg').val('');

				$('#hdpe_revenue_from_recycling_per_kg').val('');

				$('#pvc_waste_kg').val('');

				$('#pvc_cost_of_waste_removal_per_kg').val('');

				$('#pvc_qty_recycled_kg').val('');

				$('#pvc_revenue_from_recycling_per_kg').val('');



				$('#ldpe_waste_kg').val('');

				$('#ldpe_cost_of_waste_removal_per_kg').val('');

				$('#ldpe_qty_recycled_kg').val('');

				$('#ldpe_revenue_from_recycling_per_kg').val('');



				$('#pp_waste_kg').val('');

				$('#pp_cost_of_waste_removal_per_kg').val('');

				$('#pp_qty_recycled_kg').val('');

				$('#pp_revenue_from_recycling_per_kg').val('');



				$('#ps_waste_kg').val('');

				$('#ps_cost_of_waste_removal_per_kg').val('');

				$('#ps_qty_recycled_kg').val('');

				$('#ps_revenue_from_recycling_per_kg').val('');



				$('#op_waste_kg').val('');

				$('#op_cost_of_waste_removal_per_kg').val('');

				$('#op_qty_recycled_kg').val('');

				$('#op_revenue_from_recycling_per_kg').val('');



				$('#fw_waste_kg').val('');

				$('#fw_cost_of_waste_removal_per_kg').val('');

				$('#fw_qty_recycled_kg').val('');

				$('#fw_revenue_from_recycling_per_kg').val('');



				$('#glass_waste_kg').val('');

				$('#glass_cost_of_waste_removal_per_kg').val('');

				$('#glass_qty_recycled_kg').val('');

				$('#glass_revenue_from_recycling_per_kg').val('');



				$('#wh_waste_kg').val('');

				$('#wh_cost_of_waste_removal_per_kg').val('');

				$('#wh_qty_recycled_kg').val('');

				$('#wh_revenue_from_recycling_per_kg').val('');



				$('#wg_waste_kg').val('');

				$('#wg_cost_of_waste_removal_per_kg').val('');

				$('#wg_qty_recycled_kg').val('');

				$('#wg_revenue_from_recycling_per_kg').val('');



				$('#wuko_waste_kg').val('');

				$('#wuko_cost_of_waste_removal_per_kg').val('');

				$('#wuko_qty_recycled_kg').val('');

				$('#wuko_revenue_from_recycling_per_kg').val('');

				$('#wp_waste_kg').val('');

				$('#wp_cost_of_waste_removal_per_kg').val('');

				$('#wp_qty_recycled_kg').val('');

				$('#wp_revenue_from_recycling_per_kg').val('');



				$('#wc_waste_kg').val('');

				$('#wc_cost_of_waste_removal_per_kg').val('');

				$('#wc_qty_recycled_kg').val('');

				$('#wc_revenue_from_recycling_per_kg').val('');



				$('#gw_waste_kg').val('');

				$('#gw_cost_of_waste_removal_per_kg').val('');

				$('#gw_qty_recycled_kg').val('');

				$('#gw_revenue_from_recycling_per_kg').val('');



				$('.pete_waste_invoice_scan').attr('src', '');

				$('.pete_recycled_invoice_scan').attr('src', '');

				$('.hdpe_waste_invoice_scan').attr('src', '');

				$('.hdpe_recycled_invoice_scan').attr('src', '');

				$('.pvc_waste_invoice_scan').attr('src', '');

				$('.pvc_recycled_invoice_scan').attr('src', '');

				$('.ldpe_waste_invoice_scan').attr('src', '');

				$('.ldpe_recycled_invoice_scan').attr('src', '');

				$('.pp_waste_invoice_scan').attr('src', '');

				$('.pp_recycled_invoice_scan').attr('src', '');

				$('.ps_waste_invoice_scan').attr('src', '');

				$('.ps_recycled_invoice_scan').attr('src', '');

				$('.op_waste_invoice_scan').attr('src', '');

				$('.op_recycled_invoice_scan').attr('src', '');

				$('.fw_waste_invoice_scan').attr('src', '');

				$('.fw_recycled_invoice_scan').attr('src', '');

				$('.glass_waste_invoice_scan').attr('src', '');

				$('.glass_recycled_invoice_scan').attr('src', '');

				$('.wh_waste_invoice_scan').attr('src', '');

				$('.wh_recycled_invoice_scan').attr('src', '');

				$('.wg_waste_invoice_scan').attr('src', '');

				$('.wg_recycled_invoice_scan').attr('src', '');

				$('.wuko_waste_invoice_scan').attr('src', '');

				$('.wuko_recycled_invoice_scan').attr('src', '');

				$('.wp_waste_invoice_scan').attr('src', '');

				$('.wp_recycled_invoice_scan').attr('src', '');

				$('.wc_waste_invoice_scan').attr('src', '');

				$('.wc_recycled_invoice_scan').attr('src', '');

				$('.gw_waste_invoice_scan').attr('src', '');

				$('.gw_recycled_invoice_scan').attr('src', '');

			    }



							// for biodiversity tab

							// for social tab

			    $('.biodiversity_inner_div').empty();

			    $('.biodiversity_inner_div').html(data.biodiversity);



							// biodiversity_inner_div

			    // delete_waste_image

						$('.waste_image').each(function() {



							var isrc = $(this).attr('src');

							if(isrc.includes("no-image-available.jpg"))

							{

								$(this).parents('.waste_image_div').find('.delete_waste_image').hide();

								$(this).parents('.waste_image_div').css('height', '100px');

							}

							else

							{

								$(this).parents('.waste_image_div').find('.delete_waste_image').show();

							}

						});





				$('#waste_quarter').val(quarter[0]);

			    $('#waste_year').val(quarter[1]);



			    $('#biodiversity_quarter').val(quarter[0]);

			    $('#biodiversity_year').val(quarter[1]);



			    // to get new index (ajax_count_opendiv)

			    var new_index = parseInt($("#ajax_count_opendiv").val());

			    //console.log(new_index);

			    $("#count_opendiv").val(new_index);



			    $('body').find('#main_ul .ngo_block_li').find('.file_upload').attr('type','file');

			    $('body').find('#main_ul .ngo_block_li').find('.action-addition').customFile();



			    $('body').find('#biodiversity_main_div').find('.biodiversity_div .file_upload').attr('type','file');

					$('body').find('#biodiversity_main_div').find('.biodiversity_div .bio_media').customFile();



			    unblockUI();

			}

		});



	    });

	// });



	// remove ngo section

	$("body").on("click", ".close_ngo", function() {

	    var $this = $(this);

	    var id = $this.attr("data-id");



	    res = confirm('<?php echo lang('delete_confirm_ngo') ?>');



	    if(res){

		$.ajax({

			type:'POST',

			url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_ngo',

			data:{id:id},

			error: function(){

			  alert("Server problem. Please try again.");

			    return false;

			},

			complete: function(){

			   //   unblockUI();

			},

			success: function(data) {

			   // blockUI();

			    $.ajax({

				type: 'POST',

				url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_ngo',

				data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', id:id },

				success: function(data) {



				}

			    });

			    //unblockUI();

			}

		    });



		$this.closest(".ngo_block_li").remove();

	    }else{

		return false;

	    }



	   /*

	    $(".action-text-addition").trigger("change");*/

	});



	// remove div

	$(".btn-control.substract_minus").click(function (e) {

	    e.preventDefault();

	    var $this = $(this);

	    var id = $this.attr("data-id");

	   //console.log("id ---------  "+id);



	    res = confirm('<?php echo lang('delete_confirm_action') ?>');



	    if(res){



		$.ajax({

			type:'POST',

			url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_action',

			data:{id:id},

			error: function(){

			  alert("Server problem. Please try again.");

			    return false;

			},

			complete: function(){

			   //   unblockUI();

			},

			success: function(data) {

			   // blockUI();

			    $.ajax({

				type: 'POST',

				url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_action',

				data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', id:id },

				success: function(data) {



				}

			    });

			    //unblockUI();

			}

		    });



		$this.closest(".action_row").remove();

	    }else{

		return false;

	    }

	   /*

	    $(".action-text-addition").trigger("change");*/

	});



	// delete media files

	$("body").on("click", ".delete_media", function(){





	    res = confirm('<?php echo lang('delete_confirm') ?>');



	    if(res){



		var $this = $(this);

		var id = $this.attr("data-id");



	       //blockUI();

		$.ajax({

		    type:'POST',

		    url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_action_image',

		    data:{id:id},

		    error: function(){

		      alert("Server problem. Please try again.");

			return false;

		    },

		    complete: function(){

		       //   unblockUI();

		    },

		    success: function(data) {

		       // blockUI();

			$.ajax({

			    type: 'POST',

			    url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_action_image',

			    data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', id:id },

			    success: function(data) {



			    }

			});

			//unblockUI();

		    }

		});

		$(this).closest('div').remove();



	    }else{

		return false;

	    }

	});



	// delete biodiversity media files

	$("body").on("click", ".delete_media_bio", function() {



	    res = confirm('<?php echo lang('delete_confirm') ?>');



	    if(res){



		var $this = $(this);

		var id = $this.attr("data-id");



	       //blockUI();

		$.ajax({

		    type:'POST',

		    url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_biodiversity_image',

		    data:{id:id},

		    error: function(){

			// alert("Server problem. Please try again.");

			return false;

		    },

		    complete: function(){

		       //   unblockUI();

		    },

		    success: function(data) {

		       // blockUI();

			$.ajax({

			    type: 'POST',

			    url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_biodiversity_image',

			    data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', id:id },

			    success: function(data) {



			    }

			});

			//unblockUI();

		    }

		});

		$(this).closest('div').remove();



	    }else{

		return false;

	    }

	});



	//delete_waste_image

	$('.delete_waste_image').click(function(){



	    res = confirm('<?php echo lang('delete_confirm_waste') ?>');

	    if(res){



		var $this = $(this);

		var field = $this.attr("data-feild");

		var id    = $("#waste_id").val();

		var field = $this.attr("data-feild");



		$.ajax({

		    type:'POST',

		    url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_waste_image',

		    data:{id:id, field:field},

		    error: function(){

		      // alert("Server problem. Please try again.");

			return false;

		    },

		    complete: function(){

		    },

		    success: function(data) {

			// alert(data);

			$('.'+field).attr('src', '<?php echo site_url() . "/assets/uploads/no-image-available.jpg"; ?>');

			$this.parents('.waste_image_div').find('.delete_waste_image').hide();

			$this.parents('.waste_image_div').css('height', '100px');



		    }

		});

	    }else{

		return false;

	    }

	});





	var counter = parseInt($("#count_opendiv").val()); //current existing data



	// Add new ngo

	$('body').on('click','#ajax_table .add-new-ngo', function (e) {



	    e.preventDefault();



	    //$(".number_sequence").val(counter);

	    //var num_sequence = parseInt($(".number_sequence").val());



	    var new_index = $("#ajax_count_opendiv").val();

	    var index = parseInt(new_index);



	    if(parseInt(new_index) === parseInt('0'))

	    {

		$('#count_opendiv').attr('value',index);

		var counter1 = $("#count_opendiv").val();

		counter = parseInt(counter1);

	    }



	    var ngoid = 'ngo_id[' + counter + ']';

	    var ngoname = 'ngo_name[' + counter + ']';



	    var action_text = 'action_text[' + counter + '][]';

	    var action_sdg = 'action_sdg[' + counter + '][]';

	    var action_media = 'action_media[' + counter + '][0][]';

	    var action_id = 'action_id[' + counter + '][]';



	    var html = $('.ngo_main_section').html();

	    $('body').find('#main_ul .ngo_block_li:last').after(html);



	    $('body').find('#main_ul .ngo_block_li:last').find('.ngo_id:last').attr('name', ngoid);

	    $('body').find('#main_ul .ngo_block_li:last').find('.ngo_name:last').attr('name', ngoname);

	    $('body').find('#main_ul .ngo_block_li:last').find('.number_sequence:last').attr('value', counter);



	    $('body').find('#main_ul .ngo_block_li:last').find('.action_row:last .inner_action_text').attr('name', action_text);

	    $('body').find('#main_ul .ngo_block_li:last').find('.action_row:last .inner_action_sdg').attr('name', action_sdg);

	    $('body').find('#main_ul .ngo_block_li:last').find('.action_row:last .inner_action_media').attr('name', action_media);

	    $('body').find('#main_ul .ngo_block_li:last').find('.action_count:last').attr('value', 0);



	    $('body').find('#main_ul .ngo_block_li:last').find('.file_upload:last').attr('type','file');

	    $('body').find('#main_ul .ngo_block_li:last').find('.action-addition:last').customFile();



	    counter = counter + 1;

	    $("#ajax_count_opendiv").val(counter);

	});



	// add bio diversity div

	$("body").on('click', '#biodiversity_main_div .add-biodiversity', function (e) {



		var html = $('.new_biodiversity_div_outer').html();



		var bio_count = 0;

			var id = parseInt($(this).attr('data-id'));



			if( (id == '' || id == 0)  ){

				id = parseInt($(this).parents('#biodiversity_main_div').find('.bio_count').val());

				bio_count = parseInt($(this).parents('#biodiversity_main_div').find('.bio_count').val());

				// bio_count = parseInt(bio_count) + 1;



			} else {

				bio_count = parseInt($(this).parents('#biodiversity_main_div').find('.bio_count').val());

			}



			bio_count = bio_count + 1;



			var measure   = 'measure[' + bio_count + ']';

			var partner   = 'partner[' + bio_count + ']';

			var bio_media = 'bio_media[' + bio_count + '][]';

			var bio_id    = 'bio_id[' + bio_count + ']';



		$(this).parents('#biodiversity_main_div').find('.biodiversity_div:last').after(html);



			$(this).parents('#biodiversity_main_div').find('.biodiversity_div:last .inner_measure').attr('name', measure);

			$(this).parents('#biodiversity_main_div').find('.biodiversity_div:last .inner_partner').attr('name', partner);

			$(this).parents('#biodiversity_main_div').find('.biodiversity_div:last .inner_bio_media').attr('name', bio_media);

			$(this).parents('#biodiversity_main_div').find('.biodiversity_div:last .inner_bio_id').attr('name', bio_id);

		$(this).parents('#biodiversity_main_div').find('.bio_count:last ').attr('value', bio_count);



		$('body').find('#biodiversity_main_div').find('.biodiversity_div:last .file_upload:last').attr('type','file');



	    $('body').find('#biodiversity_main_div').find('.biodiversity_div:last .bio_media:last').customFile();



	    // To remove extra file input box

	    $('body').find('#biodiversity_main_div').find('.biodiversity_div:last .file-upload-input:first').remove();

	    $('body').find('#biodiversity_main_div').find('.biodiversity_div:last .file-upload-button:first').remove();

	});



	// remove biodiversity div

	$("body").on('click', '#biodiversity_main_div .substract-minus', function (e) {

	    e.preventDefault();

	    var $this = $(this);

	    $this.closest(".biodiversity_div").remove();

	});



	// delete biodiversity div

	$("body").on('click', '#biodiversity_main_div .substract_minus_biodiversity', function (e) {

	    e.preventDefault();

	    var $this = $(this);

	    var id = $this.attr("data-id");



	    res = confirm('<?php echo lang('delete_confirm_bio') ?>');



	    if(res){



		blockUI();

		$.ajax({

			type:'POST',

			url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_biodiversity',

			data:{id:id},

			async : false,

			error: function(){

			    // alert("Server problem. Please try again.");

			    return false;

			},

			complete: function(){

			   //   unblockUI();

			},

			success: function(data) {



			    $.ajax({

				type: 'POST',

				async : false,

				url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_biodiversity',

				data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', id:id },

				success: function(data) {



					// top_right_delete_button

					var total_div = $('body').find('.top_right_delete_button').length;

					if(total_div == 1)

					{

						$('body').find(".add-biodiversity").trigger('click');



						$('body').find("#biodiversity_main_div .substract-minus").find('img').attr('src', '<?php echo base_url();?>'+'themes/default/images/plus-icon.png');

						$('body').find("#biodiversity_main_div .substract-minus").find('img').attr('alt', 'Plus');

						$('body').find("#biodiversity_main_div .substract-minus").addClass('add-biodiversity');

						$('body').find("#biodiversity_main_div .substract-minus").removeClass('substract-minus');

					}

				    $this.closest(".biodiversity_div").remove();

				}

			    });

			    unblockUI();

			}

		    });

	    }else{

		return false;

	    }

	});



	// add action

	$("body").on('click', '#ajax_table .btn-control.addition-plus', function (e) {

		//insightsectioncnt = insightsectioncnt + 1;

			var action_count = 0;

			var id = $(this).attr('data-id');



			if(id == ''){

			id = $(this).parents('#main_ul .ngo_block_li').find('.number_sequence').val();

			action_count = parseInt(action_count) + 1;



			} else {

			action_count = parseInt($(this).parents('#main_ul .ngo_block_li').find('.action_count').val());

			}



			var action_text = 'action_text[' + id + '][]';

			var action_sdg = 'action_sdg[' + id + '][]';

			var action_media = 'action_media[' + id + ']['+action_count+'][]';

			var action_id = 'action_id[' + id + '][]';



			action_count = action_count +1;



			var html = $('.inner_action_row').html();



			$(this).parents('#main_ul .ngo_block_li').find('.action_row:last').after(html);

			$(this).parents('#main_ul .ngo_block_li').find('.action_row:last .inner_action_text').attr('name', action_text);

			$(this).parents('#main_ul .ngo_block_li').find('.action_row:last .inner_action_sdg').attr('name', action_sdg);

			$(this).parents('#main_ul .ngo_block_li').find('.action_row:last .inner_action_media').attr('name', action_media);

			$(this).parents('#main_ul .ngo_block_li').find('.action_row:last .action_id').attr('name', action_id);

			$(this).parents('#main_ul .ngo_block_li').find('.action_count:last').attr('value', action_count);



			$(this).parents('#main_ul .ngo_block_li').find('.action_row:last .file_upload:last').attr('type','file');

			$(this).parents('#main_ul .ngo_block_li').find('.action_row:last .action-addition:last').customFile();



	});



	// remove div

	$("body").on('click', '#ajax_table .btn-control.substract-minus', function (e) {

	    e.preventDefault();

	    var $this = $(this);

	    var id = $this.attr("data-id");





	    $this.closest(".action_row").remove();

	});



	// remove div

	$("body").on('click', '#ajax_table .btn-control.substract_minus', function (e) {

	    e.preventDefault();

	    var $this = $(this);

	    var id = $this.attr("data-id");



	    res = confirm('<?php echo lang('delete_confirm_action') ?>');



	    if(res){

		$.ajax({

			type:'POST',

			url:'<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_action',

			data:{id:id},

			error: function(){

			  alert("Server problem. Please try again.");

			    return false;

			},

			complete: function(){

			   //   unblockUI();

			},

			success: function(data) {

			   // blockUI();

			    $.ajax({

				type: 'POST',

				url: '<?php echo base_url().BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_action',

				data: {<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>', id:id },

				success: function(data) {



				}

			    });

			    //unblockUI();

			}

		    });



		$this.closest(".action_row").remove();

	    }else{

		return false;

	    }



	    // $this.closest(".action_row").remove();

	});



	// validate HR form

	$("#savehrform").validate({

	    rules: {

		hr_no_of_hrs: {

		    digits:true

		},

		hr_no_of_employees: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		nd_no_of_incidents_of_discrimination: {

		    digits:true

		},

		nd_incident_reviewed_by_org: {

		    digits:true

		},

		nd_remediation_plans_implemented: {

		    digits:true

		},

		lpd_hires_age_under_thirty: {

		    digits:true

		},

		lpd_hires_age_between_thirty_to_fifty: {

		    digits:true

		},

		lpd_hires_age_more_than_fifty: {

		    digits:true

		},

		lpd_hires_gender_male: {

		    digits:true

		},

		lpd_hires_gender_female: {

		    digits:true

		},

		lpd_turnover_age_under_thirty: {

		    digits:true

		},

		lpd_turnover_age_between_thirty_to_fifty: {

		    digits:true

		},

		lpd_turnover_age_more_than_fifty: {

		    digits:true

		},

		lpd_turnover_gender_male: {

		    digits:true

		},

		lpd_turnover_gender_female: {

		    digits:true

		},

		ohs_rate_of_occupational_diseases: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ohs_lost_day_rates: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ohs_absentee_rate: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ohs_gender_male: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ohs_gender_female: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		te_gender_male: {

		    digits:true

		},

		te_gender_female: {

		    digits:true

		},

		te_team_member: {

		    digits:true

		},

		te_supervisor: {

		    digits:true

		},

		te_manager: {

		    digits:true

		},

		te_head_of_department: {

		    digits:true

		},

		te_assistant_head_of_department: {

		    digits:true

		},

		te_general_manager: {

		    digits:true

		},

		te_senior_manager: {

		    digits:true

		},

		tae_gender_male: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tae_gender_female: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tae_team_member: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tae_supervisor: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tae_manager: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tae_head_of_department: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tae_assistant_head_of_department: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tae_general_manager: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tae_senior_manager: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		diversity_and_opportunity: {

		    digits:true

		},

		ermw_gender_male: {

		    digits:true

		},

		ermw_gender_female: {

		    digits:true

		},

		hr_no_of_hrs: {

		    digits:true

		},

		ermw_team_member: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ermw_supervisor: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ermw_manager: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ermw_head_of_department: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ermw_assistant_head_of_department: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ermw_senior_manager: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ec_ratios_of_std_gender_male: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ec_ratios_of_std_gender_female: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		ec_proportion_of_senior_management_hired: {

		    number: true,

		    min: 0,

		    max: 100,

		},

		tmsr_global_index: {

		    digits:true

		},

		tmsr_leadership_index: {

		    digits:true

		},

		tmsr_loyalty_index: {

		    digits:true

		},

		tmsr_other_index: {

		    digits:true

		},

		talent_management: {

		    digits:true

		},

	    }

	});



		// validate waste form

		$("#save_waste_form").validate({

	    rules: {

		pete_waste_kg: {

		    number:true

		},

		pete_cost_of_waste_removal_per_kg: {

		    number:true

		},

		pete_qty_recycled_kg: {

		    number:true

		},

		pete_revenue_from_recycling_per_kg: {

		    number:true

		},

		hdpe_waste_kg: {

		    number:true

		},

		hdpe_cost_of_waste_removal_per_kg: {

		    number:true

		},

		hdpe_qty_recycled_kg: {

		    number:true

		},

		hdpe_revenue_from_recycling_per_kg: {

		    number:true

		},

		pvc_waste_kg: {

		    number:true

		},

		pvc_cost_of_waste_removal_per_kg: {

		    number:true

		},

		pvc_qty_recycled_kg: {

		    number:true

		},

		pvc_revenue_from_recycling_per_kg: {

		    number:true

		},

		ldpe_waste_kg: {

		    number:true

		},

		ldpe_cost_of_waste_removal_per_kg: {

		    number:true

		},

		ldpe_qty_recycled_kg: {

		    number:true

		},

		ldpe_revenue_from_recycling_per_kg: {

		    number:true

		},

		pp_waste_kg: {

		    number:true

		},

		pp_cost_of_waste_removal_per_kg: {

		    number:true

		},

		pp_qty_recycled_kg: {

		    number:true

		},

		pp_revenue_from_recycling_per_kg: {

		    number:true

		},

		ps_waste_kg: {

		    number:true

		},

		ps_cost_of_waste_removal_per_kg: {

		    number:true

		},

		ps_qty_recycled_kg: {

		    number:true

		},

		ps_revenue_from_recycling_per_kg: {

		    number:true

		},

		op_waste_kg: {

		    number:true

		},

		op_cost_of_waste_removal_per_kg: {

		    number:true

		},

		op_qty_recycled_kg: {

		    number:true

		},

		op_revenue_from_recycling_per_kg: {

		    number:true

		},

		fw_waste_kg: {

		    number:true

		},

		fw_cost_of_waste_removal_per_kg: {

		    number:true

		},

		fw_qty_recycled_kg: {

		    number:true

		},

		fw_revenue_from_recycling_per_kg: {

		    number:true

		},

		glass_waste_kg: {

		    number:true

		},

		glass_cost_of_waste_removal_per_kg: {

		    number:true

		},

		glass_qty_recycled_kg: {

		    number:true

		},

		glass_revenue_from_recycling_per_kg: {

		    number:true

		},

		wh_waste_kg: {

		    number:true

		},

		wh_cost_of_waste_removal_per_kg: {

		    number:true

		},

		wh_qty_recycled_kg: {

		    number:true

		},

		wh_revenue_from_recycling_per_kg: {

		    number:true

		},

		wg_waste_kg: {

		    number:true

		},

		wg_cost_of_waste_removal_per_kg: {

		    number:true

		},

		wg_qty_recycled_kg: {

		    number:true

		},

		wg_revenue_from_recycling_per_kg: {

		    number:true

		},

		wuko_waste_kg: {

		    number:true

		},

		wuko_cost_of_waste_removal_per_kg: {

		    number:true

		},

		wuko_qty_recycled_kg: {

		    number:true

		},

		wuko_revenue_from_recycling_per_kg: {

		    number:true

		},

		wp_waste_kg: {

		    number:true

		},

		wp_cost_of_waste_removal_per_kg: {

		    number:true

		},

		wp_qty_recycled_kg: {

		    number:true

		},

		wp_revenue_from_recycling_per_kg: {

		    number:true

		},

		wc_waste_kg: {

		    number:true

		},

		wc_cost_of_waste_removal_per_kg: {

		    number:true

		},

		wc_qty_recycled_kg: {

		    number:true

		},

		wc_revenue_from_recycling_per_kg: {

		    number:true

		},

		gw_waste_kg: {

		    number:true

		},

		gw_cost_of_waste_removal_per_kg: {

		    number:true

		},

		gw_qty_recycled_kg: {

		    number:true

		},

		gw_revenue_from_recycling_per_kg: {

		    number:true

		},

	    }

	});



	/*$('form#saveform').on('submit', function(event) {

	    $('.csr_year').each(function() {

		$(this).rules("add",

		    {

			required: true,

			// messages: {

			//     required: "Year is required",

			// }

		    });

	    });

	    $('.quarter').each(function() {

		$(this).rules("add",

		    {

			required: true,

			// messages: {

			//     required: "Quarter is required",

			//   },

			// errorPlacement: function(error, element) {

			//         error.appendTo("#quarter");

			// },

		    });

	    });

	     $('.ngo_name').each(function() {

		$(this).rules("add",

		    {

			required: true,

			// messages: {

			//     required: "NGO name is required",

			//   },

			// errorPlacement: function(error, element) {

			//         error.appendTo("#ngo_name");

			// },

		    });

	    });

	});



	/*$('form#savengoform').on('submit', function(event) {



	    // $('.csr_year').each(function() {

	    //     $(this).rules("add",

	    //         {

	    //             required: true,

	    //         });

	    // });

	    // $('.quarter').each(function() {

	    //     $(this).rules("add",

	    //         {

	    //             required: true,

	    //         });

	    // });



	    //  $('.ngo_name').each(function() {

	    //     $(this).rules("add",

	    //         {

	    //             required: true,

	    //         });

	    // });

	});



	$("#savengoform").validate();*/



    });



</script>