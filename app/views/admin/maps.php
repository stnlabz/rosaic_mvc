<?php require APPROOT . '/views/inc/head.php'; ?>

<style>
    #strategic-map { background: #1a1a1a; height: 850px; width: 100%; border: 4px solid #333; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between py-2 bg-dark text-success px-3">
        <h2 class="h5 m-0 font-monospace text-uppercase">Tactical Command | Sector Status</h2>
    </div>
    <div id="strategic-map"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Center map on the Auburn/Tukwila Corridor
    const map = L.map('strategic-map').setView([47.38, -122.23], 11);

    L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 17, attribution: 'Topography: © OpenTopoMap'
    }).addTo(map);

    function drawTacticalGrid() {
        const gridLines = { color: '#00ff00', weight: 0.5, opacity: 0.3, interactive: false };
        for (let l = 24; l <= 50; l += 0.2) L.polyline([[l, -125], [l, -65]], gridLines).addTo(map);
        for (let n = -125; n <= -65; n += 0.2) L.polyline([[24, n], [50, n]], gridLines).addTo(map);
    }
    drawTacticalGrid();

    const members = <?php echo json_encode($data['members'] ?? []); ?>;
    const covens = <?php echo json_encode($data['covens'] ?? []); ?>;

    members.forEach(m => {
        if(m['lat'] && m['lon']) {
            let color = '#ffffff00'; // Default: None (Seeker)

            // 1. Higher Priority Overrides
            if (m['is_m'] == 1) {
                color = '#ff69b4'; // (10) Pink: Madam [cite: 2026-02-20]
            } else if (m['is_t'] == 1) {
                color = '#800000'; // (7) Blood Red: Tactical Veterans
            } else if (m['is_v'] == 1) {
                color = '#90ee90'; // (4) Light Green: Veterans (Adeptus-minor)
            } else {
                // 2. Standard Grade Colors [cite: 2026-02-13]
                switch(parseInt(m['grade'])) {
                    case 2: color = '#808080'; break; // Grey: Neophyte
                    case 3: color = '#ffffff'; break; // White: Initiate
                    case 5: color = '#800080'; break; // Purple: Priestess
                    case 6: color = '#ff0000'; break; // Red: Adeptus
                    case 8: color = '#ffff00'; break; // Yellow: Adeptus-Major
                    case 9: color = '#0000ff'; break; // Blue: Soror/Frater
                }
            }

            L.circleMarker([m['lat'], m['lon']], {
                radius: 8, color: '#fff', weight: 1, fillColor: color, fillOpacity: 0.9
            }).addTo(map).bindPopup(`<div class="font-monospace"><b>${m['name']}</b><br>MGRS: ${m['mgrs_coord']}</div>`);
        }
    });

    covens.forEach(c => {
        if(c['lat'] && c['lon']) {
            let covenColor = (c['is_active'] == 1) ? '#6f42c1' : '#6c757d';
            L.circle([c['lat'], c['lon']], {
                color: covenColor, radius: 40233, fillOpacity: 0.15, weight: 2
            }).addTo(map).bindPopup(`<div class="font-monospace"><b>COVEN:</b> ${c['coven_name']}</div>`);
        }
    });
</script>

<?php require APPROOT . '/views/inc/foot.php'; ?>
