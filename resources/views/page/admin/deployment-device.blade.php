@extends('layout.sidebar')
@section('content')

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">

<style>
    #map { height: 600px; width: 100%; border-radius: 8px; }
    #modal-map { height: 300px; width: 100%; border-radius: 8px; }

    /* --- Kontrol Peta --- */
    .map-controls .btn,
.map-controls .form-control {
    height: 38px; /* Standar tinggi Bootstrap form-control */
    border-radius: 0.375rem; /* Biar sama lengkungannya */
    font-size: 1rem;
    padding: 0.375rem 0.75rem;
    box-sizing: border-box;
}

    .map-controls {
        position: absolute; top: 15px; left: 15px; z-index: 401;
        display: flex; gap: 10px; align-items: center;
    }
    .search-wrapper {
        position: relative;
    }
    .map-search-input {
        padding-right: 2.5rem; /* Ruang untuk tombol reset */
    }
    .search-reset-button {
        position: absolute; right: 0.5rem; top: 50%;
        transform: translateY(-50%);
        background: none; border: none;
        color: #6c757d; font-size: 1.2rem;
        display: none; /* Sembunyi secara default */
        padding: 0 .5rem;
        line-height: 1;
    }

    /* --- Offcanvas Sidebar --- */
    .offsidebar-custom {
        position: fixed; top: 0; left: 0; width: 30vw; height: 100%;
        background-color: #fff; z-index: 1040;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        box-shadow: 0 0 1rem rgba(0, 0, 0, 0.2);
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        display: flex; flex-direction: column;
    }
    .offsidebar-custom.is-open { transform: translateX(0); }
    .offsidebar-header {
        padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6;
        display: flex; justify-content: space-between; align-items: center;
    }
    .offsidebar-body { overflow-y: auto; flex-grow: 1; }
    .offsidebar-title { margin-bottom: 0; font-size: 1.25rem; }
    .sidebar-search-container { padding: 1rem 1.5rem; border-bottom: 1px solid #e9ecef;}

    /* --- Backdrop --- */
    #offsidebar-backdrop {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5); z-index: 1039;
        opacity: 0; pointer-events: none;
        transition: opacity 0.3s ease-in-out;
    }
    #offsidebar-backdrop.is-visible { opacity: 1; pointer-events: auto; }
</style>

<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
             <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Peta Deployment Perangkat</li>
                        </ul>
                    </div>
                </div>
             </div>
        </div>

        <div style="position: relative;">
            <div class="map-controls">
                <button class="btn btn-primary" style="margin-left: 40px" id="map-trigger-button">
                    <i class="ti ti-table"></i> List Data
                </button>
                <button id="reset-view-btn" class="btn btn-secondary" title="Kembalikan Tampilan Awal">
                    <i class="ti ti-refresh"></i>
                </button>
                <div class="d-flex align-items-center" style="gap: 10px">
                    <div class="search-wrapper">
                        <input type="text" id="map-search" class="form-control map-search-input" placeholder="Cari nama atau instansi...">
                        <button id="map-search-reset" class="search-reset-button">×</button>
                    </div>
                </div>
            </div>
            
            <div id="map"></div>
        </div>
    </div>
</div>

<!-- 'offsidebarku' Custom -->
<div class="offsidebar-custom" id="myOffsidebar">
    <div class="offsidebar-header">
        <h5 class="offsidebar-title">Tabel Deployment</h5>
        <button type="button" class="btn-close" id="closeOffsidebar"></button>
    </div>
    <div class="sidebar-search-container">
        <div class="search-wrapper">
             <input type="text" id="sidebar-search" class="form-control" placeholder="Cari nama atau instansi...">
             <button id="sidebar-search-reset" class="search-reset-button">×</button>
        </div>
    </div>
    <div class="offsidebar-body px-0">
        <div id="deployment-list-container">
            @forelse($deployments as $deployment)
                <div class="list-item border-bottom py-3 px-3"
                    data-name="{{ strtolower($deployment->user?->profile?->name ?? '') }}"
                    data-institution="{{ strtolower($deployment->user?->profile?->institution ?? '') }}"
                    data-id="{{ $deployment->id }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="fw-bold mb-0 text-truncate">{{ $deployment->user?->profile?->name ?? 'N/A' }}</p>
                        <button class="btn btn-sm btn-light btn-details flex-shrink-0 ms-2"
                                data-bs-toggle="modal"
                                data-bs-target="#deploymentDetailModal"
                                data-deployment-id="#{{ $deployment->user?->id }}"
                                data-recipient-name="{{ $deployment->user?->profile?->name ?? $deployment->user?->name ?? 'N/A' }}"
                                data-recipient-phone="{{ $deployment->user?->profile?->phone ?? 'N/A' }}"
                                data-institution="{{ $deployment->user?->profile?->institution ?? 'N/A' }}"
                                data-address="{{ $deployment->user?->profile?->address ?? 'N/A' }}"
                                data-status="Deployed"
                                data-reference="{{ $deployment->user?->profile?->reference ?? '' }}"
                                data-details='@json($deployment->details->map(function($d) {
                                    return [
                                        'device' => ($d->storedDevice?->device?->brand ?? 'N/A') . ' ' . ($d->storedDevice?->device?->model ?? ''),
                                        'quantity' => $d->quantity
                                    ];
                                }))'>
                            Lihat Detail
                        </button>
                    </div>
                    <div class="d-flex text-muted mt-2 small">
                        <span class="me-3 text-truncate"><i class="ti ti-building me-1"></i> {{ $deployment->user?->profile?->institution ?? '-' }}</span>
                        <span class="me-3"><i class="ti ti-device-desktop me-1"></i> {{ $deployment->details->count() }} Device(s)</span>
                        <span><i class="ti ti-circle-check text-success me-1"></i> Deployed</span>
                    </div>
                </div>
            @empty
                <div class="text-center p-5 text-muted">Tidak ada data deployment.</div>
            @endforelse
        </div>
    </div>
</div>
<!-- Backdrop untuk sidebar -->
<div id="offsidebar-backdrop"></div>

<!-- =================================================================== -->
<!-- KODE MODAL DIPULIHKAN -->
<!-- =================================================================== -->
<div class="modal fade" id="deploymentDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl"> <!-- Menggunakan modal-xl untuk ruang lebih -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Deployment <span id="modal-deployment-id" class="fw-bold"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Kolom Kiri untuk Peta -->
                    <div class="col-md-5 bg-light d-flex flex-column p-3">
                        <div id="modal-map" class="flex-grow-1 rounded" style="min-height: 400px;"></div>
                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-primary" id="modal-navigate-button">
                                <i class="ti ti-map-pin"></i> Navigasi lokasi ini
                            </button>
                        </div>
                    </div>

                    <!-- Kolom Kanan untuk Informasi -->
                    <div class="col-md-7 p-4">
                        <div class="mb-4">
                            <h6>Informasi Penerima</h6>
                            <hr class="mt-1">
                            <p><strong>Nama:</strong> <span id="modal-recipient-name"></span></p>
                            <p><strong>Telepon:</strong> <span id="modal-recipient-phone"></span></p>
                            <p><strong>Instansi:</strong> <span id="modal-institution"></span></p>
                            <p><strong>Alamat:</strong> <span id="modal-address"></span></p>
                        </div>

                        <div class="mb-4">
                             <h6>Informasi Deployment</h6>
                             <hr class="mt-1">
                             <p><strong>Status:</strong> <span id="modal-status"></span></p>
                             <!-- BARIS BARU: Menambahkan elemen untuk menampilkan estimasi jarak -->
                             <p><strong>Straight Line</strong> <span id="modal-distance" style="color : rgb(0, 119, 255)" class="fw-bold"></span></p>
                        </div>

                        <div>
                            <h6>Perangkat Terpasang</h6>
                            <hr class="mt-1">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr><th>Perangkat</th><th class="text-end">Jumlah</th></tr>
                                </thead>
                                <tbody id="modal-device-details"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Leaflet & jQuery JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    // --- [MODIFIKASI] ---
    // Fungsi untuk menghitung jarak antara dua koordinat (Haversine formula)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius bumi dalam km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a =
            0.5 - Math.cos(dLat)/2 +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            (1 - Math.cos(dLon))/2;
        return R * 2 * Math.asin(Math.sqrt(a)); // Jarak dalam km
    }

    $(document).ready(function () {
        // --- Inisialisasi Sidebar ---
        const myOffsidebar = $('#myOffsidebar');
        const sidebarBackdrop = $('#offsidebar-backdrop');
        $('#map-trigger-button').on('click', () => {
            myOffsidebar.addClass('is-open');
            sidebarBackdrop.addClass('is-visible');
        });
        $('#closeOffsidebar, #offsidebar-backdrop').on('click', () => {
            myOffsidebar.removeClass('is-open');
            sidebarBackdrop.removeClass('is-visible');
        });

        // --- Inisialisasi Peta Utama ---
        const map = L.map('map');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const deployments = @json($deployments);
        const myadmin = @json($myadmin);
        const markersForBounds = [];
        const deploymentMarkers = [];

        const redIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        // "Lokasi Saya" marker
        const myRef = myadmin?.profile?.reference;
        if (myRef && myRef.includes(',')) {
            const [lat, lng] = myRef.split(',').map(c => parseFloat(c.trim()));
            if (!isNaN(lat) && !isNaN(lng)) {
                const myLocationDot = L.circleMarker([lat, lng], {
                    radius: 8, color: '#005eff', fillColor: '#3388ff', fillOpacity: 0.9,
                    pane: 'markerPane', zIndexOffset: 1000
                }).bindPopup('<b>Lokasi saya</b>').addTo(map);
                markersForBounds.push(myLocationDot);
            }
        }




        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' });
var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: '© Esri' });

var baseMaps = {
    "Peta Jalan": osm,
    "Satelit": satellite
};

// 2. Definisikan Overlay Maps (Gunakan marker group yang sudah Anda buat)
var markerLayer = L.layerGroup(deploymentMarkers); // Pastikan markerLayer ini yang Anda filter

// (Opsional) Jika Anda menerapkan heatmap
// var heatLayer = L.heatLayer(...); 

var overlayMaps = {
    "Lokasi Deployment": markerLayer,
    // "Peta Panas": heatLayer 
};

// 3. Tambahkan kontrol ke peta
map.addLayer(osm); // Tambahkan layer default
map.addLayer(markerLayer); // Tambahkan marker default
L.control.layers(baseMaps, overlayMaps).addTo(map);



// Di dalam $(document).ready()
$('.list-item').on('click', function() {
    const deploymentId = $(this).data('id');
    const deployment = deployments.find(d => d.id === deploymentId);
    
    if (deployment && deployment.user?.profile?.reference) {
        const [lat, lng] = deployment.user.profile.reference.split(',').map(parseFloat);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            // Animasi peta ke lokasi
            map.flyTo([lat, lng], 16); // 16 adalah level zoom

            // Cari marker yang sesuai dan buka popupnya
            const markerToOpen = deploymentMarkers.find(m => m.options.searchData.id === deploymentId);
            if (markerToOpen) {
                markerToOpen.openPopup();
            }
        }
    }
});






        let distanceLine;
const myAdminRef = myadmin?.profile?.reference;

$('.list-item').on('mouseenter', function() {
    const deploymentId = $(this).data('id');
    const deployment = deployments.find(d => d.id === deploymentId);
    const clientRef = deployment?.user?.profile?.reference;

    if (myAdminRef && clientRef) {
        const [myLat, myLng] = myAdminRef.split(',').map(parseFloat);
        const [clientLat, clientLng] = clientRef.split(',').map(parseFloat);

        if (!isNaN(myLat) && !isNaN(clientLat)) {
            // Hapus garis sebelumnya jika ada
            if (distanceLine) map.removeLayer(distanceLine);
            
            // Buat garis baru
            distanceLine = L.polyline([[myLat, myLng], [clientLat, clientLng]], {
                color: 'blue',
                weight: 3,
                opacity: 0.7,
                dashArray: '5, 10'
            }).addTo(map);
        }
    }
});


$('.list-item').on('mouseleave', function() {
    // Hapus garis saat kursor keluar
    if (distanceLine) {
        map.removeLayer(distanceLine);
    }
});

        // Deployment Markers
        deployments.forEach((item) => {
            const ref = item.user?.profile?.reference;
            const profile = item.user?.profile;
            if (ref && ref.includes(',')) {
                const [lat, lng] = ref.split(',').map(c => parseFloat(c.trim()));
                if (!isNaN(lat) && !isNaN(lng)) {
                    const devicesList = item.details.map(d => `${(d.stored_device?.device?.brand ?? 'N/A') + ' ' + (d.stored_device?.device?.model ?? '')} (${d.quantity})`).join('<br>') || '-';
                    const popupContent = `<strong>${profile?.name ?? 'N/A'}</strong><br>Instansi: ${profile?.institution ?? '-'}<br>Alamat: ${profile?.address ?? '-'}<br>Telp: ${profile?.phone ?? '-'}<br><u>Perangkat:</u><br>${devicesList}`;
                    
                    const marker = L.marker([lat, lng], { icon: redIcon }).bindPopup(popupContent);
                    
                    marker.options.searchData = {
                        id: item.id,
                        name: (profile?.name ?? '').toLowerCase(),
                        institution: (profile?.institution ?? '').toLowerCase()
                    };

                    deploymentMarkers.push(marker);
                    markersForBounds.push(marker);
                }
            }
        });

        if (markersForBounds.length > 0) {
            map.fitBounds(L.featureGroup(markersForBounds).getBounds().pad(0.15));
        } else {
            map.setView([-2.5, 118], 5);
        }
        
        $('#reset-view-btn').on('click', function() {
        // Cek lagi apakah ada marker untuk di-bound
        if (markersForBounds.length > 0) {
            // Jalankan kembali fungsi yang sama persis dengan saat inisialisasi peta
            map.fitBounds(L.featureGroup(markersForBounds).getBounds().pad(0.15));
        } else {
            // Jika tidak ada marker, kembalikan ke view default Indonesia
            map.setView([-2.5, 118], 5);
        }

        // Opsional: Jika Anda ingin pencarian juga direset, uncomment baris di bawah
        resetSearch();
    });

        

        // --- LOGIKA PENCARIAN REALTIME ---
        const mapSearchInput = $('#map-search');
        const mapResetBtn = $('#map-search-reset');
        const sidebarSearchInput = $('#sidebar-search');
        const sidebarResetBtn = $('#sidebar-search-reset');

        function filterContent(query) {
            const lowerCaseQuery = query.toLowerCase().trim();
            deploymentMarkers.forEach(marker => {
                const data = marker.options.searchData;
                const isMatch = data.name.includes(lowerCaseQuery) || data.institution.includes(lowerCaseQuery);
                if (isMatch) {
                    if (!map.hasLayer(marker)) marker.addTo(map);
                } else {
                    if (map.hasLayer(marker)) marker.removeFrom(map);
                }
            });
            $('.list-item').each(function() {
                const name = $(this).data('name');
                const institution = $(this).data('institution');
                const isMatch = name.includes(lowerCaseQuery) || institution.includes(lowerCaseQuery);
                $(this).toggle(isMatch);
            });
        }
        function handleSearchInput(input, resetButton) {
            const value = $(input).val();
            if ($(input).is('#map-search')) sidebarSearchInput.val(value);
            else mapSearchInput.val(value);
            
            const showReset = value.length > 0;
            mapResetBtn.toggle(showReset);
            sidebarResetBtn.toggle(showReset);
            filterContent(value);
        }
        function resetSearch() {
            mapSearchInput.val('').trigger('keyup');
        }
        mapSearchInput.on('keyup', function() { handleSearchInput(this, mapResetBtn); });
        sidebarSearchInput.on('keyup', function() { handleSearchInput(this, sidebarResetBtn); });
        mapResetBtn.on('click', resetSearch);
        sidebarResetBtn.on('click', resetSearch);
        filterContent('');

        // --- KODE LOGIKA MODAL ---
        let modalMap;
        $('#deploymentDetailModal').on('shown.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const modal = $(this);
            

            modal.find('#modal-deployment-id').text(button.data('deployment-id'));
            modal.find('#modal-recipient-name').text(button.data('recipient-name'));
            modal.find('#modal-recipient-phone').text(button.data('recipient-phone'));
            modal.find('#modal-institution').text(button.data('institution'));
            modal.find('#modal-address').text(button.data('address'));
            modal.find('#modal-status').html('<span class="badge bg-success">Deployed</span>');

            const clientReference = button.data('reference')?.toString();
            const clientCoords = clientReference?.split(',');
            
            // --- [MODIFIKASI] ---
            // Logika untuk Menghitung dan Menampilkan Jarak
            const distanceSpan = modal.find('#modal-distance');
            const myAdminReference = myadmin?.profile?.reference;

            if (clientReference && myAdminReference) {
                const myAdminCoords = myAdminReference.split(',');
                if (clientCoords.length === 2 && myAdminCoords.length === 2) {
                    const lat1 = parseFloat(myAdminCoords[0]);
                    const lon1 = parseFloat(myAdminCoords[1]);
                    const lat2 = parseFloat(clientCoords[0]);
                    const lon2 = parseFloat(clientCoords[1]);

                    if (!isNaN(lat1) && !isNaN(lon1) && !isNaN(lat2) && !isNaN(lon2)) {
                        const distance = calculateDistance(lat1, lon1, lat2, lon2);
                        distanceSpan.text(`${distance.toFixed(2)} km dari lokasi Anda`);
                    } else {
                        distanceSpan.text('Koordinat tidak valid');
                    }
                } else {
                    distanceSpan.text('Format koordinat salah');
                }
            } else {
                distanceSpan.text('Data lokasi tidak lengkap');
            }
            // --- Akhir Modifikasi ---

            if (clientCoords && clientCoords.length === 2) {
                const lat = parseFloat(clientCoords[0]);
                const lng = parseFloat(clientCoords[1]);
                if (!isNaN(lat) && !isNaN(lng)) {
                    if (!modalMap) {
                        modalMap = L.map('modal-map').setView([lat, lng], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
                    } else {
                        modalMap.setView([lat, lng], 16);
                    }
                    modalMap.eachLayer(layer => { if (layer instanceof L.Marker) modalMap.removeLayer(layer); });
                    L.marker([lat, lng], { icon: redIcon }).addTo(modalMap);
                    setTimeout(() => modalMap.invalidateSize(), 10);
                }
            }
            
            const navigateButton = $('#modal-navigate-button');
            if (clientReference) {
                navigateButton.show();
                navigateButton.off('click').on('click', function() {
                    const url = `https://www.google.com/maps/dir/?api=1&destination=${clientReference}`;
                    window.open(url, '_blank');
                });
            } else {
                navigateButton.hide();
            }

            const detailBody = modal.find('#modal-device-details');
            detailBody.empty();
            let details = button.data('details');
            if (details && Array.isArray(details) && details.length > 0) {
                details.forEach(item => {
                    detailBody.append(`<tr><td>${item.device}</td><td class="text-end">${item.quantity}</td></tr>`);
                });
            } else {
                detailBody.append('<tr><td colspan="2" class="text-center">Tidak ada data perangkat</td></tr>');
            }
        });
    });
</script>

@endsection