<div class="card">
  <div class="body">
    <form method="get" class="filters" id="dashboardFilters">
      <div class="f">
        <label>Region</label>
        <select name="region" id="fRegion">
          <option value="">All regions</option>
          <?php foreach (csd_unique_values($sites, 'region') as $value): ?>
            <option value="<?= html_escape($value); ?>" <?= (isset($_GET['region']) && $_GET['region'] === $value) ? 'selected' : ''; ?>>
              <?= html_escape($value); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="f">
        <label>Country</label>
        <select name="country" id="fCountry">
          <option value="">All countries</option>
          <?php foreach (csd_unique_values($sites, 'country') as $value): ?>
            <option value="<?= html_escape($value); ?>" <?= (isset($_GET['country']) && $_GET['country'] === $value) ? 'selected' : ''; ?>>
              <?= html_escape($value); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="f">
        <label>Brand</label>
        <select id="fBrand" name="brand">
          <?php
          $selected_brand = isset($_GET['brand']) ? $_GET['brand'] : '';
          ?>
          <option value="" <?= $selected_brand === '' ? 'selected' : ''; ?>>All brands</option>
          <option value="Arjaan" <?= $selected_brand === 'Arjaan' ? 'selected' : ''; ?>>Arjaan</option>
          <option value="Centro" <?= $selected_brand === 'Centro' ? 'selected' : ''; ?>>Centro</option>
          <option value="Edge" <?= $selected_brand === 'Edge' ? 'selected' : ''; ?>>Edge</option>
          <option value="Rayhaan" <?= $selected_brand === 'Rayhaan' ? 'selected' : ''; ?>>Rayhaan</option>
          <option value="Residences" <?= $selected_brand === 'Residences' ? 'selected' : ''; ?>>Residences</option>
          <option value="Rotana" <?= $selected_brand === 'Rotana' ? 'selected' : ''; ?>>Rotana</option>
        </select>
        <!-- <select name="brand" id="fBrand">
          <option value="">All brands</option>
          <?php foreach (csd_unique_values($sites, 'brand') as $value): ?>
            <option value="<?= html_escape($value); ?>" <?= (isset($_GET['brand']) && $_GET['brand'] === $value) ? 'selected' : ''; ?>>
              <?= html_escape($value); ?>
            </option>
          <?php endforeach; ?>
        </select> -->
      </div>

      <div class="f">
        <label>Site</label>
        <select name="site" id="fSite">
          <option value="">All properties</option>
          <?php foreach (csd_unique_values($sites, 'site') as $value): ?>
            <option value="<?= html_escape($value); ?>" <?= (isset($_GET['site']) && $_GET['site'] === $value) ? 'selected' : ''; ?>>
              <?= html_escape($value); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="f">
        <label>Search</label>
        <input
          id="fSearch"
          name="search"
          value="<?= isset($_GET['search']) ? html_escape($_GET['search']) : ''; ?>"
          placeholder="Hotel name&hellip;" />
      </div>

      <div class="f" style="display:flex;gap:8px">
        <a class="btn" id="reset" href="<?= html_escape(current_url()); ?>">Reset</a>
        <button class="btn primary" id="submit" type="submit">Apply</button>
        <button class="btn" type="button" id="csv">
            Export CSV
        </button>
      </div>
    </form>
  </div>
</div>


<!-- <script>
  (function() {

    /*
     * Actual site data coming from:
     *
     * $dashboard['sites']
     *
     * We are NOT making another database request.
     */
    const sites = <?= json_encode($sites); ?>;


    const regionSelect = document.getElementById('fRegion');
    const countrySelect = document.getElementById('fCountry');
    const brandSelect = document.getElementById('fBrand');
    const siteSelect = document.getElementById('fSite');


    /*
     * Return unique, sorted values.
     */
    function uniqueValues(values) {

      return [...new Set(
        values.filter(function(value) {

          return value !== null &&
            value !== undefined &&
            value !== '';

        })
      )].sort(function(a, b) {

        return String(a).localeCompare(
          String(b)
        );

      });

    }


    /*
     * Get countries for selected region.
     */
    function getCountries(region) {

      return uniqueValues(
        sites
        .filter(function(row) {

          return !region ||
            row.region === region;

        })
        .map(function(row) {

          return row.country;

        })
      );

    }


    /*
     * Get brands for selected region + country.
     */
    function getBrands(region, country) {

      return uniqueValues(
        sites
        .filter(function(row) {

          return (!region || row.region === region) &&
            (!country || row.country === country);

        })
        .map(function(row) {

          return row.brand;

        })
      );

    }


    /*
     * Get sites for selected region + country + brand.
     */
    function getSites(region, country, brand) {

      const selectedBrand = String(brand || '').toLowerCase();

      return uniqueValues(
        sites
          .filter(function(row) {

            if (region && row.region !== region) {
              return false;
            }

            if (country && row.country !== country) {
              return false;
            }

            /*
            * Brand is matched against property/site name.
            */
            if (selectedBrand) {

              const propertyName =
                String(row.site || '').toLowerCase();

              if (propertyName.indexOf(selectedBrand) === -1) {
                return false;
              }

            }

            return true;

          })
          .map(function(row) {

            return row.site;

          })
      );

    }


    /*
     * Rebuild a select dropdown.
     */
    function populateSelect(
      select,
      values,
      defaultText
    ) {

      select.innerHTML = '';

      const defaultOption =
        document.createElement('option');

      defaultOption.value = '';
      defaultOption.textContent = defaultText;

      select.appendChild(defaultOption);


      values.forEach(function(value) {

        const option =
          document.createElement('option');

        option.value = value;
        option.textContent = value;

        select.appendChild(option);

      });

    }


    /*
     * Region changed.
     *
     * Region
     *   ↓
     * Country
     *   ↓
     * Brand
     *   ↓
     * Site
     */
    regionSelect.addEventListener(
      'change',
      function() {

        const region = this.value;

        const countries =
          getCountries(region);

        populateSelect(
          countrySelect,
          countries,
          'All countries'
        );

        /*
        * Brand is static.
        * Keep the currently selected brand.
        */
        const brand = brandSelect.value;

        populateSelect(
          siteSelect,
          getSites(
            region,
            '',
            brand
          ),
          'All properties'
        );

      }
    );


    /*
     * Country changed.
     *
     * Keep Region.
     * Filter Brand.
     * Filter Site.
     */
    countrySelect.addEventListener(
      'change',
      function() {

        const region = regionSelect.value;
        const country = this.value;


        const brands =
          getBrands(
            region,
            country
          );

        // populateSelect(
        //   brandSelect,
        //   brands,
        //   'All brands'
        // );


        /*
         * Brand is reset,
         * therefore Site is rebuilt
         * without a brand restriction.
         */
        populateSelect(
          siteSelect,
          getSites(
            region,
            country,
            ''
          ),
          'All properties'
        );

      }
    );


    /*
     * Brand changed.
     *
     * Keep Region + Country.
     * Filter Site.
     */
    brandSelect.addEventListener(
      'change',
      function() {

        const region = regionSelect.value;
        const country = countrySelect.value;
        const brand = this.value;


        const sitesForSelection =
          getSites(
            region,
            country,
            brand
          );


        populateSelect(
          siteSelect,
          sitesForSelection,
          'All properties'
        );

      }
    );


    /*
     * Reset everything.
     */
    document
      .getElementById('reset')
      .addEventListener(
        'click',
        function() {

          regionSelect.value = '';

          populateSelect(
            countrySelect,
            getCountries(''),
            'All countries'
          );

          populateSelect(
            brandSelect,
            getBrands('', ''),
            'All brands'
          );

          populateSelect(
            siteSelect,
            getSites('', '', ''),
            'All properties'
          );

          document.getElementById('fSearch').value = '';

        }
      );

  })();

  $('#csv').on('click', function () {
    $.blockUI();
});
</script> -->

<script>
$(document).ready(function () {

    const sites = <?= json_encode($sites); ?>;

    const form = $('.filters');

    const regionSelect  = document.getElementById('fRegion');
    const countrySelect = document.getElementById('fCountry');
    const brandSelect   = document.getElementById('fBrand');
    const siteSelect    = document.getElementById('fSite');
    const searchInput   = document.getElementById('fSearch');

    /*
     * Current sort state, carried across every AJAX filter/sort
     * reload so sorting a column never drops the active filters,
     * and filtering never drops the active sort.
     */
    let currentSort = <?= json_encode(isset($sort_key) ? $sort_key : ''); ?>;
    let currentDir  = <?= json_encode(isset($sort_dir) ? $sort_dir : ''); ?>;

    /*
     * Energy/Carbon mode - one global value for the whole dashboard,
     * even though the toggle control itself appears inside every
     * chart card's own header (per the approved prototype). Clicking
     * ANY of those instances updates this one value and reloads every
     * card together - never just the card the control sits in.
     */
    let currentMode = <?= json_encode(isset($mode) ? $mode : 'energy'); ?>;

    /*
     * ---------------------------------------------------------
     * Common helpers
     * ---------------------------------------------------------
     */

    function uniqueValues(values) {
        return [...new Set(
            values.filter(function (value) {
                return value !== null &&
                       value !== undefined &&
                       value !== '';
            })
        )].sort(function (a, b) {
            return String(a).localeCompare(String(b));
        });
    }


    function populateSelect(select, values, defaultText, selectedValue) {

        select.innerHTML = '';

        const defaultOption = document.createElement('option');

        defaultOption.value = '';
        defaultOption.textContent = defaultText;

        select.appendChild(defaultOption);

        values.forEach(function (value) {

            const option = document.createElement('option');

            option.value = value;
            option.textContent = value;

            if (String(value) === String(selectedValue || '')) {
                option.selected = true;
            }

            select.appendChild(option);
        });
    }


    /*
     * ---------------------------------------------------------
     * Get filtered dropdown values
     * ---------------------------------------------------------
     */

    function getCountries(region) {

        return uniqueValues(
            sites
                .filter(function (row) {
                    return !region || row.region === region;
                })
                .map(function (row) {
                    return row.country;
                })
        );
    }


    function getBrands(region, country) {

        return uniqueValues(
            sites
                .filter(function (row) {

                    if (region && row.region !== region) {
                        return false;
                    }

                    if (country && row.country !== country) {
                        return false;
                    }

                    return true;
                })
                .map(function (row) {
                    return row.brand;
                })
        );
    }


    function getSites(region, country, brand) {

        const selectedBrand = String(brand || '').toLowerCase();

        return uniqueValues(
            sites
                .filter(function (row) {

                    if (region && row.region !== region) {
                        return false;
                    }

                    if (country && row.country !== country) {
                        return false;
                    }

                    /*
                     * Brand is matched against property name.
                     *
                     * Example:
                     * "Rotana" matches "Centro Rotana Dubai"
                     */
                    if (selectedBrand) {

                        const propertyName =
                            String(row.site || '').toLowerCase();

                        if (propertyName.indexOf(selectedBrand) === -1) {
                            return false;
                        }
                    }

                    return true;
                })
                .map(function (row) {
                    return row.site;
                })
        );
    }


    /*
     * ---------------------------------------------------------
     * Block UI
     * ---------------------------------------------------------
     */

    function showDashboardLoader() {

        $.blockUI({
            message: `
                <div style="padding:18px 25px;">
                    <div style="font-size:16px;font-weight:600;">
                        Loading Dashboard...<br>
                        Please wait while the dashboard is being updated.
                    </div>
                </div>
            `,
            css: {
                border: 'none',
                padding: '0',
                backgroundColor: '#fff',
                borderRadius: '8px',
                color: '#333',
                width: '300px',
                height: '120px',
                left: '50%',
                top: '50%',
                transform: 'translate(-50%, -50%)'
            },
            overlayCSS: {
                backgroundColor: '#000',
                opacity: 0.35,
                cursor: 'wait'
            }
        });
    }


    /*
     * ---------------------------------------------------------
     * AJAX dashboard reload
     * ---------------------------------------------------------
     */

    function loadDashboard() {

        showDashboardLoader();

        /*
         * Current filter values + current sort state + current
         * Energy/Carbon mode, so none of the three ever overwrites
         * another.
         */
        const data = form.serialize() +
            '&sort=' + encodeURIComponent(currentSort || '') +
            '&dir=' + encodeURIComponent(currentDir || '') +
            '&mode=' + encodeURIComponent(currentMode || 'energy');

        /*
         * Keep the address bar / refresh / back-button in sync
         * without a full page navigation.
         */
        window.history.replaceState(
            {},
            document.title,
            window.location.pathname + '?' + data
        );

        $.ajax({
            url: window.location.pathname,
            type: 'GET',

            data: data,

            success: function (response) {

                /*
                 * Get only the dashboard portion from
                 * the returned HTML.
                 */
                const dashboardHtml = $(response)
                    .find('#dashboard-content')
                    .html();

                if (dashboardHtml !== undefined) {

                    $('#dashboard-content').html(dashboardHtml);

                } else {

                    console.error(
                        'Dashboard content was not found in AJAX response.'
                    );
                }
            },

            error: function (xhr, status, error) {

                console.error('Dashboard AJAX error:', error);

                alert('Unable to load dashboard.');
            },

            complete: function () {

                $.unblockUI();
            }
        });
    }


    /*
     * ---------------------------------------------------------
     * Sort column header clicks
     * ---------------------------------------------------------
     *
     * Purely client-side: the rows already on screen (post-filter)
     * are just reordered in place, no server round-trip and no
     * loading spinner. currentSort/currentDir are still tracked so
     * that the NEXT filter change (which does hit the server) asks
     * for data in the same order instead of resetting to default.
     *
     * Delegated on document because #dashboard-content (and the
     * table inside it) is replaced on every AJAX filter reload.
     */

    function sortTable(key, dir) {

        const tbody = document.querySelector('#sites tbody');

        if (!tbody) {
            return;
        }

        const isText = (key === 'site' || key === 'country' || key === 'brand');

        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort(function (rowA, rowB) {

            let a = rowA.dataset[key];
            let b = rowB.dataset[key];

            if (isText) {

                a = (a || '').toLowerCase();
                b = (b || '').toLowerCase();

                if (a < b) return dir === 'desc' ? 1 : -1;
                if (a > b) return dir === 'desc' ? -1 : 1;

                return 0;
            }

            /*
             * Blank/missing values always sort to the bottom,
             * regardless of direction.
             */
            const aEmpty = (a === undefined || a === '');
            const bEmpty = (b === undefined || b === '');

            if (aEmpty && bEmpty) return 0;
            if (aEmpty) return 1;
            if (bEmpty) return -1;

            a = parseFloat(a);
            b = parseFloat(b);

            return dir === 'desc' ? (b - a) : (a - b);
        });

        rows.forEach(function (row) {
            tbody.appendChild(row);
        });
    }

    $(document).on('click', '.sort-link', function (e) {

        e.preventDefault();

        const $link = $(this);
        const key   = $link.data('sort');
        const dir   = $link.closest('th').hasClass('asc') ? 'desc' : 'asc';

        $('#sites thead th').removeClass('asc desc');
        $link.closest('th').addClass(dir);

        sortTable(key, dir);

        currentSort = key;
        currentDir  = dir;
    });


    /*
     * ---------------------------------------------------------
     * Energy/Carbon mode toggle
     * ---------------------------------------------------------
     *
     * One control appears inside each chart card's header (4 of
     * them on screen at once), but they all share one currentMode
     * value. No AJAX call here: both Energy and Carbon versions of
     * every card are already in the DOM (rendered server-side), so
     * switching is just showing one set and hiding the other -
     * instant, and no server round trip for something that doesn't
     * depend on fresh data.
     *
     * currentMode still rides along inside loadDashboard()'s own
     * request (see above) purely so a FILTER/SORT reload - which
     * does hit the server - re-renders with the right mode already
     * visible, not because switching mode itself needs that call.
     *
     * Delegated on document because #dashboard-content, where every
     * one of these controls and their [data-mode] content lives, is
     * replaced on every filter/sort AJAX reload.
     */

    function applyMode(mode) {

        currentMode = mode;

        document.querySelectorAll('.mode-content[data-mode]').forEach(function (el) {
            el.style.display = (el.dataset.mode === mode) ? '' : 'none';
        });

        document.querySelectorAll('.mode-toggle button').forEach(function (btn) {
            btn.classList.toggle('on', btn.dataset.mode === mode);
        });
    }

    $(document).on('click', '.mode-toggle button', function (e) {

        e.preventDefault();

        const mode = $(this).data('mode');

        if (mode === currentMode) {
            return;
        }

        applyMode(mode);
    });


    /*
     * ---------------------------------------------------------
     * Region change
     * ---------------------------------------------------------
     */

    regionSelect.addEventListener('change', function () {

        const region = this.value;

        /*
         * Region changed:
         *
         * Region
         *   ↓
         * Country
         *   ↓
         * Brand
         *   ↓
         * Site
         */

        populateSelect(
            countrySelect,
            getCountries(region),
            'All countries',
            ''
        );

        populateSelect(
            brandSelect,
            getBrands(region, ''),
            'All brands',
            ''
        );

        populateSelect(
            siteSelect,
            getSites(region, '', ''),
            'All properties',
            ''
        );

        loadDashboard();
    });


    /*
     * ---------------------------------------------------------
     * Country change
     * ---------------------------------------------------------
     */

    countrySelect.addEventListener('change', function () {

        const region  = regionSelect.value;
        const country = this.value;

        /*
         * Country changed:
         *
         * Country
         *   ↓
         * Brand
         *   ↓
         * Site
         */

        populateSelect(
            brandSelect,
            getBrands(region, country),
            'All brands',
            ''
        );

        populateSelect(
            siteSelect,
            getSites(region, country, ''),
            'All properties',
            ''
        );

        loadDashboard();
    });


    /*
     * ---------------------------------------------------------
     * Brand change
     * ---------------------------------------------------------
     */

    brandSelect.addEventListener('change', function () {

        const region  = regionSelect.value;
        const country = countrySelect.value;
        const brand   = this.value;

        /*
         * Brand changed:
         *
         * Brand
         *   ↓
         * Site
         */

        populateSelect(
            siteSelect,
            getSites(region, country, brand),
            'All properties',
            ''
        );

        loadDashboard();
    });


    /*
     * ---------------------------------------------------------
     * Site change
     * ---------------------------------------------------------
     */

    siteSelect.addEventListener('change', function () {

        loadDashboard();
    });


    /*
     * ---------------------------------------------------------
     * Search + Apply
     * ---------------------------------------------------------
     *
     * Search should NOT trigger AJAX on every key press.
     * User enters the search text and clicks Apply.
     */

    form.on('submit', function (e) {

        e.preventDefault();

        loadDashboard();
    });


    /*
     * ---------------------------------------------------------
     * Reset
     * ---------------------------------------------------------
     */

    $('#reset').on('click', function (e) {

        e.preventDefault();

        /*
         * Reset URL/filter/sort state.
         */
        currentSort = '';
        currentDir  = '';

        window.history.replaceState(
            {},
            document.title,
            $(this).attr('href')
        );

        /*
         * Reset dropdowns.
         */
        regionSelect.value = '';

        populateSelect(
            countrySelect,
            getCountries(''),
            'All countries',
            ''
        );

        populateSelect(
            brandSelect,
            getBrands('', ''),
            'All brands',
            ''
        );

        populateSelect(
            siteSelect,
            getSites('', '', ''),
            'All properties',
            ''
        );

        searchInput.value = '';

        loadDashboard();
    });

});
</script>
<script>
document.getElementById('csv').addEventListener('click', function () {

    var form = document.getElementById('dashboardFilters');

    var params = new URLSearchParams(new FormData(form));

    params.set('export', 'csv');

    window.location.href = form.action + '?' + params.toString();
});
</script>