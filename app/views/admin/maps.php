<?php require APPROOT . '/views/inc/head.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .tactical-frame { height: 700px; width: 100%; background: #000; border: 1px solid #333; }
    #strategic-map { height: 100%; width: 100%; }

    @keyframes radar-ping {
        0% { transform: scale(0.5); opacity: 1; }
        80% { transform: scale(2.5); opacity: 0; }
        100% { transform: scale(3); opacity: 0; }
    }

    .ping-effect {
        border-radius: 50%;
        background: currentColor;
        animation: radar-ping 2s infinite ease-out;
    }
</style>

<div class="container-fluid py-2">
    <div class="bg-dark text-success p-2 font-monospace border-bottom border-success mb-3">
        COMMAND OVERWATCH // STABLE BUILD
    </div>
    <div class="tactical-frame">
        <div id="strategic-map"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const members = <?php echo json_encode($members ?? []); ?>;

    const map = L.map('strategic-map');

    L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 17
    }).addTo(map);

    const boundsGroup = L.featureGroup();

    members.forEach(m => {

        const lat = parseFloat(m.lat);
        const lon = parseFloat(m.lon);
        const isM = parseInt(m.is_m ?? 0);
        const name = m.chosen_name ?? '';
        const mgrs = m.mgrs_coord ?? '';

        if (!isNaN(lat) && !isNaN(lon)) {

            let fill = 'rgba(0,0,0,0)';
            let border = '#00ff00';
            let opacity = 0;

            // Madam precedence
            if (isM === 1 || name === 'M.R.') {
                fill = '#ff69b4';
                border = '#ffffff';
                opacity = 1;
            }

            // Ping layer
            L.marker([lat, lon], {
                icon: L.divIcon({
                    className: '',
                    html: `<div class="ping-effect" style="color:${border}; width:14px; height:14px;"></div>`,
                    iconSize: [14, 14]
                }),
                interactive: false
            }).addTo(map);

            // Main marker
            const marker = L.circleMarker([lat, lon], {
                radius: 10,
                color: border,
                weight: 2,
                fillColor: fill,
                fillOpacity: opacity
            }).addTo(map)
              .bindPopup(`<b>${name}</b><br>MGRS: ${mgrs}`);

            boundsGroup.addLayer(marker);
        }
    });

    if (boundsGroup.getLayers().length > 0) {
        map.fitBounds(boundsGroup.getBounds(), { padding: [50, 50] });
    } else {
        map.setView([47.40, -122.25], 11);
    }

    setTimeout(() => {
        map.invalidateSize();
    }, 300);
</script>

<?php require APPROOT . '/views/inc/foot.php'; ?>
