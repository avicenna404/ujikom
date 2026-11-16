<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $kategoris = \App\Models\Kategori::query()->paginate(15);
        return view('pages.kategoris.index', ['kategoris' => $kategoris]);
    }

    public function create(): View
    {
        return view('pages.kategoris.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['nama_kategori' => ['required', 'string']]);
        \App\Models\Kategori::create($validated);
        return redirect()->route('kategoris.index')->with('success', 'Kategori created successfully.');
    }

    public function edit(\App\Models\Kategori $kategori): View
    {
        return view('pages.kategoris.edit', ['kategori' => $kategori]);
    }


    public function update(Request $request, \App\Models\Kategori $kategori): RedirectResponse
    {
        $validated = $request->validate(['nama_kategori' => ['required', 'string']]);
        $kategori->update($validated);
        return redirect()->route('kategoris.index')->with('success', 'Kategori updated successfully.');
    }

    public function destroy(\App\Models\Kategori $kategori): RedirectResponse
    {
        $kategori->delete();
        return redirect()->route('kategoris.index')->with('success', 'Kategori deleted successfully.');
    }
}
