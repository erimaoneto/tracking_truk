@extends('layouts.app')

@section('content')
<div class="top-header">
    <div class="page-title">
        <h2>Master Kendaraan</h2>
        <p>Kelola dan pantau seluruh status armada pengangkut gas</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">🚚</div>
        <div class="stat-info">
            <h4>Total Armada</h4>
            <div class="value">{{ $totalVehicles }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">⚡</div>
        <div class="stat-info">
            <h4>Aktif (Jalan)</h4>
            <div class="value">{{ $activeVehicles }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue" style="color: var(--accent-color); background: rgba(59, 130, 246, 0.15);">✅</div>
        <div class="stat-info">
            <h4>Tersedia (Pool)</h4>
            <div class="value">{{ $availableVehicles }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">🛠️</div>
        <div class="stat-info">
            <h4>Perawatan</h4>
            <div class="value">{{ $maintenanceVehicles }}</div>
        </div>
    </div>
</div>

<div class="split-grid">
    <!-- Panel Kiri: Form Kendaraan Baru -->
    <div class="card-form">
        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--glass-border);">
            Daftarkan Truk
        </h3>
        
        <form action="{{ route('admin.vehicles.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="license_plate">Plat Nomor</label>
                <input type="text" name="license_plate" id="license_plate" class="form-input" placeholder="Contoh: B 1234 XY..." required style="text-transform: uppercase;">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="type">Tipe Kendaraan</label>
                <input type="text" name="type" id="type" class="form-input" placeholder="Contoh: Engkel, Fuso, Pick-up..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="capacity">Kapasitas Muatan (Kg)</label>
                <input type="number" name="capacity" id="capacity" class="form-input" placeholder="Kapasitas tabung gas..." min="0" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="status">Status Operasional</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="available">Tersedia (di Pool)</option>
                    <option value="maintenance">Perawatan (Maintenance)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="tax_date">Tanggal Pajak STNK</label>
                <input type="date" name="tax_date" id="tax_date" class="form-input" required>
            </div>
            
            <button type="submit" class="btn-premium" style="width: 100%;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Daftarkan Armada
            </button>
        </form>
    </div>

    <!-- Panel Kanan: Tabel Armada -->
    <div class="table-container" style="margin-bottom: 0;">
        <div class="table-header">
            <h3>Daftar Armada Truk</h3>
        </div>
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">No</th>
                        <th>Plat Nomor</th>
                        <th>Tipe</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Tanggal Pajak</th>
                        <th style="width: 32%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $index => $vehicle)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="plate-number">{{ $vehicle->license_plate }}</span></td>
                            <td>{{ $vehicle->type }}</td>
                            <td>{{ $vehicle->capacity }} Kg</td>
                            <td>
                                @if($vehicle->status === 'on_trip')
                                    <span class="badge badge-success">Sedang Jalan</span>
                                @elseif($vehicle->status === 'available')
                                    <span class="badge badge-primary">Tersedia</span>
                                @else
                                    <span class="badge badge-warning">Perawatan</span>
                                @endif
                            </td>
                            <td>
                                <span class="tax-date {{ \Carbon\Carbon::parse($vehicle->tax_date)->isPast() ? 'tax-expired' : '' }}">
                                    {{ \Carbon\Carbon::parse($vehicle->tax_date)->format('d-m-Y') }}
                                    @if(\Carbon\Carbon::parse($vehicle->tax_date)->isPast())
                                        <span class="tax-alert" title="Pajak Lewat Tempo!">(Lewat)</span>
                                    @endif
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <button class="btn-primary-sm btn-detail" 
                                            data-plate="{{ $vehicle->license_plate }}" 
                                            data-type="{{ $vehicle->type }}" 
                                            data-capacity="{{ $vehicle->capacity }}"
                                            data-status="{{ $vehicle->status }}"
                                            data-tax="{{ \Carbon\Carbon::parse($vehicle->tax_date)->format('Y-m-d') }}"
                                            data-tax-formatted="{{ \Carbon\Carbon::parse($vehicle->tax_date)->format('d-m-Y') }}"
                                            data-created="{{ \Carbon\Carbon::parse($vehicle->created_at)->format('d-m-Y H:i') }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Detail
                                    </button>
                                    
                                    <button class="btn-warning-sm btn-edit" 
                                            data-id="{{ $vehicle->id }}"
                                            data-plate="{{ $vehicle->license_plate }}" 
                                            data-type="{{ $vehicle->type }}" 
                                            data-capacity="{{ $vehicle->capacity }}"
                                            data-status="{{ $vehicle->status }}"
                                            data-tax="{{ \Carbon\Carbon::parse($vehicle->tax_date)->format('Y-m-d') }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit
                                    </button>

                                    @if($vehicle->status !== 'on_trip')
                                        <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus armada ini?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger-sm">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-danger-sm" disabled style="opacity: 0.5; cursor: not-allowed;" title="Armada sedang aktif jalan, tidak dapat dihapus.">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                Tidak ada data armada truk tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL KENDARAAN ================= -->
<div id="detailModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Detail Armada Kendaraan</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid">
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Plat Nomor</div>
                    <div id="detail-plate" class="val" style="font-size: 1.25rem; font-weight: 700; color: var(--accent-color); letter-spacing: 0.05em;">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Tipe Kendaraan</div>
                    <div id="detail-type" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Kapasitas Muatan</div>
                    <div id="detail-capacity" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Status Operasional</div>
                    <div id="detail-status" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Tanggal Pajak STNK</div>
                    <div id="detail-tax" class="val">-</div>
                </div>
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Tanggal Terdaftar</div>
                    <div id="detail-created" class="val">-</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-premium btn-close-modal" style="background: var(--text-secondary); box-shadow: none;">Tutup</button>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT KENDARAAN ================= -->
<div id="editModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Ubah Informasi Armada</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit-plate">Plat Nomor</label>
                    <input type="text" name="license_plate" id="edit-plate" class="form-input" style="text-transform: uppercase;" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-type">Tipe Kendaraan</label>
                    <input type="text" name="type" id="edit-type" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-capacity">Kapasitas Muatan (Kg)</label>
                    <input type="number" name="capacity" id="edit-capacity" class="form-input" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-status">Status Operasional</label>
                    <select name="status" id="edit-status" class="form-select" required>
                        <option value="available">Tersedia (di Pool)</option>
                        <option value="on_trip">Sedang Jalan (Aktif)</option>
                        <option value="maintenance">Perawatan (Maintenance)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-tax">Tanggal Pajak STNK</label>
                    <input type="date" name="tax_date" id="edit-tax" class="form-input" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-premium btn-close-modal" style="background: var(--text-secondary); box-shadow: none;">Batal</button>
                <button type="submit" class="btn-premium">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Modal Selectors
        var detailModal = document.getElementById('detailModal');
        var editModal = document.getElementById('editModal');
        var editForm = document.getElementById('editForm');

        // Detail elements
        var detailPlate = document.getElementById('detail-plate');
        var detailType = document.getElementById('detail-type');
        var detailCapacity = document.getElementById('detail-capacity');
        var detailStatus = document.getElementById('detail-status');
        var detailTax = document.getElementById('detail-tax');
        var detailCreated = document.getElementById('detail-created');

        // Edit form inputs
        var editPlate = document.getElementById('edit-plate');
        var editType = document.getElementById('edit-type');
        var editCapacity = document.getElementById('edit-capacity');
        var editStatus = document.getElementById('edit-status');
        var editTax = document.getElementById('edit-tax');

        // Helper to Close Modals
        function closeAllModals() {
            detailModal.classList.remove('show');
            editModal.classList.remove('show');
        }

        // Setup triggers for Detail
        document.querySelectorAll('.btn-detail').forEach(function(btn) {
            btn.addEventListener('click', function() {
                detailPlate.innerText = btn.getAttribute('data-plate');
                detailType.innerText = btn.getAttribute('data-type');
                detailCapacity.innerText = btn.getAttribute('data-capacity') + ' Kg';
                
                var rawStatus = btn.getAttribute('data-status');
                var statusText = 'Tersedia';
                if(rawStatus === 'on_trip') statusText = 'Sedang Jalan';
                else if(rawStatus === 'maintenance') statusText = 'Perawatan';
                detailStatus.innerText = statusText;

                detailTax.innerText = btn.getAttribute('data-tax-formatted');
                detailCreated.innerText = btn.getAttribute('data-created') + ' WIB';
                
                detailModal.classList.add('show');
            });
        });

        // Setup triggers for Edit
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = btn.getAttribute('data-id');
                editPlate.value = btn.getAttribute('data-plate');
                editType.value = btn.getAttribute('data-type');
                editCapacity.value = btn.getAttribute('data-capacity');
                editStatus.value = btn.getAttribute('data-status');
                editTax.value = btn.getAttribute('data-tax');
                
                // Set form action dynamically
                editForm.setAttribute('action', `/admin/vehicles/${id}`);
                
                editModal.classList.add('show');
            });
        });

        // Close triggers
        document.querySelectorAll('.modal-close-btn, .btn-close-modal').forEach(function(btn) {
            btn.addEventListener('click', closeAllModals);
        });

        // Click outside closes modal
        window.addEventListener('click', function(e) {
            if (e.target === detailModal || e.target === editModal) {
                closeAllModals();
            }
        });
    });
</script>
@endpush
