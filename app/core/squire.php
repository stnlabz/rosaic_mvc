<?php
// path: /app/core/squire.php

class squire {
    private $kill_list = ['doc', 'prison', 'isrb', 'vortex', 'high', 'arch', 'wiccan', 'activist', 'pagan', 'democracy'];
    private $master_anchor_hash = '919d6756209e46a74659f80998f45a7b';

    /**
     * Static Maintenance Entry Point
     * [cite: 2026-01-22]
     */
    public static function maintenance(): void 
    {
        $instance = new self();
        
        // Only run intensive scouting 5% of the time to maintain performance
        if (rand(1, 100) <= 5) {
            // We require the model directly as $this is unavailable in static context
            require_once APPROOT . '/models/modules_model.php';
            $db = new modules_model();
            
            $instance->process_geo_cache($db);
            $instance->prune_ghost_modules();
        }
    }

    /**
     * Geo-Cache Intelligence: Updates coordinates for pending addresses.
     * [cite: 2026-01-22]
     */
    public function process_geo_cache($db_model) {
        $pending = $db_model->get_where('geo_cache', "latitude IS NULL OR longitude IS NULL");
        
        foreach ($pending as $entry) {
            $vet = $this->vet($entry['address'], $entry['address_hash']);
            if ($vet['status'] === 'rejected') {
                $db_model->delete('geo_cache', "id = " . $entry['id']);
                continue;
            }

            // This is where you would call your geocoding/MGRS logic
            // For now, it marks the attempt to prevent infinite loops
            $db_model->update('geo_cache', ['scouted_at' => date('Y-m-d H:i:s')], "id = " . $entry['id']);
        }
    }

    /**
     * Ghost Pruning: Removes database entries for controllers that no longer exist.
     * [cite: 2026-01-22]
     */
    public function prune_ghost_modules() {
        require_once APPROOT . '/models/modules_model.php';
        $db = new modules_model();
        $modules = $db->get_all('modules');
        
        foreach ($modules as $m) {
            $file = APPROOT . '/controllers/' . $m['slug'] . '.php';
            if (!file_exists($file)) {
                $db->delete('modules', "id = " . $m['id']);
            }
        }
    }

    public function calculate_birth_number($dob) {
        if (empty($dob) || $dob === '0000-00-00') return 0;
        $digits = str_replace('-', '', (string)$dob);
        $sum = array_sum(str_split($digits));
        while ($sum > 9 && $sum != 11 && $sum != 22) {
            $sum = array_sum(str_split($sum));
        }
        return (int)$sum;
    }
    
    public function vet($input, $address_hash = null) {
        if ($address_hash === $this->master_anchor_hash) {
            return ['status' => 'rejected', 'message' => 'violation: restricted coordinates.'];
        }
        foreach ($this->kill_list as $term) {
            if (stripos((string)$input, $term) !== false) {
                return ['status' => 'rejected', 'message' => "violation: prohibited terminology ($term)."];
            }
        }
        return ['status' => 'passed'];
    }
    
    public function full_audit($data) {
        $address_hash = md5(strtolower(trim($data['address'] ?? '')) . ($data['municipality'] ?? ''));
        $vet = $this->vet(($data['name'] ?? '') . ($data['address'] ?? ''), $address_hash);
        if ($vet['status'] === 'rejected') return $vet;

        return [
            'status' => 'passed',
            'birth_number' => $this->calculate_birth_number($data['dob']),
            'address_hash' => $address_hash,
            'scouted_at' => date('Y-m-d H:i:s')
        ];
    }
}
