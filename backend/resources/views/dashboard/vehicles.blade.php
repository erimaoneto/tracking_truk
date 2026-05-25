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

    <!-- Panel Righ: Tabel Armada -->
    <div class="table-container" style="margin-bottom: 0;">
        <div class="table-header">
            <h3>Daftar Armada Truk</h3>
        </div>
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Plat Nomor</th>
                        <th>Tipe</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Tanggal Pajak</th>
                        <th style="text-align: center;">Aksi</th>
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
                                @if($vehicle->status !== 'on_trip')
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus armada ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-sm">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size: 0.8rem; color: var(--text-secondary); font-style: italic;">Aktif Jalan</span>
                                @endif
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
@endsection
