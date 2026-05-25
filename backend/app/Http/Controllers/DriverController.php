<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::with('user')->orderBy('id', 'desc')->get();
        return view('dashboard.drivers', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'nik' => 'required|string|size:16|unique:drivers,nik',
            'phone' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // Find role 'Supir'
            $roleSupir = Role::where('name', 'Supir')->first();
            if (!$roleSupir) {
                $roleSupir = Role::create(['name' => 'Supir']);
            }

            // 1. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $roleSupir->id,
            ]);

            // 2. Create Driver
            Driver::create([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'phone' => $request->phone,
                'is_active' => true,
            ]);

            DB::commit();
            return redirect()->route('admin.drivers')->with('success', 'Supir baru dan Akun Login berhasil didaftarkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal mendaftarkan supir: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Delete associated User (which cascades or we delete manually)
            $user = $driver->user;
            $driver->delete();
            if ($user) {
                $user->delete();
            }
            
            DB::commit();
            return redirect()->route('admin.drivers')->with('success', 'Data supir dan akun login berhasil dihapus dari sistem!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus supir: ' . $e->getMessage()]);
        }
    }
}
