@extends('layouts.app')

@section('content')
<div class="top-header">
    <div class="page-title">
        <h2>Master Supir</h2>
        <p>Daftarkan dan kelola akun serta profil supir pengangkut gas</p>
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
    <!-- Panel Kiri: Form Driver Baru -->
    <div class="card-form">
        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--glass-border);">
            Daftarkan Supir
        </h3>
        
        <form action="{{ route('admin.drivers.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-input" placeholder="Nama supir..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Alamat Email (Login App)</label>
                <input type="email" name="email" id="email" class="form-input" placeholder="email@erickman.com..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi (Min 6 Karakter)</label>
                <input type="password" name="password" id="password" class="form-input" placeholder="Sandi login mobile..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="nik">NIK KTP (16 Digit)</label>
                <input type="text" name="nik" id="nik" class="form-input" placeholder="3171xxxxxxxxxxxx..." maxlength="16" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="phone">Nomor HP / WhatsApp</label>
                <input type="text" name="phone" id="phone" class="form-input" placeholder="Contoh: 0812xxxxxxxx..." required>
            </div>
            
            <button type="submit" class="btn-premium" style="width: 100%;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Daftarkan Supir
            </button>
        </form>
    </div>

    <!-- Panel Kanan: Tabel Driver -->
    <div class="table-container" style="margin-bottom: 0;">
        <div class="table-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1rem;">
            <h3>Daftar Supir Gas Aktif</h3>
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
                <input type="text" class="table-search" placeholder="Cari supir..." style="padding: 6px 12px 6px 32px; font-size: 0.85rem; border-radius: 8px; border: 1px solid var(--glass-border); background: var(--glass-bg); color: var(--text-primary); outline: none; transition: all 0.2s; width: 200px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">No</th>
                        <th style="white-space: nowrap;">Nama Supir</th>
                        <th style="white-space: nowrap;">Email Login</th>
                        <th style="white-space: nowrap;">NIK KTP</th>
                        <th style="white-space: nowrap;">No. Telepon</th>
                        <th style="white-space: nowrap;">Status</th>
                        <th style="white-space: nowrap; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $index => $driver)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="white-space: nowrap;"><strong>{{ $driver->user ? $driver->user->name : 'Unknown User' }}</strong></td>
                            <td style="white-space: nowrap;">{{ $driver->user ? $driver->user->email : '-' }}</td>
                            <td style="white-space: nowrap;"><span style="font-family: monospace; font-size: 0.85rem;">{{ $driver->nik }}</span></td>
                            <td style="white-space: nowrap;">{{ $driver->phone }}</td>
                            <td>
                                @if($driver->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: nowrap; white-space: nowrap;">
                                    <button class="btn-primary-sm btn-detail" 
                                            data-name="{{ $driver->user ? $driver->user->name : 'Unknown User' }}" 
                                            data-email="{{ $driver->user ? $driver->user->email : '-' }}" 
                                            data-nik="{{ $driver->nik }}"
                                            data-phone="{{ $driver->phone }}"
                                            data-is-active="{{ $driver->is_active ? '1' : '0' }}"
                                            data-created="{{ \Carbon\Carbon::parse($driver->created_at)->format('d-m-Y H:i') }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Detail
                                    </button>
                                    
                                    <button class="btn-warning-sm btn-edit" 
                                            data-id="{{ $driver->id }}"
                                            data-name="{{ $driver->user ? $driver->user->name : '' }}" 
                                            data-email="{{ $driver->user ? $driver->user->email : '' }}" 
                                            data-nik="{{ $driver->nik }}"
                                            data-phone="{{ $driver->phone }}"
                                            data-is-active="{{ $driver->is_active ? '1' : '0' }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit
                                    </button>
                                    
                                    <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data supir ini? Ini juga akan menghapus akun login supir terkait.');" style="margin: 0;">
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
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                Tidak ada data supir terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL SUPIR ================= -->
<div id="detailModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Detail Profil Supir</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid">
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Nama Lengkap</div>
                    <div id="detail-name" class="val" style="font-size: 1.2rem; font-weight: 700; color: var(--accent-color);">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Email Login</div>
                    <div id="detail-email" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">NIK KTP</div>
                    <div id="detail-nik" class="val" style="font-family: monospace; letter-spacing: 0.05em;">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">No. Telepon / WA</div>
                    <div id="detail-phone" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Status Akun</div>
                    <div id="detail-status" class="val">-</div>
                </div>
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Tanggal Bergabung</div>
                    <div id="detail-created" class="val">-</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-premium btn-close-modal" style="background: var(--text-secondary); box-shadow: none;">Tutup</button>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT SUPIR ================= -->
<div id="editModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Ubah Informasi Supir</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit-name">Nama Lengkap</label>
                    <input type="text" name="name" id="edit-name" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-email">Alamat Email (Login App)</label>
                    <input type="email" name="email" id="edit-email" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-password">Kata Sandi Baru (Opsional)</label>
                    <input type="password" name="password" id="edit-password" class="form-input" placeholder="Kosongkan jika tidak ingin mengubah sandi...">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-nik">NIK KTP (16 Digit)</label>
                    <input type="text" name="nik" id="edit-nik" class="form-input" maxlength="16" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-phone">Nomor HP / WhatsApp</label>
                    <input type="text" name="phone" id="edit-phone" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-status">Status Akun</label>
                    <select name="is_active" id="edit-status" class="form-select" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
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
        var detailEmail = document.getElementById('detail-email');
        var detailNik = document.getElementById('detail-nik');
        var detailPhone = document.getElementById('detail-phone');
        var detailStatus = document.getElementById('detail-status');
        var detailCreated = document.getElementById('detail-created');

        // Edit form inputs
        var editName = document.getElementById('edit-name');
        var editEmail = document.getElementById('edit-email');
        var editPassword = document.getElementById('edit-password');
        var editNik = document.getElementById('edit-nik');
        var editPhone = document.getElementById('edit-phone');
        var editStatus = document.getElementById('edit-status');

        // Helper to Close Modals
        function closeAllModals() {
            detailModal.classList.remove('show');
            editModal.classList.remove('show');
        }

        // Setup triggers for Detail
        document.querySelectorAll('.btn-detail').forEach(function(btn) {
            btn.addEventListener('click', function() {
                detailName.innerText = btn.getAttribute('data-name');
                detailEmail.innerText = btn.getAttribute('data-email');
                detailNik.innerText = btn.getAttribute('data-nik');
                detailPhone.innerText = btn.getAttribute('data-phone');
                
                var isActive = btn.getAttribute('data-is-active');
                detailStatus.innerText = isActive === '1' ? 'Aktif' : 'Nonaktif';
                
                detailCreated.innerText = btn.getAttribute('data-created') + ' WIB';
                
                detailModal.classList.add('show');
            });
        });

        // Setup triggers for Edit
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = btn.getAttribute('data-id');
                editName.value = btn.getAttribute('data-name');
                editEmail.value = btn.getAttribute('data-email');
                editPassword.value = ''; // Always clear password field on open
                editNik.value = btn.getAttribute('data-nik');
                editPhone.value = btn.getAttribute('data-phone');
                editStatus.value = btn.getAttribute('data-is-active');
                
                // Set form action dynamically
                editForm.setAttribute('action', `/admin/drivers/${id}`);
                
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
