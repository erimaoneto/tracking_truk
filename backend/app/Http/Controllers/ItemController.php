<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        // Auto-populate items if table is empty
        if (Item::count() == 0) {
            Item::create(['name' => 'Tabung Gas LPG 3kg', 'category' => 'Gas', 'current_stock' => 150]);
            Item::create(['name' => 'Tabung Gas LPG 12kg', 'category' => 'Gas', 'current_stock' => 75]);
            Item::create(['name' => 'Ban Serep Truk Engkel', 'category' => 'Sparepart', 'current_stock' => 5]);
            Item::create(['name' => 'Oli Mesin Meditran SX', 'category' => 'Pelumas', 'current_stock' => 12]);
        }

        $items = Item::orderBy('name', 'asc')->get();
        return view('dashboard.items', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:items,name',
            'category' => 'required|string|max:255',
            'current_stock' => 'required|integer|min:0',
        ]);

        Item::create([
            'name' => $request->name,
            'category' => $request->category,
            'current_stock' => $request->current_stock,
        ]);

        return redirect()->route('admin.items')->with('success', 'Barang baru berhasil ditambahkan ke Master Data!');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        
        // Prevent deleting if it is used in stock_opnames (optional, let's cascade delete or check)
        $item->delete();

        return redirect()->route('admin.items')->with('success', 'Barang berhasil dihapus dari Master Data!');
    }
}
