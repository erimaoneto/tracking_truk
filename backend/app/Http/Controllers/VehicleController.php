<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('license_plate', 'asc')->get();
        
        $totalVehicles = $vehicles->count();
        $activeVehicles = $vehicles->where('status', 'on_trip')->count();
        $availableVehicles = $vehicles->where('status', 'available')->count();
        $maintenanceVehicles = $vehicles->where('status', 'maintenance')->count();
        
        return view('dashboard.vehicles', compact(
            'vehicles', 
            'totalVehicles', 
            'activeVehicles', 
            'availableVehicles', 
            'maintenanceVehicles'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate',
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:0',
            'status' => 'required|in:available,on_trip,maintenance',
            'tax_date' => 'required|date',
        ]);

        Vehicle::create([
            'license_plate' => strtoupper($request->license_plate),
            'type' => $request->type,
            'capacity' => $request->capacity,
            'status' => $request->status,
            'tax_date' => $request->tax_date,
        ]);

        return redirect()->route('admin.vehicles')->with('success', 'Kendaraan baru berhasil didaftarkan ke Armada!');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return redirect()->route('admin.vehicles')->with('success', 'Kendaraan berhasil dihapus dari Armada!');
    }
}
