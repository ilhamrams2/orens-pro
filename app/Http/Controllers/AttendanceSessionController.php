<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Organisation;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceSessionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AttendanceSession::class);
        AttendanceSession::deactivateExpiredSessions();
        $user = $request->user();
        $query = AttendanceSession::with(['organisation', 'division', 'creator']);

        if ($user->role === 'pengurus') {
            $query->where('division_id', $user->division_id)
                  ->where('organisation_id', $user->organisation_id);
        } elseif ($user->role === 'pembina') {
            $query->where('organisation_id', $user->organisation_id);
        }

        $sessions = $query->latest()->paginate(10);

        if (request()->expectsJson()) {
            return response()->json($sessions);
        }

        return view('sessions.index', compact('sessions'));
    }

    /**
     * Local QR Generation Engine (Singapore Standard)
     * Generates a dynamic QR code based on the 30-second rotating token.
     */
    public function getQr(AttendanceSession $session)
    {
        // Check permissions (Singapore Standard Policy)
        $this->authorize('view', $session);

        // Generate dynamic token
        $dynamicToken = $session->dynamic_token;

        // Create QR locally (No third-party dependency)
        $qrCode = QrCode::size(300)
            ->format('svg')
            ->margin(2)
            ->errorCorrection('H')
            ->color(255, 107, 0) // Brand Color: Orens
            ->generate($dynamicToken);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    public function create(Request $request)
    {
        $this->authorize('create', AttendanceSession::class);
        $user = $request->user();
        
        if ($user->role === 'superadmin') {
            $organisations = Organisation::all();
            $divisions = Division::all();
        } else {
            $organisations = Organisation::where('id', $user->organisation_id)->get();
            $divisions = Division::where('organisation_id', $user->organisation_id);
            if ($user->role === 'pengurus') {
                $divisions->where('id', $user->division_id);
            }
            $divisions = $divisions->get();
        }
        
        return view('sessions.create', compact('organisations', 'divisions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AttendanceSession::class);
        $user = $request->user();

        $request->validate([
            'title' => 'required|string|max:200',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'organisation_id' => 'required|exists:organisations,id',
            'division_id' => 'nullable|exists:divisions,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1',
        ]);

        $divisionId = $request->division_id;
        if ($user->role === 'pengurus') {
            $divisionId = $user->division_id;
        }

        $session = AttendanceSession::create([
            'organisation_id' => $request->organisation_id,
            'division_id' => $divisionId,
            'title' => $request->title,
            'session_date' => $request->session_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius ?? 100, // Default 100m
            'qr_token' => Str::random(32),
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json($session, 201);
        }

        return redirect()->route('sessions.index')->with('success', 'Sesi presensi berhasil dibuat.');
    }

    public function edit(Request $request, AttendanceSession $session)
    {
        $this->authorize('update', $session);
        $user = $request->user();

        if ($user->role === 'superadmin') {
            $organisations = Organisation::all();
            $divisions = Division::all();
        } else {
            $organisations = Organisation::where('id', $user->organisation_id)->get();
            $divisions = Division::where('organisation_id', $user->organisation_id)->get();
        }

        return view('sessions.edit', compact('session', 'organisations', 'divisions'));
    }

    public function update(Request $request, AttendanceSession $session)
    {
        $this->authorize('update', $session);
        $user = $request->user();

        $request->validate([
            'title' => 'required|string|max:200',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        $session->update($request->all());

        if ($request->expectsJson()) {
            return response()->json($session);
        }

        return redirect()->route('sessions.index')->with('success', 'Sesi presensi berhasil diperbarui.');
    }

    public function destroy(Request $request, AttendanceSession $session)
    {
        $this->authorize('delete', $session);
        $user = $request->user();

        $session->delete();

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('sessions.index')->with('success', 'Sesi presensi berhasil dihapus.');
    }
}
