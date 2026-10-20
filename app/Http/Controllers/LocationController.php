<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $locations = Location::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('gedung', 'like', "%{$search}%")
                    ->orWhere('lantai', 'like', "%{$search}%")
                    ->orWhere('ruangan', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->withCount('assets')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('locations.index', compact(
            'locations',
            'search'
        ));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'gedung' => ['nullable', 'string', 'max:255'],
            'lantai' => ['nullable', 'string', 'max:255'],
            'ruangan' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Location::create($validated);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function show(Location $location)
    {
        $location->load('assets');

        return view('locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'gedung' => ['nullable', 'string', 'max:255'],
            'lantai' => ['nullable', 'string', 'max:255'],
            'ruangan' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $location->update($validated);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        if ($location->assets()->exists()) {
            return redirect()
                ->route('locations.index')
                ->with('error', 'Lokasi tidak dapat dihapus karena masih digunakan oleh asset.');
        }

        $location->delete();

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}