<div class="card">
  <h2 class="orange">Method &amp; factors</h2>

  <div class="body" id="method">
    <p style="margin-top:0">
      <b>Source.</b>
      <?= html_escape($source); ?>
      &mdash; <?= (int) $total_sites; ?> properties,
      <?= html_escape($period_label); ?>.
    </p>

    <p>
      <b>Intensity.</b>
      Energy is reported per room night; carbon and water are reported per guest night.
      Portfolio and regional intensity values are weighted roll-ups
      (total consumption &divide; total denominator), not averages of property ratios.
    </p>

    <p>
      <b>Like-for-like.</b>
      The model's <code>energy_mt</code>, <code>carbon_mt</code>, and
      <code>water_mt</code> objects contain current/prior totals and denominators used
      for the comparable-period calculations.
    </p>

    <p>
      <b>Targets.</b>
      Base year <?= html_escape($previous_year); ?> YTD actuals:
      energy <?= number_format((float) $targets['energy'], 2); ?>%,
      carbon <?= number_format((float) $targets['carbon'], 2); ?>%,
      water <?= number_format((float) $targets['water'], 2); ?>%.
    </p>

    <p>
      <b>Data source.</b>
      This template does not hard-code utility values or emission factors.
      It renders the values already returned by
      <code>corporate_dashboard_model-&gt;get_dashboard_data()</code>.
    </p>
  </div>
</div>
