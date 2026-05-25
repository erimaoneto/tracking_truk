<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with(['vehicle', 'driver.user', 'trips'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($contract) {
                // Calculate remaining days
                $endDate = Carbon::parse($contract->end_date);
                $now = Carbon::now();
                if ($contract->status === 'active') {
                    if ($now->greaterThan($endDate)) {
                        $contract->remaining_days = 0;
                        $contract->status = 'completed';
                        $contract->save();
                    } else {
                        $contract->remaining_days = $now->diffInDays($endDate);
                    }
                } else {
                    $contract->remaining_days = 0;
                }
                return $contract;
            });

        // Get available vehicles and drivers for the contract dropdown
        $vehicles = Vehicle::where('status', 'available')->orderBy('license_plate', 'asc')->get();
        
        // Exclude drivers who already have an active contract
        $activeDriverIds = Contract::where('status', 'active')->pluck('driver_id')->toArray();
        $drivers = Driver::with('user')
            ->where('is_active', true)
            ->whereNotIn('id', $activeDriverIds)
            ->get();

        return view('dashboard.contracts', compact('contracts', 'vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'contract_number' => 'required|string|max:100|unique:contracts,contract_number',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'gas_qty_kg' => 'required|integer|min:1',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
        ]);

        Contract::create([
            'client_name' => $request->client_name,
            'contract_number' => strtoupper($request->contract_number),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'gas_qty_kg' => $request->gas_qty_kg,
            'vehicle_id' => $request->vehicle_id,
            'driver_id' => $request->driver_id,
            'status' => 'active',
        ]);

        return redirect()->route('admin.contracts')->with('success', 'Kontrak sewa baru berhasil ditandatangani dan diaktifkan!');
    }

    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();

        return redirect()->route('admin.contracts')->with('success', 'Kontrak sewa berhasil dihapus dari sistem.');
    }
}
