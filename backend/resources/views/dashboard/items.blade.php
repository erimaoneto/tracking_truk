@extends('layouts.app')

@section('content')
<div class="top-header">
    <div class="page-title">
        <h2>Master Barang</h2>
        <p>Kelola daftar barang persediaan, tabung gas, sparepart, dan oli operasional</p>
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
    <!-- Panel Kiri: Form Tambah Barang -->
    <div class="card-form">
        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--glass-border);">
            Tambah Barang Baru
        </h3>
        
        <form action="{{ route('admin.items.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="name">Nama Barang</label>
                <input type="text" name="name" id="name" class="form-input" placeholder="Contoh: Tabung LPG 50kg..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="category">Kategori</label>
                <select name="category" id="category" class="form-select" required>
                    <option value="Gas">Gas</option>
                    <option value="Sparepart">Sparepart</option>
                    <option value="Pelumas">Pelumas</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="current_stock">Stok Awal (Unit)</label>
                <input type="number" name="current_stock" id="current_stock" class="form-input" value="0" min="0" required>
            </div>
            
            <button type="submit" class="btn-premium" style="width: 100%;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Daftarkan Barang
            </button>
        </form>
    </div>

    <!-- Panel Kanan: Tabel Barang -->
    <div class="table-container" style="margin-bottom: 0;">
        <div class="table-header">
            <h3>Daftar Inventori Barang</h3>
        </div>
        
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok Saat Ini</th>
                        <th style="width: 30%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item->name }}</strong></td>
                            <td>
                                @if($item->category === 'Gas')
                                    <span class="badge badge-success">Gas</span>
                                @elseif($item->category === 'Sparepart')
                                    <span class="badge badge-primary">Sparepart</span>
                                @elseif($item->category === 'Pelumas')
                                    <span class="badge badge-warning">Pelumas</span>
                                @else
                                    <span class="badge badge-danger" style="background: rgba(255,255,255,0.05); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1);">Lain-lain</span>
                                @endif
                            </td>
                            <td><span style="font-size: 1.1rem; font-weight: 600;">{{ $item->current_stock }}</span> unit</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <button class="btn-primary-sm btn-detail" 
                                            data-name="{{ $item->name }}" 
                                            data-category="{{ $item->category }}" 
                                            data-stock="{{ $item->current_stock }}"
                                            data-created="{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Detail
                                    </button>
                                    
                                    <button class="btn-warning-sm btn-edit" 
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}" 
                                            data-category="{{ $item->category }}" 
                                            data-stock="{{ $item->current_stock }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit
                                    </button>
                                    
                                    <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');" style="margin: 0;">
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
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                Belum ada daftar barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL BARANG ================= -->
<div id="detailModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Detail Informasi Barang</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid">
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Nama Barang</div>
                    <div id="detail-name" class="val" style="font-size: 1.15rem; color: var(--accent-color);">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Kategori Barang</div>
                    <div id="detail-category" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Stok Saat Ini</div>
                    <div id="detail-stock" class="val">-</div>
                </div>
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Tanggal Registrasi</div>
                    <div id="detail-created" class="val">-</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-premium btn-close-modal" style="background: var(--text-secondary); box-shadow: none;">Tutup</button>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT BARANG ================= -->
<div id="editModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Ubah Informasi Barang</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit-name">Nama Barang</label>
                    <input type="text" name="name" id="edit-name" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-category">Kategori</label>
                    <select name="category" id="edit-category" class="form-select" required>
                        <option value="Gas">Gas</option>
                        <option value="Sparepart">Sparepart</option>
                        <option value="Pelumas">Pelumas</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-stock">Jumlah Stok (Unit)</label>
                    <input type="number" name="current_stock" id="edit-stock" class="form-input" min="0" required>
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
        var detailName = document.getElementById('detail-name');
        var detailCategory = document.getElementById('detail-category');
        var detailStock = document.getElementById('detail-stock');
        var detailCreated = document.getElementById('detail-created');

        // Edit form inputs
        var editName = document.getElementById('edit-name');
        var editCategory = document.getElementById('edit-category');
        var editStock = document.getElementById('edit-stock');

        // Helper to Close Modals
        function closeAllModals() {
            detailModal.classList.remove('show');
            editModal.classList.remove('show');
        }

        // Setup triggers for Detail
        document.querySelectorAll('.btn-detail').forEach(function(btn) {
            btn.addEventListener('click', function() {
                detailName.innerText = btn.getAttribute('data-name');
                detailCategory.innerText = btn.getAttribute('data-category');
                detailStock.innerText = btn.getAttribute('data-stock') + ' unit';
                detailCreated.innerText = btn.getAttribute('data-created') + ' WIB';
                
                detailModal.classList.add('show');
            });
        });

        // Setup triggers for Edit
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = btn.getAttribute('data-id');
                editName.value = btn.getAttribute('data-name');
                editCategory.value = btn.getAttribute('data-category');
                editStock.value = btn.getAttribute('data-stock');
                
                // Set form action dynamically
                editForm.setAttribute('action', `/admin/items/${id}`);
                
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
