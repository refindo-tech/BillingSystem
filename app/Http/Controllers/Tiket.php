<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keluhan;

class Tiket extends Controller
{
    //
}
public function index()
{
    $keluhan = Keluhan::all();
    return view('tiket.index', compact('keluhan'));
}

// public function create()
// {
//     return view('keluhan.create');
// }

public function store(Request $request)
{
    $request->validate([
        'id_pelanggan' => 'required|integer',
        'judul_keluhan' => 'required|string|max:50',
        'nomor_tiket' => 'required|string|max:255',
        'isi_keluhan' => 'required|string',
        'gambar' => 'nullable|string',
        'masalah' => 'nullable|string',
        'no_wa' => 'required|string|max:15',
        'status_keluhan' => 'required|in:menunggu,proses,selesai,tidak merespon',
        'tanggal' => 'required|date',
        'user_id' => 'nullable|integer',
    ]);

    Keluhan::create($request->all());

    return redirect()->route('keluhan.index')->with('success', 'Keluhan created successfully.');
}

// public function show($id)
// {
//     $keluhan = Keluhan::findOrFail($id);
//     return view('keluhan.show', compact('keluhan'));
// }

// public function edit($id)
// {
//     $keluhan = Keluhan::findOrFail($id);
//     return view('keluhan.edit', compact('keluhan'));
// }

public function update(Request $request, $id)
{
    $request->validate([
        'id_pelanggan' => 'required|integer',
        'judul_keluhan' => 'required|string|max:50',
        'nomor_tiket' => 'required|string|max:255',
        'isi_keluhan' => 'required|string',
        'gambar' => 'nullable|string',
        'masalah' => 'nullable|string',
        'no_wa' => 'required|string|max:15',
        'status_keluhan' => 'required|in:menunggu,proses,selesai,tidak merespon',
        'tanggal' => 'required|date',
        'user_id' => 'nullable|integer',
    ]);

    $keluhan = Keluhan::findOrFail($id);
    $keluhan->update($request->all());

    return redirect()->route('tiket.index')->with('success', 'Keluhan updated successfully.');
}

public function destroy($id)
{
    $keluhan = Keluhan::findOrFail($id);
    $keluhan->delete();

    return redirect()->route('tiket.index')->with('success', 'Keluhan deleted successfully.');
}