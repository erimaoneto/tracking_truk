<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GpsLogController extends Controller
{
    public function sync(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'logs' => 'required|array',
            'logs.*.latitude' => 'required|numeric',
            'logs.*.longitude' => 'required|numeric',
            'logs.*.speed' => 'required|numeric',
            'logs.*.timestamp' => 'required'
        ]);

        $trip = \App\Models\Trip::findOrFail($request->trip_id);
        
        $inserts = [];
        foreach ($request->logs as $log) {
            $inserts[] = [
                'trip_id' => $trip->id,
                'latitude' => $log['latitude'],
                'longitude' => $log['longitude'],
                'speed' => $log['speed'],
                'recorded_at' => date('Y-m-d H:i:s', strtotime($log['timestamp'])),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \App\Models\GpsLog::insert($inserts);

        return response()->json(['status' => 'success', 'inserted' => count($inserts)]);
    }
}
