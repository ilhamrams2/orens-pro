<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Organisation;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Division::with('organisation');

        if ($user->role === 'admin') {
            $query->where('organisation_id', $user->organisation_id);
        } elseif ($user->role !== 'superadmin') {
            abort(403);
        }

        $divisions = $query->get();
        return view('divisions.index', compact('divisions'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            $organisations = Organisation::where('id', $user->organisation_id)->get();
        } elseif ($user->role === 'superadmin') {
            $organisations = Organisation::all();
        } else {
            abort(403);
        }
        return view('divisions.create', compact('organisations'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin' && (int)$request->organisation_id !== $user->organisation_id) {
            abort(403);
        } elseif ($user->role !== 'superadmin' && $user->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'name' => 'required|string|max:150',
        ]);

        Division::create($request->all());

        return redirect()->route('divisions.index')->with('success', 'Division created successfully.');
    }

    public function edit(Request $request, Division $division)
    {
        $user = $request->user();
        if ($user->role === 'admin' && $division->organisation_id !== $user->organisation_id) {
            abort(403);
        } elseif ($user->role !== 'superadmin' && $user->role !== 'admin') {
            abort(403);
        }

        if ($user->role === 'admin') {
            $organisations = Organisation::where('id', $user->organisation_id)->get();
        } else {
            $organisations = Organisation::all();
        }
        return view('divisions.edit', compact('division', 'organisations'));
    }

    public function update(Request $request, Division $division)
    {
        $user = $request->user();
        if ($user->role === 'admin' && ($division->organisation_id !== $user->organisation_id || (int)$request->organisation_id !== $user->organisation_id)) {
            abort(403);
        } elseif ($user->role !== 'superadmin' && $user->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'name' => 'required|string|max:150',
        ]);

        $division->update($request->all());

        return redirect()->route('divisions.index')->with('success', 'Division updated successfully.');
    }

    public function destroy(Request $request, Division $division)
    {
        $user = $request->user();
        if ($user->role === 'admin' && $division->organisation_id !== $user->organisation_id) {
            abort(403);
        } elseif ($user->role !== 'superadmin' && $user->role !== 'admin') {
            abort(403);
        }

        $division->delete();
        return redirect()->route('divisions.index')->with('success', 'Division deleted successfully.');
    }
}
