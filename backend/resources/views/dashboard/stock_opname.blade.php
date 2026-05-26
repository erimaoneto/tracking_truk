@extends('layouts.app')

@section('content')
<div class="top-header">
    <div class="page-title">
        <h2>Stok Opname</h2>
        <p>Catat dan selaraskan persediaan barang fisik dengan data sistem</p>
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
    <!-- Panel Kiri: Form Pencatatan -->
    <div class="card-form">
        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--glass-border);">
            Pencatatan Stok
        </h3>
        
        <form action="{{ route('stock-opname.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="item_id">Pilih Barang</label>
                <select name="item_id" id="item_id" class="form-select" required>
                    <option value="" disabled selected>Pilih nama barang...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->name }} (Sistem: {{ $item->current_stock }} unit)
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="physical_stock">Stok Fisik Aktual (Unit)</label>
                <input type="number" name="physical_stock" id="physical_stock" class="form-input" placeholder="Masukkan jumlah fisik..." min="0" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="notes">Catatan / Keterangan</label>
                <textarea name="notes" id="notes" class="form-textarea" placeholder="Alasan selisih, kondisi barang, dll..."></textarea>
            </div>
            
            <button type="submit" class="btn-premium" style="width: 100%;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Simpan Stok Opname
            </button>
        </form>
    </div>


    <!-- Panel Kanan: Tabel Riwayat -->
    <div class="table-container" style="margin-bottom: 0;">
        <div class="table-header">
            <h3>Log Stok Opname</h3>
            @if($opnames->count() > 0)
                <a href="{{ route('stock-opname.export-pdf') }}" class="btn-premium" style="padding: 8px 16px; font-size: 0.85rem;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Unduh PDF
                </a>
            @endif
        </div>
        
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Sistem</th>
                        <th>Fisik</th>
                        <th>Selisih</th>
                        <th>Petugas</th>
                        <th style="width: 25%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opnames as $index => $op)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($op->opname_date)->format('d-m-Y') }}</td>
                            <td><strong>{{ $op->item->name }}</strong></td>
                            <td>{{ $op->system_stock }}</td>
                            <td>{{ $op->physical_stock }}</td>
                            <td>
                                @if($op->variance < 0)
                                    <span style="color: var(--danger); font-weight: 600;">{{ $op->variance }}</span>
                                @elseif($op->variance > 0)
                                    <span style="color: var(--success); font-weight: 600;">+{{ $op->variance }}</span>
                                @else
                                    <span style="color: var(--text-secondary);">0</span>
                                @endif
                            </td>
                            <td>{{ $op->user->name }}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <button class="btn-primary-sm btn-detail" 
                                            data-item-name="{{ $op->item->name }}" 
                                            data-date="{{ \Carbon\Carbon::parse($op->opname_date)->format('d-m-Y H:i') }}" 
                                            data-system-stock="{{ $op->system_stock }}"
                                            data-physical-stock="{{ $op->physical_stock }}"
                                            data-variance="{{ $op->variance }}"
                                            data-user-name="{{ $op->user->name }}"
                                            data-notes="{{ $op->notes ?? '-' }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Detail
                                    </button>
                                    
                                    <button class="btn-warning-sm btn-edit" 
                                            data-id="{{ $op->id }}"
                                            data-item-name="{{ $op->item->name }}" 
                                            data-system-stock="{{ $op->system_stock }}"
                                            data-physical-stock="{{ $op->physical_stock }}"
                                            data-notes="{{ $op->notes ?? '' }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit
                                    </button>
                                    
                                    <form action="{{ route('stock-opname.destroy', $op->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus log stok opname ini?');" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-sm">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                Belum ada riwayat stok opname tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL STOK OPNAME ================= -->
<div id="detailModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Detail Riwayat Stok Opname</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid">
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Nama Barang</div>
                    <div id="detail-item-name" class="val" style="font-size: 1.2rem; font-weight: 700; color: var(--accent-color);">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Tanggal Opname</div>
                    <div id="detail-date" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Petugas Lapangan</div>
                    <div id="detail-user-name" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Stok Tercatat Sistem</div>
                    <div id="detail-system-stock" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Stok Fisik Aktual</div>
                    <div id="detail-physical-stock" class="val">-</div>
                </div>
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Selisih (Variance)</div>
                    <div id="detail-variance" class="val" style="font-size: 1.15rem; font-weight: 600;">-</div>
                </div>
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Catatan / Keterangan</div>
                    <div id="detail-notes" class="val" style="white-space: pre-wrap; line-height: 1.5; color: var(--text-secondary); background: var(--bg-hover); padding: 10px; border-radius: 8px; border: 1px solid var(--glass-border); min-height: 50px;">-</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-premium btn-close-modal" style="background: var(--text-secondary); box-shadow: none;">Tutup</button>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT STOK OPNAME ================= -->
<div id="editModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Ubah Catatan Stok Opname</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" id="edit-item-name" class="form-input" disabled style="background: rgba(0, 0, 0, 0.05); color: var(--text-secondary);">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Stok Tercatat di Sistem</label>
                    <input type="text" id="edit-system-stock" class="form-input" disabled style="background: rgba(0, 0, 0, 0.05); color: var(--text-secondary);">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-physical-stock">Stok Fisik Aktual Baru (Unit)</label>
                    <input type="number" name="physical_stock" id="edit-physical-stock" class="form-input" min="0" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-notes">Catatan / Keterangan Baru</label>
                    <textarea name="notes" id="edit-notes" class="form-textarea" placeholder="Alasan selisih, kondisi barang, dll..."></textarea>
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
        var detailItemName = document.getElementById('detail-item-name');
        var detailDate = document.getElementById('detail-date');
        var detailUserName = document.getElementById('detail-user-name');
        var detailSystemStock = document.getElementById('detail-system-stock');
        var detailPhysicalStock = document.getElementById('detail-physical-stock');
        var detailVariance = document.getElementById('detail-variance');
        var detailNotes = document.getElementById('detail-notes');

        // Edit form inputs
        var editItemName = document.getElementById('edit-item-name');
        var editSystemStock = document.getElementById('edit-system-stock');
        var editPhysicalStock = document.getElementById('edit-physical-stock');
        var editNotes = document.getElementById('edit-notes');

        // Helper to Close Modals
        function closeAllModals() {
            detailModal.classList.remove('show');
            editModal.classList.remove('show');
        }

        // Setup triggers for Detail
        document.querySelectorAll('.btn-detail').forEach(function(btn) {
            btn.addEventListener('click', function() {
                detailItemName.innerText = btn.getAttribute('data-item-name');
                detailDate.innerText = btn.getAttribute('data-date') + ' WIB';
                detailUserName.innerText = btn.getAttribute('data-user-name');
                detailSystemStock.innerText = btn.getAttribute('data-system-stock') + ' unit';
                detailPhysicalStock.innerText = btn.getAttribute('data-physical-stock') + ' unit';
                
                var varianceVal = parseInt(btn.getAttribute('data-variance'));
                if (varianceVal < 0) {
                    detailVariance.innerHTML = `<span style="color: var(--danger);">${varianceVal} unit (Kurang)</span>`;
                } else if (varianceVal > 0) {
                    detailVariance.innerHTML = `<span style="color: var(--success);">+${varianceVal} unit (Surplus)</span>`;
                } else {
                    detailVariance.innerHTML = `<span style="color: var(--text-secondary);">0 unit (Sesuai)</span>`;
                }
                
                detailNotes.innerText = btn.getAttribute('data-notes');
                
                detailModal.classList.add('show');
            });
        });

        // Setup triggers for Edit
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = btn.getAttribute('data-id');
                editItemName.value = btn.getAttribute('data-item-name');
                editSystemStock.value = btn.getAttribute('data-system-stock') + ' unit';
                editPhysicalStock.value = btn.getAttribute('data-physical-stock');
                editNotes.value = btn.getAttribute('data-notes');
                
                // Set form action dynamically
                editForm.setAttribute('action', `/stock-opname/${id}`);
                
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
