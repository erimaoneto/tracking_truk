@extends('layouts.app')

@section('content')
<div class="top-header">
    <div class="page-title">
        <h2>Master Pengguna</h2>
        <p>Daftarkan dan kelola hak akses staf internal perusahaan</p>
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
    <!-- Panel Kiri: Form User Baru -->
    <div class="card-form">
        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--glass-border);">
            Tambah Akun Staf
        </h3>
        
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-input" placeholder="Nama staf baru..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Alamat Email (Login)</label>
                <input type="email" name="email" id="email" class="form-input" placeholder="email@erickman.com..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi (Min 6 Karakter)</label>
                <input type="password" name="password" id="password" class="form-input" placeholder="Kata sandi..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="role_id">Jabatan / Peran (Role)</label>
                <select name="role_id" id="role_id" class="form-select" required>
                    <option value="" disabled selected>Pilih jabatan...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn-premium" style="width: 100%;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Tambah Akun Staf
            </button>
        </form>
    </div>

    <!-- Panel Kanan: Tabel User -->
    <div class="table-container" style="margin-bottom: 0;">
        <div class="table-header">
            <h3>Daftar Staf Internal</h3>
        </div>
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">No</th>
                        <th>Nama Lengkap</th>
                        <th>Email Login</th>
                        <th>Jabatan</th>
                        <th style="width: 32%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if(auth()->id() == $user->id)
                                    <span style="font-size: 0.75rem; color: var(--accent-color); font-weight: bold; margin-left: 5px;">(Anda)</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role->name === 'Admin')
                                    <span class="badge badge-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.25);">Administrator</span>
                                @elseif($user->role->name === 'Operator')
                                    <span class="badge badge-primary">Operator</span>
                                @else
                                    <span class="badge badge-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.25);">Pimpinan</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <button class="btn-primary-sm btn-detail" 
                                            data-name="{{ $user->name }}" 
                                            data-email="{{ $user->email }}" 
                                            data-role-name="{{ $user->role ? $user->role->name : '' }}"
                                            data-created="{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i') }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Detail
                                    </button>
                                    
                                    <button class="btn-warning-sm btn-edit" 
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" 
                                            data-email="{{ $user->email }}" 
                                            data-role-id="{{ $user->role_id }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit
                                    </button>

                                    @if(auth()->id() != $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun staf ini?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger-sm">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-danger-sm" disabled style="opacity: 0.5; cursor: not-allowed;" title="Anda tidak dapat menghapus akun Anda sendiri.">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                Tidak ada data staf terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL PENGGUNA ================= -->
<div id="detailModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Detail Akun Staf</h3>
            <button class="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid">
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Nama Lengkap</div>
                    <div id="detail-name" class="val" style="font-size: 1.25rem; font-weight: 700; color: var(--accent-color);">-</div>
                </div>
                <div class="detail-info-box" style="grid-column: span 2;">
                    <div class="label">Email Login</div>
                    <div id="detail-email" class="val">-</div>
                </div>
                <div class="detail-info-box">
                    <div class="label">Jabatan (Role)</div>
                    <div id="detail-role" class="val">-</div>
                </div>
                <div class="detail-info-box">
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

<!-- ================= MODAL EDIT PENGGUNA ================= -->
<div id="editModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Ubah Akun Staf</h3>
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
                    <label class="form-label" for="edit-email">Alamat Email (Login)</label>
                    <input type="email" name="email" id="edit-email" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-password">Kata Sandi Baru (Opsional)</label>
                    <input type="password" name="password" id="edit-password" class="form-input" placeholder="Kosongkan jika tidak ingin mengubah sandi...">
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit-role">Jabatan / Peran (Role)</label>
                    <select name="role_id" id="edit-role" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
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
        var detailRole = document.getElementById('detail-role');
        var detailCreated = document.getElementById('detail-created');

        // Edit form inputs
        var editName = document.getElementById('edit-name');
        var editEmail = document.getElementById('edit-email');
        var editPassword = document.getElementById('edit-password');
        var editRole = document.getElementById('edit-role');

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
                detailRole.innerText = btn.getAttribute('data-role-name');
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
                editRole.value = btn.getAttribute('data-role-id');
                
                // Set form action dynamically
                editForm.setAttribute('action', `/admin/users/${id}`);
                
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
