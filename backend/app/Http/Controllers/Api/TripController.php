<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function startTrip(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $driver = \App\Models\Driver::where('user_id', $request->user()->id)->first();
        if (!$driver) return response()->json(['error' => 'Not a driver'], 403);

        // Cari kontrak sewa gas aktif yang ditugaskan ke supir ini
        $activeContract = \App\Models\Contract::where('driver_id', $driver->id)
            ->where('status', 'active')
            ->first();

        $trip = \App\Models\Trip::create([
            'vehicle_id' => $request->vehicle_id,
            'driver_id' => $driver->id,
            'contract_id' => $activeContract ? $activeContract->id : null,
            'start_time' => now(),
            'status' => 'ongoing'
        ]);

        \App\Models\Vehicle::where('id', $request->vehicle_id)->update(['status' => 'on_trip']);

        return response()->json(['status' => 'success', 'trip' => $trip]);
    }

    public function endTrip(Request $request, $id)
    {
        $trip = \App\Models\Trip::findOrFail($id);
        
        $trip->update([
            'end_time' => now(),
            'status' => 'completed'
        ]);

        \App\Models\Vehicle::where('id', $trip->vehicle_id)->update(['status' => 'available']);

        return response()->json(['status' => 'success', 'trip' => $trip]);
    }
}
