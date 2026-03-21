<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    public function index()
    {
        $organisations = Organisation::all();
        return view('organisations.index', compact('organisations'));
    }

    public function create()
    {
        return view('organisations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Organisation::create($request->all());

        return redirect()->route('organisations.index')->with('success', 'Organisation created successfully.');
    }

    public function edit(Organisation $organisation)
    {
        return view('organisations.edit', compact('organisation'));
    }

    public function update(Request $request, Organisation $organisation)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $organisation->update($request->all());

        return redirect()->route('organisations.index')->with('success', 'Organisation updated successfully.');
    }

    public function destroy(Organisation $organisation)
    {
        $organisation->delete();
        return redirect()->route('organisations.index')->with('success', 'Organisation deleted successfully.');
    }
}
