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
        $birth_number = (int)($data['birth_number'] ?? 0);

        $rune   = $this->fetch("SELECT id FROM rune_reference WHERE id = :v LIMIT 1",   ['v' => $birth_number]);
        $ogham  = $this->fetch("SELECT id FROM ogham_reference WHERE id = :v LIMIT 1",  ['v' => $birth_number]);
        $rosaic = $this->fetch("SELECT id FROM rosaic_reference WHERE id = :v LIMIT 1", ['v' => $birth_number]);

        $tarot = null;
        try {
            $tarot = $this->fetch("SELECT id FROM tarot_reference WHERE id = :v LIMIT 1", ['v' => $birth_number]);
        } catch (\Throwable $e) {
            $tarot = null;
        }

        // ---------------------------
        // GEO CACHE (zip included)
        // ---------------------------

        $address      = trim((string)($data['address'] ?? ''));
        $municipality = trim((string)($data['municipality'] ?? ''));
        $zip_code     = trim((string)($data['zip_code'] ?? ''));

        $normalized   = strtolower($address . $municipality . $zip_code);
        $address_hash = md5($normalized);

        if ($address && $municipality && $zip_code) {

            $geo = $this->fetch(
                "SELECT lat, lon, mgrs_coord FROM geo_cache WHERE address_hash = :h LIMIT 1",
                ['h' => $address_hash]
            );

            if ($geo) {
                if (empty($data['lat'])) {
                    $data['lat'] = $geo['lat'];
                }
                if (empty($data['lon'])) {
                    $data['lon'] = $geo['lon'];
                }
                if (empty($data['mgrs_coord'])) {
                    $data['mgrs_coord'] = $geo['mgrs_coord'];
                }
            } else {

                $this->query(
                    "INSERT INTO geo_cache 
                        (address_hash, full_address, municipality, zip_code, lat, lon, mgrs_coord, is_locked)
                     VALUES 
                        (:hash, :full, :muni, :zip, NULL, NULL, NULL, 0)
                     ON DUPLICATE KEY UPDATE 
                        full_address = VALUES(full_address),
                        municipality = VALUES(municipality),
                        zip_code = VALUES(zip_code)",
                    [
                        'hash' => $address_hash,
                        'full' => $address,
                        'muni' => $municipality,
                        'zip'  => $zip_code
                    ]
                );
            }
        }

        // ---------------------------
        // MEMBER INSERT
        // ---------------------------

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
            'legal'        => $data['legal_name'] ?? null,
            'chosen'       => $data['chosen_name'] ?? null,
            'initials'     => $data['initials'] ?? null,
            'sex'          => $data['sex'] ?? '',
            'dob'          => $data['dob'] ?? null,
            'birth_num'    => $birth_number,
            'rune'         => $rune['id'] ?? null,
            'ogham'        => $ogham['id'] ?? null,
            'rosaic'       => $rosaic['id'] ?? null,
            'tarot'        => $tarot['id'] ?? null,
            'rid'          => (int)($data['region_id'] ?? 0),
            'sid'          => (int)($data['state_id'] ?? 0),
            'cid'          => (int)($data['county_id'] ?? 0),
            'address'      => $address,
            'municipality' => $municipality,
            'zip_code'     => $zip_code,
            'is_v'         => $data['is_v'] ?? 0,
            'mgrs'         => $data['mgrs_coord'] ?? null,
            'contact'      => $data['contact_text'] ?? null,
            'zodiac'       => $data['zodiac'] ?? '',
            'lat'          => $data['lat'] ?? null,
            'lon'          => $data['lon'] ?? null
        ];

        $this->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
