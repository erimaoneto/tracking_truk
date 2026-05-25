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
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Email Login</th>
                        <th>Jabatan</th>
                        <th style="text-align: center;">Aksi</th>
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
                                @if(auth()->id() != $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun staf ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-sm">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size: 0.8rem; color: var(--text-secondary); font-style: italic;">Aktif</span>
                                @endif
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
@endsection
