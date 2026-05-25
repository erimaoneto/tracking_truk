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
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Sistem</th>
                        <th>Fisik</th>
                        <th>Selisih</th>
                        <th>Petugas</th>
                        <th>Catatan</th>
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
                            <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $op->notes }}">
                                {{ $op->notes ?? '-' }}
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
@endsection
