<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StockOpnameController extends Controller
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
        $opnames = StockOpname::with(['item', 'user'])->orderBy('opname_date', 'desc')->get();

        return view('dashboard.stock_opname', compact('items', 'opnames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'physical_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $item = Item::findOrFail($request->item_id);
        $systemStock = $item->current_stock;
        $variance = $request->physical_stock - $systemStock;

        StockOpname::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'opname_date' => now(),
            'system_stock' => $systemStock,
            'physical_stock' => $request->physical_stock,
            'variance' => $variance,
            'notes' => $request->notes,
        ]);

        // Update current stock of item
        $item->update(['current_stock' => $request->physical_stock]);

        return redirect()->route('stock-opname')->with('success', 'Stok Opname berhasil dicatat!');
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'physical_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $opname = StockOpname::findOrFail($id);
        $item = $opname->item;

        $newPhysicalStock = $request->physical_stock;
        $variance = $newPhysicalStock - $opname->system_stock;

        $opname->update([
            'physical_stock' => $newPhysicalStock,
            'variance' => $variance,
            'notes' => $request->notes,
        ]);

        // Also update the item's current stock
        if ($item) {
            $item->update(['current_stock' => $newPhysicalStock]);
        }

        return redirect()->route('stock-opname')->with('success', 'Stok Opname berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $opname = StockOpname::findOrFail($id);
        $opname->delete();

        return redirect()->route('stock-opname')->with('success', 'Catatan Stok Opname berhasil dihapus!');
    }

    public function exportPdf()
    {
        $opnames = StockOpname::with(['item', 'user'])->orderBy('opname_date', 'desc')->get();
        
        $pdf = Pdf::loadView('reports.stock_opname_pdf', compact('opnames'));
        return $pdf->download('Laporan_Stok_Opname_PT_Erickman.pdf');
    }
}
