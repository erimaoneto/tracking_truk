<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $ongoingTrips = \App\Models\Trip::with(['vehicle', 'driver.user'])
            ->where('status', 'ongoing')
            ->get()
            ->map(function ($trip) {
                // Get the latest GPS log for this trip
                $latestGps = \App\Models\GpsLog::where('trip_id', $trip->id)
                    ->orderBy('recorded_at', 'desc')
                    ->first();
                $trip->latest_gps_log = $latestGps;
                return $trip;
            });

        $availableVehiclesCount = \App\Models\Vehicle::where('status', 'available')->count();

        return view('dashboard.map', compact('ongoingTrips', 'availableVehiclesCount'));
    }
}
