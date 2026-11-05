<div class="card">
  <h2 class="teal">Regional performance</h2>

  <div class="body" style="overflow-x:auto">
    <table id="regions">
      <thead>
        <tr>
          <th>Region</th>
          <th>Properties</th>
          <th>Energy YTD</th>
          <th><?= html_escape($current_year); ?> kWh/RN</th>
          <th><?= html_escape($previous_year); ?> kWh/RN</th>
          <th>YoY</th>
          <th>Carbon YoY</th>
          <th>Water YoY</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($regions as $region_name => $region_rows): ?>
          <?php
          $region_energy = csd_roll($region_rows, 'energy', $targets);
          $region_carbon = csd_roll($region_rows, 'carbon', $targets);
          $region_water = csd_roll($region_rows, 'water', $targets);
          ?>

          <?php if ($region_energy === null): continue; endif; ?>

          <tr>
            <td><b><?= html_escape($region_name); ?></b></td>
            <td><?= (int) $region_energy['n']; ?></td>
            <td><?= csd_big($region_energy['ct']); ?> kWh</td>
            <td><?= csd_num($region_energy['ci'], 1); ?></td>
            <td><?= csd_num($region_energy['pi'], 1); ?></td>

            <td class="<?= csd_cls($region_energy['yoy']); ?>">
              <?= csd_pct($region_energy['yoy']); ?>
              <?= csd_arrow($region_energy['yoy']); ?>
            </td>

            <td class="<?= csd_cls($region_carbon !== null ? $region_carbon['yoy'] : null); ?>">
              <?= $region_carbon !== null ? csd_pct($region_carbon['yoy']) : '&ndash;'; ?>
            </td>

            <td class="<?= csd_cls($region_water !== null ? $region_water['yoy'] : null); ?>">
              <?= $region_water !== null ? csd_pct($region_water['yoy']) : '&ndash;'; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
