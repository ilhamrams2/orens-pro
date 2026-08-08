<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(15);

        $title = 'Log Audit';
        return view('audit_logs.index', compact('title', 'logs'));
    }
}
