<?php
// path: /app/models/signup_model.php

class signup_model extends model {

    public function get_regions() {
        return $this->fetchAll("SELECT id, name FROM regions ORDER BY id ASC");
    }

    public function get_states_by_region($rid) {
        // This query fetches states strictly based on the region_id column
        return $this->fetchAll("SELECT id, name FROM states WHERE region_id = :rid ORDER BY name ASC", ['rid' => (int)$rid]);
    }

    public function get_counties_by_state($sid) {
        return $this->fetchAll("SELECT id, name FROM counties WHERE state_id = :sid ORDER BY name ASC", ['sid' => (int)$sid]);
    }

    public function register_final_member($data) {
        $rune = $this->fetch("SELECT id FROM rune_reference WHERE id = :v LIMIT 1", ['v' => $data['birth_number']]);
        $ogham = $this->fetch("SELECT id FROM ogham_reference WHERE id = :v LIMIT 1", ['v' => $data['birth_number']]);
        $rosaic = $this->fetch("SELECT id FROM rosaic_reference WHERE id = :v LIMIT 1", ['v' => $data['birth_number']]);

        $sql = "INSERT INTO members (
                    legal_name_encrypted, chosen_name, initials_display, birth_date, 
                    birth_number, rune_id, ogham_id, primary_glyph_id,
                    region_id, state_id, county_id, zodiac_name, created_at
                ) VALUES (
                    :legal, :chosen, :initials, :dob, :birth_num, 
                    :rune, :ogham, :rosaic, :rid, :sid, :cid, :zodiac, NOW()
                )";

        $params = [
            'legal'     => $data['legal_name'] ?? null,
            'chosen'    => $data['chosen_name'] ?? null,
            'initials'  => $data['initials'] ?? null,
            'dob'       => $data['dob'] ?? null,
            'birth_num' => $data['birth_number'] ?? null,
            'rune'      => $rune['id'] ?? null,
            'ogham'     => $ogham['id'] ?? null,
            'rosaic'    => $rosaic['id'] ?? null,
            'rid'       => $data['region_id'] ?? null,
            'sid'       => $data['state_id'] ?? null,
            'cid'       => $data['county_id'] ?? null,
            'zodiac'    => $data['zodiac'] ?? ''
        ];

        $this->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
