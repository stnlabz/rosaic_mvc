<?php
/**
 * squire core class
 * handles birth number calculation, geographic mapping, and term scrubbing.
 */
class squire {
    private $kill_list = ['doc', 'prison', 'isrb', 'vortex', 'high', 'arch', 'wiccan', 'activist', 'pagan', 'democracy'];

    public function vet($input) {
        foreach ($this->kill_list as $term) {
            if (stripos($input, $term) !== false) {
                return [
                    'status' => 'rejected',
                    'reason' => "contaminant: $term",
                    'message' => "violation of tradition: prohibited terminology detected."
                ];
            }
        }
        return ['status' => 'passed'];
    }

    public function calculate_birth_number($dob) {
        $digits = str_replace('-', '', $dob);
        $sum = array_sum(str_split($digits));
        while ($sum > 9 && $sum != 11 && $sum != 22) {
            $sum = array_sum(str_split($sum));
        }
        return $sum;
    }
}
