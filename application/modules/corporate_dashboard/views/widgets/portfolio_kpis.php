<div class="kpis" id="kpis">
  <?php
  $kpi_definitions = array(
      'energy' => array('title' => 'Energy YoY progress', 'unit' => 'kWh/RN', 'decimals' => 2),
      'carbon' => array('title' => 'Carbon YoY progress', 'unit' => 'kgCO2e/GN', 'decimals' => 2),
      'water'  => array('title' => 'Water YoY progress', 'unit' => 'm3/GN', 'decimals' => 3)
  );
  ?>

  <?php foreach ($kpi_definitions as $metric => $definition): ?>
    <?php $kpi = $kpi_rolls[$metric]; ?>

    <div class="kpi <?= html_escape($metric); ?>">
      <div class="t"><?= html_escape($definition['title']); ?></div>

      <?php if ($kpi === null): ?>
        <div class="lab">Intensity (<?= html_escape($definition['unit']); ?>)</div>
        <div class="v">&ndash;</div>
        <div class="sub">No comparable data</div>
      <?php else: ?>
        <div class="lab">Intensity (<?= html_escape($definition['unit']); ?>)</div>
        <div class="v <?= csd_cls($kpi['yoy']); ?>">
          <?= csd_pct($kpi['yoy']); ?>
          <?= csd_arrow($kpi['yoy']); ?>
        </div>

        <div class="lab">Absolute consumption</div>
        <div class="v <?= csd_cls($kpi['tot']); ?>">
          <?= csd_pct($kpi['tot']); ?>
          <?= csd_arrow($kpi['tot']); ?>
        </div>

        <div class="sub">
          <b><?= csd_num($kpi['ci'], $definition['decimals']); ?></b>
          vs
          <b><?= csd_num($kpi['pi'], $definition['decimals']); ?></b>
          <?= html_escape($definition['unit']); ?>
          &middot;
          <?= (int) $kpi['n']; ?>
          propert<?= $kpi['n'] == 1 ? 'y' : 'ies'; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <div class="kpi rn">
    <div class="t">Activity</div>

    <div class="lab">Room nights YoY</div>
    <div class="v">
      <?= $energy_roll !== null ? csd_pct($energy_roll['den']) : '&ndash;'; ?>
      <?= $energy_roll !== null ? csd_activity_arrow($energy_roll['den']) : ''; ?>
    </div>

    <div class="lab">
      Room nights <?= html_escape($current_year); ?> YTD
    </div>
    <div class="v">
      <?= $energy_roll !== null ? $energy_roll['cd'] : '&ndash;'; ?>
    </div>

    <div class="sub">
      vs
      <b><?= $energy_roll !== null ? $energy_roll['pd'] : '&ndash;'; ?></b>
      in <?= html_escape($previous_year); ?>
    </div>
  </div>
</div>
