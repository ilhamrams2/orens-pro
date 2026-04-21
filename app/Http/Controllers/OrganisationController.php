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
        $organisations = Organisation::all();
        return view('organisations.index', compact('organisations'));
    }

    public function create(Request $request)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        return view('organisations.create');
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

        return redirect()->route('organisations.index')->with('success', 'Organisation created successfully.');
    }

    public function edit(Request $request, Organisation $organisation)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        return view('organisations.edit', compact('organisation'));
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

        return redirect()->route('organisations.index')->with('success', 'Organisation updated successfully.');
    }

    public function destroy(Request $request, Organisation $organisation)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403);
        }
        $organisation->delete();
        return redirect()->route('organisations.index')->with('success', 'Organisation deleted successfully.');
    }
}
