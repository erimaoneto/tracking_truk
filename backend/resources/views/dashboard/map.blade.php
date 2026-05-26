@extends('layouts.app')

@section('content')
<div class="top-header">
    <div class="page-title">
        <h2>Dashboard Utama</h2>
        <p>Ringkasan real-time kontrak sewa gas, armada pengangkut, dan status supir</p>
    </div>
</div>

<!-- Grid Ringkasan / Stats -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon blue">📂</div>
        <div class="stat-info">
            <h4>Kontrak Diterima</h4>
            <div class="value">{{ $receivedContracts->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">⚡</div>
        <div class="stat-info">
            <h4>Sedang Berlangsung</h4>
            <div class="value">{{ $ongoingContracts->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gray" style="background: rgba(100, 116, 139, 0.15); color: #64748b;">🏁</div>
        <div class="stat-info">
            <h4>Kontrak Selesai</h4>
            <div class="value">{{ $completedContracts->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">🚚</div>
        <div class="stat-info">
            <h4>Armada Aktif / Jalan</h4>
            <div class="value">{{ $ongoingTrips->count() }} / {{ $ongoingTrips->count() + $availableVehiclesCount }}</div>
        </div>
    </div>
</div>

<!-- Peta Live Tracking -->
<div class="table-container" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <div class="table-header" style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981; animation: pulse 1.5s infinite;"></span>
            Peta Live Pelacakan Armada Aktif
        </h3>
    </div>
    <div class="map-container" style="height: 300px; border-radius: 12px; overflow: hidden; border: 1px solid var(--glass-border);">
        <div id="map" style="height: 100%; width: 100%;"></div>
    </div>
</div>

<!-- Manajemen & Informasi Kontrak Dashboard -->
<div class="table-container" style="margin-bottom: 0;">
    <div class="table-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3 style="font-size: 1.1rem; font-weight: 600;">Manajemen Kontrak Sewa Gas</h3>
        
        <!-- Tab Navigation Buttons -->
        <div class="tab-navigation" style="display: flex; gap: 6px; background: rgba(0,0,0,0.03); padding: 4px; border-radius: 8px; border: 1px solid var(--glass-border);">
            <button class="tab-btn active" data-target="tab-ongoing" style="padding: 6px 14px; font-size: 0.85rem; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; background: var(--accent-color); color: white; transition: all 0.2s;">
                ⚡ Berlangsung ({{ $ongoingContracts->count() }})
            </button>
            <button class="tab-btn" data-target="tab-received" style="padding: 6px 14px; font-size: 0.85rem; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; background: transparent; color: var(--text-secondary); transition: all 0.2s;">
                📂 Diterima ({{ $receivedContracts->count() }})
            </button>
            <button class="tab-btn" data-target="tab-completed" style="padding: 6px 14px; font-size: 0.85rem; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; background: transparent; color: var(--text-secondary); transition: all 0.2s;">
                🏁 Selesai ({{ $completedContracts->count() }})
            </button>
        </div>
    </div>

    <!-- TAB 1: KONTRAK SEDANG BERLANGSUNG -->
    <div id="tab-ongoing" class="tab-content">
        <div class="contracts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
            @forelse($ongoingContracts as $contract)
                @include('dashboard._contract_card', ['contract' => $contract, 'theme' => 'success'])
            @empty
                <div style="grid-column: span 3; text-align: center; color: var(--text-secondary); padding: 40px 20px;">
                    Tidak ada kontrak sewa gas yang sedang berlangsung aktif saat ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 2: KONTRAK DITERIMA / BARU -->
    <div id="tab-received" class="tab-content" style="display: none;">
        <div class="contracts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
            @forelse($receivedContracts as $contract)
                @include('dashboard._contract_card', ['contract' => $contract, 'theme' => 'info'])
            @empty
                <div style="grid-column: span 3; text-align: center; color: var(--text-secondary); padding: 40px 20px;">
                    Belum ada kontrak baru yang diterima untuk masa depan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 3: KONTRAK SELESAI -->
    <div id="tab-completed" class="tab-content" style="display: none;">
        <div class="contracts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
            @forelse($completedContracts as $contract)
                @include('dashboard._contract_card', ['contract' => $contract, 'theme' => 'secondary'])
            @empty
                <div style="grid-column: span 3; text-align: center; color: var(--text-secondary); padding: 40px 20px;">
                    Belum ada riwayat kontrak yang diselesaikan.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL ARMADA & SUPIR ================= -->
<div id="contractDetailModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Detail Alokasi Armada & Supir</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <h4 style="font-size: 1.05rem; font-weight: bold; margin-bottom: 0.75rem; color: var(--accent-color); border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; display: flex; justify-content: space-between;">
                <span>Kontrak: <strong id="modal-ctr-num" style="color:var(--text-primary);"></strong></span>
                <span style="font-size: 0.85rem; font-weight: normal; color: var(--text-secondary);">Klien: <strong id="modal-client-name" style="color:var(--text-primary);"></strong></span>
            </h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem; margin-top: 1rem;">
                <!-- Kolom Kiri: Detail Armada -->
                <div style="background: rgba(59, 130, 246, 0.04); padding: 1.25rem; border-radius: 12px; border: 1px solid rgba(59, 130, 246, 0.15);">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.75rem;">
                        <span style="font-size: 1.3rem;">🚚</span>
                        <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e3a8a;">Armada Truk</h4>
                    </div>
                    <div class="detail-grid" style="grid-template-columns: 1fr; gap: 0.6rem; font-size: 0.85rem;">
                        <div>
                            <span style="color: var(--text-secondary); display:block; font-size:0.75rem;">Plat Nomor</span>
                            <strong id="modal-veh-plate" style="font-size: 1.1rem; color: var(--accent-color); font-weight: 700; letter-spacing: 0.05em;">-</strong>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary); display:block; font-size:0.75rem;">Tipe Kendaraan</span>
                            <strong id="modal-veh-type">-</strong>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary); display:block; font-size:0.75rem;">Kapasitas Muatan</span>
                            <strong id="modal-veh-capacity">-</strong>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail Supir -->
                <div style="background: rgba(16, 185, 129, 0.04); padding: 1.25rem; border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.15);">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.75rem;">
                        <span style="font-size: 1.3rem;">👤</span>
                        <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #064e3b;">Supir Pengantar</h4>
                    </div>
                    <div class="detail-grid" style="grid-template-columns: 1fr; gap: 0.6rem; font-size: 0.85rem;">
                        <div>
                            <span style="color: var(--text-secondary); display:block; font-size:0.75rem;">Nama Lengkap</span>
                            <strong id="modal-drv-name" style="font-size: 1.1rem; color: var(--text-primary); font-weight: 700;">-</strong>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary); display:block; font-size:0.75rem;">NIK KTP (16 Digit)</span>
                            <strong id="modal-drv-nik" style="font-family: monospace;">-</strong>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary); display:block; font-size:0.75rem;">No. Telepon / WA</span>
                            <strong id="modal-drv-phone">-</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="background: rgba(0,0,0,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--glass-border); font-size: 0.85rem;">
                <strong>Catatan Alokasi:</strong> Supir dan armada truk ini bersifat <em>dedicated</em> (dikhususkan) untuk melayani pengantaran gas milik klien selama masa kontrak kerja sama sewa gas aktif.
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-premium btn-close-modal" style="background: var(--text-secondary); box-shadow: none;">Tutup</button>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Init Leaflet map with clean light theme tiles
        var map = L.map('map').setView([-6.2088, 106.8456], 12);
        
        // CartoDB Positron for a beautiful, clean light look
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        var trips = @json($ongoingTrips);
        var markers = {};

        // Custom Truck Icon SVG
        const truckSvg = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>`;

        function createTruckIcon(color) {
            return L.divIcon({
                html: `<div style="background:${color}; padding:8px; border-radius:50%; box-shadow:0 0 10px ${color}; color:white; display:flex; align-items:center; justify-content:center;">${truckSvg}</div>`,
                className: 'custom-leaflet-icon',
                iconSize: [40, 40],
                iconAnchor: [20, 20],
                popupAnchor: [0, -20]
            });
        }

        trips.forEach(function(trip) {
            if(trip.latest_gps_log) {
                var lat = trip.latest_gps_log.latitude;
                var lng = trip.latest_gps_log.longitude;
                var speed = trip.latest_gps_log.speed;
                var plate = trip.vehicle ? trip.vehicle.license_plate : 'Unknown';
                var driverName = trip.driver && trip.driver.user ? trip.driver.user.name : 'Unknown';
                
                var color = speed > 0 ? '#10b981' : '#ef4444'; // Green if moving, Red if stopped
                
                var marker = L.marker([lat, lng], {icon: createTruckIcon(color)}).addTo(map);
                marker.bindPopup(`
                    <div style="margin-bottom:8px;"><span class="marker-label">${plate}</span></div>
                    <div style="font-size:14px; font-weight:600; margin-bottom:4px;">${driverName}</div>
                    <div style="color:var(--text-secondary); font-size:12px;">Kecepatan: <b>${speed} km/h</b></div>
                `);
                
                markers[trip.id] = marker;
            }
        });

        // Fit map bounds to show all markers if any exist
        if(Object.keys(markers).length > 0) {
            var group = new L.featureGroup(Object.values(markers));
            map.fitBounds(group.getBounds().pad(0.1));
        }

        // ================= TAB NAVIGATION LOGIC =================
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Remove active classes
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.background = 'transparent';
                    b.style.color = 'var(--text-secondary)';
                });
                document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
                
                // Add active to current
                btn.classList.add('active');
                btn.style.background = 'var(--accent-color)';
                btn.style.color = 'white';
                
                var target = btn.getAttribute('data-target');
                document.getElementById(target).style.display = 'block';
            });
        });

        // ================= DETAIL MODAL LOGIC =================
        var detailModal = document.getElementById('contractDetailModal');
        
        var modalCtrNum = document.getElementById('modal-ctr-num');
        var modalClientName = document.getElementById('modal-client-name');
        var modalVehPlate = document.getElementById('modal-veh-plate');
        var modalVehType = document.getElementById('modal-veh-type');
        var modalVehCapacity = document.getElementById('modal-veh-capacity');
        var modalDrvName = document.getElementById('modal-drv-name');
        var modalDrvNik = document.getElementById('modal-drv-nik');
        var modalDrvPhone = document.getElementById('modal-drv-phone');

        function closeDetailModal() {
            detailModal.classList.remove('show');
        }

        document.querySelectorAll('.btn-view-alloc').forEach(function(btn) {
            btn.addEventListener('click', function() {
                modalCtrNum.innerText = btn.getAttribute('data-ctr-num');
                modalClientName.innerText = btn.getAttribute('data-client-name');
                modalVehPlate.innerText = btn.getAttribute('data-veh-plate');
                modalVehType.innerText = btn.getAttribute('data-veh-type');
                modalVehCapacity.innerText = btn.getAttribute('data-veh-capacity');
                modalDrvName.innerText = btn.getAttribute('data-drv-name');
                modalDrvNik.innerText = btn.getAttribute('data-drv-nik');
                modalDrvPhone.innerText = btn.getAttribute('data-drv-phone');

                detailModal.classList.add('show');
            });
        });

        // Close triggers
        document.querySelectorAll('.modal-close-btn, .btn-close-modal').forEach(function(btn) {
            btn.addEventListener('click', closeDetailModal);
        });

        // Click outside closes modal
        window.addEventListener('click', function(e) {
            if (e.target === detailModal) {
                closeDetailModal();
            }
        });
    });
</script>
@endpush
