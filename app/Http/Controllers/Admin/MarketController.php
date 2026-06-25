<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        $query = Market::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('district', 'ilike', '%' . $request->search . '%')
                  ->orWhere('province', 'ilike', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $markets = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.markets.index', compact('markets'));
    }

    public function create()
    {
        return view('admin.markets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'coordinates' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:markets,slug',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'] . '-' . $validated['district']);
        }

        Market::create($validated);

        return redirect()->route('admin.markets.index')->with('success', 'Market created successfully.');
    }

    public function edit(Market $market)
    {
        return view('admin.markets.edit', compact('market'));
    }

    public function update(Request $request, Market $market)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'coordinates' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:markets,slug,' . $market->id,
        ]);

        $market->update($validated);

        return redirect()->route('admin.markets.index')->with('success', 'Market updated successfully.');
    }

    public function destroy(Market $market)
    {
        // Don't delete if it has price records to avoid orphaned data
        if ($market->priceRecords()->count() > 0) {
            return redirect()->route('admin.markets.index')->with('error', 'Cannot delete market with existing price records. Change status to inactive instead.');
        }

        $market->delete();
        return redirect()->route('admin.markets.index')->with('success', 'Market deleted successfully.');
    }
}
