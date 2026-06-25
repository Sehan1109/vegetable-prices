<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vegetable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class VegetableController extends Controller
{
    public function index(Request $request)
    {
        $query = Vegetable::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('category', 'ilike', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $vegetables = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Vegetable::select('category')->distinct()->pluck('category');

        return view('admin.vegetables.index', compact('vegetables', 'categories'));
    }

    public function create()
    {
        $categories = Vegetable::select('category')->distinct()->pluck('category');
        return view('admin.vegetables.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'slug' => 'nullable|string|max:255|unique:vegetables,slug',
            'image' => 'nullable|image|max:2048',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vegetables', 'public');
            $validated['image'] = $path;
        }

        Vegetable::create($validated);

        return redirect()->route('admin.vegetables.index')->with('success', 'Vegetable created successfully.');
    }

    public function edit(Vegetable $vegetable)
    {
        $categories = Vegetable::select('category')->distinct()->pluck('category');
        return view('admin.vegetables.edit', compact('vegetable', 'categories'));
    }

    public function update(Request $request, Vegetable $vegetable)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'slug' => 'required|string|max:255|unique:vegetables,slug,' . $vegetable->id,
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($vegetable->image) {
                Storage::disk('public')->delete($vegetable->image);
            }
            $path = $request->file('image')->store('vegetables', 'public');
            $validated['image'] = $path;
        }

        $vegetable->update($validated);

        return redirect()->route('admin.vegetables.index')->with('success', 'Vegetable updated successfully.');
    }

    public function destroy(Vegetable $vegetable)
    {
        if ($vegetable->priceRecords()->count() > 0) {
            return redirect()->route('admin.vegetables.index')->with('error', 'Cannot delete vegetable with existing price records. Change status to inactive instead.');
        }

        if ($vegetable->image) {
            Storage::disk('public')->delete($vegetable->image);
        }

        $vegetable->delete();
        return redirect()->route('admin.vegetables.index')->with('success', 'Vegetable deleted successfully.');
    }
}
