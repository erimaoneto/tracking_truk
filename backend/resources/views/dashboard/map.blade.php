@extends('layouts.app')

@section('content')
<div class="top-header">
    <div class="page-title">
        <h2>Live Tracking Armada</h2>
        <p>Pemantauan pergerakan truk gas secara real-time</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">🚚</div>
        <div class="stat-info">
            <h4>Truk Aktif (Jalan)</h4>
            <div class="value">{{ $ongoingTrips->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <h4>Truk Tersedia</h4>
            <div class="value">{{ $availableVehiclesCount }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">⚠️</div>
        <div class="stat-info">
            <h4>Peringatan</h4>
            <div class="value">0</div>
        </div>
    </div>
</div>

<div class="map-container">
    <div id="map"></div>
</div>
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

        /* 
        Optional: Polling logic to fetch latest GPS data 
        setInterval(function() {
            fetch('/api/trips/ongoing')
                .then(res => res.json())
                .then(data => {
                    // Update markers logic here
                });
        }, 10000);
        */
    });
</script>
@endpush
