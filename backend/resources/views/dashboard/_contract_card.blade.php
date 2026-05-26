<div class="contract-card" style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 16px; padding: 1.25rem; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.3s; position: relative; overflow: hidden;">
    <!-- Ornamen Badge Status Atas -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
        <div>
            <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: var(--accent-color); font-size: 0.75rem; padding: 4px 10px; font-weight: 700; border-radius: 20px; border: 1px solid rgba(59, 130, 246, 0.25);">
                {{ $contract->contract_number }}
            </span>
        </div>
        
        @if($theme === 'success')
            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.75rem; padding: 4px 10px; font-weight: 700; border-radius: 20px; border: 1px solid rgba(16, 185, 129, 0.25);">
                Berlangsung
            </span>
        @elseif($theme === 'info')
            <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-size: 0.75rem; padding: 4px 10px; font-weight: 700; border-radius: 20px; border: 1px solid rgba(59, 130, 246, 0.25);">
                Diterima / Baru
            </span>
        @else
            <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: #64748b; font-size: 0.75rem; padding: 4px 10px; font-weight: 700; border-radius: 20px; border: 1px solid rgba(148, 163, 184, 0.25);">
                Selesai
            </span>
        @endif
    </div>

    <!-- Informasi Utama Klien -->
    <div style="margin-bottom: 1.25rem;">
        <h4 style="margin: 0 0 0.25rem 0; font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">
            {{ $contract->client_name }}
        </h4>
        <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary); display: flex; align-items: center; gap: 4px;">
            💼 Volume Gas: <strong>{{ number_format($contract->gas_qty_kg) }} Kg</strong>
        </p>
    </div>

    <!-- Divider -->
    <div style="height: 1px; background: var(--glass-border); margin-bottom: 1rem;"></div>

    <!-- Informasi Detail Durasi & Alokasi Ringkas -->
    <div style="font-size: 0.85rem; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem;">
        <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--text-secondary);">Mulai Sewa:</span>
            <strong>{{ \Carbon\Carbon::parse($contract->start_date)->format('d M Y') }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--text-secondary);">Selesai Sewa:</span>
            <strong>{{ \Carbon\Carbon::parse($contract->end_date)->format('d M Y') }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--text-secondary);">Total Pengantaran:</span>
            <strong style="color: var(--accent-color);">{{ $contract->trips->count() }} kali jalan</strong>
        </div>
    </div>

    <!-- Alokasi Ringkas & Tombol Aksi -->
    <div style="background: rgba(0, 0, 0, 0.02); padding: 0.75rem; border-radius: 10px; border: 1px solid var(--glass-border); margin-bottom: 1.25rem; font-size: 0.8rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
            <span>🚚 Truk Armada:</span>
            <strong>{{ $contract->vehicle ? $contract->vehicle->license_plate : 'Unknown' }}</strong>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span>👤 Supir Pengantar:</span>
            <strong>{{ $contract->driver && $contract->driver->user ? $contract->driver->user->name : 'Unknown' }}</strong>
        </div>
    </div>

    <!-- Button Trigger Modal Detail -->
    <button class="btn-premium btn-view-alloc" 
            style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.85rem; padding: 10px;"
            data-ctr-num="{{ $contract->contract_number }}"
            data-client-name="{{ $contract->client_name }}"
            data-veh-plate="{{ $contract->vehicle ? $contract->vehicle->license_plate : 'Unknown' }}"
            data-veh-type="{{ $contract->vehicle ? $contract->vehicle->type : 'Unknown' }}"
            data-veh-capacity="{{ $contract->vehicle ? $contract->vehicle->capacity . ' Kg' : '-' }}"
            data-drv-name="{{ $contract->driver && $contract->driver->user ? $contract->driver->user->name : 'Unknown' }}"
            data-drv-nik="{{ $contract->driver ? $contract->driver->nik : '-' }}"
            data-drv-phone="{{ $contract->driver ? $contract->driver->phone : '-' }}">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        Detail Armada & Supir
    </button>
</div>
