<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container-fluid">
    <h2 class="py-2">Ars Rosaic Regional Map</h2>
    <div id="public-map" style="height: 700px; width: 100%; border: 1px solid #ccc;"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initial view covering the regional corridor
    const map = L.map('public-map').setView([47.38, -122.23], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // The controller ensures Madam and Tactical assets are pre-filtered out of this data
    const pins = <?php echo json_encode($data['pins'] ?? []); ?>;
    const covens = <?php echo json_encode($data['covens'] ?? []); ?>;

    /**
     * Plot Public Members
     * Vets: Light Green. Others: Colors of Grade.
     */
    pins.forEach(p => {
        if(p['lat'] && p['lon']) {
            let color = '#ffffff00'; // Default: None (Seeker)

            // Override for Veterans
            if (p['is_v'] == 1) {
                color = '#90ee90'; // (4) Light Green: Veterans
            } else {
                // Traditional Colors of Grade [cite: 2026-02-13]
                switch(parseInt(p['grade'])) {
                    case 2: color = '#808080'; break; // Grey: Neophyte
                    case 3: color = '#ffffff'; break; // White: Initiate
                    case 5: color = '#800080'; break; // Purple: Priestess
                    case 6: color = '#ff0000'; break; // Red: Adeptus
                    case 8: color = '#ffff00'; break; // Yellow: Adeptus-Major
                    case 9: color = '#0000ff'; break; // Blue: Soror/Frater
                }
            }

            L.circleMarker([p['lat'], p['lon']], {
                radius: 7, 
                color: '#666', 
                weight: 1, 
                fillColor: color, 
                fillOpacity: 0.8
            }).addTo(map).bindPopup(`<b>Member:</b> ${p['name']}`);
        }
    });

    /**
     * Plot Covens
     * Displays a generalized 15km radius for the public view.
     */
    covens.forEach(c => {
        if(c['lat'] && c['lon']) {
            let covenColor = (c['is_active'] == 1) ? '#6f42c1' : '#6c757d';
            L.circle([c['lat'], c['lon']], {
                color: covenColor, 
                fillColor: covenColor, 
                fillOpacity: 0.1, 
                radius: 15000 
            }).addTo(map).bindPopup(`<b>Coven:</b> ${c['coven_name']}`);
        }
    });
</script>

<?php require APPROOT . '/views/inc/foot.php'; ?>
