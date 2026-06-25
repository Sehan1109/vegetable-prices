<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceRecord;
use App\Models\Market;
use App\Models\Vegetable;
use Illuminate\Http\Request;

class PriceRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = PriceRecord::with(['market', 'vegetable']);

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('date', $request->date);
        }

        if ($request->has('market') && $request->market != '') {
            $query->where('market_id', $request->market);
        }

        if ($request->has('vegetable') && $request->vegetable != '') {
            $query->where('vegetable_id', $request->vegetable);
        }

        $records = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();
        $markets = Market::orderBy('name')->get();
        $vegetables = Vegetable::orderBy('name')->get();

        return view('admin.prices.index', compact('records', 'markets', 'vegetables'));
    }

    public function create()
    {
        $markets = Market::orderBy('name')->get();
        $vegetables = Vegetable::orderBy('name')->get();
        return view('admin.prices.create', compact('markets', 'vegetables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'market_id' => 'required|string|exists:markets,slug',
            'vegetable_id' => 'required|string|exists:vegetables,slug',
            'price' => 'nullable|numeric|min:0',
            'price_yesterday' => 'nullable|numeric|min:0',
            'change_percent' => 'nullable|numeric',
            'trend' => 'nullable|string|in:up,down,flat,none',
        ]);

        PriceRecord::create($validated);

        return redirect()->route('admin.prices.index')->with('success', 'Price record created successfully.');
    }

    public function edit(PriceRecord $price)
    {
        $markets = Market::orderBy('name')->get();
        $vegetables = Vegetable::orderBy('name')->get();
        return view('admin.prices.edit', compact('price', 'markets', 'vegetables'));
    }

    public function update(Request $request, PriceRecord $price)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'market_id' => 'required|string|exists:markets,slug',
            'vegetable_id' => 'required|string|exists:vegetables,slug',
            'price' => 'nullable|numeric|min:0',
            'price_yesterday' => 'nullable|numeric|min:0',
            'change_percent' => 'nullable|numeric',
            'trend' => 'nullable|string|in:up,down,flat,none',
        ]);

        $price->update($validated);

        return redirect()->route('admin.prices.index')->with('success', 'Price record updated successfully.');
    }

    public function destroy(PriceRecord $price)
    {
        $price->delete();
        return redirect()->route('admin.prices.index')->with('success', 'Price record deleted successfully.');
    }
}
