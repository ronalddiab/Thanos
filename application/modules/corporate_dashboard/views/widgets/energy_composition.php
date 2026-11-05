<?php
/*
 * Shared donut renderer - Energy and Carbon composition use the exact
 * same drawing logic, just different totals/unit label, so this is
 * written once and called twice instead of duplicating the SVG math.
 */
if (!function_exists('csd_render_mix_donut')) {
    function csd_render_mix_donut($totals, $grand_total, $labels, $colors, $center_unit)
    {
        if ($grand_total <= 0) {
            echo '<p class="foot">No data.</p>';
            return;
        }

        $cx = 88;
        $cy = 88;
        $radius = 64;
        $stroke_width = 26;
        $angle = -M_PI / 2;
        ?>
        <svg viewBox="0 0 176 176" width="250" height="250" style="margin:0 auto 8px">
          <?php foreach ($totals as $key => $value): ?>
            <?php if ($value <= 0): continue; endif; ?>

            <?php
            $fraction = $value / $grand_total;
            $next_angle = $angle + ($fraction * M_PI * 2);

            $x1 = $cx + $radius * cos($angle);
            $y1 = $cy + $radius * sin($angle);
            $x2 = $cx + $radius * cos($next_angle);
            $y2 = $cy + $radius * sin($next_angle);

            $large_arc = $fraction > 0.5 ? 1 : 0;
            $color = $colors[$key];
            ?>

            <?php if ($fraction > 0.999): ?>
              <circle
                cx="<?= $cx; ?>"
                cy="<?= $cy; ?>"
                r="<?= $radius; ?>"
                fill="none"
                stroke="<?= html_escape($color); ?>"
                stroke-width="<?= $stroke_width; ?>"
              >
                <title>
                  <?= html_escape($labels[$key]); ?>:
                  <?= number_format($fraction * 100, 1); ?>%
                </title>
              </circle>
            <?php else: ?>
              <path
                d="M <?= number_format($x1, 2, '.', ''); ?> <?= number_format($y1, 2, '.', ''); ?>
                   A <?= $radius; ?> <?= $radius; ?> 0 <?= $large_arc; ?> 1
                   <?= number_format($x2, 2, '.', ''); ?> <?= number_format($y2, 2, '.', ''); ?>"
                fill="none"
                stroke="<?= html_escape($color); ?>"
                stroke-width="<?= $stroke_width; ?>"
              >
                <title>
                  <?= html_escape($labels[$key]); ?>:
                  <?= number_format($fraction * 100, 1); ?>%
                </title>
              </path>
            <?php endif; ?>

            <?php $angle = $next_angle; ?>
          <?php endforeach; ?>

          <text x="88" y="84" text-anchor="middle" font-size="17" font-weight="600" fill="#333">
            <?= csd_big($grand_total); ?>
          </text>
          <text x="88" y="99" text-anchor="middle" font-size="10.5" fill="#8b959e">
            <?= html_escape($center_unit); ?>
          </text>
        </svg>

        <?php foreach ($totals as $key => $value): ?>
          <?php if ($value <= 0): continue; endif; ?>

          <div style="display:flex;justify-content:space-between;font-size:12.5px;padding:3px 0">
            <span>
              <i style="display:inline-block;width:10px;height:10px;background:<?= html_escape($colors[$key]); ?>;border-radius:2px;margin-right:6px"></i>
              <?= html_escape($labels[$key]); ?>
            </span>
            <b><?= number_format(($value / $grand_total) * 100, 1); ?>%</b>
          </div>
        <?php endforeach;
    }
}

$mix_colors = array(
    'elec' => '#3c8dbc',
    'dc'   => '#00a6b3',
    'lpg'  => '#f39c12',
    'fo'   => '#dd4b39',
    'ng'   => '#8e6bb5',
    'dh'   => '#7fb069'
);
?>

  <div class="card">
    <div class="card-head">
      <h2 class="orange"><span class="mode-content" data-mode="energy" style="<?= $mode === 'energy' ? '' : 'display:none'; ?>">Energy</span><span class="mode-content" data-mode="carbon" style="<?= $mode === 'carbon' ? '' : 'display:none'; ?>">Carbon</span> composition</h2>
      <?= csd_mode_toggle($mode); ?>
    </div>

    <div class="body" id="mix">
      <div class="mode-content" data-mode="carbon" style="<?= $mode === 'carbon' ? '' : 'display:none'; ?>">
        <?php csd_render_mix_donut($carbon_mix_totals, $carbon_mix_total, $mix_labels, $mix_colors, 'kgCO2e YTD'); ?>
      </div>
      <div class="mode-content" data-mode="energy" style="<?= $mode === 'energy' ? '' : 'display:none'; ?>">
        <?php csd_render_mix_donut($mix_totals, $mix_total, $mix_labels, $mix_colors, 'kWh YTD'); ?>
      </div>
    </div>
  </div>
</div>
