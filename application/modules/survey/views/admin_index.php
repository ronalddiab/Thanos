<?php
echo add_js(array('easyResponsiveTabs', 'MonthPicker.min' , 'bootstrap-datepicker-new'));
echo add_css(array('MonthPicker.min', 'bootstrap-datepicker-new'));
?>
<article class="card" id="ajax_table">
    <div class="article-header">
	<?php echo lang('survey-management'); ?>
    </div>
    <div class="card-wrap">
	<div class="row survey-controls-outer">
	    <div class="col-sm-12 col-lg-8">
		<form class="default-form form-inline clearfix">
		    <div class="form-group form-control-outer" style="width: 171px;">
			<?php
			$input_data = array(
			    'name' => 'search_term',
			    'id' => 'search_term',
			    'class' => 'form-control',
			    'placeholder' => lang('search-by-question-text'),
			    'value' => set_value('search_term', urldecode($search_term))
			);
			echo form_input($input_data);
			?>
		    </div>
		    <div class="form-group">
			<div class="form-group form-control-outer" style="width: 171px; margin-top: 10px;">
			    <select name="search_section" id="search_section" class="form-control">
				<option value="">Select Section</option>
				<?php
				foreach ($sections as $key => $value) {
				    $selected = ($search_section === $value) ? 'selected' : '';
				    ?>
				    <option value="<?php echo $key; ?>" <?php echo  $selected; ?> ><?php echo ucfirst($value); ?></option>
				    <?php
				 } ?>
			    </select>
			</div>
		    </div>
		    <div class="form-group">
			<div class="form-group form-control-outer" style="width: 171px; margin-top: 10px;">
			    <select name="search_source" id="search_source" class="form-control">
				<option value="">Select Source</option>
				<?php
				foreach ($sources as $key => $value) {
				    $selected = ($search_source === $value) ? 'selected' : '';
				    ?>
				    <option value="<?php echo $key; ?>" <?php echo  $selected; ?> ><?php echo ucfirst($value); ?></option>
				    <?php
				 } ?>
			    </select>
			</div>
		    </div>
		    <div class="form-group">
			<?php
			$search_button = array(
			    'content' => add_image(array('search-icon.png')) . ' ' . lang('btn-search'),
			    'class' => 'btn btn-secondary',
			    'onclick' => "submit_search()",
			);
			echo form_button($search_button);
			?>
		    </div>
		    <div class="form-group">
			<?php
			$reset_button = array(
			    'content' => add_image(array('reset-icon.png')) . ' ' . lang('btn-reset'),
			    'class' => 'btn btn-reset',
			    'onclick' => "reset_data()",
			);
			echo form_button($reset_button);
			?>
		    </div>
		</form>
	    </div>
	    <div class="col-lg-4">
	    <?php $add_survey_permission = check_user_permission_by_label('admin.survey.action.add'); ?>
	    <?php if($add_survey_permission){ ?>
		<?php echo anchor(BASE_ADMIN_URL_CUSTOM . 'survey/action/add', '<span>' . add_image(array('plus-icon.png')) . '</span> ' . lang('add-survey'), 'class="btn btn-blue pull-right"'); ?>
	    <?php } ?>
	    <?php $sort_survey_permission = check_user_permission_by_label('admin.survey.action.sort'); ?>
	    <?php if($sort_survey_permission){ ?>
		<button id="save" class="btn btn-blue pull-right" style="padding: 6px 14px;margin-right: 2px;"><?= lang('sort-survey');?></button>
	    <?php } ?>
	    </div>
	</div>
	<div class="row survey-controls-outer">
	    <?php $open_survey_permission = check_user_permission_by_label('admin.survey.survey_open');?>
	    <?php if($open_survey_permission && $this->_ci->session->userdata[$this->_data['section_name']]['user_id'] == 1){ ?>
		<form method="post" action="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM .  'survey/open';?>">
		    <div class="col-lg-2">
			<?php if(CheckIfSurveyIsOpen() != 1) { ?>
			<div id="monthly_report_popup" style="display:block;">
			    <div class="form-group form-control-outer" style="width: 171px;">
				<input type="text" placeholder="Select Survey Close Date" id="DateFormat" class='form-control' value="" name="close_date" style="border:1px solid #ccc;" required >
				<?php if (form_error('close_date')) { ?><label class="input-label validation_error"><?php echo form_error('close_date'); ?></label> <?php } ?>
			    </div>
			</div>
			<?php } else { echo '<label class="input-label validation_error">Survey is already open.</label>'; }?>
		    </div>
		    <div class="col-lg-2">
			<?php
			$disabled = (CheckIfSurveyIsOpen() != 1) ? '' : ' disabled';
			// echo anchor(BASE_ADMIN_URL_CUSTOM . 'survey/open', lang('open-survey'), 'class="btn btn-blue pull-right" style="padding:6px 14px" id="monthly_report_popup_btn" name="submit" value="download_monthly_hidden"'.$disabled.'');
			?>
			<button type="submit" class="btn btn-blue pull-right btn-submit" style="padding:6px 14px" <?php echo $disabled;?> ><?php echo lang('open-survey');?></button>
		    </div>
		</form>
	    <?php } ?>
	    <?php $export_survey_permission = check_user_permission_by_label('admin.survey.export'); ?>
	    <?php if($export_survey_permission){ ?>
		<div class="col-lg-2 pull-right"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey/export" class="btn btn-primary" style="width:100%"><?php echo lang('export');?></a></div>
	    <?php } ?>
	    <?php $export_archieve_survey_permission = check_user_permission_by_label('admin.survey.exportarchieve'); ?>
	    <?php if($export_archieve_survey_permission){ ?>
		<div class="col-lg-2 pull-right"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey/exportarchieve" class="btn btn-primary" style="width:100%;background-color:#D49947;"><?php echo lang('export-archieve');?></a></div>
	    <?php } ?>
	    </div>
	<?php if (!empty($surveys)) { ?>
	<div class="table-responsive">
			<?php echo form_open(); ?>
	    <table class="table table-striped">
		<thead>
		<?php
		    if (!empty($surveys)) {
		?>
		<tr>
		    <th class="digits-col"><?php echo lang('no') ?></th>
		    <th>
			<?php
			$field_sort_order = 'asc';
			$sort_image = 'srt_down.png';
			if ($sort_by == 's.question_text' && $sort_order == 'asc') {
			    $sort_image = 'srt_up.png';
			    $field_sort_order = 'desc';
			}
			?>
			<a href="javascript:void(0)" onclick="sort_data('s.question_text', '<?php echo $field_sort_order; ?>');" >
			    <?php echo lang('question_text'); ?>
			    <?php
			    if ($sort_by == 's.question_text') {
				?>
				<div class="sorting">
				    <?php echo add_image(array($sort_image)); ?>
				</div>
			    <?php }
			    ?>
			</a>
		    </th>
		    <th><?php echo lang('question_type') ?></th>
		    <th><?php echo lang('source') ?></th>
		    <th><?php echo lang('section') ?></th>
		    <?php if(check_user_permission_by_label('admin.survey.delete') || check_user_permission_by_label('admin.survey.action.edit')) { ?>
		    <th>Actions</th>
		    <?php } ?>
		</thead>
		<tbody class="sortable">
		    <?php
			    $i = 1;
		    foreach ($surveys as $survey) {
			$survey_id = $survey['s']['question_id'];
		    ?>
			<tr data-id="<?php echo $survey_id;?>">
			    <td><?php echo $i; ?></td>
			    <td><?php echo $survey['s']['question_text']; ?></td>
			    <td><?php echo ucfirst($survey['s']['question_type']); ?></td>
			    <td><?php echo ucfirst($survey['s']['source']); ?></td>
			    <td><?php echo ucfirst(str_replace("and","&",$survey['s']['section'])); ?></td>
			    <?php if(check_user_permission_by_label('admin.survey.delete') || check_user_permission_by_label('admin.survey.action.edit')) { ?>
			    <td>
				<?php
				$edit_survey_permission = check_user_permission_by_label('admin.survey.action.edit');
				if($edit_survey_permission){
				?>
				    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey/action/edit/<?php echo $survey_id ?>" title="<?php echo lang('edit') ?>"><?php echo add_image(array('edit-icon.png')); ?></a></div>
				<?php } ?>
				<?php
				$encrypted_id = base64_encode($survey_id);
				$deletelink = "<a href='javascript:;' title='Delete' onclick='delete_survey(\"$encrypted_id\")'>" . add_image(array('delete-icon.png')) . "</a>";
				?>
				<?php
				$delete_survey_permission = check_user_permission_by_label('admin.survey.delete');
				if($delete_survey_permission){
				    echo $deletelink;
				}
				?>
				<i class='handle fa fa-bars'></i>
			    </td>
			    <?php } ?>
			</tr>
			<?php
			$i++;
		    }
		}
	    ?>
	    </tbody>
	</table>
	<?php echo form_close(); ?>
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
	    <?php
	} ?>
	<?php
	if (!empty($data['surveys'])) {
	    ?>
	    <?php
	    $sections = explode('|', QUESTION_SECTIONS);
	    $sectionQ = array_search($search_section, $sections);
	    $sources = explode('|', QUESTION_SOURCES);
	    $sourceQ = array_search($search_source, $sources);
	    $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()) . '&search_term=' . urlencode($search_term) .'&search_section=' . urlencode($sectionQ) .'&search_source=' . urlencode($sourceQ) . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order;
	} ?>
    </div>
</article>
<script type="text/javascript">
    //remove dynamically populate error
    $("#search_term").keypress(function(event) {
	if (event.which == 13) {
	    event.preventDefault();
	    submit_search();
	}
    });

    function delete_survey(id) {
	$('#error_msg').fadeOut(1000);
	res = confirm('<?php echo lang('delete-alert') ?>');
	if (res) {
	    $.ajax({
		type: 'POST',
		url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey/delete',
		data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', id: id},
		success: function(data) {
		    //set responce message
		    $("#messages").show();
		    $("#messages").html(data);
		}
	    });

	} else {
	    return false;
	}
    }

    function submit_search()
    {
	$('#error_msg').fadeOut(1000);
	if ($('#search_term').val() != '' || $('#search_term').val() != '' || $('#search_section').val() != '' || $('#search_source').val() != '') {
	    blockUI();
	    $.ajax({
		type: 'POST',
		url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey',
		data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val()), search_section: encodeURIComponent($('#search_section').val()), search_source: encodeURIComponent($('#search_source').val())},
		success: function(data) {
		    $("#ajax_table").html(data);
		    unblockUI();
		}
	    });
	} else {
	    alert("Please enter something to search");
	}
    }

    function sort_data(sort_by, sort_order)
    {
	$('#error_msg').fadeOut(1000); //hide error message it shown up while search
	blockUI();
	$.ajax({
	    type: 'POST',
	    url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey',
	    data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: encodeURIComponent($('#search_term').val()), sort_by: sort_by, sort_order: sort_order},
	    success: function(data) {
		$("#ajax_table").html(data);
		unblockUI();
	    }
	});
    }

    function reset_data()
    {
	$('#error_msg').fadeOut(1000); //hide error message it shown up while search
	blockUI();
	$.ajax({
	    type: 'POST',
	    url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey',
	    data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', search_term: ""},
	    success: function(data) {
		$("#ajax_table").html(data);
		unblockUI();
	    }
	});
    }

    $(document).ready(function(){
	var curDate = new Date();
	$("#DateFormat").datepicker({
	    'maxDate' : curDate,
	    'minDate' : curDate,
	    onSelect: function(selected) {
		var dt = new Date(selected);
		dt.setDate(dt.getDate());
		$("#DateFormat").datepicker("option", "minDate", dt);
	    }
	});
    });

    $(function() {
	$(".sortable").sortable({
	    connectWith: ".sortable",
	    handle: '.handle'
	}).disableSelection();
    });

    // bind a callback to the button click
    $('#save').on('click', function () {
	var selectedSection = $('#search_section').val();
	if(selectedSection == ''){
	    alert('Please select and filter section');
	} else {
	    // array where we store the IDs
	    var ids = [];
	    // loop through the <li> and extract data-id
	    $('.sortable tr').each(function() {
		ids.push($(this).data('id'));
	    });

	    console.log(ids);
	    $.ajax({
		type: 'POST',
		url: '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>survey/sortsave',
		data: {<?php echo $this->_ci->security->get_csrf_token_name(); ?>: '<?php echo $this->_ci->security->get_csrf_hash(); ?>', 'ids[]': ids,'section':selectedSection},
		success: function(data) {
		    // $("#ajax_table").html(data);
		    submit_search();
		    unblockUI();
		}
	    });
	}
    });

    $('#search_section').on('change',function(){
	var selectedSection = $(this).val();
	if(selectedSection != '') {
	    submit_search();
	}
    });

    $(document).ready(function(){
	if($('#search_section').val() != '' && $('#search_source').val() == '' && $('#search_term').val() == '') {
	    $(".panel-footer").hide();
	} else {
	    $(".panel-footer").show();
	}
    });
</script>