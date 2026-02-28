<?php
// path: /app/core/squire.php

class squire {
    private $kill_list = ['doc', 'prison', 'isrb', 'vortex', 'high', 'arch', 'wiccan', 'activist', 'pagan', 'democracy'];
    private $master_anchor_hash = '919d6756209e46a74659f80998f45a7b';

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
