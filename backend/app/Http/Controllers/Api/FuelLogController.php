<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FuelLogController extends Controller
{
    public function report(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'volume_liters' => 'required|numeric',
            'cost' => 'required|numeric',
            'odometer' => 'required|integer',
            'location' => 'required|string',
            'receipt_photo' => 'nullable|image'
        ]);

        $path = null;
        if ($request->hasFile('receipt_photo')) {
            $path = $request->file('receipt_photo')->store('receipts', 'public');
        }

        // Find active trip if any
        $activeTrip = \App\Models\Trip::where('vehicle_id', $request->vehicle_id)
            ->where('status', 'ongoing')
            ->first();

        $log = \App\Models\FuelLog::create([
            'vehicle_id' => $request->vehicle_id,
            'trip_id' => $activeTrip ? $activeTrip->id : null,
            'date' => now(),
            'volume_liters' => $request->volume_liters,
            'cost' => $request->cost,
            'odometer' => $request->odometer,
            'location' => $request->location,
            'receipt_photo' => $path,
            'is_approved' => false
        ]);

        return response()->json(['status' => 'success', 'data' => $log]);
    }
}
