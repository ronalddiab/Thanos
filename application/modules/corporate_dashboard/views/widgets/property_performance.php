<div class="card">
  <h2>Property performance &mdash; YTD year-on-year intensity</h2>

  <div class="body" style="padding:0">
    <div class="scroll">
      <table id="sites">
        <thead>
          <tr>
            <?php
            $columns = array(
                'site'           => 'Property',
                'country'        => 'Country',
                'brand'          => 'Brand',
                'energy_cur'     => 'Energy YTD',
                'energy_match_i' => 'kWh/RN ' . $current_year,
                'energy_yoy'     => 'Energy YoY',
                'carbon_yoy'     => 'Carbon YoY',
                'water_yoy'      => 'Water YoY',
                'energy_perf'    => 'vs target',
                'rn_cur'         => 'Room nights'
            );
            ?>

            <?php foreach ($columns as $key => $label): ?>
              <?php
              $next_dir = ($sort_key === $key && $sort_dir === 'asc') ? 'desc' : 'asc';

              $query = $_GET;
              $query['sort'] = $key;
              $query['dir'] = $next_dir;
              $sort_url = current_url() . '?' . http_build_query($query);
              ?>

              <th class="s <?= $sort_key === $key ? ($sort_dir === 'asc' ? 'asc' : 'desc') : ''; ?>">
                <a href="<?= html_escape($sort_url); ?>" class="sort-link" data-sort="<?= html_escape($key); ?>" data-dir="<?= html_escape($next_dir); ?>">
                  <?= html_escape($label); ?>
                </a>
              </th>
            <?php endforeach; ?>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($visible_sites as $row): ?>
            <?php
            // Raw, unformatted values for client-side sorting (see
            // widgets/filters.php) - keeps sorting instant and filter-safe
            // without another server round-trip for data already on screen.
            $sort_values = array(
                'site'           => isset($row['site']) ? $row['site'] : '',
                'country'        => isset($row['country']) ? $row['country'] : '',
                'brand'          => isset($row['brand']) ? $row['brand'] : '',
                'energy_cur'     => isset($row['energy_cur']) && is_numeric($row['energy_cur']) ? $row['energy_cur'] : '',
                'energy_match_i' => isset($row['energy_match_i']) && is_numeric($row['energy_match_i']) ? $row['energy_match_i'] : '',
                'energy_yoy'     => isset($row['energy_yoy']) && is_numeric($row['energy_yoy']) ? $row['energy_yoy'] : '',
                'carbon_yoy'     => isset($row['carbon_yoy']) && is_numeric($row['carbon_yoy']) ? $row['carbon_yoy'] : '',
                'water_yoy'      => isset($row['water_yoy']) && is_numeric($row['water_yoy']) ? $row['water_yoy'] : '',
                'energy_perf'    => isset($row['energy_perf']) && is_numeric($row['energy_perf']) ? $row['energy_perf'] : '',
                'rn_cur'         => isset($row['rn_cur']) && is_numeric($row['rn_cur']) ? $row['rn_cur'] : ''
            );
            $sort_attrs = '';
            foreach ($sort_values as $sort_key_attr => $sort_value_attr) {
                $sort_attrs .= ' data-' . $sort_key_attr . '="' . html_escape($sort_value_attr) . '"';
            }
            ?>
            <tr<?= $sort_attrs; ?>>
              <td>
                <b><?= html_escape(isset($row['site']) ? $row['site'] : ''); ?></b>
                <span class="sub2"><?= html_escape(isset($row['city']) ? $row['city'] : ''); ?></span>
              </td>

              <td style="text-align:left">
                <?= html_escape(isset($row['country']) ? $row['country'] : ''); ?>
              </td>

              <td style="text-align:left">
                <?= html_escape(isset($row['brand']) ? $row['brand'] : ''); ?>
              </td>

              <td><?= csd_big(isset($row['energy_cur']) ? $row['energy_cur'] : null); ?></td>

              <td>
                <?php if (isset($row['energy_match_i']) && $row['energy_match_i'] !== null): ?>
                  <?= number_format((float) $row['energy_match_i'], 1); ?>
                <?php else: ?>
                  <span class="na">&ndash;</span>
                <?php endif; ?>
              </td>

              <td class="<?= csd_cls(isset($row['energy_yoy']) ? $row['energy_yoy'] : null); ?>">
                <?= csd_pct(isset($row['energy_yoy']) ? $row['energy_yoy'] : null); ?>
                <?= csd_arrow(isset($row['energy_yoy']) ? $row['energy_yoy'] : null); ?>
              </td>

              <td class="<?= csd_cls(isset($row['carbon_yoy']) ? $row['carbon_yoy'] : null); ?>">
                <?= csd_pct(isset($row['carbon_yoy']) ? $row['carbon_yoy'] : null); ?>
                <?= csd_arrow(isset($row['carbon_yoy']) ? $row['carbon_yoy'] : null); ?>
              </td>

              <td class="<?= csd_cls(isset($row['water_yoy']) ? $row['water_yoy'] : null); ?>">
                <?= csd_pct(isset($row['water_yoy']) ? $row['water_yoy'] : null); ?>
                <?= csd_arrow(isset($row['water_yoy']) ? $row['water_yoy'] : null); ?>
              </td>

              <td>
                <?php if (isset($row['energy_perf']) && $row['energy_perf'] !== null): ?>
                  <span class="pill <?= $row['energy_perf'] < 0 ? 'g' : 'r'; ?>">
                    <?= csd_pct($row['energy_perf']); ?>
                  </span>
                <?php else: ?>
                  <span class="pill n">n/a</span>
                <?php endif; ?>
              </td>

              <td>
                <?= csd_num(isset($row['rn_cur']) ? $row['rn_cur'] : null, 0); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <p class="foot" style="padding:12px 16px 12px">
    Green = intensity reduced versus the same period last year. Percentages compare
    like-for-like months only; a month is included only where both years reported the utility
    and its denominator.
  </p>
</div>
