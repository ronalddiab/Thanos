<div class="csd-root" id="corporate-sustainability-dashboard">
  <style>
    .content-wrap{
      padding: unset;
    }
    .csd-root {
      --blue: #3d8ebf;
      --orange: #f0a030;
      --green: #5cb85c;
      --red: #e8544a;
      --teal: #2f9c9c;
      --ink: #4a4a4a;
      --muted: #9a9a9a;
      --line: #ececec;
      --bg: #e9e9e9;
      --shadow: 0 1px 4px rgba(0, 0, 0, .13);
    }

    .csd-root * {
      box-sizing: border-box
    }

    .csd-root {
      margin: 0;
      background: var(--bg);
      color: var(--ink);
      font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
      font-size: 16px;
      line-height: 1.5
    }

    .csd-root a {
      color: var(--blue);
      text-decoration: none
    }

    /* ---------- top bar / breadcrumb ---------- */
    .csd-root .topbar {
      background: #fff;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px
    }

    .csd-root .brand {
      display: flex;
      align-items: center;
      gap: 14px
    }

    .csd-root .mark {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      background: #fff;
      border: 2px solid var(--blue);
      color: var(--blue);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 19px;
      font-family: Georgia, serif
    }

    .csd-root .siteImage{
      height: unset;
      width: unset;
    }

    .csd-root .brand h1 {
      margin: 0;
      display: none;
      font-size: 17.5px;
      font-weight: 700;
      letter-spacing: .6px;
      color: #5a5a5a
    }

    .csd-root .brand span {
      display: block;
      font-size: 18px;
      color: var(--muted);
      font-weight: 400;
      letter-spacing: .2px
    }

    .csd-root .asof {
      font-size: 16px;
      color: #6a6a6a
    }

    .csd-root .crumbs {
      background: #f4f4f4;
      border-top: 1px solid #e2e2e2;
      border-bottom: 1px solid #e2e2e2;
      padding: 8px 24px;
      font-size: 15px;
      color: var(--muted)
    }

    .csd-root .crumbs b {
      color: #5a5a5a;
      font-weight: 400
    }

    /* ---------- tabs ---------- */
    .csd-root .tabs {
      background: #fff;
      padding: 0 24px;
      display: flex;
      gap: 0;
      border-bottom: 1px solid #e2e2e2
    }

    .csd-root .tab {
      padding: 11px 22px;
      font-size: 16px;
      cursor: pointer;
      color: var(--muted);
      border-bottom: 3px solid transparent
    }

    .csd-root .tab.on {
      color: var(--blue);
      border-bottom-color: var(--blue)
    }

    .csd-root .tab:hover {
      color: var(--blue)
    }

    .csd-root .wrap {
      padding: 20px 24px 60px;
      max-width: auto;
      margin: 0 0
    }

    .csd-root .period {
      font-style: italic;
      color: #8a8a8a;
      font-size: 15px;
      margin: 0 0 14px
    }

    /* ---------- cards (Rotana style: white, rounded, soft shadow, coloured title) ---------- */
    .csd-root .card {
      background: #fff;
      border-radius: 6px;
      margin-bottom: 20px;
      box-shadow: var(--shadow)
    }

    .csd-root .card>h2,
    .csd-root .card-head>h2 {
      margin: 0;
      padding: 15px 20px 10px;
      font-size: 18px;
      font-weight: 400;
      color: var(--blue)
    }

    .csd-root .card>h2.orange,
    .csd-root .card-head>h2.orange {
      color: var(--orange)
    }

    .card>h2.red,
    .card-head>h2.red {
      color: var(--red)
    }

    .csd-root .card>h2.green,
    .csd-root .card-head>h2.green {
      color: var(--green)
    }

    .card>h2.teal,
    .card-head>h2.teal {
      color: var(--teal)
    }

    .csd-root .card>h2+.body,
    .csd-root .card-head+.body {
      padding-top: 4px
    }

    /*
     * ---------- per-card Energy/Carbon toggle (header row) ----------
     * .card-head wraps the same <h2> the plain .card>h2 rules above
     * already style - it only adds the flex row and strips the h2's
     * own padding (moved onto .card-head itself) so the toggle can
     * sit beside the title without double padding.
     */
    .csd-root .card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      padding: 15px 20px 10px;
    }

    .csd-root .card-head>h2 {
      padding: 0;
      flex: 1;
      min-width: 0;
      white-space: normal;
    }

    /*
     * A global reset rule (h1 span, h2 span, ... { display: block })
     * forces every <span> inside a heading onto its own line - that
     * was the real cause of the broken title, not text wrapping.
     * Override it specifically for our mode-switch spans.
     */
    .csd-root h2 span.mode-content {
      display: inline;
    }

    .csd-root .mode-toggle {
      display: inline-flex;
      flex: none;
      border: 1px solid #e3e6e4;
      border-radius: 7px;
      overflow: hidden;
    }

    .csd-root .mode-toggle button {
      border: none;
      background: none;
      padding: 5px 13px;
      font-size: 12px;
      font-weight: 600;
      color: var(--muted);
      cursor: pointer;
      font-family: inherit;
    }

    .csd-root .mode-toggle button+button {
      border-left: 1px solid #e3e6e4;
    }

    .csd-root .mode-toggle button.on {
      background: #1f3864;
      color: #fff;
    }

    .csd-root .card .body {
      padding: 8px 20px 18px
    }

    .csd-root .card .rule {
      height: 1px;
      background: #efefef;
      margin: 0 20px
    }

    .csd-root .grid {
      display: grid;
      gap: 20px
    }

    /* ---------- filters ---------- */
    .csd-root .filters {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 5px;
      align-items: end
    }

    .csd-root .f label {
      display: block;
      font-size: 14.5px;
      color: var(--muted);
      margin-bottom: 5px
    }

    .csd-root .f select,
    .csd-root .f input {
      width: 100%;
      padding: 6px 9px;
      border: 1px solid #d8d8d8;
      border-radius: 4px;
      background: #fff;
      font-size: 15.5px;
      font-family: inherit;
      color: var(--ink);
      height: 34px
    }

    .csd-root .f select:focus,
    .csd-root .f input:focus {
      outline: none;
      border-color: var(--blue)
    }

    .csd-root .btn {
      padding: 6px 15px;
      border: 1px solid #d8d8d8;
      background: #fff;
      border-radius: 4px;
      cursor: pointer;
      font-size: 15.5px;
      font-family: inherit;
      height: 34px;
      color: #6a6a6a
    }

    .csd-root .btn:hover {
      border-color: var(--blue);
      color: var(--blue)
    }

    .csd-root .btn.primary {
      background: var(--blue);
      border-color: var(--blue);
      color: #fff
    }

    .csd-root .btn.primary:hover {
      background: #347ba6;
      color: #fff
    }

    /* ---------- KPI cards ---------- */
    .csd-root .kpis {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(238px, 1fr));
      gap: 20px;
      margin-bottom: 20px
    }

    .csd-root .kpi {
      background: #fff;
      border-radius: 6px;
      box-shadow: var(--shadow);
      padding: 15px 20px 17px
    }

    .csd-root .kpi .t {
      font-size: 15.5px;
      font-weight: 400;
      margin-bottom: 10px
    }

    .csd-root .kpi.energy .t {
      color: var(--blue)
    }

    .kpi.carbon .t {
      color: var(--teal)
    }

    .csd-root .kpi.water .t {
      color: var(--blue)
    }

    .kpi.rn .t {
      color: var(--orange)
    }

    .csd-root .kpi .lab {
      font-size: 14.5px;
      color: var(--muted);
      margin-top: 7px
    }

    .csd-root .kpi .v {
      font-size: 23px;
      font-weight: 400;
      line-height: 1.3;
      display: flex;
      align-items: center;
      gap: 8px
    }

    .kpi.energy .v {
      color: var(--blue)
    }

    .kpi.carbon .v {
      color: var(--teal)
    }

    .csd-root .kpi.water .v {
      color: var(--blue)
    }

    .kpi.rn .v {
      color: var(--orange)
    }

    .csd-root .kpi .v.pos {
      color: var(--red)
    }

    .kpi .v.neg {
      color: var(--green)
    }

    .csd-root .kpi .sub {
      margin-top: 11px;
      font-size: 14.5px;
      color: var(--muted)
    }

    .csd-root .kpi .sub b {
      color: #6a6a6a;
      font-weight: 700
    }

    /* ---------- tables ---------- */
    .csd-root table {
      width: 100%;
      border-collapse: collapse;
      font-size: 18px
    }

    .csd-root th,
    .csd-root td {
      padding: 8px 10px;
      text-align: right;
      border-bottom: 1px solid #f0f0f0
    }

    .csd-root th {
      color: var(--muted);
      font-weight: 400;
      font-size: 14.5px;
      white-space: nowrap;
      border-bottom: 1px solid #e4e4e4;
      background: #fff
    }

    .csd-root th:first-child,
    .csd-root td:first-child {
      text-align: left
    }

    .csd-root th.s {
      cursor: pointer;
      user-select: none
    }

    th.s:hover {
      color: var(--blue)
    }

    .csd-root th.s::after {
      content: "\2195";
      opacity: .35;
      margin-left: 4px;
      font-size: 10px
    }

    .csd-root th.s.asc::after {
      content: "\2191";
      opacity: .95
    }

    th.s.desc::after {
      content: "\2193";
      opacity: .95
    }

    .csd-root tbody tr:hover {
      background: #f8fbfd
    }

    .csd-root .sub2 {
      font-size: 14px;
      color: var(--muted);
      display: block
    }

    .csd-root .scroll {
      max-height: 620px;
      overflow: auto
    }

    .scroll thead th {
      position: sticky;
      top: 0;
      z-index: 2
    }

    .csd-root .good {
      color: var(--green) !important
    }

    .bad {
      color: var(--red) !important
    }

    .na {
      color: #c4c4c4
    }

    .csd-root .pill {
      display: inline-block;
      padding: 2px 9px;
      border-radius: 11px;
      font-size: 14px
    }

    .csd-root .pill.g {
      background: #eaf6ea;
      color: #3f9142
    }

    .pill.r {
      background: #fdeceb;
      color: #c8433a
    }

    .csd-root .pill.a {
      background: #fdf3e3;
      color: #b3801f
    }

    .pill.n {
      background: #f2f2f2;
      color: #8a8a8a
    }

    /* ---------- ranking bars ---------- */
    .csd-root .rank {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 7px 0;
      border-bottom: 1px solid #f4f4f4
    }

    .csd-root .rank:last-child {
      border-bottom: none
    }

    .csd-root .rank .nm {
      flex: 0 0 44%;
      font-size: 15.5px;
      line-height: 1.3;
      color: #5a5a5a
    }

    .csd-root .rank .nm small {
      color: var(--muted);
      display: block;
      font-size: 11px
    }

    .csd-root .rank .track {
      flex: 1;
      height: 20px;
      background: #f4f4f4;
      border-radius: 3px;
      position: relative
    }

    .csd-root .rank .fill {
      position: absolute;
      top: 0;
      bottom: 0;
      border-radius: 3px
    }

    .csd-root .rank .pct {
      flex: 0 0 68px;
      text-align: right;
      font-size: 13px
    }

    /* ---------- charts ---------- */
    .csd-root .legend {
      display: flex;
      gap: 18px;
      flex-wrap: wrap;
      font-size: 12px;
      color: var(--muted);
      margin-top: 10px
    }

    .csd-root .legend i {
      display: inline-block;
      width: 11px;
      height: 11px;
      border-radius: 2px;
      margin-right: 6px;
      vertical-align: -1px
    }

    .csd-root svg {
      display: block;
      max-width: 100%
    }

    .csd-root .note {
      background: #fff;
      border-radius: 6px;
      box-shadow: var(--shadow);
      border-left: 4px solid var(--orange);
      padding: 14px 18px;
      font-size: 13px;
      margin-bottom: 20px;
      color: #6a6a6a
    }

    .csd-root .note b {
      color: var(--orange);
      font-weight: 400
    }

    .csd-root .arr {
      display: inline-block;
      vertical-align: -2px;
      margin-left: 4px
    }

    .csd-root .foot {
      color: var(--muted);
      font-size: 14.5px;
      padding: 8px 2px 0;
      margin: 0
    }

    @media(max-width:780px) {
      .wrap {
        padding: 14px 12px 50px
      }

      .topbar {
        padding: 12px 14px
      }

      .csd-root .crumbs,
      .csd-root .tabs {
        padding-left: 14px;
        padding-right: 14px
      }
    }
  </style>
  <?php
include('widgets/_helpers.php');
?>
  <div class="tabs">
    <div class="tab on" data-tab="summary">Utilities and Carbon</div>
    <div class="tab" data-tab="quality" style="display: none;">Data Quality</div>
  </div>
  <div class="wrap">
    <!-- ============ SUMMARY ============ -->
    <!-- <div id="tab-summary">
      <p class="period" id="periodlbl"></p>
      <div class="card">
        <div class="body">
          <div class="filters">
            <div class="f"><label>Region</label><select id="fRegion"></select></div>
            <div class="f"><label>Country</label><select id="fCountry"></select></div>
            <div class="f"><label>Brand</label><select id="fBrand"></select></div>
            <div class="f"><label>Site</label><select id="fSite"></select></div>
            <div class="f"><label>Search</label><input id="fSearch" placeholder="Hotel name…" /></div>
            <div class="f" style="display:flex;gap:8px">
              <button class="btn" id="reset">Reset</button>
              <button class="btn primary" id="csv">Export CSV</button>
            </div>
          </div>
        </div>
      </div>
      <div class="kpis" id="kpis"></div>
      <div class="card">
        <h2 class="orange">Progress on portfolio reduction targets</h2>
        <div class="body" style="overflow-x:auto">
          <table id="tgt"></table>
          <p class="foot" id="tgtfoot"></p>
        </div>
      </div>
      <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(390px,1fr))">
        <div class="card">
          <h2 class="green">Properties with the highest energy reduction</h2>
          <div class="body">
            <div id="best"></div>
          </div>
        </div>
        <div class="card">
          <h2 class="red">Properties with the lowest energy reduction</h2>
          <div class="body">
            <div id="worst"></div>
          </div>
        </div>
      </div>
      <div class="grid" style="grid-template-columns:2fr 1fr">
        <div class="card">
          <h2 class="teal">Energy intensity by month — <span id="trendyrs"></span></h2>
          <div class="body">
            <div id="trend"></div>
            <div class="legend">
              <span><i style="background:#3c8dbc"></i><span id="lg1"></span></span>
              <span><i style="background:#c9ced3"></i><span id="lg2"></span></span>
            </div>
          </div>
        </div>
        <div class="card">
          <h2 class="orange">Energy composition</h2>
          <div class="body">
            <div id="mix"></div>
          </div>
        </div>
      </div>
      <div class="card">
        <h2 class="teal">Regional performance</h2>
        <div class="body" style="overflow-x:auto">
          <table id="regions"></table>
        </div>
      </div>
      <div class="card">
        <h2>Property performance — YTD year-on-year intensity</h2>
        <div class="body" style="padding:0">
          <div class="scroll">
            <table id="sites"></table>
          </div>
        </div>
        <p class="foot" style="padding:8px 16px 12px">
          Green = intensity reduced versus the same period last year. Percentages compare
          like-for-like months only; a month is included only where both years reported the utility
          and its denominator.</p>
      </div>
    </div> -->
    <!-- ============ QUALITY ============ -->
    <!-- <div id="tab-quality" style="display:none">
      <div class="note" id="dqnote"></div>
      <div class="kpis" id="dqkpis"></div>
      <div class="card">
        <h2 class="red">Flagged properties</h2>
        <div class="body" style="padding:0">
          <div class="scroll">
            <table id="dqtbl"></table>
          </div>
        </div>
      </div>
      <div class="card">
        <h2 class="teal">Reporting coverage by month</h2>
        <div class="body" style="overflow-x:auto">
          <table id="cov"></table>
          <p class="foot">Number of properties reporting each utility, by month. Gaps here are the
            most common cause of a property dropping out of the year-on-year comparison.</p>
        </div>
      </div>
      <div class="card">
        <h2 class="orange">Method &amp; factors</h2>
        <div class="body" id="method"></div>
      </div>
    </div> -->

    <div id="tab-summary">
      <p class="period">
        Data compared against <?= html_escape($period_label); ?>
        &nbsp;&middot;&nbsp; <?= (int) $total_sites; ?> properties
      </p>

      <?php include('widgets/filters.php'); ?>
      <div id="dashboard-content">
          <?php include('widgets/portfolio_kpis.php'); ?>
          <?php include('widgets/target_progress.php'); ?>
          <?php include('widgets/energy_reduction.php'); ?>
          <?php include('widgets/energy_trend.php'); ?>
          <?php include('widgets/energy_composition.php'); ?>
          <?php include('widgets/regional_performance.php'); ?>
          <?php include('widgets/property_performance.php'); ?>
      </div>
    </div>

    <div id="tab-quality" style="display:none">
      <?php include('widgets/quality.php'); ?>
      <?php include('widgets/method.php'); ?>
    </div>
  </div>



  <!-- <script>
    const D = <?php echo json_encode($dashboard_json); ?>;
    const M = D.meta,
      MON = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const UNIT = {
      energy: 'kWh/RN',
      carbon: 'kgCO2e/GN',
      water: 'm3/GN'
    };
    const LBL = {
      energy: 'Energy',
      carbon: 'Carbon',
      water: 'Water'
    };
    const TOTU = {
      energy: 'kWh',
      carbon: 'kgCO2e',
      water: 'm3'
    };

    const fmt = (n, d = 0) => n == null || isNaN(n) ? '&ndash;' :
      n.toLocaleString('en-US', {
        minimumFractionDigits: d,
        maximumFractionDigits: d
      });
    const pct = n => n == null || isNaN(n) ? '&ndash;' : (n >= 0 ? '+' : '') + n.toFixed(2) + '%';

    function big(n) {
      if (n == null || isNaN(n)) return '&ndash;';
      const a = Math.abs(n);
      if (a >= 1e9) return (n / 1e9).toFixed(2) + 'bn';
      if (a >= 1e6) return (n / 1e6).toFixed(2) + 'm';
      if (a >= 1e3) return (n / 1e3).toFixed(1) + 'k';
      return n.toFixed(0);
    }
    /* Solid Rotana-style arrow: green down = improving, red up = worsening. */
    function arrowSvg(n, size, invert) {
      if (n == null) return '';
      const s = size || 15,
        w = s * 0.72;
      const good = invert ? n > 0 : n < 0; // for activity metrics, up is the good direction
      const col = good ? '#5cb85c' : '#e8544a';
      const d = n < 0 ? 'M4 0h4v9h3.2L6 16 1 9h3z' : 'M4 16h4V7h3.2L6 0 1 7h3z';
      return `<svg class="arr" width="${w}" height="${s}" viewBox="0 0 12 16" aria-hidden="true">
            <path d="${d}" fill="${col}"/></svg>`;
    }
    const arrow = n => arrowSvg(n, 13);
    const cls = n => n == null ? 'na' : (n < 0 ? 'good' : 'bad');

    /* ---------------- filter state ---------------- */
    const S = {
      region: '',
      country: '',
      brand: '',
      site: '',
      q: ''
    };

    function visible() {
      return D.sites.filter(r =>
        (!S.region || r.region === S.region) &&
        (!S.country || r.country === S.country) &&
        (!S.brand || r.brand === S.brand) &&
        (!S.site || r.site === S.site) &&
        (!S.q || r.site.toLowerCase().includes(S.q.toLowerCase())));
    }

    /* Re-aggregate a metric over the visible subset using matched-month totals.
       Intensity is total/total (a weighted roll-up), never a mean of ratios. */
    function roll(rows, m) {
      let ct = 0,
        pt = 0,
        cd = 0,
        pd = 0,
        n = 0;
      rows.forEach(r => {
        const t = r[m + '_mt'];
        if (t && t.cd > 0 && t.pd > 0) {
          ct += t.ct;
          pt += t.pt;
          cd += t.cd;
          pd += t.pd;
          n++;
        }
      });
      if (!n) return null;
      const ci = ct / cd,
        pi = pt / pd,
        tg = pi * (1 + M.targets[m] / 100);
      return {
        ci,
        pi,
        tg,
        ct,
        pt,
        cd,
        pd,
        n,
        yoy: (ci / pi - 1) * 100,
        perf: (ci / tg - 1) * 100,
        tot: (ct / pt - 1) * 100,
        den: (cd / pd - 1) * 100
      };
    }

    /* ---------------- KPI tiles ---------------- */
    function drawKpis(rows) {
      let h = '';
      ['energy', 'carbon', 'water'].forEach(m => {
        const k = roll(rows, m),
          dp = m === 'water' ? 3 : 2;
        if (!k) {
          h += `<div class="kpi ${m}"><div class="t">${LBL[m]} YoY progress</div>
      <div class="lab">Intensity</div><div class="v">&ndash;</div>
      <div class="sub">No comparable data</div></div>`;
          return;
        }
        h += `<div class="kpi ${m}">
      <div class="t">${LBL[m]} YoY progress</div>
      <div class="lab">Intensity (${UNIT[m]})</div>
      <div class="v ${k.yoy<0?'neg':'pos'}">${pct(k.yoy)}${arrowSvg(k.yoy,20)}</div>
      <div class="lab">Absolute consumption</div>
      <div class="v ${k.tot<0?'neg':'pos'}">${pct(k.tot)}${arrowSvg(k.tot,20)}</div>
      <div class="sub"><b>${k.ci.toFixed(dp)}</b> vs <b>${k.pi.toFixed(dp)}</b> ${UNIT[m]}
        &middot; ${k.n} propert${k.n===1?'y':'ies'}</div></div>`;
      });
      const e = roll(rows, 'energy');
      h += `<div class="kpi rn"><div class="t">Activity</div>
    <div class="lab">Room nights YoY</div>
    <div class="v">${e?pct(e.den):'&ndash;'}${e?arrowSvg(e.den,20,true):''}</div>
    <div class="lab">Room nights ${M.cur_year} YTD</div>
    <div class="v">${e?fmt(e.cd):'&ndash;'}</div>
    <div class="sub">vs <b>${e?fmt(e.pd):'&ndash;'}</b> in ${M.prior_year}</div></div>`;
      document.getElementById('kpis').innerHTML = h;
    }

    /* ---------------- reduction-target table ---------------- */
    function drawTarget(rows) {
      let h = `<thead><tr><th></th><th>YTD ${M.cur_year}</th><th>Base year YTD ${M.prior_year}</th>
    <th>YTD intensity</th><th>Target YTD intensity</th><th>Reduction target</th>
    <th>Performance vs target</th><th>YoY progress</th></tr></thead><tbody>`;
      ['energy', 'carbon', 'water'].forEach(m => {
        const k = roll(rows, m);
        const dp = m === 'water' ? 3 : 2;
        h += `<tr><td><b>${LBL[m]}</b></td>`;
        if (!k) {
          h += `<td colspan="7" class="na">No comparable data for this selection</td></tr>`;
          return;
        }
        h += `<td>${big(k.ct)} ${TOTU[m]}</td><td>${big(k.pt)} ${TOTU[m]}</td>
      <td>${k.ci.toFixed(dp)} ${UNIT[m]}</td><td>${k.tg.toFixed(dp)} ${UNIT[m]}</td>
      <td>${M.targets[m].toFixed(2)}%</td>
      <td class="${cls(k.perf)}">${pct(k.perf)} ${arrow(k.perf)}</td>
      <td class="${cls(k.yoy)}">${pct(k.yoy)} ${arrow(k.yoy)}</td></tr>`;
      });
      document.getElementById('tgt').innerHTML = h + '</tbody>';
      const e = roll(rows, 'energy');
      document.getElementById('tgtfoot').innerHTML = e ?
        `Absolute energy ${pct(e.tot)} on room nights ${pct(e.den)} &mdash; intensity moves ${pct(e.yoy)}.
       Targets are applied to the ${M.prior_year} base-year intensity (energy &minus;2.00%,
       carbon &minus;3.00%, water 0.00%).` : '';
    }

    /* ---------------- ranking panels ---------------- */
    function drawRanks(rows) {
      const r = rows.filter(x => x.energy_yoy != null).sort((a, b) => a.energy_yoy - b.energy_yoy);
      const max = Math.max(1, ...r.map(x => Math.abs(x.energy_yoy)));
      const bar = (list, colour) => list.map(x => {
        const w = Math.abs(x.energy_yoy) / max * 100;
        return `<div class="rank"><div class="nm">${x.site}<small>${x.city}, ${x.country}</small></div>
      <div class="track"><div class="fill" style="width:${w}%;background:${colour}"></div></div>
      <div class="pct ${cls(x.energy_yoy)}">${pct(x.energy_yoy)}</div></div>`;
      }).join('') || '<p class="foot">No comparable properties in this selection.</p>';
      document.getElementById('best').innerHTML = bar(r.slice(0, 5), '#7fb069');
      document.getElementById('worst').innerHTML = bar(r.slice(-5).reverse(), '#e08a7d');
    }

    /* ---------------- monthly trend (inline SVG) ---------------- */
    function drawTrend(rows) {
      const cur = Array(12).fill(0),
        pri = Array(12).fill(0),
        rc = Array(12).fill(0),
        rp = Array(12).fill(0);
      rows.forEach(r => {
        const s = D.series[r.site];
        if (!s) return;
        for (let i = 0; i < 12; i++) {
          if (s.cur[i] != null && s.rn_cur[i] > 0) {
            cur[i] += s.cur[i];
            rc[i] += s.rn_cur[i];
          }
          if (s.pri[i] != null && s.rn_pri[i] > 0) {
            pri[i] += s.pri[i];
            rp[i] += s.rn_pri[i];
          }
        }
      });
      const a = cur.map((v, i) => rc[i] > 0 ? v / rc[i] : null);
      const b = pri.map((v, i) => rp[i] > 0 ? v / rp[i] : null);
      const all = [...a, ...b].filter(v => v != null);
      if (!all.length) {
        document.getElementById('trend').innerHTML = '<p class="foot">No data.</p>';
        return;
      }
      const W = 660,
        H = 270,
        P = {
          l: 52,
          r: 14,
          t: 14,
          b: 30
        };
      const hi = Math.max(...all) * 1.12,
        lo = 0;
      const X = i => P.l + (i * (W - P.l - P.r) / 11);
      const Y = v => H - P.b - ((v - lo) / (hi - lo)) * (H - P.t - P.b);
      const path = arr => {
        let d = '',
          pen = false;
        arr.forEach((v, i) => {
          if (v == null) {
            pen = false;
            return;
          }
          d += (pen ? 'L' : 'M') + X(i).toFixed(1) + ' ' + Y(v).toFixed(1) + ' ';
          pen = true;
        });
        return d;
      };
      let g = '';
      for (let t = 0; t <= 4; t++) {
        const v = lo + (hi - lo) * t / 4,
          y = Y(v);
          console.log(v);
        console.log(lo);
        console.log(hi);
        console.log(t);
        g += `<line x1="${P.l}" y1="${y}" x2="${W-P.r}" y2="${y}" stroke="#eef1f3"/>
          <text x="${P.l-8}" y="${y+4}" text-anchor="end" font-size="10.5" fill="#8b959e">${v.toFixed(0)}</text>`;
      }
      MON.forEach((m, i) => g += `<text x="${X(i)}" y="${H-10}" text-anchor="middle" font-size="10.5" fill="#8b959e">${m}</text>`);
      g += `<path d="${path(b)}" fill="none" stroke="#c9ced3" stroke-width="2.2"/>`;
      g += `<path d="${path(a)}" fill="none" stroke="#3c8dbc" stroke-width="2.6"/>`;
      a.forEach((v, i) => {
        if (v != null) g += `<circle cx="${X(i)}" cy="${Y(v)}" r="3.4" fill="#3c8dbc"><title>${MON[i]} ${M.cur_year}: ${v.toFixed(1)} kWh/RN</title></circle>`;
      });
      b.forEach((v, i) => {
        if (v != null) g += `<circle cx="${X(i)}" cy="${Y(v)}" r="3" fill="#c9ced3"><title>${MON[i]} ${M.prior_year}: ${v.toFixed(1)} kWh/RN</title></circle>`;
      });
      document.getElementById('trend').innerHTML =
        `<svg viewBox="0 0 ${W} ${H}" width="100%" height="${H}">${g}</svg>`;
      document.getElementById('lg1').textContent = M.cur_year + ' (kWh/RN)';
      document.getElementById('lg2').textContent = M.prior_year + ' (kWh/RN)';
      document.getElementById('trendyrs').textContent = M.cur_year + ' vs ' + M.prior_year;
    }

    /* ---------------- energy composition donut ---------------- */
    function drawMix(rows) {
      const K = [
        ['elec', 'Electricity', '#3c8dbc'],
        ['dc', 'District cooling', '#00a6b3'],
        ['lpg', 'LPG', '#f39c12'],
        ['fo', 'Fuel oil', '#dd4b39'],
        ['ng', 'Natural gas', '#8e6bb5'],
        ['dh', 'District heating', '#7fb069']
      ];
      const tot = {};
      let sum = 0;
      rows.forEach(r => {
        const t = r.energy_mt;
        if (!t) return;
        const share = r.mix;
        if (!share) return;
        K.forEach(([k]) => {
          const v = (share[k] || 0) / 100 * t.ct;
          tot[k] = (tot[k] || 0) + v;
          sum += v;
        });
      });
      if (!sum) {
        document.getElementById('mix').innerHTML = '<p class="foot">No data.</p>';
        return;
      }
      const R = 64,
        C = 88,
        SW = 26;
      let ang = -Math.PI / 2,
        g = '',
        rows2 = '';
      K.forEach(([k, label, col]) => {
        const v = tot[k] || 0;
        if (v <= 0) return;
        const frac = v / sum,
          a2 = ang + frac * Math.PI * 2,
          lg = frac > 0.5 ? 1 : 0;
        const x1 = C + R * Math.cos(ang),
          y1 = C + R * Math.sin(ang),
          x2 = C + R * Math.cos(a2),
          y2 = C + R * Math.sin(a2);
        g += frac > 0.999 ?
          `<circle cx="${C}" cy="${C}" r="${R}" fill="none" stroke="${col}" stroke-width="${SW}"/>` :
          `<path d="M ${x1.toFixed(2)} ${y1.toFixed(2)} A ${R} ${R} 0 ${lg} 1 ${x2.toFixed(2)} ${y2.toFixed(2)}"
           fill="none" stroke="${col}" stroke-width="${SW}"><title>${label}: ${(frac*100).toFixed(1)}%</title></path>`;
        ang = a2;
        rows2 += `<div style="display:flex;justify-content:space-between;font-size:12.5px;padding:3px 0">
      <span><i style="display:inline-block;width:10px;height:10px;background:${col};border-radius:2px;margin-right:6px"></i>${label}</span>
      <b>${(frac*100).toFixed(1)}%</b></div>`;
      });
      document.getElementById('mix').innerHTML =
        `<svg viewBox="0 0 176 176" width="176" height="176" style="margin:0 auto 8px">${g}
       <text x="88" y="84" text-anchor="middle" font-size="17" font-weight="600" fill="#333">${big(sum)}</text>
       <text x="88" y="99" text-anchor="middle" font-size="10.5" fill="#8b959e">kWh YTD</text></svg>${rows2}`;
    }

    /* ---------------- regions ---------------- */
    function drawRegions(rows) {
      const by = {};
      rows.forEach(r => {
        (by[r.region] = by[r.region] || []).push(r);
      });
      let h = `<thead><tr><th>Region</th><th>Properties</th><th>Energy YTD</th>
    <th>${M.cur_year} kWh/RN</th><th>${M.prior_year} kWh/RN</th><th>YoY</th><th>Carbon YoY</th><th>Water YoY</th></tr></thead><tbody>`;
      Object.keys(by).sort().forEach(rg => {
        const e = roll(by[rg], 'energy'),
          c = roll(by[rg], 'carbon'),
          w = roll(by[rg], 'water');
        if (!e) return;
        h += `<tr><td><b>${rg}</b></td><td>${e.n}</td><td>${big(e.ct)} kWh</td>
      <td>${e.ci.toFixed(1)}</td><td>${e.pi.toFixed(1)}</td>
      <td class="${cls(e.yoy)}">${pct(e.yoy)} ${arrow(e.yoy)}</td>
      <td class="${cls(c&&c.yoy)}">${c?pct(c.yoy):'&ndash;'}</td>
      <td class="${cls(w&&w.yoy)}">${w?pct(w.yoy):'&ndash;'}</td></tr>`;
      });
      document.getElementById('regions').innerHTML = h + '</tbody>';
    }

    /* ---------------- site table ---------------- */
    let sortKey = 'energy_yoy',
      sortDir = 1;
    const COLS = [
      ['site', 'Property', 0],
      ['country', 'Country', 0],
      ['brand', 'Brand', 0],
      ['energy_cur', 'Energy YTD', 1],
      ['energy_match_i', `kWh/RN ${M.cur_year}`, 1],
      ['energy_yoy', 'Energy YoY', 1],
      ['carbon_yoy', 'Carbon YoY', 1],
      ['water_yoy', 'Water YoY', 1],
      ['energy_perf', 'vs target', 1],
      ['rn_cur', 'Room nights', 1]
    ];

    function drawSites(rows) {
      const r = [...rows].sort((a, b) => {
        let x = a[sortKey],
          y = b[sortKey];
        if (x == null) return 1;
        if (y == null) return -1;
        return (typeof x === 'string' ? x.localeCompare(y) : x - y) * sortDir;
      });
      let h = '<thead><tr>' + COLS.map(([k, l]) =>
        `<th class="s ${k===sortKey?(sortDir>0?'asc':'desc'):''}" data-k="${k}">${l}</th>`).join('') + '</tr></thead><tbody>';
      r.forEach(x => {
        h += `<tr><td><b>${x.site}</b><span class="sub2">${x.city}</span></td>
      <td style="text-align:left">${x.country}</td><td style="text-align:left">${x.brand}</td>
      <td>${big(x.energy_cur)}</td>
      <td>${x.energy_match_i!=null?x.energy_match_i.toFixed(1):'<span class="na">&ndash;</span>'}</td>
      <td class="${cls(x.energy_yoy)}">${pct(x.energy_yoy)} ${arrow(x.energy_yoy)}</td>
      <td class="${cls(x.carbon_yoy)}">${pct(x.carbon_yoy)} ${arrow(x.carbon_yoy)}</td>
      <td class="${cls(x.water_yoy)}">${pct(x.water_yoy)} ${arrow(x.water_yoy)}</td>
      <td>${x.energy_perf!=null
            ?`<span class="pill ${x.energy_perf<0?'g':'r'}">${pct(x.energy_perf)}</span>`
            :'<span class="pill n">n/a</span>'}</td>
      <td>${fmt(x.rn_cur)}</td></tr>`;
      });
      document.getElementById('sites').innerHTML = h + '</tbody>';
      document.querySelectorAll('#sites th.s').forEach(th => th.onclick = () => {
        const k = th.dataset.k;
        if (k === sortKey) sortDir *= -1;
        else {
          sortKey = k;
          sortDir = k === 'site' || k === 'country' || k === 'brand' ? 1 : 1;
        }
        drawSites(visible());
      });
    }

    /* ---------------- data quality tab ---------------- */
    function drawQuality() {
      const tot = D.meta.total_sites,
        flagged = D.dq.length;
      const hi = D.dq.filter(x => x.sev === 'high').length;
      const e = roll(D.sites, 'energy'),
        w = roll(D.sites, 'water');
      document.getElementById('dqnote').innerHTML =
        `<b>Read the headline with care.</b> Across the portfolio absolute energy fell
     ${pct(e.tot)} while room nights fell ${pct(e.den)}, so intensity rises ${pct(e.yoy)}.
     Several resorts show room nights down 25&ndash;50% on broadly flat consumption; confirm whether
     that reflects genuine occupancy or an occupancy-reporting gap before this is presented as a
     performance decline.`;
      const tile = (c, t, v, u) => `<div class="kpi ${c}"><div class="t">${t}</div>
    <div class="v">${v}</div><div class="sub">${u}</div></div>`;
      document.getElementById('dqkpis').innerHTML =
        tile('energy', 'Properties reporting', tot, `of ${tot} in ${M.cur_year}`) +
        tile('carbon', 'Complete energy series', D.sites.filter(x => x.months_cur >= M.ytd_months).length,
          `all ${M.ytd_months} months Jan&ndash;Jul`) +
        tile('water', 'Water coverage', w ? w.n : 0, `properties with comparable water`) +
        tile('rn', 'Properties flagged', flagged, `${hi} high severity`);

      let h = `<thead><tr><th>Property</th><th>Country</th><th>Severity</th><th>Findings</th></tr></thead><tbody>`;
      D.dq.forEach(x => {
        h += `<tr><td><b>${x.site}</b></td><td style="text-align:left">${x.country}</td>
      <td style="text-align:left"><span class="pill ${x.sev==='high'?'r':'a'}">${x.sev==='high'?'High':'Review'}</span></td>
      <td style="text-align:left">${x.issues.map(i=>'&bull; '+i).join('<br>')}</td></tr>`;
      });
      if (!D.dq.length) h += '<tr><td colspan="4" class="foot">No issues detected.</td></tr>';
      document.getElementById('dqtbl').innerHTML = h + '</tbody>';

      const util = [
        ['energy', 'Energy'],
        ['water', 'Water']
      ];
      let c = `<thead><tr><th>Month</th>` + MON.slice(0, M.ytd_months).map(m => `<th>${m}</th>`).join('') + `</tr></thead><tbody>`;
      util.forEach(([k, l]) => {
        c += `<tr><td><b>${l}</b></td>`;
        for (let i = 1; i <= M.ytd_months; i++) {
          const n = D.sites.filter(s => {
            const q = D.series[s.site];
            return k === 'energy' ? (q && q.cur[i - 1] != null) : (s[k + '_cur'] != null);
          }).length;
          c += `<td>${k==='energy'?n:'&mdash;'}</td>`;
        }
        c += '</tr>';
      });
      document.getElementById('cov').innerHTML = c + '</tbody>';

      document.getElementById('method').innerHTML = `
    <p style="margin-top:0"><b>Source.</b> ${M.source} &mdash; ${M.total_sites} properties,
       Jan&ndash;Jul ${M.cur_year} versus Jan&ndash;Jul ${M.prior_year}.</p>
    <p><b>Energy</b> = electricity + district cooling + district heating + LPG + fuel oil + natural
       gas, each converted to kWh (RTH &times;3.516853, LPG litres &times;6.653,
       <b>LPG kg &times;6.9</b>, fuel oil &times;10.684642, natural gas m&sup3; &times;10.639801).</p>
    <p><b>Carbon</b> = electricity &times; country grid factor (UAE 0.400, Saudi Arabia 0.627,
       Qatar/Bahrain 0.572, Iraq 0.753, Egypt 0.511, Jordan 0.537, Tanzania 0.430 kgCO&#8322;e/kWh)
       plus fuel oil 0.26832, LPG 0.22774, natural gas 0.20254, district cooling 0.12021 and
       district heating 0.17073 kgCO&#8322;e per kWh.</p>
    <p><b>Intensity</b> is a weighted roll-up (total consumption &divide; total denominator), not an
       average of property ratios. Energy is per room night; carbon and water per guest night.</p>
    <p><b>Like-for-like.</b> A property-month enters the comparison only when both years reported
       the utility and its denominator, so a missing month cannot flatter or penalise the result.</p>
    <p><b>Targets.</b> Base year ${M.prior_year} YTD actuals; energy &minus;2.00%,
       carbon &minus;3.00%, water 0.00%.</p>`;
    }

    /* ---------------- wiring ---------------- */
    function opts(sel, list, label) {
      sel.innerHTML = `<option value="">All ${label}</option>` + [...new Set(list)].sort().map(v => `<option>${v}</option>`).join('');
    }

    function render() {
      const rows = visible();
      drawKpis(rows);
      drawTarget(rows);
      drawRanks(rows);
      drawTrend(rows);
      drawMix(rows);
      drawRegions(rows);
      drawSites(rows);
    }

    function init() {
      // document.getElementById('asof').textContent = M.generated;
      document.getElementById('periodlbl').textContent =
        'Data compared against ' + M.period_label + '  ·  ' + M.total_sites + ' properties';
      opts(document.getElementById('fRegion'), D.sites.map(r => r.region), 'regions');
      opts(document.getElementById('fCountry'), D.sites.map(r => r.country), 'countries');
      opts(document.getElementById('fBrand'), D.sites.map(r => r.brand), 'brands');
      opts(document.getElementById('fSite'), D.sites.map(r => r.site), 'properties');

      const bind = (id, key) => document.getElementById(id).addEventListener('change', e => {
        S[key] = e.target.value;
        if (key === 'region' || key === 'country') {
          const sub = D.sites.filter(r => (!S.region || r.region === S.region) && (!S.country || r.country === S.country));
          opts(document.getElementById('fSite'), sub.map(r => r.site), 'properties');
          S.site = '';
        }
        render();
      });
      bind('fRegion', 'region');
      bind('fCountry', 'country');
      bind('fBrand', 'brand');
      bind('fSite', 'site');
      document.getElementById('fSearch').addEventListener('input', e => {
        S.q = e.target.value;
        render();
      });
      document.getElementById('reset').onclick = () => {
        Object.keys(S).forEach(k => S[k] = '');
        ['fRegion', 'fCountry', 'fBrand', 'fSite'].forEach(i => document.getElementById(i).value = '');
        opts(document.getElementById('fSite'), D.sites.map(r => r.site), 'properties');
        document.getElementById('fSearch').value = '';
        render();
      };
      document.getElementById('csv').onclick = () => {
        const head = ['Property', 'City', 'Country', 'Region', 'Brand',
          `Energy YTD ${M.cur_year} kWh`, `Energy YTD ${M.prior_year} kWh`,
          `kWh/RN ${M.cur_year}`, `kWh/RN ${M.prior_year}`, 'Energy YoY %', 'Energy vs target %',
          'Carbon YoY %', 'Water YoY %', `Room nights ${M.cur_year}`, 'Months reported'
        ];
        const body = visible().map(r => [r.site, r.city, r.country, r.region, r.brand,
            r.energy_cur, r.energy_pri, r.energy_match_i, r.energy_pri_i, r.energy_yoy, r.energy_perf,
            r.carbon_yoy, r.water_yoy, r.rn_cur, r.months_cur
          ]
          .map(v => v == null ? '' : (typeof v === 'number' ? v.toFixed(3) : `"${v}"`)).join(','));
        const blob = new Blob([
          [head.join(','), ...body].join('\n')
        ], {
          type: 'text/csv;charset=utf-8'
        });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `Rotana_Portfolio_YTD_${M.cur_year}.csv`;
        a.click();
      };
      document.querySelectorAll('.tab').forEach(t => t.onclick = () => {
        document.querySelectorAll('.tab').forEach(x => x.classList.remove('on'));
        t.classList.add('on');
        document.getElementById('tab-summary').style.display = t.dataset.tab === 'summary' ? '' : 'none';
        document.getElementById('tab-quality').style.display = t.dataset.tab === 'quality' ? '' : 'none';
      });
      render();
      drawQuality();
    }
    init();
  </script> -->
</div>

<script>
	document.querySelectorAll('.tab').forEach(function(tab) {
    tab.addEventListener('click', function() {

        document.querySelectorAll('.tab').forEach(function(item) {
            item.classList.remove('on');
        });

        this.classList.add('on');

        document.getElementById('tab-summary').style.display =
            this.dataset.tab === 'summary' ? '' : 'none';

        document.getElementById('tab-quality').style.display =
            this.dataset.tab === 'quality' ? '' : 'none';
    });
});
</script>
