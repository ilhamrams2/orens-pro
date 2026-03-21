<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Organisation;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceSessionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = AttendanceSession::with(['organisation', 'division', 'creator']);

        if ($user->role === 'leader') {
            $query->where('division_id', $user->division_id);
        }

        $sessions = $query->latest()->get();

        if (request()->expectsJson()) {
            return response()->json($sessions);
        }

        return view('sessions.index', compact('sessions'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $organisations = Organisation::all();
        $divisions = $user->role === 'admin' ? Division::all() : Division::where('id', $user->division_id)->get();
        
        return view('sessions.create', compact('organisations', 'divisions'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'title' => 'required|string|max:200',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'organisation_id' => 'required|exists:organisations,id',
            'division_id' => $user->role === 'admin' ? 'nullable|exists:divisions,id' : 'required',
        ]);

        $divisionId = $user->role === 'admin' ? $request->division_id : $user->division_id;

        $session = AttendanceSession::create([
            'organisation_id' => $user->role === 'admin' ? $request->organisation_id : $user->organisation_id,
            'division_id' => $divisionId,
            'title' => $request->title,
            'session_date' => $request->session_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'qr_token' => Str::random(32),
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json($session, 201);
        }

        return redirect()->route('sessions.index')->with('success', 'Session created successfully.');
    }

    public function edit(Request $request, AttendanceSession $session)
    {
        $user = $request->user();
        if ($user->role === 'leader' && $session->division_id !== $user->division_id) {
            abort(403);
        }

        $organisations = Organisation::all();
        $divisions = $user->role === 'admin' ? Division::all() : Division::where('id', $user->division_id)->get();

        return view('sessions.edit', compact('session', 'organisations', 'divisions'));
    }

    public function update(Request $request, AttendanceSession $session)
    {
        $user = $request->user();
        if ($user->role === 'leader' && $session->division_id !== $user->division_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'is_active' => 'required|boolean',
        ]);

        $session->update($request->all());

        if ($request->expectsJson()) {
            return response()->json($session);
        }

        return redirect()->route('sessions.index')->with('success', 'Session updated successfully.');
    }

    public function destroy(Request $request, AttendanceSession $session)
    {
        $user = $request->user();
        if ($user->role === 'leader' && $session->division_id !== $user->division_id) {
            abort(403);
        }

        $session->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('sessions.index')->with('success', 'Session deleted successfully.');
    }
}
