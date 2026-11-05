<?php
/*
 * Shared line-chart renderer - Energy and Carbon trend use the exact
 * same drawing logic, just different series/unit label, so this is
 * written once and called twice instead of duplicating the SVG math.
 */
if (!function_exists('csd_render_trend_chart')) {
    function csd_render_trend_chart($current_intensity, $previous_intensity, $months, $current_year, $previous_year, $unit)
    {
        $trend_values = array_merge(
            array_filter($current_intensity, function ($v) { return $v !== null; }),
            array_filter($previous_intensity, function ($v) { return $v !== null; })
        );

        if (empty($trend_values)) {
            echo '<p class="foot">No data.</p>';
            return;
        }

        $trend_max = max($trend_values) * 1.12;
        $trend_min = 0;
        $chart_width = 660;
        $chart_height = 350;
        $padding_left = 52;
        $padding_right = 14;
        $padding_top = 14;
        $padding_bottom = 30;

        $x = function ($i) use ($chart_width, $padding_left, $padding_right) {
            return $padding_left + ($i * ($chart_width - $padding_left - $padding_right) / 11);
        };

        $y = function ($value) use ($chart_height, $padding_bottom, $padding_top, $trend_min, $trend_max) {
            if ($trend_max <= $trend_min) {
                return $chart_height - $padding_bottom;
            }

            return $chart_height - $padding_bottom
                - (($value - $trend_min) / ($trend_max - $trend_min))
                * ($chart_height - $padding_top - $padding_bottom);
        };

        $build_path = function ($values) use ($x, $y) {
            $path = '';
            $pen = false;

            foreach ($values as $i => $value) {
                if ($value === null) {
                    $pen = false;
                    continue;
                }

                $path .= ($pen ? 'L' : 'M')
                    . number_format($x($i), 1, '.', '')
                    . ' '
                    . number_format($y($value), 1, '.', '')
                    . ' ';

                $pen = true;
            }

            return trim($path);
        };
        ?>
        <div>
          <svg viewBox="0 0 <?= $chart_width; ?> <?= $chart_height; ?>" width="100%" height="<?= $chart_height; ?>">
            <?php for ($t = 0; $t <= 4; $t++): ?>
              <?php
              $value = $trend_min + (($trend_max - $trend_min) * $t / 4);
              $line_y = $y($value);
              ?>
              <line
                x1="<?= $padding_left; ?>"
                y1="<?= number_format($line_y, 1, '.', ''); ?>"
                x2="<?= $chart_width - $padding_right; ?>"
                y2="<?= number_format($line_y, 1, '.', ''); ?>"
                stroke="#eef1f3"
              ></line>
              <text
                x="<?= $padding_left - 8; ?>"
                y="<?= number_format($line_y + 4, 1, '.', ''); ?>"
                text-anchor="end"
                font-size="10.5"
                fill="#8b959e"
              ><?= number_format($value, 0); ?></text>
            <?php endfor; ?>

            <?php foreach ($months as $i => $month): ?>
              <text
                x="<?= number_format($x($i), 1, '.', ''); ?>"
                y="<?= $chart_height - 10; ?>"
                text-anchor="middle"
                font-size="10.5"
                fill="#8b959e"
              ><?= html_escape($month); ?></text>
            <?php endforeach; ?>

            <path
              d="<?= html_escape($build_path($previous_intensity)); ?>"
              fill="none"
              stroke="#c9ced3"
              stroke-width="2.2"
            ></path>

            <path
              d="<?= html_escape($build_path($current_intensity)); ?>"
              fill="none"
              stroke="#3c8dbc"
              stroke-width="2.6"
            ></path>

            <?php foreach ($current_intensity as $i => $value): ?>
              <?php if ($value !== null): ?>
                <circle
                  cx="<?= number_format($x($i), 1, '.', ''); ?>"
                  cy="<?= number_format($y($value), 1, '.', ''); ?>"
                  r="3.4"
                  fill="#3c8dbc"
                >
                  <title>
                    <?= html_escape($months[$i]); ?>
                    <?= html_escape($current_year); ?>:
                    <?= number_format($value, 1); ?> <?= html_escape($unit); ?>
                  </title>
                </circle>
              <?php endif; ?>
            <?php endforeach; ?>

            <?php foreach ($previous_intensity as $i => $value): ?>
              <?php if ($value !== null): ?>
                <circle
                  cx="<?= number_format($x($i), 1, '.', ''); ?>"
                  cy="<?= number_format($y($value), 1, '.', ''); ?>"
                  r="3"
                  fill="#c9ced3"
                >
                  <title>
                    <?= html_escape($months[$i]); ?>
                    <?= html_escape($previous_year); ?>:
                    <?= number_format($value, 1); ?> <?= html_escape($unit); ?>
                  </title>
                </circle>
              <?php endif; ?>
            <?php endforeach; ?>
          </svg>
        </div>

        <div class="legend">
          <span>
            <i style="background:#3c8dbc"></i>
            <span><?= html_escape($current_year); ?> (<?= html_escape($unit); ?>)</span>
          </span>
          <span>
            <i style="background:#c9ced3"></i>
            <span><?= html_escape($previous_year); ?> (<?= html_escape($unit); ?>)</span>
          </span>
        </div>
        <?php
    }
}
?>
<div class="grid" style="grid-template-columns:2fr 1fr">
  <div class="card">
    <div class="card-head">
      <h2 class="teal"><span class="mode-content" data-mode="energy" style="<?= $mode === 'energy' ? '' : 'display:none'; ?>">Energy</span><span class="mode-content" data-mode="carbon" style="<?= $mode === 'carbon' ? '' : 'display:none'; ?>">Carbon</span> intensity by month &mdash; <span id="trendyrs"><?= html_escape($current_year); ?> vs <?= html_escape($previous_year); ?></span></h2>
      <?= csd_mode_toggle($mode); ?>
    </div>

    <div class="body">
      <div class="mode-content" data-mode="carbon" style="<?= $mode === 'carbon' ? '' : 'display:none'; ?>">
        <?php csd_render_trend_chart($trend_carbon_current_intensity, $trend_carbon_previous_intensity, $months, $current_year, $previous_year, 'kgCO2e/GN'); ?>
      </div>
      <div class="mode-content" data-mode="energy" style="<?= $mode === 'energy' ? '' : 'display:none'; ?>">
        <?php csd_render_trend_chart($trend_current_intensity, $trend_previous_intensity, $months, $current_year, $previous_year, 'kWh/RN'); ?>
      </div>
    </div>
  </div>
