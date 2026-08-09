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
        $selectedOrgId = $request->query('organisation_id');
        $selectedDivisionId = $request->query('division_id');
        $selectedRole = $request->query('role');

        $organisations = Organisation::all();

        // Division query for Eskul dropdown/filter
        $divisionsQuery = Division::query();
        if ($user->role === 'pembina' || $user->role === 'pengurus') {
            $divisionsQuery->where('organisation_id', $user->organisation_id);
        } elseif ($selectedOrgId) {
            $divisionsQuery->where('organisation_id', $selectedOrgId);
        }
        $divisions = $divisionsQuery->get();

        $baseQuery = User::where('id', '!=', $user->id);

        if ($user->role === 'pembina' || $user->role === 'pengurus') {
            $baseQuery->where('organisation_id', $user->organisation_id);
        } elseif ($selectedOrgId) {
            $baseQuery->where('organisation_id', $selectedOrgId);
        }

        // Apply Eskul/Division filter if selected
        if ($selectedDivisionId) {
            if ($selectedDivisionId === 'none') {
                $baseQuery->whereNull('division_id');
            } else {
                $baseQuery->where('division_id', $selectedDivisionId);
            }
        }

        // Role counts for sub-tabs under selected Eskul
        $totalCount = (clone $baseQuery)->count();
        $memberCount = (clone $baseQuery)->where('role', 'member')->count();
        $pengurusCount = (clone $baseQuery)->where('role', 'pengurus')->count();
        $pembinaCount = (clone $baseQuery)->where('role', 'pembina')->count();
        $superadminCount = (clone $baseQuery)->where('role', 'superadmin')->count();

        $query = (clone $baseQuery)
            ->with(['organisation', 'division'])
            ->withCount(['attendances' => function($q) use ($lastResetAt) {
                $q->where('status', 'hadir');
                if ($lastResetAt) {
                    $q->where('created_at', '>', $lastResetAt);
                }
            }]);

        if ($selectedRole && in_array($selectedRole, ['superadmin', 'pembina', 'pengurus', 'member'])) {
            $query->where('role', $selectedRole);
        }

        $users = $query->paginate(15)->withQueryString();
        
        $users->getCollection()->transform(function($u) {
            $count = $u->attendances_count;
            if ($count >= 4) {
                $u->grade = 'A';
                $u->grade_class = 'bg-green-50 text-green-600 border-green-100';
            } elseif ($count >= 2) {
                $u->grade = 'B';
                $u->grade_class = 'bg-blue-50 text-blue-600 border-blue-100';
            } else {
                $u->grade = '-';
                $u->grade_class = 'bg-gray-50 text-gray-500 border-gray-100';
            }
            return $u;
        });

        $title = 'Anggota';
        $organisation = $user->organisation;
        return view('users.index', compact(
            'title',
            'users', 
            'organisation', 
            'organisations',
            'divisions',
            'selectedOrgId',
            'selectedDivisionId',
            'selectedRole', 
            'totalCount', 
            'memberCount', 
            'pengurusCount', 
            'pembinaCount', 
            'superadminCount'
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = $request->user();
        $orgId = (in_array($user->role, ['pembina', 'pengurus'])) ? $user->organisation_id : null;
        
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

        if (in_array($user->role, ['pembina', 'pengurus'])) {
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
        if ($user->role !== 'pembina' && $user->role !== 'superadmin') {
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
        if ($user->role === 'pembina') {
            $organisations = Organisation::where('id', $user->organisation_id)->get();
            $divisions = Division::where('organisation_id', $user->organisation_id)->get();
        } elseif ($user->role === 'superadmin') {
            $organisations = Organisation::all();
            $divisions = Division::all();
        } else {
            abort(403);
        }
        $title = 'Tambah Anggota';
        return view('users.create', compact('title', 'organisations', 'divisions'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'superadmin' && $user->role !== 'pembina') {
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
                        $fail('Email harus menggunakan domain prestasiprima (@smkprestasiprima.sch.id atau @smaprestasiprima.sch.id).');
                    }
                },
            ],
            'password' => 'required|min:8',
        ];

        if ($user->role === 'pembina') {
            $rules['role'] = 'required|in:pengurus,member';
            $rules['organisation_id'] = 'required|in:'.$user->organisation_id;
            $rules['division_id'] = 'nullable|exists:divisions,id';
        } elseif ($user->role === 'superadmin') {
            $rules['role'] = 'required|in:superadmin,pembina,pengurus,member';
            $rules['organisation_id'] = $request->role === 'superadmin' ? 'nullable' : 'required|exists:organisations,id';
            $rules['division_id'] = 'nullable|exists:divisions,id';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        if ($user->role === 'pembina') {
            $data['role'] = $request->role;
            $data['organisation_id'] = $user->organisation_id;
            $data['division_id'] = $request->division_id;
        } else {
            $data['role'] = $request->role;
            $data['organisation_id'] = $request->role === 'superadmin' ? null : $request->organisation_id;
            $data['division_id'] = $request->role === 'superadmin' ? null : $request->division_id;
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    public function edit(Request $request, User $user)
    {
        $authUser = $request->user();
        if ($authUser->role === 'pembina' && $user->organisation_id !== $authUser->organisation_id) {
            abort(403);
        } elseif ($authUser->role !== 'superadmin' && $authUser->role !== 'pembina') {
            abort(403);
        }

        if ($authUser->role === 'pembina') {
            $organisations = Organisation::where('id', $authUser->organisation_id)->get();
            $divisions = Division::where('organisation_id', $authUser->organisation_id)->get();
        } else {
            $organisations = Organisation::all();
            $divisions = Division::all();
        }
        $title = 'Edit Anggota';
        return view('users.edit', compact('title', 'user', 'organisations', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        $authUser = $request->user();
        if ($authUser->role === 'pembina' && ($user->organisation_id !== $authUser->organisation_id || (int)$request->organisation_id !== $authUser->organisation_id)) {
            abort(403);
        } elseif ($authUser->role !== 'superadmin' && $authUser->role !== 'pembina') {
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
                        $fail('Email harus menggunakan domain prestasiprima (@smkprestasiprima.sch.id atau @smaprestasiprima.sch.id).');
                    }
                },
            ],
            'password' => 'nullable|min:8',
        ];

        if ($authUser->role === 'pembina') {
            $rules['role'] = 'required|in:pengurus,member';
            $rules['organisation_id'] = 'required|in:'.$authUser->organisation_id;
            $rules['division_id'] = 'nullable|exists:divisions,id';
        } elseif ($authUser->role === 'superadmin') {
            $rules['role'] = 'required|in:superadmin,pembina,pengurus,member';
            $rules['organisation_id'] = $request->role === 'superadmin' ? 'nullable' : 'required|exists:organisations,id';
            $rules['division_id'] = 'nullable|exists:divisions,id';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($authUser->role === 'pembina') {
            $data['role'] = $request->role;
            $data['organisation_id'] = $authUser->organisation_id;
            $data['division_id'] = $request->division_id;
        } elseif ($authUser->role === 'superadmin') {
            $data['role'] = $request->role;
            $data['organisation_id'] = $request->role === 'superadmin' ? null : $request->organisation_id;
            $data['division_id'] = $request->role === 'superadmin' ? null : $request->division_id;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        $authUser = $request->user();
        if ($authUser->role === 'pembina' && $user->organisation_id !== $authUser->organisation_id) {
            abort(403);
        } elseif ($authUser->role !== 'superadmin' && $authUser->role !== 'pembina') {
            abort(403);
        }

        if ($user->id === $authUser->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function importCsv(Request $request)
    {
        $authUser = $request->user();
        if ($authUser->role !== 'superadmin' && $authUser->role !== 'pembina') {
            abort(403);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file CSV.');
        }

        // Auto-detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = ',';
        if ($firstLine !== false) {
            $semiCount = substr_count($firstLine, ';');
            $commaCount = substr_count($firstLine, ',');
            if ($semiCount > $commaCount) {
                $delimiter = ';';
            }
        }

        // Read header
        $header = fgetcsv($handle, 1000, $delimiter);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong.');
        }

        // Clean headers (remove BOM or spaces)
        $header = array_map(function($h) {
            return trim(strtolower(preg_replace('/[\x{FEFF}\x{FFFE}]/u', '', $h)));
        }, $header);

        // Required headers: nama, email, password
        $required = ['nama', 'email', 'password'];
        foreach ($required as $req) {
            if (!in_array($req, $header)) {
                fclose($handle);
                return back()->with('error', "Format CSV salah. Kolom wajib yang harus ada di baris pertama: " . implode(', ', $required) . ". Pembatas terdeteksi: '" . $delimiter . "'");
            }
        }

        $rowNum = 1;
        $successCount = 0;
        $errors = [];

        // DB Transaction for safety
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $rowNum++;
                // Skip empty lines
                if (count($row) === 1 && empty($row[0])) {
                    continue;
                }

                if (count($row) !== count($header)) {
                    $errors[] = "Baris $rowNum: Jumlah kolom (" . count($row) . ") tidak sesuai dengan header (" . count($header) . ").";
                    continue;
                }

                $data = array_combine($header, $row);
                if (!$data) {
                    $errors[] = "Baris $rowNum: Gagal memproses data baris.";
                    continue;
                }

                $name = trim($data['nama'] ?? '');
                $email = trim($data['email'] ?? '');
                $password = trim($data['password'] ?? '');
                $phone = trim($data['telepon'] ?? '');
                $divName = trim($data['divisi'] ?? '');
                $orgName = trim($data['organisasi'] ?? '');

                // Validation
                if (empty($name) || empty($email) || empty($password)) {
                    $errors[] = "Baris $rowNum: Nama, Email, dan Password wajib diisi.";
                    continue;
                }

                // Domain check
                $allowedDomains = ['smkprestasiprima.sch.id', 'smaprestasiprima.sch.id'];
                $emailDomain = substr(strrchr($email, "@"), 1);
                if (!in_array($emailDomain, $allowedDomains)) {
                    $errors[] = "Baris $rowNum: Domain email $email tidak diizinkan. Harus menggunakan domain sekolah (@smkprestasiprima.sch.id atau @smaprestasiprima.sch.id).";
                    continue;
                }

                // Email uniqueness
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris $rowNum: Email $email sudah digunakan.";
                    continue;
                }

                // Resolve Organisation ID
                $orgId = null;
                if ($authUser->role === 'superadmin') {
                    if (empty($orgName)) {
                        $errors[] = "Baris $rowNum: Kolom organisasi wajib diisi untuk Superadmin.";
                        continue;
                    }
                    $organisation = Organisation::where('name', $orgName)->first();
                    if (!$organisation) {
                        $errors[] = "Baris $rowNum: Organisasi '$orgName' tidak ditemukan.";
                        continue;
                    }
                    $orgId = $organisation->id;
                } else {
                    $orgId = $authUser->organisation_id;
                }

                // Resolve Division ID (Optional)
                $divisionId = null;
                if (!empty($divName)) {
                    $division = Division::where('organisation_id', $orgId)
                        ->where('name', $divName)
                        ->first();
                    if (!$division) {
                        $errors[] = "Baris $rowNum: Divisi '$divName' tidak ditemukan di organisasi terkait.";
                        continue;
                    }
                    $divisionId = $division->id;
                }

                // Create User
                User::create([
                    'organisation_id' => $orgId,
                    'division_id' => $divisionId,
                    'name' => $name,
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'phone' => $phone,
                    'role' => 'member',
                    'is_active' => true,
                ]);

                $successCount++;
            }

            if (!empty($errors)) {
                \Illuminate\Support\Facades\DB::rollBack();
                fclose($handle);
                return back()->with('error', 'Gagal mengimpor CSV. Detail error:<br>' . implode('<br>', array_slice($errors, 0, 5)) . (count($errors) > 5 ? '<br>...dan ' . (count($errors) - 5) . ' error lainnya.' : ''));
            }

            \Illuminate\Support\Facades\DB::commit();
            fclose($handle);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        return back()->with('success', "Berhasil mengimpor $successCount member.");
    }

    public function downloadCsvTemplate(Request $request)
    {
        $authUser = $request->user();
        if ($authUser->role !== 'superadmin' && $authUser->role !== 'pembina') {
            abort(403);
        }

        $filename = 'template_import_anggota.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($authUser) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($authUser->role === 'superadmin') {
                fputcsv($file, ['nama', 'email', 'password', 'telepon', 'divisi', 'organisasi']);
                fputcsv($file, ['Ahmad Pratama', 'ahmad@smkprestasiprima.sch.id', 'Password123!', '081234567890', 'Pengurus', 'PRAMUKA']);
                fputcsv($file, ['Siti Rahma', 'siti@smaprestasiprima.sch.id', 'Password123!', '089876543210', 'Humas', 'PASKIBRA']);
            } else {
                fputcsv($file, ['nama', 'email', 'password', 'telepon', 'divisi']);
                fputcsv($file, ['Ahmad Pratama', 'ahmad@smkprestasiprima.sch.id', 'Password123!', '081234567890', 'Pengurus']);
                fputcsv($file, ['Siti Rahma', 'siti@smaprestasiprima.sch.id', 'Password123!', '089876543210', 'Humas']);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
