<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Get all internal staff users (exclude role 'Supir' for a cleaner staff list)
        $users = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('name', '!=', 'Supir');
            })
            ->orderBy('id', 'desc')
            ->get();
            
        // Get internal staff roles
        $roles = Role::where('name', '!=', 'Supir')->get();
        
        return view('dashboard.users', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.users')->with('success', 'Akun staff baru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent self-deletion
        if (auth()->id() == $user->id) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri!']);
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Akun staff berhasil dihapus dari sistem!');
    }
}
