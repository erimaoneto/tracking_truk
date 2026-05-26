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
        $totalVehicles = \App\Models\Vehicle::count();
        $totalDrivers = \App\Models\Driver::count();

        // Get all contracts with vehicles and drivers
        $contracts = \App\Models\Contract::with(['vehicle', 'driver.user', 'trips'])->get();
        
        $now = \Carbon\Carbon::now();
        
        // Categorize contracts dynamically
        $receivedContracts = $contracts->filter(function($c) use ($now) {
            $startDate = \Carbon\Carbon::parse($c->start_date);
            return $c->status === 'active' && $startDate->greaterThan($now);
        });
        
        $ongoingContracts = $contracts->filter(function($c) use ($now) {
            $startDate = \Carbon\Carbon::parse($c->start_date);
            $endDate = \Carbon\Carbon::parse($c->end_date);
            return ($c->status === 'active' && $startDate->lessThanOrEqualTo($now) && $endDate->greaterThanOrEqualTo($now));
        });
        
        $completedContracts = $contracts->filter(function($c) use ($now) {
            $endDate = \Carbon\Carbon::parse($c->end_date);
            return $c->status === 'completed' || $endDate->lessThan($now);
        });

        return view('dashboard.map', compact(
            'ongoingTrips', 
            'availableVehiclesCount',
            'totalVehicles',
            'totalDrivers',
            'receivedContracts',
            'ongoingContracts',
            'completedContracts'
        ));
    }
}
