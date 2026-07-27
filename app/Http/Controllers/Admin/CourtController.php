<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function index()
    {
        $courts = Court::orderBy('name')->get();

        return view('admin.courts.index', compact('courts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'price_per_hour' => 'required|numeric|min:0',
        ]);

        Court::create($request->only('name', 'price_per_hour') + ['is_active' => true]);

        return back()->with('success', 'Lapangan ditambahkan.');
    }

    public function update(Request $request, Court $court)
    {
        $request->validate(['is_active' => 'required|boolean']);

        $court->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Status lapangan diperbarui.');
    }

    public function destroy(Court $court)
    {
        $court->delete();

        return back()->with('success', 'Lapangan dihapus.');
    }
}
