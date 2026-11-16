<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlatController extends Controller
{
    public function index(): View
    {
        $alats = \App\Models\Alat::query()->paginate(15);
        return view('pages.alats.index', ['alats' => $alats]);
    }

    public function create(): View
    {
        $kategoris = \App\Models\Kategori::query()->orderBy('id')->get();
        return view('pages.alats.create', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['nama_alat' => ['required', 'string'], 'kategori_id' => ['required', 'integer', 'exists:kategoris,id'], 'stok' => ['required', 'integer'], 'kondisi' => ['required', 'string']]);
        \App\Models\Alat::create($validated);
        return redirect()->route('alats.index')->with('success', 'Alat created successfully.');
    }

    public function edit(\App\Models\Alat $alat): View
    {
        $kategoris = \App\Models\Kategori::query()->orderBy('id')->get();
        return view('pages.alats.edit', compact('alat', 'kategoris'));
    }

    public function update(Request $request, \App\Models\Alat $alat): RedirectResponse
    {
        $validated = $request->validate(['nama_alat' => ['required', 'string'], 'kategori_id' => ['required', 'integer', 'exists:kategoris,id'], 'stok' => ['required', 'integer'], 'kondisi' => ['required', 'string']]);
        $alat->update($validated);
        return redirect()->route('alats.index')->with('success', 'Alat updated successfully.');
    }

    public function destroy(\App\Models\Alat $alat): RedirectResponse
    {
        $alat->delete();
        return redirect()->route('alats.index')->with('success', 'Alat deleted successfully.');
    }
}
