<div id="ajax_table">
    <div class="main-container">
        <div  class="content-center">
        <article class="card">
            <div class="article-header"><?php echo lang('import-emission-scope2-3'); ?></div>
            <div class="card-wrap">
            <?php
                $year = isset($year) ? (int) $year : (int) date('Y');
                $attributes = array('name' => 'import_form', 'id' => 'import_form', 'enctype' => 'multipart/form-data');
                echo form_open('import/emission_scope2_3?year=' . $year, $attributes);
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
                        echo form_upload($importfile_data, '', isset($disabled) ? $disabled : '');
                        ?>
                        <span class="warning-msg"><?php echo form_error('importfile'); ?></span>
                        <p style="margin-top:8px;">
                            Row 1 is the sheet label <strong>Scope 3 Waste Emission Factors</strong>.
                            Row 2 group labels are <strong>Site Name</strong>, <strong>Year</strong>, then <strong>Category | Stream | Typical Destination</strong>.
                            Row 3 column headers are <strong>tCO2e/short ton</strong> and <strong>kgCO2e/MT</strong>.
                            Enter tCO2e/short ton and kgCO2e/MT auto-calculates (× 1102.3113). If you only have kgCO2e/MT, enter that value and leave tCO2e blank — it is calculated on import.
                            The downloaded template uses the same category / stream / destination columns as the Scope 3 waste emission factors screen for that year. It does not use waste tracking tab data.
                        </p>
                        <p>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission_scope2_3?download_template=1&year=' . $year; ?>">
                                Download Excel Template (<?php echo $year; ?>)
                            </a>
                        </p>
                    </div>
                </div>
            </li>
        </ul>
        <input type="hidden" name="year" value="<?php echo $year; ?>">
        <div class="form-btn-outer">
            <button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="mysubmit"><?php echo lang('btn-import'); ?></button>
            <button type="button" class="btn btn-secondary reset-btn btn-submit" onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission_scope2_3?year=' . $year; ?>'"><?php echo lang('btn-cancel'); ?></button>
        </div>
    <?php echo form_close(); ?>
</div>
</article>
        </div>
    </div>
</div>
