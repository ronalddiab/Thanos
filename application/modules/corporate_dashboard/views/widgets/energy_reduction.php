<?php
/*
 * Both Energy and Carbon versions of each card are rendered here; the
 * toggle (csd_mode_toggle()) just shows/hides them client-side - see
 * filters.php - so switching mode never needs another server call.
 */
?>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(390px,1fr))">
  <div class="card">
    <div class="card-head">
      <h2 class="green">Properties with the highest <span class="mode-content" data-mode="energy" style="<?= $mode === 'energy' ? '' : 'display:none'; ?>">energy</span><span class="mode-content" data-mode="carbon" style="<?= $mode === 'carbon' ? '' : 'display:none'; ?>">carbon</span> reduction</h2>
      <?= csd_mode_toggle($mode); ?>
    </div>

    <div class="body">
      <?php foreach ($rank_sets as $rank_mode => $rank_set): ?>
        <div class="mode-content" data-mode="<?= html_escape($rank_mode); ?>" style="<?= $rank_mode === $mode ? '' : 'display:none'; ?>">
          <?php if (empty($rank_set['best'])): ?>
            <p class="foot">No comparable properties in this selection.</p>
          <?php else: ?>
            <?php foreach ($rank_set['best'] as $row): ?>
              <?php $width = abs((float) $row[$rank_set['metric']]) / $rank_set['max'] * 100; ?>

              <div class="rank">
                <div class="nm">
                  <?= html_escape($row['site']); ?>
                  <small>
                    <?= html_escape($row['city']); ?>,
                    <?= html_escape($row['country']); ?>
                  </small>
                </div>

                <div class="track">
                  <div
                    class="fill"
                    style="width:<?= number_format($width, 2, '.', ''); ?>%;background:#7fb069"
                  ></div>
                </div>

                <div class="pct <?= csd_cls($row[$rank_set['metric']]); ?>">
                  <?= csd_pct($row[$rank_set['metric']]); ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <h2 class="red">Properties with the lowest <span class="mode-content" data-mode="energy" style="<?= $mode === 'energy' ? '' : 'display:none'; ?>">energy</span><span class="mode-content" data-mode="carbon" style="<?= $mode === 'carbon' ? '' : 'display:none'; ?>">carbon</span> reduction</h2>
      <?= csd_mode_toggle($mode); ?>
    </div>

    <div class="body">
      <?php foreach ($rank_sets as $rank_mode => $rank_set): ?>
        <div class="mode-content" data-mode="<?= html_escape($rank_mode); ?>" style="<?= $rank_mode === $mode ? '' : 'display:none'; ?>">
          <?php if (empty($rank_set['worst'])): ?>
            <p class="foot">No comparable properties in this selection.</p>
          <?php else: ?>
            <?php foreach ($rank_set['worst'] as $row): ?>
              <?php $width = abs((float) $row[$rank_set['metric']]) / $rank_set['max'] * 100; ?>

              <div class="rank">
                <div class="nm">
                  <?= html_escape($row['site']); ?>
                  <small>
                    <?= html_escape($row['city']); ?>,
                    <?= html_escape($row['country']); ?>
                  </small>
                </div>

                <div class="track">
                  <div
                    class="fill"
                    style="width:<?= number_format($width, 2, '.', ''); ?>%;background:#e08a7d"
                  ></div>
                </div>

                <div class="pct <?= csd_cls($row[$rank_set['metric']]); ?>">
                  <?= csd_pct($row[$rank_set['metric']]); ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
