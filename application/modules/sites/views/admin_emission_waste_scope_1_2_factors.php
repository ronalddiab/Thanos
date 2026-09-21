<?php
$canEdit = (isset($_SESSION['admin']['role_id']) && (int) $_SESSION['admin']['role_id'] === 1);
$wasteEmissionFactors = isset($waste_emission_factors) ? $waste_emission_factors : [];
$wasteEmissionYear = isset($utilities_year) ? (int) $utilities_year : (int) date('Y');

$groupedFactors = [];
foreach ($wasteEmissionFactors as $row) {
    $categoryLabel = isset($row['category_label']) ? $row['category_label'] : '';
    $groupLabel = isset($row['group_label']) ? $row['group_label'] : '';
    $streamLabel = !empty($row['stream_label']) ? $row['stream_label'] : (!empty($groupLabel) ? $groupLabel : $categoryLabel);
    $streamKey = isset($row['column_key']) ? $row['column_key'] : $streamLabel;
    if (!isset($groupedFactors[$categoryLabel])) {
        $groupedFactors[$categoryLabel] = [];
    }
    if (!isset($groupedFactors[$categoryLabel][$streamKey])) {
        $groupedFactors[$categoryLabel][$streamKey] = [
            'label' => $streamLabel,
            'rows' => [],
        ];
    }
    $groupedFactors[$categoryLabel][$streamKey]['rows'][] = $row;
}

$renderFactorRow = function ($row, $rowLabel) use ($canEdit) {
    $masterFactorId = isset($row['master_factor_id']) ? (int) $row['master_factor_id'] : 0;
    $prefix = 'waste_ef[' . $masterFactorId . ']';
    $isEditable = ($canEdit && $masterFactorId > 0);
    ?>
    <li>
        <label class="main-label"><?php echo htmlspecialchars($rowLabel); ?></label>
        <div class="row">
            <div class="form-col-3">
                <?php
                $epaAttrs = [
                    'name' => $prefix . '[epa_source_factor]',
                    'value' => isset($row['epa_source_factor']) ? $row['epa_source_factor'] : '',
                    'class' => 'input-control decimalcheck waste-ef-epa-factor',
                ];
                if (!$isEditable) {
                    $epaAttrs['readonly'] = 'readonly';
                    $epaAttrs['style'] = 'cursor: not-allowed;';
                }
                echo form_input($epaAttrs);
                ?>
                <label class="input-label">Emission Factor (tCO2e/short ton)</label>
            </div>
            <label class="main-label col-sm-4 rightLabel"></label>
            <div class="form-col-3">
                <?php
                $hepAttrs = [
                    'name' => $prefix . '[hep_factor]',
                    'value' => isset($row['hep_factor']) ? $row['hep_factor'] : '',
                    'class' => 'input-control decimalcheck waste-ef-hep-factor',
                ];
                if (!$isEditable) {
                    $hepAttrs['readonly'] = 'readonly';
                    $hepAttrs['style'] = 'cursor: not-allowed;';
                }
                echo form_input($hepAttrs);
                ?>
                <label class="input-label">Emission Factor (kgCO2e/MT)</label>
            </div>
        </div>
    </li>
    <?php
};

$categoryNeedsStreamLevel = function ($categoryLabel, $streams) {
    if (count($streams) > 1) {
        return true;
    }
    $onlyStream = reset($streams);
    return strcasecmp($onlyStream['label'], $categoryLabel) !== 0;
};
?>
<style type="text/css">
    .waste-ef-form-wrap {
        max-height: 520px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .waste-ef-category-panel,
    .waste-ef-stream-panel {
        border: none;
        box-shadow: none;
        margin-bottom: 4px;
        background: transparent;
    }
    .waste-ef-category-panel > .panel-heading,
    .waste-ef-stream-panel > .panel-heading {
        border: none;
        border-radius: 0;
        min-height: 0;
        line-height: 1.3;
    }
    .waste-ef-form-wrap .waste-ef-category-panel.panel-default > .panel-heading.waste-ef-category-heading {
        background: #22A16D !important;
        background-color: #22A16D !important;
        padding: 4px 10px 5px;
    }
    .waste-ef-form-wrap .waste-ef-category-heading .panel-title {
        margin: 0;
        font-size: 13px;
        font-weight: normal;
        padding-bottom: 5px !important;
    }
    .waste-ef-form-wrap .waste-ef-category-heading a {
        color: #fff !important;
        display: block;
        text-decoration: none;
    }
    .waste-ef-stream-panel {
        margin-left: 15px;
    }
    .waste-ef-form-wrap .waste-ef-stream-panel.panel-default > .panel-heading.waste-ef-stream-heading {
        background: #afddca !important;
        background-color: #afddca !important;
        padding: 4px 10px 5px;
    }
    .waste-ef-form-wrap .waste-ef-stream-heading .panel-title {
        margin: 0;
        font-size: 13px;
        font-weight: normal;
        padding-bottom: 5px !important;
    }
    .waste-ef-form-wrap .waste-ef-stream-heading a {
        color: #000 !important;
        display: block;
        text-decoration: none;
    }
    .waste-ef-toggle-icon {
        margin-right: 6px;
        width: 10px;
        font-size: 11px;
        display: inline-block;
    }
    .waste-ef-category-panel .panel-body,
    .waste-ef-stream-panel .panel-body {
        padding: 6px 8px 4px;
        border: none;
    }
    .waste-ef-form-wrap .form-outer-block > li {
        padding-top: 6px;
        padding-bottom: 6px;
    }
</style>

<div class="waste-ef-form-wrap">
<?php if (!empty($groupedFactors)) { ?>
    <?php
    $categoryIndex = 0;
    foreach ($groupedFactors as $categoryLabel => $streams) {
        $categoryIndex++;
        $categoryId = 'waste-ef-category-' . $categoryIndex;
        $useStreamCards = $categoryNeedsStreamLevel($categoryLabel, $streams);
        $categoryOpen = ($categoryIndex === 1) ? ' in' : '';
    ?>
    <div class="panel panel-default waste-ef-category-panel">
        <div class="panel-heading waste-ef-category-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#<?php echo $categoryId; ?>" aria-expanded="<?php echo ($categoryIndex === 1) ? 'true' : 'false'; ?>" class="<?php echo ($categoryIndex === 1) ? '' : 'collapsed'; ?>">
                    <i class="fa fa-chevron-down waste-ef-toggle-icon"></i><?php echo htmlspecialchars($categoryLabel); ?>
                </a>
            </h4>
        </div>
        <div id="<?php echo $categoryId; ?>" class="panel-collapse collapse<?php echo $categoryOpen; ?>">
            <div class="panel-body">
                <?php if (!$useStreamCards) {
                    $streamData = reset($streams);
                ?>
                <ul class="form-outer-block">
                    <?php foreach ($streamData['rows'] as $row) {
                        $renderFactorRow($row, isset($row['typical_destination_label']) ? $row['typical_destination_label'] : '');
                    } ?>
                </ul>
                <?php } else {
                    $streamIndex = 0;
                    foreach ($streams as $streamData) {
                        $streamIndex++;
                        $streamId = 'waste-ef-stream-' . $categoryIndex . '-' . $streamIndex;
                ?>
                <div class="panel panel-default waste-ef-stream-panel">
                    <div class="panel-heading waste-ef-stream-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#<?php echo $streamId; ?>" aria-expanded="false" class="collapsed">
                                <i class="fa fa-chevron-right waste-ef-toggle-icon"></i><?php echo htmlspecialchars($streamData['label']); ?>
                            </a>
                        </h4>
                    </div>
                    <div id="<?php echo $streamId; ?>" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul class="form-outer-block">
                                <?php foreach ($streamData['rows'] as $row) {
                                    $rowLabel = isset($row['typical_destination_label']) ? $row['typical_destination_label'] : '';
                                    if (count($streams) > 1 || strcasecmp($streamData['label'], $categoryLabel) !== 0) {
                                        $rowLabel = $streamData['label'] . ' - ' . $rowLabel;
                                    }
                                    $renderFactorRow($row, $rowLabel);
                                } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } ?>
            </div>
        </div>
    </div>
    <?php } ?>
<?php } else { ?>
    <ul class="form-outer-block">
        <li>
            <label class="main-label">No emission factors found for this year.</label>
        </li>
    </ul>
<?php } ?>
</div>
<?php if ($canEdit) { ?>
<div class="form-btn-outer" style="margin-bottom:10px;">
    <a class="btn btn-secondary btn-submit" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission_scope2_3?download_template=1&year=' . $wasteEmissionYear; ?>">Download Excel Template</a>
    <a class="btn btn-secondary btn-submit" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission_scope2_3?year=' . $wasteEmissionYear; ?>">Import from Excel</a>
</div>
<?php } ?>
<?php if ($canEdit && !empty($wasteEmissionFactors)) { ?>
<div class="form-btn-outer">
    <button type="submit" name="wasteEmissionFactorsSubmit" value="1" class="btn btn-secondary btn-submit">Save Waste Factors</button>
</div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.waste-ef-form-wrap').on('shown.bs.collapse', function (e) {
            var $link = $(e.target).prev('.panel-heading').find('a');
            $link.removeClass('collapsed').find('.waste-ef-toggle-icon')
                .removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }).on('hidden.bs.collapse', function (e) {
            var $link = $(e.target).prev('.panel-heading').find('a');
            $link.addClass('collapsed').find('.waste-ef-toggle-icon')
                .removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });

        $('.waste-ef-form-wrap .panel-collapse.in').each(function () {
            $(this).prev('.panel-heading').find('.waste-ef-toggle-icon')
                .removeClass('fa-chevron-right').addClass('fa-chevron-down');
        });
        $('.waste-ef-form-wrap a.collapsed .waste-ef-toggle-icon')
            .removeClass('fa-chevron-down').addClass('fa-chevron-right');

        var wasteEfConversion = <?php echo json_encode((float) WASTE_EF_TCO2E_SHORT_TON_TO_KGCO2E_MT); ?>;
        var formatWasteEf = function (value, precision) {
            if (!isFinite(value)) {
                return '';
            }
            return parseFloat(value.toFixed(precision)).toString();
        };
        var syncWasteEfPair = function ($source, $target, convertFn, precision) {
            var raw = $.trim($source.val());
            if (raw === '') {
                $target.val('');
                return;
            }
            var numeric = parseFloat(raw);
            if (isNaN(numeric)) {
                return;
            }
            $target.val(formatWasteEf(convertFn(numeric), precision));
        };

        $('.waste-ef-form-wrap').on('input', '.waste-ef-epa-factor', function () {
            var $epa = $(this);
            var $hep = $epa.closest('.row').find('.waste-ef-hep-factor');
            syncWasteEfPair($epa, $hep, function (val) {
                return val * wasteEfConversion;
            }, 2);
        });
        $('.waste-ef-form-wrap').on('input', '.waste-ef-hep-factor', function () {
            var $hep = $(this);
            var $epa = $hep.closest('.row').find('.waste-ef-epa-factor');
            syncWasteEfPair($hep, $epa, function (val) {
                return val / wasteEfConversion;
            }, 4);
        });
    });
</script>
