<div id="ajax_table">
	<div class="main-container">
		<div class="content-center">
			<article class="card">
				<div class="article-header"><?php echo lang('import-survey-question-answer'); ?></div>
				<div class="card-wrap">
					<?php
					$attributes = array('name' => 'import_form', 'id' => 'import_form', 'enctype' => 'multipart/form-data');
					echo form_open('import/survey', $attributes);
					?>
					<ul class="form-outer-block">
						<li>
							<label class="main-label"><?php echo form_label(lang('import-file'), 'Import File'); ?> : </label>
							<div class="row">
								<div class="form-col-12">
									<?php
									$importfile_data = array(
										'name' => 'importfile',
										'id' => 'importfile',
										'class' => 'form-control'
									);
									echo form_upload($importfile_data, '', $disabled);
									?>
									<span class="warning-msg"><?php echo form_error('importfile'); ?></span>
									<?php
									if (!empty($forumavtar)) {
										echo "<br /><img src='" . site_url() . "assets/uploads/csv/" . $forumavtar . "' width='50px' />";
									}
									?>
								</div>
							</div>
						</li>

					</ul>
					<div class="form-btn-outer">
						<button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="mysubmit"><?php echo lang('btn-import'); ?></button>
						<button type="button" class="btn btn-secondary reset-btn btn-submit" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission' ?>'"><?php echo lang('btn-cancel'); ?></button>
					</div>
					<?php echo form_close(); ?>
				</div>
			</article>


		</div>
	</div>
</div>
