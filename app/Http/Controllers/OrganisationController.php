<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        $title = 'Organisasi';
        $organisations = Organisation::all();
        return view('organisations.index', compact('title', 'organisations'));
    }

    public function create(Request $request)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        $title = 'Tambah Organisasi';
        return view('organisations.create', compact('title'));
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Organisation::create($request->all());

        return redirect()->route('organisations.index')->with('success', 'Organisasi berhasil dibuat.');
    }

    public function edit(Request $request, Organisation $organisation)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        $title = 'Edit Organisasi';
        return view('organisations.edit', compact('title', 'organisation'));
    }

    public function update(Request $request, Organisation $organisation)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $organisation->update($request->all());

        return redirect()->route('organisations.index')->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy(Request $request, Organisation $organisation)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        $organisation->delete();
        return redirect()->route('organisations.index')->with('success', 'Organisasi berhasil dihapus.');
    }
}
