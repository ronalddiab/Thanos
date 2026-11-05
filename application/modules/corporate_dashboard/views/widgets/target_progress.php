<div class="card">
  <h2 class="orange">Progress on portfolio reduction targets</h2>

  <div class="body" style="overflow-x:auto">
    <table id="tgt">
      <thead>
        <tr>
          <th></th>
          <th>YTD <?= html_escape($current_year); ?></th>
          <th>Base year YTD <?= html_escape($previous_year); ?></th>
          <th>YTD intensity</th>
          <th>Target YTD intensity</th>
          <th>Reduction target</th>
          <th>Performance vs target</th>
          <th>YoY progress</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $target_units = array(
            'energy' => array('total' => 'kWh', 'intensity' => 'kWh/RN', 'decimals' => 2),
            'carbon' => array('total' => 'kgCO2e', 'intensity' => 'kgCO2e/GN', 'decimals' => 2),
            'water'  => array('total' => 'm3', 'intensity' => 'm3/GN', 'decimals' => 3)
        );

        foreach ($target_units as $metric => $unit):
            $roll = $kpi_rolls[$metric];
        ?>
          <tr>
            <td><b><?= ucfirst(html_escape($metric)); ?></b></td>

            <?php if ($roll === null): ?>
              <td colspan="7" class="na">No comparable data for this selection</td>
            <?php else: ?>
              <td><?= csd_big($roll['ct']); ?> <?= html_escape($unit['total']); ?></td>
              <td><?= csd_big($roll['pt']); ?> <?= html_escape($unit['total']); ?></td>
              <td><?= csd_num($roll['ci'], $unit['decimals']); ?> <?= html_escape($unit['intensity']); ?></td>
              <td><?= csd_num($roll['tg'], $unit['decimals']); ?> <?= html_escape($unit['intensity']); ?></td>
              <td><?= number_format((float) $targets[$metric], 2); ?>%</td>
              <td class="<?= csd_cls($roll['perf']); ?>">
                <?= csd_pct($roll['perf']); ?>
                <?= csd_arrow($roll['perf']); ?>
              </td>
              <td class="<?= csd_cls($roll['yoy']); ?>">
                <?= csd_pct($roll['yoy']); ?>
                <?= csd_arrow($roll['yoy']); ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <p class="foot" id="tgtfoot">
      <?php if ($energy_roll !== null): ?>
        Absolute energy <?= csd_pct($energy_roll['tot']); ?>
        on room nights <?= csd_pct($energy_roll['den']); ?>
        &mdash; intensity moves <?= csd_pct($energy_roll['yoy']); ?>.
        Targets are applied to the <?= html_escape($previous_year); ?> base-year intensity
        (energy <?= number_format((float) $targets['energy'], 2); ?>%,
        carbon <?= number_format((float) $targets['carbon'], 2); ?>%,
        water <?= number_format((float) $targets['water'], 2); ?>%).
      <?php endif; ?>
    </p>
  </div>
</div>
