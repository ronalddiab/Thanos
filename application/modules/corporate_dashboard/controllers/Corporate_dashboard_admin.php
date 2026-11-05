<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Corporate_dashboard_admin extends Base_Admin_Controller
{
    public function __construct()
    {
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            ob_start();
        }
        parent::__construct();
        $this->access_control($this->access_rules());
        $this->load->model('corporate_dashboard_model');
        $this->load->helper('corporate_dashboard/corporate_dashboard');
        $this->breadcrumb->add('Corporate Dashboard', base_url() . 'corporate_dashboard');
    }

    private function access_rules()
    {
        return array(array('actions' => array('index','jsindex'), 'users' => array('@')));
    }

    public function index()
    {
        $this->theme->set('page_title', 'Corporate Sustainability Dashboard');

        $is_export = isset($_GET['export']) && $_GET['export'] === 'csv';

        if ($is_export) {
            ob_start();
        }

        $dashboard = $this->corporate_dashboard_model->get_dashboard_data();

        if ($is_export) {
            $this->export_csv($dashboard);
            exit;
        }

        $data = array(
            'dashboard' => $dashboard
        );

        $this->theme->view($data);
    }

    public function jsindex()
    {
        $this->theme->set('page_title', 'Corporate Sustainability Dashboard');
        $dashboard = $this->corporate_dashboard_model->get_dashboard_data();
        // dd($dashboard);
        $data = array(
            'dashboard' => $dashboard
        );

        $this->theme->view($data);
    }

    private function export_csv($dashboard)
    {
        $sites = isset($dashboard['sites'])
            ? $dashboard['sites']
            : array();

        $sites = csd_filter_sites($sites);

        $meta = isset($dashboard['meta'])
            ? $dashboard['meta']
            : array();

        $cur_year = isset($meta['cur_year'])
            ? (int) $meta['cur_year']
            : date('Y');

        $prior_year = isset($meta['prior_year'])
            ? (int) $meta['prior_year']
            : ($cur_year - 1);

        $filename = 'Rotana_Portfolio_YTD_' . $cur_year . '.csv';

        // Clear any output that may already exist.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header(
            'Content-Disposition: attachment; filename="' . $filename . '"'
        );
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, array(
            'Property',
            'City',
            'Country',
            'Region',
            'Brand',
            'Energy YTD ' . $cur_year . ' kWh',
            'Energy YTD ' . $prior_year . ' kWh',
            'kWh/RN ' . $cur_year,
            'kWh/RN ' . $prior_year,
            'Energy YoY %',
            'Energy vs target %',
            'Carbon YoY %',
            'Water YoY %',
            'Room nights ' . $cur_year,
            'Months reported'
        ));

        foreach ($sites as $row) {
            fputcsv($output, array(
                isset($row['site']) ? $row['site'] : '',
                isset($row['city']) ? $row['city'] : '',
                isset($row['country']) ? $row['country'] : '',
                isset($row['region']) ? $row['region'] : '',
                isset($row['brand']) ? $row['brand'] : '',
                isset($row['energy_cur']) ? $row['energy_cur'] : '',
                isset($row['energy_pri']) ? $row['energy_pri'] : '',
                isset($row['energy_match_i']) ? $row['energy_match_i'] : '',
                isset($row['energy_pri_i']) ? $row['energy_pri_i'] : '',
                isset($row['energy_yoy']) ? $row['energy_yoy'] : '',
                isset($row['energy_perf']) ? $row['energy_perf'] : '',
                isset($row['carbon_yoy']) ? $row['carbon_yoy'] : '',
                isset($row['water_yoy']) ? $row['water_yoy'] : '',
                isset($row['rn_cur']) ? $row['rn_cur'] : '',
                isset($row['months_cur']) ? $row['months_cur'] : ''
            ));
        }

        fclose($output);
        exit;
    }
}
