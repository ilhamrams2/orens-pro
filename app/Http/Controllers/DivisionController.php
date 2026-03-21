<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Organisation;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::with('organisation')->get();
        return view('divisions.index', compact('divisions'));
    }

    public function create()
    {
        $organisations = Organisation::all();
        return view('divisions.create', compact('organisations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'name' => 'required|string|max:150',
        ]);

        Division::create($request->all());

        return redirect()->route('divisions.index')->with('success', 'Division created successfully.');
    }

    public function edit(Division $division)
    {
        $organisations = Organisation::all();
        return view('divisions.edit', compact('division', 'organisations'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'name' => 'required|string|max:150',
        ]);

        $division->update($request->all());

        return redirect()->route('divisions.index')->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return redirect()->route('divisions.index')->with('success', 'Division deleted successfully.');
    }
}
