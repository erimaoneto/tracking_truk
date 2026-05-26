@extends('layouts.app')

@section('content')
<div class="top-header">
    <div class="page-title">
        <h2>Kontrak Sewa Gas</h2>
        <p>Kelola sewa gas jangka panjang dengan alokasi armada dan supir khusus (*dedicated*)</p>
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

<div class="split-grid">
    <!-- Panel Kiri: Form Kontrak Baru -->
    <div class="card-form">
        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--glass-border);">
            Teken Kontrak Baru
        </h3>
        
        <form action="{{ route('admin.contracts.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="client_name">Nama Perusahaan Klien</label>
                <input type="text" name="client_name" id="client_name" class="form-input" placeholder="Contoh: Perusahaan B..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="contract_number">Nomor Kontrak</label>
                <input type="text" name="contract_number" id="contract_number" class="form-input" placeholder="CON-{{ date('Y') }}-0001" required style="text-transform: uppercase;">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="start_date">Tanggal Mulai Kontrak</label>
                <input type="date" name="start_date" id="start_date" class="form-input" value="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label class="form-label" style="margin-bottom: 0;" for="end_date">Tanggal Selesai Kontrak</label>
                    <span id="auto-duration-info" style="font-size: 0.75rem; color: var(--accent-color); font-weight: 600; cursor: pointer;">(Auto +4 Bulan)</span>
                </div>
                <input type="date" name="end_date" id="end_date" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="gas_qty_kg">Volume Sewa Gas (Kg)</label>
                <input type="number" name="gas_qty_kg" id="gas_qty_kg" class="form-input" placeholder="Total kapasitas sewa..." min="1" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="vehicle_id">Alokasikan Armada (Truk)</label>
                <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                    <option value="" disabled selected>Pilih armada yang tersedia...</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">
                            {{ $vehicle->license_plate }} - {{ $vehicle->type }} (Kapasitas: {{ $vehicle->capacity }}kg)
                        </option>
                    @endforeach
                </select>
                @if($vehicles->count() === 0)
                    <p style="font-size: 0.75rem; color: var(--danger); margin-top: 4px; font-style: italic;">Semua armada sedang bertugas / maintenance.</p>
                @endif
            </div>
            
            <div class="form-group">
                <label class="form-label" for="driver_id">Alokasikan Supir Utama</label>
                <select name="driver_id" id="driver_id" class="form-select" required>
                    <option value="" disabled selected>Pilih supir yang tersedia...</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}">
                            {{ $driver->user->name }} (NIK: {{ $driver->nik }})
                        </option>
                    @endforeach
                </select>
                @if($drivers->count() === 0)
                    <p style="font-size: 0.75rem; color: var(--danger); margin-top: 4px; font-style: italic;">Semua supir sedang aktif bertugas dalam kontrak lain.</p>
                @endif
            </div>
            
            <button type="submit" class="btn-premium" style="width: 100%;" @if($vehicles->count() === 0 || $drivers->count() === 0) disabled style="opacity: 0.6; cursor: not-allowed;" @endif>
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Tandatangani Kontrak
            </button>
        </form>
    </div>

    <!-- Panel Kanan: Tabel Kontrak -->
    <div class="table-container" style="margin-bottom: 0;">
        <div class="table-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1rem;">
            <h3>Monitoring Kontrak Sewa Gas</h3>
        </div>

        <!-- Controls: Search & Page Limit -->
        <div class="table-controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 1rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-size: 0.85rem; color: var(--text-secondary);">Tampilkan:</label>
                <select class="entries-select" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 8px; border: 1px solid var(--glass-border); background: var(--glass-bg); color: var(--text-primary); cursor: pointer; outline: none;">
                    <option value="5" selected>5 data</option>
                    <option value="10">10 data</option>
                    <option value="25">25 data</option>
                    <option value="50">50 data</option>
                    <option value="-1">Semua data</option>
                </select>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px; position: relative;">
                <input type="text" class="table-search" placeholder="Cari kontrak..." style="padding: 6px 12px 6px 32px; font-size: 0.85rem; border-radius: 8px; border: 1px solid var(--glass-border); background: var(--glass-bg); color: var(--text-primary); outline: none; transition: all 0.2s; width: 200px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">No</th>
                        <th style="white-space: nowrap;">Nomor Kontrak</th>
                        <th style="white-space: nowrap;">Nama Klien</th>
                        <th style="white-space: nowrap;">Supir & Armada</th>
                        <th style="white-space: nowrap;">Kapasitas Sewa</th>
                        <th style="white-space: nowrap;">Durasi Sewa</th>
                        <th style="white-space: nowrap;">Sisa Kontrak</th>
                        <th style="white-space: nowrap; text-align: center;">Pengantaran</th>
                        <th style="white-space: nowrap; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $index => $contract)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td><span class="plate-number" style="background:#f1f5f9; color:#0f172a; border: 1px solid var(--glass-border);">{{ $contract->contract_number }}</span></td>
                            <td><strong>{{ $contract->client_name }}</strong></td>
                            <td>
                                <div style="font-weight: 600;">👨‍✈️ {{ $contract->driver->user->name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">🚚 Plat: {{ $contract->vehicle->license_plate }}</div>
                            </td>
                            <td><span style="font-size:1rem; font-weight:600;">{{ number_format($contract->gas_qty_kg) }}</span> Kg</td>
                            <td>
                                <div style="font-size: 0.85rem;">Mulai: {{ \Carbon\Carbon::parse($contract->start_date)->format('d-m-Y') }}</div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary);">Selesai: {{ \Carbon\Carbon::parse($contract->end_date)->format('d-m-Y') }}</div>
                            </td>
                            <td>
                                @if($contract->status === 'active')
                                    @if($contract->remaining_days > 30)
                                        <span class="badge badge-success">{{ $contract->remaining_days }} Hari</span>
                                    @elseif($contract->remaining_days > 7)
                                        <span class="badge badge-warning">{{ $contract->remaining_days }} Hari</span>
                                    @else
                                        <span class="badge badge-danger">{{ $contract->remaining_days }} Hari</span>
                                    @endif
                                @else
                                    <span class="badge badge-danger" style="background: rgba(255,255,255,0.05); color: #94a3b8; border: 1px solid var(--glass-border);">Selesai / Mati</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-primary" style="font-size: 0.9rem; padding: 4px 12px;" title="Total pengantaran GPS log">
                                    🔄 {{ $contract->trips->count() }} Kali
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.contracts.destroy', $contract->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kontrak sewa ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger-sm">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                Belum ada kontrak sewa gas aktif terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var startDateInput = document.getElementById('start_date');
        var endDateInput = document.getElementById('end_date');
        var autoDurationSpan = document.getElementById('auto-duration-info');

        function updateEndDateTo4Months() {
            var startDateVal = startDateInput.value;
            if (startDateVal) {
                var date = new Date(startDateVal);
                // Add exactly 4 months
                date.setMonth(date.getMonth() + 4);
                
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                
                endDateInput.value = `${year}-${month}-${day}`;
            }
        }

        // Auto-run on start
        updateEndDateTo4Months();

        // Listen for start date change
        startDateInput.addEventListener('change', updateEndDateTo4Months);

        // Click span triggers reset to 4 months
        autoDurationSpan.addEventListener('click', updateEndDateTo4Months);

        // ================= SEARCH & PAGINATION CONTROLS =================
        const searchInput = document.querySelector('.table-search');
        const entriesSelect = document.querySelector('.entries-select');
        const tableBody = document.querySelector('.premium-table tbody');
        const originalRows = Array.from(tableBody.querySelectorAll('tr'));

        function filterAndLimitTable() {
            const query = searchInput.value.toLowerCase().trim();
            const limit = parseInt(entriesSelect.value);

            let visibleRowsCount = 0;
            originalRows.forEach((row) => {
                if (row.cells.length === 1 && row.cells[0].getAttribute('colspan') && !row.classList.contains('no-results-row')) {
                    row.style.display = 'none';
                    return;
                }

                const text = row.innerText.toLowerCase();
                const matches = text.includes(query);

                if (matches) {
                    if (limit === -1 || visibleRowsCount < limit) {
                        row.style.display = '';
                        visibleRowsCount++;
                    } else {
                        row.style.display = 'none';
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle "No results found"
            let noResultsRow = tableBody.querySelector('.no-results-row');
            if (visibleRowsCount === 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `<td colspan="${originalRows[0].cells.length}" style="text-align: center; color: var(--text-secondary); padding: 30px;">Tidak ada data yang cocok dengan pencarian Anda.</td>`;
                    tableBody.appendChild(noResultsRow);
                } else {
                    noResultsRow.style.display = '';
                }
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }

        if (searchInput && entriesSelect) {
            searchInput.addEventListener('input', filterAndLimitTable);
            entriesSelect.addEventListener('change', filterAndLimitTable);
            filterAndLimitTable();
        }
    });
</script>
@endpush
