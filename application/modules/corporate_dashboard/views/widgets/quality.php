<div class="note" id="dqnote">
  <?php if ($energy_roll !== null): ?>
    <b>Read the headline with care.</b>
    Across the portfolio absolute energy fell <?= csd_pct($energy_roll['tot']); ?>
    while room nights moved <?= csd_pct($energy_roll['den']); ?>,
    so intensity moved <?= csd_pct($energy_roll['yoy']); ?>.
    Like-for-like comparisons use only property-month records where both years
    have the required utility and denominator.
  <?php else: ?>
    <b>Data quality.</b>
    No comparable energy data is available for the current selection.
  <?php endif; ?>
</div>

<div class="kpis" id="dqkpis">
  <div class="kpi energy">
    <div class="t">Properties reporting</div>
    <div class="v"><?= (int) count($sites); ?></div>
    <div class="sub">of <?= (int) $total_sites; ?> in <?= html_escape($current_year); ?></div>
  </div>

  <div class="kpi carbon">
    <div class="t">Complete energy series</div>
    <div class="v">
      <?php
      $complete_energy = 0;

      foreach ($sites as $row) {
          if (isset($row['months_cur']) && (int) $row['months_cur'] >= $ytd_months) {
              $complete_energy++;
          }
      }
      ?>
      <?= (int) $complete_energy; ?>
    </div>
    <div class="sub">all <?= (int) $ytd_months; ?> reported months</div>
  </div>

  <div class="kpi water">
    <div class="t">Water coverage</div>
    <div class="v"><?= $water_roll !== null ? (int) $water_roll['n'] : 0; ?></div>
    <div class="sub">properties with comparable water</div>
  </div>

  <div class="kpi rn">
    <div class="t">Properties flagged</div>
    <div class="v"><?= (int) $flagged_count; ?></div>
    <div class="sub"><?= (int) $high_count; ?> high severity</div>
  </div>
</div>

<div class="card">
  <h2 class="red">Flagged properties</h2>

  <div class="body" style="padding:0">
    <div class="scroll">
      <table id="dqtbl">
        <thead>
          <tr>
            <th>Property</th>
            <th>Country</th>
            <th>Severity</th>
            <th>Findings</th>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($dq)): ?>
            <tr>
              <td colspan="4" class="foot">No issues detected.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($dq as $item): ?>
              <tr>
                <td>
                  <b><?= html_escape(isset($item['site']) ? $item['site'] : ''); ?></b>
                </td>

                <td style="text-align:left">
                  <?= html_escape(isset($item['country']) ? $item['country'] : ''); ?>
                </td>

                <td style="text-align:left">
                  <?php $high = isset($item['sev']) && $item['sev'] === 'high'; ?>
                  <span class="pill <?= $high ? 'r' : 'a'; ?>">
                    <?= $high ? 'High' : 'Review'; ?>
                  </span>
                </td>

                <td style="text-align:left">
                  <?php foreach ((array) $item['issues'] as $issue): ?>
                    &bull; <?= html_escape($issue); ?><br>
                  <?php endforeach; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card">
  <h2 class="teal">Reporting coverage by month</h2>

  <div class="body" style="overflow-x:auto">
    <table id="cov">
      <thead>
        <tr>
          <th>Month</th>
          <?php for ($i = 0; $i < $ytd_months && $i < 12; $i++): ?>
            <th><?= html_escape($months[$i]); ?></th>
          <?php endfor; ?>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td><b>Energy</b></td>

          <?php for ($i = 0; $i < $ytd_months && $i < 12; $i++): ?>
            <?php
            $count = 0;

            foreach ($sites as $row) {
                $site_name = isset($row['site']) ? $row['site'] : '';
                $site_series = isset($series[$site_name]) ? $series[$site_name] : array();

                if (isset($site_series['cur'][$i]) && $site_series['cur'][$i] !== null) {
                    $count++;
                }
            }
            ?>
            <td><?= (int) $count; ?></td>
          <?php endfor; ?>
        </tr>

        <tr>
          <td><b>Water</b></td>

          <?php for ($i = 0; $i < $ytd_months && $i < 12; $i++): ?>
            <td>&mdash;</td>
          <?php endfor; ?>
        </tr>
      </tbody>
    </table>

    <p class="foot">
      Number of properties reporting each utility, by month. The current model exposes
      monthly energy series; it does not expose monthly water series, so water monthly
      coverage is shown as &mdash; rather than inferred.
    </p>
  </div>
</div>
