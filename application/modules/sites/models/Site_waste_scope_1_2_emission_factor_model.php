<?php

/**
 * Site Scope 3 Waste Emission Factor Model
 *
 * Emission-factor matrix keyed by site_id, year, waste stream column_key,
 * and typical_destination_id (from getWasteTypicalDestinationArray()).
 */
class Site_Waste_scope_1_2_emission_factor_model extends Base_Model
{
    protected $_table = 'site_waste_scope_1_2_emission_factors';

    public $year_id = null;

    /**
     * Master rows define the available category / stream / destination hierarchy.
     * Both zero and NULL are accepted for compatibility with existing data.
     */
    public function getMasterRows()
    {
        $this->db->select('f.*');
        $this->db->from($this->_table . ' AS f');
        $this->db->where('f.deleted_at', null);
        $this->db->where('f.deleted_by', null);
        $this->db->where('(f.site_id = 0 OR f.site_id IS NULL)', null, false);
        $this->db->where('(f.year_id = 0 OR f.year_id IS NULL)', null, false);
        $this->db->where('f.status', 1);
        $this->db->order_by('f.waste_emission_factor_id', 'ASC');
        return $this->db->get()->result_array();
    }

    public function getByYear($yearId, $siteId)
    {
        $this->db->select('f.*');
        $this->db->from($this->_table . ' AS f');
        $this->db->where('f.deleted_at', null);
        $this->db->where('f.deleted_by', null);
        $this->db->where('f.site_id', (int) $siteId);
        $this->db->where('f.year_id', (int) $yearId);
        $this->db->order_by('f.category_label', 'ASC');
        $this->db->order_by('f.group_label', 'ASC');
        $this->db->order_by('f.stream_label', 'ASC');
        $this->db->order_by('f.typical_destination_id', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Return the master hierarchy with factor values overlaid for one site/year.
     */
    public function getRowsWithValues($yearId, $siteId)
    {
        $masterRows = $this->getMasterRows();
        $storedRows = $this->getByYear($yearId, $siteId);
        $storedByKey = [];

        foreach ($storedRows as $row) {
            $storedByKey[$this->factorKey($row['column_key'], $row['typical_destination_id'])] = $row;
        }

        $rows = [];
        foreach ($masterRows as $masterRow) {
            $key = $this->factorKey($masterRow['column_key'], $masterRow['typical_destination_id']);
            $storedRow = isset($storedByKey[$key]) ? $storedByKey[$key] : [];

            $masterRow['master_factor_id'] = (int) $masterRow['waste_emission_factor_id'];
            $masterRow['waste_emission_factor_id'] = isset($storedRow['waste_emission_factor_id'])
                ? (int) $storedRow['waste_emission_factor_id']
                : null;
            $masterRow['site_id'] = (int) $siteId;
            $masterRow['year_id'] = (int) $yearId;
            $masterRow['epa_source_factor'] = array_key_exists('epa_source_factor', $storedRow)
                ? $storedRow['epa_source_factor']
                : $masterRow['epa_source_factor'];
            $masterRow['hep_factor'] = array_key_exists('hep_factor', $storedRow)
                ? $storedRow['hep_factor']
                : $masterRow['hep_factor'];
            $rows[] = $masterRow;
        }

        return $rows;
    }

    public function getFactor($siteId, $yearId, $columnKey, $typicalDestinationId)
    {
        $this->db->select('f.*');
        $this->db->from($this->_table . ' AS f');
        $this->db->where('f.deleted_at', null);
        $this->db->where('f.deleted_by', null);
        $this->db->where('f.site_id', (int) $siteId);
        $this->db->where('f.year_id', (int) $yearId);
        $this->db->where('f.column_key', $columnKey);
        $this->db->where('f.typical_destination_id', (int) $typicalDestinationId);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function upsert(array $data)
    {
        $userId = $this->session->userdata[get_current_section($this, true)]['user_id'];
        $siteId = (int) $data['site_id'];
        $existing = $this->getFactor(
            $siteId,
            $data['year_id'],
            $data['column_key'],
            $data['typical_destination_id']
        );

        $payload = [
            'site_id' => $siteId,
            'year_id' => (int) $data['year_id'],
            'column_key' => $data['column_key'],
            'node_level' => $data['node_level'],
            'category_label' => $data['category_label'],
            'group_label' => $data['group_label'] ?? null,
            'stream_label' => $data['stream_label'] ?? null,
            'typical_destination_id' => (int) $data['typical_destination_id'],
            'typical_destination_label' => $data['typical_destination_label'],
            'epa_source_factor' => $data['epa_source_factor'] ?? null,
            'hep_factor' => $data['hep_factor'] ?? null,
            'status' => isset($data['status']) ? (int) $data['status'] : 1,
        ];

        if (!empty($existing)) {
            $payload['modified_at'] = GetCurrentDateTime();
            $payload['modified_by'] = $userId;
            $this->db->where('waste_emission_factor_id', (int) $existing['waste_emission_factor_id']);
            $this->db->update($this->_table, $payload);
            return (int) $existing['waste_emission_factor_id'];
        }

        $payload['created_at'] = GetCurrentDateTime();
        $payload['created_by'] = $userId;
        $this->db->insert($this->_table, $payload);
        return (int) $this->db->insert_id();
    }

    public function bulkUpsert(array $rows)
    {
        $ids = [];
        foreach ($rows as $row) {
            $ids[] = $this->upsert($row);
        }
        return $ids;
    }

    /**
     * Store site/year values using master row IDs as the only hierarchy source.
     */
    public function saveSiteYearValues($siteId, $yearId, array $valuesByMasterId)
    {
        $masterById = [];
        foreach ($this->getMasterRows() as $masterRow) {
            $masterById[(int) $masterRow['waste_emission_factor_id']] = $masterRow;
        }
        $storedByKey = [];
        foreach ($this->getByYear($yearId, $siteId) as $storedRow) {
            $storedByKey[$this->factorKey($storedRow['column_key'], $storedRow['typical_destination_id'])] = $storedRow;
        }

        $ids = [];
        foreach ($valuesByMasterId as $masterId => $values) {
            $masterId = (int) $masterId;
            if (!isset($masterById[$masterId])) {
                continue;
            }

            $masterRow = $masterById[$masterId];
            $key = $this->factorKey($masterRow['column_key'], $masterRow['typical_destination_id']);
            $epaFactor = isset($values['epa_source_factor']) ? $values['epa_source_factor'] : null;
            $hepFactor = isset($values['hep_factor']) ? $values['hep_factor'] : null;
            if (!isset($storedByKey[$key])) {
                $masterEpa = isset($masterRow['epa_source_factor']) ? $masterRow['epa_source_factor'] : null;
                $masterHep = isset($masterRow['hep_factor']) ? $masterRow['hep_factor'] : null;
                if ($this->factorValuesEqual($epaFactor, $masterEpa)
                    && $this->factorValuesEqual($hepFactor, $masterHep)) {
                    continue;
                }
            }

            $ids[] = $this->upsert([
                'site_id' => (int) $siteId,
                'year_id' => (int) $yearId,
                'column_key' => $masterRow['column_key'],
                'node_level' => $masterRow['node_level'],
                'category_label' => $masterRow['category_label'],
                'group_label' => $masterRow['group_label'],
                'stream_label' => $masterRow['stream_label'],
                'typical_destination_id' => $masterRow['typical_destination_id'],
                'typical_destination_label' => $masterRow['typical_destination_label'],
                'epa_source_factor' => $epaFactor,
                'hep_factor' => $hepFactor,
                'status' => 1,
            ]);
        }

        return $ids;
    }

    public function copyYear($fromYear, $toYear, $siteId)
    {
        $sourceRows = $this->getByYear($fromYear, $siteId);
        if (empty($sourceRows)) {
            return 0;
        }

        $copied = 0;
        foreach ($sourceRows as $row) {
            $row['site_id'] = (int) $siteId;
            $row['year_id'] = (int) $toYear;
            unset($row['waste_emission_factor_id'], $row['created_at'], $row['created_by'], $row['modified_at'], $row['modified_by'], $row['deleted_at'], $row['deleted_by']);
            $this->upsert($row);
            $copied++;
        }
        return $copied;
    }

    private function factorKey($columnKey, $typicalDestinationId)
    {
        return strtolower(trim((string) $columnKey)) . '|' . (int) $typicalDestinationId;
    }

    private function factorValuesEqual($left, $right)
    {
        if (($left === null || $left === '') && ($right === null || $right === '')) {
            return true;
        }
        return is_numeric($left) && is_numeric($right) && (float) $left === (float) $right;
    }
}
