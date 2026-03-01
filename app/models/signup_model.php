<?php
// path: /app/models/signup_model.php

class signup_model extends model
{
    public function get_regions()
    {
        return $this->fetchAll("SELECT id, name FROM regions ORDER BY id ASC");
    }

    public function get_states_by_region($rid)
    {
        return $this->fetchAll(
            "SELECT id, name FROM states WHERE region_id = :rid ORDER BY name ASC",
            ['rid' => (int)$rid]
        );
    }

    public function get_counties_by_state($sid)
    {
        return $this->fetchAll(
            "SELECT id, name FROM counties WHERE state_id = :sid ORDER BY name ASC",
            ['sid' => (int)$sid]
        );
    }

    public function register_final_member($data)
    {
        // birth_number must exist or everything downstream becomes NULL
        $birth_number = (int)($data['birth_number'] ?? 0);

        // references (1-9 style mapping)
        $rune   = $this->fetch("SELECT id FROM rune_reference WHERE id = :v LIMIT 1",   ['v' => $birth_number]);
        $ogham  = $this->fetch("SELECT id FROM ogham_reference WHERE id = :v LIMIT 1",  ['v' => $birth_number]);
        $rosaic = $this->fetch("SELECT id FROM rosaic_reference WHERE id = :v LIMIT 1", ['v' => $birth_number]);

        // optional if table exists
        $tarot = null;
        try {
            $tarot = $this->fetch("SELECT id FROM tarot_reference WHERE id = :v LIMIT 1", ['v' => $birth_number]);
        } catch (\Throwable $e) {
            $tarot = null;
        }

        $sql = "INSERT INTO members (
                    legal_name_encrypted,
                    chosen_name,
                    initials_display,
                    sex,
                    birth_date,
                    birth_number,
                    primary_glyph_id,
                    tarot_id,
                    ogham_id,
                    rune_id,
                    region_id,
                    state_id,
                    county_id,
                    address,
                    municipality,
                    zip_code,
                    is_v,
                    mgrs_coord,
                    contact_text,
                    zodiac_name,
                    lat,
                    lon,
                    created_at
                ) VALUES (
                    :legal,
                    :chosen,
                    :initials,
                    :sex,
                    :dob,
                    :birth_num,
                    :rosaic,
                    :tarot,
                    :ogham,
                    :rune,
                    :rid,
                    :sid,
                    :cid,
                    :address,
                    :municipality,
                    :zip_code,
                    :is_v,
                    :mgrs,
                    :contact,
                    :zodiac,
                    :lat,
                    :lon,
                    NOW()
                )";

        $params = [
            'legal'       => $data['legal_name'] ?? null,
            'chosen'      => $data['chosen_name'] ?? null,
            'initials'    => $data['initials'] ?? null,

            // keep if your schema enforces NOT NULL; otherwise harmless
            'sex'         => $data['sex'] ?? '',

            // this is the actual bug: controller stores dob, model inserts birth_date
            'dob'         => $data['dob'] ?? null,

            'birth_num'   => $birth_number,

            'rune'        => $rune['id'] ?? null,
            'ogham'       => $ogham['id'] ?? null,
            'rosaic'      => $rosaic['id'] ?? null,
            'tarot'       => $tarot['id'] ?? null,

            'rid'         => (int)($data['region_id'] ?? 0),
            'sid'         => (int)($data['state_id'] ?? 0),
            'cid'         => (int)($data['county_id'] ?? 0),

            'address'     => $data['address'] ?? '',
            'municipality'=> $data['municipality'] ?? '',
            'zip_code'    => $data['zip'] ?? '',

            'mgrs'        => $data['mgrs_coord'] ?? null,
            'contact'     => $data['contact_text'] ?? null,
            'zodiac'      => $data['zodiac'] ?? '',

            'lat'         => $data['lat'] ?? null,
            'lon'         => $data['lon'] ?? null,
        ];

        $this->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
