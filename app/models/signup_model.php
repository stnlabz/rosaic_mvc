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

        // --- geo_cache (internal spatial anchor, no external API) ------------
        $address      = trim((string)($data['address'] ?? ''));
        $municipality = trim((string)($data['municipality'] ?? ''));
        $zip_code     = trim((string)($data['zip_code'] ?? ''));
        $addr_key     = strtolower(trim($address) . trim($municipality) . trim($zip_code));
        $address_hash = md5($addr_key);

        if ($address_hash && $address !== '' && $municipality !== '') {
            $geo = $this->fetch(
                "SELECT lat, lon, mgrs_coord, is_locked FROM geo_cache WHERE address_hash = :h LIMIT 1",
                ['h' => $address_hash]
            );

            // If we have a cached point, use it (only if caller didn't already supply lat/lon/mgrs)
            if (is_array($geo) && !empty($geo)) {
                if (empty($data['lat']) && isset($geo['lat'])) {
                    $data['lat'] = $geo['lat'];
                }
                if (empty($data['lon']) && isset($geo['lon'])) {
                    $data['lon'] = $geo['lon'];
                }
                if (empty($data['mgrs_coord']) && isset($geo['mgrs_coord'])) {
                    $data['mgrs_coord'] = $geo['mgrs_coord'];
                }
            } else {
                // Insert a placeholder cache record for later enrichment (Squire can lock/verify)
                $this->query(
                    "INSERT INTO geo_cache (address_hash, full_address, municipality, lat, lon, mgrs_coord, is_locked)
                     VALUES (:h, :full, :muni, NULL, NULL, NULL, 0)
                     ON DUPLICATE KEY UPDATE full_address = VALUES(full_address), municipality = VALUES(municipality)",
                    [
                        'h'    => $address_hash,
                        'full' => $address,
                        'muni' => $municipality
                    ]
                );
            }
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

            // controller stores dob, model inserts birth_date
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
            'zip_code'    => $data['zip_code'] ?? '',

            'mgrs'        => $data['mgrs_coord'] ?? null,
            'contact'     => $data['contact_text'] ?? null,
            'zodiac'      => $data['zodiac'] ?? '',

            'lat'         => $data['lat'] ?? null,
            'lon'         => $data['lon'] ?? null,
            'is_v'        => $data['is_v'] ?? 0,
        ];

        $this->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
