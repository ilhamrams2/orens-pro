<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $lastResetAt = $user->organisation?->last_grade_reset_at;

        $query = User::with(['organisation', 'division'])
            ->withCount(['attendances' => function($q) use ($lastResetAt) {
                $q->where('status', 'hadir');
                if ($lastResetAt) {
                    $q->where('created_at', '>', $lastResetAt);
                }
            }]);

        if ($user->role === 'admin' || $user->role === 'leader') {
            $query->where('organisation_id', $user->organisation_id);
        } elseif ($user->role !== 'superadmin') {
            abort(403);
        }

        $users = $query->get()->map(function($u) {
            $count = $u->attendances_count;
            if ($count >= 4) {
                $u->grade = 'A';
                $u->grade_class = 'bg-green-100 text-green-800';
            } elseif ($count >= 2) {
                $u->grade = 'B';
                $u->grade_class = 'bg-blue-100 text-blue-800';
            } else {
                $u->grade = '-';
                $u->grade_class = 'bg-gray-100 text-gray-800';
            }
            return $u;
        });

        $organisation = $user->organisation;
        return view('users.index', compact('users', 'organisation'));
    }

    public function exportExcel(Request $request)
    {
        $user = $request->user();
        $orgId = (in_array($user->role, ['admin', 'leader'])) ? $user->organisation_id : null;
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MembersExport($orgId), 'members_export_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $query = User::with(['organisation', 'division'])
            ->withCount(['attendances' => function($q) {
                $q->where('status', 'hadir');
            }])
            ->where('role', 'member');

        if (in_array($user->role, ['admin', 'leader'])) {
            $query->where('organisation_id', $user->organisation_id);
        }

        $users = $query->get()->map(function($u) {
            $count = $u->attendances_count;
            if ($count >= 4) {
                $u->grade = 'A';
            } elseif ($count >= 2) {
                $u->grade = 'B';
            } else {
                $u->grade = '-';
            }
            return $u;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.members_pdf', [
            'users' => $users,
            'organisation' => $user->organisation->name ?? 'Global System',
            'date' => now()->format('d M Y')
        ]);

        return $pdf->download('members_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function resetGrades(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'admin' && $user->role !== 'superadmin') {
            abort(403);
        }

        $organisation = $user->organisation;
        if (!$organisation && $user->role === 'superadmin') {
            return back()->with('error', 'Silakan pilih organisasi terlebih dahulu atau gunakan fitur ini di level organisasi.');
        }

        $organisation->update([
            'last_grade_reset_at' => now()
        ]);

        return back()->with('success', 'Periode penilaian telah di-reset untuk ' . $organisation->name);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            $organisations = Organisation::where('id', $user->organisation_id)->get();
            $divisions = Division::where('organisation_id', $user->organisation_id)->get();
        } elseif ($user->role === 'superadmin') {
            $organisations = Organisation::all();
            $divisions = Division::all();
        } else {
            abort(403);
        }
        return view('users.create', compact('organisations', 'divisions'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'superadmin' && $user->role !== 'admin') {
            abort(403);
        }
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'unique:users,email',
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['smkprestasiprima.sch.id', 'smaprestasiprima.sch.id'];
                    $domain = substr(strrchr($value, "@"), 1);
                    if (!in_array($domain, $allowedDomains)) {
                        $fail('The email must belong to a prestasiprima domain (@smkprestasiprima.sch.id or @smaprestasiprima.sch.id).');
                    }
                },
            ],
            'password' => 'required|min:8',
        ];

        if ($user->role === 'admin') {
            $rules['role'] = 'required|in:leader,member';
            $rules['organisation_id'] = 'required|in:'.$user->organisation_id;
            $rules['division_id'] = 'nullable|exists:divisions,id';
        } elseif ($user->role === 'superadmin') {
            $rules['role'] = 'required|in:admin,leader,member';
            $rules['organisation_id'] = 'required|exists:organisations,id';
            $rules['division_id'] = 'nullable|exists:divisions,id';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        if ($user->role === 'admin') {
            $data['role'] = $request->role;
            $data['organisation_id'] = $user->organisation_id;
            $data['division_id'] = $request->division_id;
        } else {
            $data['role'] = $request->role;
            $data['organisation_id'] = $request->organisation_id;
            $data['division_id'] = $request->division_id;
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(Request $request, User $user)
    {
        $authUser = $request->user();
        if ($authUser->role === 'admin' && $user->organisation_id !== $authUser->organisation_id) {
            abort(403);
        } elseif ($authUser->role !== 'superadmin' && $authUser->role !== 'admin') {
            abort(403);
        }

        if ($authUser->role === 'admin') {
            $organisations = Organisation::where('id', $authUser->organisation_id)->get();
            $divisions = Division::where('organisation_id', $authUser->organisation_id)->get();
        } else {
            $organisations = Organisation::all();
            $divisions = Division::all();
        }
        return view('users.edit', compact('user', 'organisations', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        $authUser = $request->user();
        if ($authUser->role === 'admin' && ($user->organisation_id !== $authUser->organisation_id || (int)$request->organisation_id !== $authUser->organisation_id)) {
            abort(403);
        } elseif ($authUser->role !== 'superadmin' && $authUser->role !== 'admin') {
            abort(403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', Rule::unique('users')->ignore($user->id),
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['smkprestasiprima.sch.id', 'smaprestasiprima.sch.id'];
                    $domain = substr(strrchr($value, "@"), 1);
                    if (!in_array($domain, $allowedDomains)) {
                        $fail('The email must belong to a prestasiprima domain (@smkprestasiprima.sch.id or @smaprestasiprima.sch.id).');
                    }
                },
            ],
            'password' => 'nullable|min:8',
        ];

        if ($authUser->role === 'admin') {
            $rules['role'] = 'required|in:leader,member';
            $rules['organisation_id'] = 'required|in:'.$authUser->organisation_id;
            $rules['division_id'] = 'nullable|exists:divisions,id';
        } elseif ($authUser->role === 'superadmin') {
            $rules['role'] = 'required|in:admin,leader,member';
            $rules['organisation_id'] = 'required|exists:organisations,id';
            $rules['division_id'] = 'nullable|exists:divisions,id';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($authUser->role === 'admin') {
            $data['role'] = $request->role;
            $data['organisation_id'] = $authUser->organisation_id;
            $data['division_id'] = $request->division_id;
        } elseif ($authUser->role === 'superadmin') {
            $data['role'] = $request->role;
            $data['organisation_id'] = $request->organisation_id;
            $data['division_id'] = $request->division_id;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        $authUser = $request->user();
        if ($authUser->role === 'admin' && $user->organisation_id !== $authUser->organisation_id) {
            abort(403);
        } elseif ($authUser->role !== 'superadmin' && $authUser->role !== 'admin') {
            abort(403);
        }

        if ($user->id === $authUser->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
