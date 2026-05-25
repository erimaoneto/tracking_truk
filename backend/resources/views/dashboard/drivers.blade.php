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
        <div class="table-header">
            <h3>Daftar Supir Gas Aktif</h3>
        </div>
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supir</th>
                        <th>Email Login</th>
                        <th>NIK KTP</th>
                        <th>No. Telepon</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $index => $driver)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $driver->user ? $driver->user->name : 'Unknown User' }}</strong></td>
                            <td>{{ $driver->user ? $driver->user->email : '-' }}</td>
                            <td><span style="font-family: monospace; font-size: 0.85rem;">{{ $driver->nik }}</span></td>
                            <td>{{ $driver->phone }}</td>
                            <td>
                                @if($driver->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data supir ini? Ini juga akan menghapus akun login supir terkait.');">
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
@endsection
