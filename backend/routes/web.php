<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContractController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Data Master Group
    Route::prefix('admin')->group(function () {
        // Master Barang
        Route::get('/items', [ItemController::class, 'index'])->name('admin.items');
        Route::post('/items', [ItemController::class, 'store'])->name('admin.items.store');
        Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('admin.items.destroy');
        
        // Master Kendaraan
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('admin.vehicles');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('admin.vehicles.store');
        Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy'])->name('admin.vehicles.destroy');
        
        // Master Supir
        Route::get('/drivers', [DriverController::class, 'index'])->name('admin.drivers');
        Route::post('/drivers', [DriverController::class, 'store'])->name('admin.drivers.store');
        Route::delete('/drivers/{id}', [DriverController::class, 'destroy'])->name('admin.drivers.destroy');
        
        // Master Pengguna Staf Internal
        Route::get('/users', [UserController::class, 'index'])->name('admin.users');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });
    
    // Kontrak Sewa Gas
    Route::get('/contracts', [ContractController::class, 'index'])->name('admin.contracts');
    Route::post('/contracts', [ContractController::class, 'store'])->name('admin.contracts.store');
    Route::delete('/contracts/{id}', [ContractController::class, 'destroy'])->name('admin.contracts.destroy');
    
    // Stok Opname
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname');
    Route::post('/stock-opname', [StockOpnameController::class, 'store'])->name('stock-opname.store');
    Route::get('/stock-opname/export-pdf', [StockOpnameController::class, 'exportPdf'])->name('stock-opname.export-pdf');
});
